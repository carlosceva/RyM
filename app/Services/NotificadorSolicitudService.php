<?php

namespace App\Services;

use App\Models\Solicitud;
use App\Models\User;
use App\Services\Contracts\WhatsAppServiceInterface;

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
        
        \Log::info('Usuarios destinatarios:', $usuarios->pluck('id', 'name')->toArray());

        $mensaje = $this->generarMensaje($solicitud, $etapa, $tipo);

        foreach ($usuarios as $user) {
            $numero = trim($user->telefono);

            // Validar que tenga exactamente 8 dígitos numéricos
            if (preg_match('/^\d{8}$/', $numero)) {
                $telefono = '+591' . $numero;

                \Log::info("Enviando mensaje a: {$user->name} – $telefono");

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
                \Log::warning("Número inválido de WhatsApp para usuario {$user->name}: {$user->telefono}. Se omitió el envío.");
            }
        }

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
                if (
                    $tiene->contains('Devolucion_entrega') &&
                    $usuario->hasRole('Almacenero')
                ) {
                    $almacenId = null;

                    if ($solicitud->tipo === 'Devolucion de Venta') {
                        $almacenId = $solicitud->devolucion?->almacen;
                    }

                    if ($solicitud->tipo === 'Baja de Mercaderia') {
                        $almacenId = $solicitud->bajasMercaderia?->almacen;
                    }

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
                    $almacenId = null;

                    if ($solicitud->tipo === 'Devolucion de Venta') {
                        $almacenId = $solicitud->devolucion?->almacen;
                    }

                    if ($solicitud->tipo === 'Baja de Mercaderia') {
                        $almacenId = $solicitud->bajasMercaderia?->almacen;
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

}
