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
});

require __DIR__ . '/settings.php';
