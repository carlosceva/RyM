<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrecioEspecialController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\RolPermisoController;


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
    Route::post('/general/{id}/aprobar', [SolicitudController::class, 'aprobar'])->name('general.aprobar');
    Route::post('/general/{id}/rechazar', [SolicitudController::class, 'rechazar'])->name('general.rechazar');

    Route::post('general/aprobar_o_rechazar', [SolicitudController::class, 'aprobar_o_rechazar'])->name('general.aprobar_o_rechazar');

    // web.php
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

Route::get('/sobregiros', function () {
    return view('GestionSolicitudes.sobregiro.index');
})->middleware(['auth', 'verified'])->name('sobregiros');

Route::get('/muestras', function () {
    return view('GestionSolicitudes.muestra.index');
})->middleware(['auth', 'verified'])->name('muestras');

Route::get('/bajas', function () {
    return view('GestionSolicitudes.baja.index');
})->middleware(['auth', 'verified'])->name('bajas');


require __DIR__.'/auth.php';
