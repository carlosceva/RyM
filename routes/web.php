<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrecioEspecialController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\RolPermisoController;
use App\Http\Controllers\MuestraMercaderiaController;
use App\Http\Controllers\BajaMercaderiaController;
use App\Http\Controllers\SobregiroController;
use App\Http\Controllers\AnulacionController;
use App\Http\Controllers\DevolucionController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\BackupController;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dashboard', function () {
    return view('dashboard'); // Cambia a la vista que tú quieras
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::resource('/usuario', UsuarioController::class)->names([
        'index' => 'usuario.index',
        'store' => 'usuario.store',
        'update' => 'usuario.update',
        'destroy' => 'usuario.destroy',
    ])->middleware('can:usuarios_ver');

    Route::get('/administracion/sistema', [BackupController::class, 'index'])
    ->name('backup.index')
    ->middleware('auth');

    Route::post('/administracion/sistema/backup', [BackupController::class, 'realizarBackup'])
        ->name('backup.realizar')
        ->middleware('auth');

    Route::post('/administracion/sistema/restore', [BackupController::class, 'restaurarBackup'])
        ->name('backup.restaurar')
        ->middleware('auth');

    Route::put('usuarios/{user}/asignar-rol', [UsuarioController::class, 'asignarRol'])->name('roles.asignar');

    Route::resource('/rol', RoleController::class)->names([
        'index' => 'roles.index',
        'store' => 'roles.store',
        'update' => 'roles.update',
        'destroy' => 'roles.destroy',
    ])->middleware('can:roles_ver');

    Route::resource('/cliente', ClienteController::class)->names([
        'index' => 'clientes.index',
        'store' => 'clientes.store',
        'update' => 'clientes.update',
        'destroy' => 'clientes.destroy',
    ]);

    Route::resource('PrecioEspecial', PrecioEspecialController::class)->middleware('can:Precio_especial_ver');
    Route::post('PrecioEspecial/aprobar_o_rechazar', [PrecioEspecialController::class, 'aprobar_o_rechazar'])->name('precioespecial.aprobar_o_rechazar');
    Route::get('/PrecioEspecial/{id}/descargar/pdf', [PrecioEspecialController::class, 'descargarPDF'])->name('precioEspecial.descargar.pdf');
    Route::get('/PrecioEspecial/{id}/descargar/excel', [PrecioEspecialController::class, 'descargarExcel'])->name('precioEspecial.descargar.excel');

    Route::get('/general', [SolicitudController::class, 'index'])->name('general.index')->middleware('admin');
    Route::post('general/aprobar_o_rechazar', [SolicitudController::class, 'aprobar_o_rechazar'])->name('general.aprobar_o_rechazar');

    Route::resource('Muestra', MuestraMercaderiaController::class)->middleware('can:ver-muestra')->middleware('can:Muestra_ver');
    Route::post('Muestra/aprobar_o_rechazar', [MuestraMercaderiaController::class, 'aprobar_o_rechazar'])->name('muestra.aprobar_o_rechazar');
    Route::get('/Muestra/{id}/descargar/pdf', [MuestraMercaderiaController::class, 'descargarPDF'])->name('muestra.descargar.pdf');
    Route::get('/Muestra/{id}/descargar/excel', [MuestraMercaderiaController::class, 'descargarExcel'])->name('muestra.descargar.excel');
    Route::post('/Muestra/{id}/ejecutar', [MuestraMercaderiaController::class, 'ejecutar'])->name('muestra.ejecutar');

    Route::resource('Baja', BajaMercaderiaController::class)->middleware('can:Baja_ver');
    Route::post('Baja/aprobar_o_rechazar', [BajaMercaderiaController::class, 'aprobar_o_rechazar'])->name('baja.aprobar_o_rechazar');
    Route::get('/Baja/{id}/descargar/pdf', [BajaMercaderiaController::class, 'descargarPDF'])->name('baja.descargar.pdf');
    Route::get('/Baja/{id}/descargar/excel', [BajaMercaderiaController::class, 'descargarExcel'])->name('baja.descargar.excel');
    Route::post('/Baja/{id}/ejecutar', [BajaMercaderiaController::class, 'ejecutar'])->name('baja.ejecutar');

    Route::resource('Sobregiro', SobregiroController::class)->middleware('can:Sobregiro_ver');
    Route::post('Sobregiro/aprobar_o_rechazar', [SobregiroController::class, 'aprobar_o_rechazar'])->name('sobregiro.aprobar_o_rechazar');
    Route::post('/Sobregiro/{id}/ejecutar', [SobregiroController::class, 'ejecutar'])->name('sobregiro.ejecutar');
    Route::get('/Sobregiro/{id}/descargar/pdf', [SobregiroController::class, 'descargarPDF'])->name('sobregiro.descargar.pdf');
    Route::get('/Sobregiro/{id}/descargar/excel', [SobregiroController::class, 'descargarExcel'])->name('sobregiro.descargar.excel');

    Route::resource('Anulacion', AnulacionController::class)->middleware('can:Anulacion_ver');
    Route::post('Anulacion/aprobar_o_rechazar', [AnulacionController::class, 'aprobar_o_rechazar'])->name('anulacion.aprobar_o_rechazar');
    Route::post('/Anulacion/{id}/ejecutar', [AnulacionController::class, 'ejecutar'])->name('anulacion.ejecutar');
    Route::get('/Anulacion/{id}/descargar/pdf', [AnulacionController::class, 'descargarPDF'])->name('anulacion.descargar.pdf');
    Route::get('/Anulacion/{id}/descargar/excel', [AnulacionController::class, 'descargarExcel'])->name('anulacion.descargar.excel');
    Route::post('/Anulacion/{id}/verificar-Entrega', [AnulacionController::class, 'verificarEntrega'])->name('solicitud.anulacion.verificarEntrega');
    Route::post('/Anulacion/{id}/verificar-Entrega-Fisica', [AnulacionController::class, 'verificarEntregaFisica'])->name('solicitud.anulacion.verificarEntregaFisica');

    Route::resource('Devolucion', DevolucionController::class)->middleware('can:Devolucion_ver');
    Route::post('Devolucion/aprobar_o_rechazar', [DevolucionController::class, 'aprobar_o_rechazar'])->name('devolucion.aprobar_o_rechazar');
    Route::post('/Devolucion/{id}/ejecutar', [DevolucionController::class, 'ejecutar'])->name('devolucion.ejecutar');
    Route::get('/Devolucion/{id}/descargar/pdf', [DevolucionController::class, 'descargarPDF'])->name('devolucion.descargar.pdf');
    Route::get('/Devolucion/{id}/descargar/excel', [DevolucionController::class, 'descargarExcel'])->name('devolucion.descargar.excel');
    Route::post('/Devolucion/{id}/verificar-Entrega', [DevolucionController::class, 'verificarEntrega'])->name('solicitud.devolucion.verificarEntrega');
    Route::post('/Devolucion/{id}/verificar-Entrega-Fisica', [DevolucionController::class, 'verificarEntregaFisica'])->name('solicitud.devolucion.verificarEntregaFisica');

    Route::get('/permisos', [RolPermisoController::class, 'index'])->name('permisos.index')->middleware('can:permisos_ver');;
    Route::post('/permisos/guardar/{id}', [RolPermisoController::class, 'guardar'])->name('permisos.guardar');

    Route::get('/profile', [UserProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [UserProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/change-password', [UserProfileController::class, 'changePassword'])->name('profile.changePassword');
});


Route::middleware(['role:Administrador'])->group(function () {
    Route::post('/solicitudes/{solicitud}/autorizar', [SolicitudController::class, 'autorizar'])->name('solicitudes.autorizar');
});

Route::get('/hoja-en-blanco', function () {
    return view('HojaEnBlanco');
})->middleware(['auth', 'verified'])->name('HojaEnBlanco');


require __DIR__.'/auth.php';
