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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('/usuario', UsuarioController::class)->names([
        'index' => 'usuario.index',
        'store' => 'usuario.store',
        'update' => 'usuario.update',
        'destroy' => 'usuario.destroy',
    ]);

    Route::put('usuarios/{user}/asignar-rol', [UsuarioController::class, 'asignarRol'])->name('roles.asignar');

    Route::resource('/rol', RoleController::class)->names([
        'index' => 'roles.index',
        'store' => 'roles.store',
        'update' => 'roles.update',
        'destroy' => 'roles.destroy',
    ]);

    Route::resource('/cliente', ClienteController::class)->names([
        'index' => 'clientes.index',
        'store' => 'clientes.store',
        'update' => 'clientes.update',
        'destroy' => 'clientes.destroy',
    ]);

    Route::resource('PrecioEspecial', PrecioEspecialController::class);
    Route::post('PrecioEspecial/aprobar_o_rechazar', [PrecioEspecialController::class, 'aprobar_o_rechazar'])->name('precioespecial.aprobar_o_rechazar');
    Route::get('/PrecioEspecial/{id}/descargar/pdf', [PrecioEspecialController::class, 'descargarPDF'])->name('precioEspecial.descargar.pdf');
    Route::get('/PrecioEspecial/{id}/descargar/excel', [PrecioEspecialController::class, 'descargarExcel'])->name('precioEspecial.descargar.excel');

    Route::get('/general', [SolicitudController::class, 'index'])->name('general.index');
    Route::post('general/aprobar_o_rechazar', [SolicitudController::class, 'aprobar_o_rechazar'])->name('general.aprobar_o_rechazar');

    Route::resource('Muestra', MuestraMercaderiaController::class);
    Route::post('Muestra/aprobar_o_rechazar', [MuestraMercaderiaController::class, 'aprobar_o_rechazar'])->name('muestra.aprobar_o_rechazar');
    Route::get('/Muestra/{id}/descargar/pdf', [MuestraMercaderiaController::class, 'descargarPDF'])->name('muestra.descargar.pdf');
    Route::get('/Muestra/{id}/descargar/excel', [MuestraMercaderiaController::class, 'descargarExcel'])->name('muestra.descargar.excel');
    Route::post('/Muestra/{id}/ejecutar', [MuestraMercaderiaController::class, 'ejecutar'])->name('muestra.ejecutar');

    Route::resource('Baja', BajaMercaderiaController::class);
    Route::post('Baja/aprobar_o_rechazar', [BajaMercaderiaController::class, 'aprobar_o_rechazar'])->name('baja.aprobar_o_rechazar');
    Route::get('/Baja/{id}/descargar/pdf', [BajaMercaderiaController::class, 'descargarPDF'])->name('baja.descargar.pdf');
    Route::get('/Baja/{id}/descargar/excel', [BajaMercaderiaController::class, 'descargarExcel'])->name('baja.descargar.excel');
    Route::post('/Baja/{id}/ejecutar', [BajaMercaderiaController::class, 'ejecutar'])->name('baja.ejecutar');

    Route::resource('Sobregiro', SobregiroController::class);
    Route::post('Sobregiro/aprobar_o_rechazar', [SobregiroController::class, 'aprobar_o_rechazar'])->name('sobregiro.aprobar_o_rechazar');
    Route::post('/Sobregiro/{id}/ejecutar', [SobregiroController::class, 'ejecutar'])->name('sobregiro.ejecutar');
    Route::get('/Sobregiro/{id}/descargar/pdf', [SobregiroController::class, 'descargarPDF'])->name('sobregiro.descargar.pdf');
    Route::get('/Sobregiro/{id}/descargar/excel', [SobregiroController::class, 'descargarExcel'])->name('sobregiro.descargar.excel');

    Route::get('/permisos', [RolPermisoController::class, 'index'])->name('permisos.index');
    Route::post('/permisos/guardar/{id}', [RolPermisoController::class, 'guardar'])->name('permisos.guardar');
});


Route::middleware(['role:Administrador'])->group(function () {
    Route::post('/solicitudes/{solicitud}/autorizar', [SolicitudController::class, 'autorizar'])->name('solicitudes.autorizar');
});

Route::get('/hoja-en-blanco', function () {
    return view('HojaEnBlanco');
})->middleware(['auth', 'verified'])->name('HojaEnBlanco');

Route::get('/anulaciones', function () {
    return view('GestionSolicitudes.anulacion.index');
})->middleware(['auth', 'verified'])->name('anulaciones');

Route::get('/devoluciones', function () {
    return view('GestionSolicitudes.devolucion.index');
})->middleware(['auth', 'verified'])->name('devoluciones');


require __DIR__.'/auth.php';
