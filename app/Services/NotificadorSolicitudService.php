<?php

namespace App\Services;

use App\Models\Solicitud;
use App\Models\User;
use App\Models\NotificacionLocal;
use App\Services\Contracts\WhatsAppServiceInterface;
use App\Models\Configuracion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NotificadorSolicitudService
{
    protected $whatsapp;

    public function __construct(WhatsAppServiceInterface $whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    public function notificar(Solicitud $solicitud, string $etapa)
    {
        $tipo = $solicitud->tipo;
        
        $config = config("notificaciones_solicitudes.$tipo.$etapa");

        if (!$config) return;

        $usuarios = $this->resolverDestinatarios($config, $solicitud);
        
        Log::info('👥 Usuarios destinatarios:', $usuarios->pluck('id', 'name')->toArray());

        $mensaje = $this->generarMensaje($solicitud, $etapa, $tipo);

        foreach ($usuarios as $user) {
            $numero = trim($user->telefono);

            if (preg_match('/^\d{8}$/', $numero)) {
                $telefono = '+591' . $numero;

                if (Configuracion::getValor('notificaciones_twilio') === '1') {
                    Log::info("📲 Enviando mensaje a: {$user->name} – $telefono");

                    if (is_array($mensaje) && isset($mensaje['template'], $mensaje['params'])) {
                        $this->whatsapp->sendWhatsAppTemplateMessage(
                            $telefono,
                            $mensaje['template'],
                            $mensaje['params']
                        );

                    } else {
                        $this->whatsapp->sendWhatsAppMessage($telefono, $mensaje);
                    }
                } else {
                    Log::info("🔕 WhatsApp desactivado. No se envió mensaje a {$user->name} – $telefono");
                }
            } else {
                Log::warning("Número inválido de WhatsApp para usuario {$user->name}: {$user->telefono}. Se omitió el envío.");
            }
        }
        $this->notificarLocalmente($solicitud, $usuarios, $etapa);
    }

    protected function resolverDestinatarios(array $config, Solicitud $solicitud)
    {
        $usuarios = collect();

        if (isset($config['condiciones'])) {
            $rol = $solicitud->usuario->roles->pluck('name')->first(); // Suponemos que tiene un solo rol principal
            $reglas = $config['condiciones']["rol:$rol"] ?? $config['condiciones']['default'] ?? [];
        } else {
            $reglas = $config['destinatarios'] ?? [];
        }

        // ✅ Si solo hay "creador", devolvemos directamente
        if (count($reglas) === 1 && $reglas[0] === 'creador') {
            return collect([$solicitud->usuario]);
        }

        // ✅ Lógica especial para Anulación de Venta
        if ($solicitud->tipo === 'Anulacion de Venta' && !empty($reglas)) {
            $tiene = collect($reglas)->filter(fn($r) => str_starts_with($r, 'permiso:'))
                ->map(fn($r) => explode(':', $r)[1])->values();

            $no_tiene = collect($reglas)->filter(fn($r) => str_starts_with($r, 'no_permiso:'))
                ->map(fn($r) => explode(':', $r)[1])->values();

            $query = User::query();

            foreach ($tiene as $permiso) {
                $query->whereHas('roles.permissions', fn($q) => $q->where('name', $permiso));
            }

            foreach ($no_tiene as $permiso) {
                $query->whereDoesntHave('roles.permissions', fn($q) => $q->where('name', $permiso));
            }

            $usuariosConPermisos = $query->get();

            foreach ($usuariosConPermisos as $usuario) {
                if (
                    $tiene->contains('Anulacion_entrega') &&
                    $usuario->hasRole('Almacenero')
                ) {
                    $almacenId = $solicitud->anulacion?->id_almacen;

                    if (
                        $almacenId &&
                        $usuario->almacenesEncargados->pluck('id')->contains($almacenId)
                    ) {
                        $usuarios->push($usuario);
                    }
                } else {
                    $usuarios->push($usuario);
                }
            }

            foreach ($reglas as $regla) {
                if ($regla === 'encargado_almacen') {
                    $almacenId = $solicitud->anulacion?->id_almacen;
                    if ($almacenId) {
                        $almacen = \App\Models\Almacen::find($almacenId);
                        if ($almacen && $almacen->encargado) {
                            $usuarios->push($almacen->encargado);
                        }
                    }
                }

                if ($regla === 'creador' && $solicitud->usuario) {
                    $usuarios->push($solicitud->usuario);
                }

                if (str_starts_with($regla, 'rol:')) {
                    $rol = explode(':', $regla)[1];
                    $usuarios = $usuarios->merge(User::role($rol)->get());
                }
            }

            return $usuarios->unique('id');
        }

        // ✅ Lógica especial para Devolución de Venta y Baja de Mercadería
        if (in_array($solicitud->tipo, ['Devolucion de Venta', 'Baja de Mercaderia']) && !empty($reglas)) {
            $tiene = collect($reglas)->filter(fn($r) => str_starts_with($r, 'permiso:'))
                ->map(fn($r) => explode(':', $r)[1])->values();

            $no_tiene = collect($reglas)->filter(fn($r) => str_starts_with($r, 'no_permiso:'))
                ->map(fn($r) => explode(':', $r)[1])->values();

            $query = User::query();

            foreach ($tiene as $permiso) {
                $query->whereHas('roles.permissions', fn($q) => $q->where('name', $permiso));
            }

            foreach ($no_tiene as $permiso) {
                $query->whereDoesntHave('roles.permissions', fn($q) => $q->where('name', $permiso));
            }

            $usuariosConPermisos = $query->get();

            foreach ($usuariosConPermisos as $usuario) {
                if ($tiene->contains('Devolucion_entrega') && $usuario->hasRole('Almacenero')){
                    $almacenId = null;

                    if ($solicitud->tipo === 'Devolucion de Venta') {
                        $almacenId = $solicitud->devolucion?->almacen;
                    }

                    if ($almacenId && $usuario->almacenesEncargados->pluck('id')->contains($almacenId)) {
                        $usuarios->push($usuario);
                    }

                } elseif ($tiene->contains('Baja_confirmar') && $usuario->hasRole('Almacenero')) {
                    $almacenId = $solicitud->bajaMercaderia?->almacen_nombre?->id;

                    if ($almacenId && $usuario->almacenesEncargados->pluck('id')->contains($almacenId)) {
                        $usuarios->push($usuario);
                    }
                }else {
                    $usuarios->push($usuario);
                }
            }

            foreach ($reglas as $regla) {
                if ($regla === 'encargado_almacen') {
                    $almacenId = null;

                    if ($solicitud->tipo === 'Devolucion de Venta') {
                        $almacenId = $solicitud->devolucion?->almacen;
                    }

                    if ($solicitud->tipo === 'Baja de Mercaderia') {
                        $almacenId = $solicitud->bajaMercaderia?->almacen_nombre?->id;
                    }

                    if ($almacenId) {
                        $almacen = \App\Models\Almacen::find($almacenId);
                        if ($almacen && $almacen->encargado) {
                            $usuarios->push($almacen->encargado);
                        }
                    }
                }

                if ($regla === 'creador' && $solicitud->usuario) {
                    $usuarios->push($solicitud->usuario);
                }

                if (str_starts_with($regla, 'rol:')) {
                    $rol = explode(':', $regla)[1];
                    $usuarios = $usuarios->merge(User::role($rol)->get());
                }
            }

            return $usuarios->unique('id');
        }

        // Lógica por defecto para otros tipos
        foreach ($reglas as $regla) {
            if (str_starts_with($regla, 'permiso:')) {
                $permiso = explode(':', $regla)[1];
                $usuarios = $usuarios->merge(User::whereHas('roles.permissions', function ($q) use ($permiso) {
                    $q->where('name', $permiso);
                })->get());
            }

            if (str_starts_with($regla, 'no_permiso:')) {
                $permiso = explode(':', $regla)[1];
                $usuarios = $usuarios->merge(User::whereDoesntHave('roles.permissions', function ($q) use ($permiso) {
                    $q->where('name', $permiso);
                })->get());
            }

            if (str_starts_with($regla, 'rol:')) {
                $rol = explode(':', $regla)[1];
                $usuarios = $usuarios->merge(User::role($rol)->get());
            }

            if ($regla === 'creador' && $solicitud->usuario) {
                $usuarios->push($solicitud->usuario);
            }
        }

        return $usuarios->unique('id');
    }

    protected function generarMensaje(Solicitud $solicitud, string $etapa, string $tipo)
    {
        $clase = match ($tipo) {
            'precio_especial' => \App\Services\GeneradoresMensajes\PrecioEspecialMensaje::class,
            'Anulacion de Venta' => \App\Services\GeneradoresMensajes\AnulacionMensaje::class,
            'Muestra de Mercaderia' => \App\Services\GeneradoresMensajes\MuestraMensaje::class,
            'Sobregiro de Venta' => \App\Services\GeneradoresMensajes\SobregiroMensaje::class,
            'Devolucion de Venta' => \App\Services\GeneradoresMensajes\DevolucionMensaje::class,
            'Baja de Mercaderia' => \App\Services\GeneradoresMensajes\BajaMensaje::class,
            // 'otro_tipo' => \App\Services\GeneradoresMensajes\OtroTipoMensaje::class,
            default => null,
        };

        if ($clase && method_exists($clase, 'generar')) {
            return $clase::generar($solicitud, $etapa);
        }

        // Fallback si no hay clase específica
        return "🔔 Notificación de solicitud N° {$solicitud->id} – etapa *$etapa*.";
    }

    private function notificarLocalmente(Solicitud $solicitud, Collection $usuarios, string $etapa)
    {
        \Log::info('Notificando localmente a usuarios', $usuarios->pluck('id', 'name')->toArray());

        foreach ($usuarios as $usuario) {
            try {
                $template = 'solicitud_plantilla'; // plantilla por defecto
                $params = [];

                switch ($etapa) {
                    case 'aprobar':
                        if ($solicitud->tipo === 'Sobregiro de Venta') {
                            $template = 'solicitud_plantilla';
                            $params =  [
                                'aprobado',
                                'Sobregiro de Venta',
                                'confirmación',
                                $solicitud->id,
                                $solicitud->fecha_autorizacion->format('d/m/Y H:i'),
                                'Autorizado',
                                $solicitud->autorizador->name,
                            ];
                        }else if($solicitud->tipo === 'Devolucion de Venta'){
                            $template = 'solicitud_plantilla';
                            $params =  [
                                'aprobado',
                                'Devolución de Venta',
                                'confirmación',
                                $solicitud->id,
                                $solicitud->fecha_autorizacion->format('d/m/Y H:i'),
                                'Autorizado',
                                $solicitud->autorizador->name,
                            ];
                        }else if($solicitud->tipo === 'Anulacion de Venta'){
                            $template = 'solicitud_plantilla';
                            $params =  [
                                'aprobado',
                                'Anulación de Venta',
                                'confirmación',
                                $solicitud->id,
                                $solicitud->fecha_autorizacion->format('d/m/Y H:i'),
                                'Autorizado',
                                $solicitud->autorizador->name,
                            ];
                        }else{
                            $template = 'solicitud_plantilla';
                            $params =  [
                                'aprobado',
                                Str::replace('Baja de Mercaderia', 'Ajuste de Inv.', $solicitud->tipo),
                                'ejecucion',
                                $solicitud->id,
                                $solicitud->fecha_autorizacion->format('d/m/Y H:i'),
                                'Autorizado',
                                $solicitud->autorizador->name,
                            ];
                        }
                        break;

                    case 'reprobar':
                        $template = 'solicitud_reprobar';
                        $params = [
                            Str::replace('Baja de Mercaderia', 'Ajuste de Inv.', $solicitud->tipo),
                            $solicitud->id,
                        ];
                        break;

                    case 'ejecutar':
                        $template = 'solicitud_ejecutar';
                        $params = [
                            Str::replace('Baja de Mercaderia', 'Ajuste de Inv.', $solicitud->tipo),
                            $solicitud->id,
                            now()->format('d/m/Y H:i'),
                            auth()->user()->name,
                        ];
                        break;

                    case 'verificar_entrega':
                        $template = 'verificar_entrega';
                        $params = [
                            $solicitud->tipo,
                            $solicitud->id,
                            now()->format('d/m/Y H:i'),
                        ];
                        break;

                    case 'verificar_entrega_fisica':
                        $template = 'verificar_entrega_fisica';
                        $params = [
                            $solicitud->tipo,
                            $solicitud->id,
                            now()->format('d/m/Y H:i'),
                        ];
                        break;

                    case 'confirmar': // para sobregiro confirmado
                        if ($solicitud->tipo === 'Sobregiro de Venta') {
                            $template = 'sobregiro_confirmar';
                            $params = [
                                $solicitud->id,
                                $solicitud->sobregiro->fecha_confirmacion->format('d/m/Y H:i'),
                                $solicitud->sobregiro->confirmador->name,
                                $solicitud->sobregiro->cod_sobregiro
                            ];
                            break;
                        }else if($solicitud->tipo === 'Baja de Mercaderia'){
                            $template = 'solicitud_plantilla';
                            $params = [
                            'confirmado',
                            'Ajuste de Inv.',
                            'aprobacion',
                            $solicitud->id,
                            $solicitud->bajaMercaderia->fecha_autorizacion->format('d/m/Y H:i'),
                            'autorizado',
                            $solicitud->bajaMercaderia->autorizador->name,
                        ];
                            break;
                        }
                        // si no es sobregiro, cae al default 'solicitud_plantilla'
                    case 'ejecutar_anulacion':
                        $template = 'solicitud_ejecutar';
                        $params = [
                            'anulacion',
                            $solicitud->id,
                            now()->format('d/m/Y H:i'),
                            auth()->user()->name,
                        ];
                        break;

                    case 'ejecutar_devolucion':
                        $template = 'solicitud_ejecutar';
                        $params = [
                            'devolucion',
                            $solicitud->id,
                            now()->format('d/m/Y H:i'),
                            auth()->user()->name,
                        ];
                        break;

                    default:
                        $template = 'solicitud_plantilla';
                        $params = [
                            'creado',
                            Str::replace('Baja de Mercaderia', 'Ajuste de Inv.', $solicitud->tipo),
                            'aprobacion',
                            $solicitud->id,
                            $solicitud->fecha_solicitud->format('d/m/Y H:i'),
                            'Solicitado',
                            $solicitud->usuario->name,
                        ];
                        break;
                }

                NotificacionLocal::create([
                    'user_id' => $usuario->id,
                    'solicitud_id' => $solicitud->id,
                    'template' => $template,
                    'params' => $params,
                    'estado' => 'noread',
                ]);
            } catch (\Throwable $e) {
                \Log::error('Error guardando notificación local: ' . $e->getMessage());
            }
        }
    }

}
