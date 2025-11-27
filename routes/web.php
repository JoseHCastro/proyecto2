<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('users', \App\Http\Controllers\UserController::class);
    Route::resource('membresias', \App\Http\Controllers\MembresiaController::class);
    Route::resource('horarios', \App\Http\Controllers\HorarioController::class);
    Route::resource('disciplinas', \App\Http\Controllers\DisciplinaController::class);
    Route::resource('sesiones', \App\Http\Controllers\SesionController::class);
    Route::resource('paquetes', \App\Http\Controllers\PaqueteController::class);
    Route::resource('rutinas', \App\Http\Controllers\RutinaController::class);
    Route::post('mediciones-progreso', [\App\Http\Controllers\MedicionProgresoController::class, 'store']);
    Route::delete('mediciones-progreso/{id}', [\App\Http\Controllers\MedicionProgresoController::class, 'destroy']);
    Route::get('informacion', [\App\Http\Controllers\InformacionController::class, 'index'])->name('informacion.index');
    Route::get('informacion/edit', [\App\Http\Controllers\InformacionController::class, 'edit'])->name('informacion.edit');
    Route::put('informacion', [\App\Http\Controllers\InformacionController::class, 'update'])->name('informacion.update');
});

require __DIR__ . '/settings.php';
