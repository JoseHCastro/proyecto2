<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    $membresias = \App\Models\Membresia::with(['paquetes' => function ($query) {
        $query->with(['sesiones.disciplina', 'membresia']);
    }])->get();
    
    $disciplinas = \App\Models\Disciplina::all();
    
    $gimnasio = \App\Models\Informacion::first();
    
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
        'membresias' => $membresias,
        'disciplinas' => $disciplinas,
        'gimnasio' => $gimnasio,
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
    Route::resource('suscripciones', \App\Http\Controllers\SuscripcionController::class);
    Route::post('suscripciones/pagar/{pago}', [\App\Http\Controllers\SuscripcionController::class, 'pagarCuota'])->name('suscripciones.pagar');
    Route::get('recibos/{pago}', [\App\Http\Controllers\ReciboController::class, 'ver'])->name('recibos.ver');
    Route::get('recibos/{pago}/descargar', [\App\Http\Controllers\ReciboController::class, 'descargar'])->name('recibos.descargar');
    Route::post('mediciones-progreso', [\App\Http\Controllers\MedicionProgresoController::class, 'store']);
    Route::delete('mediciones-progreso/{id}', [\App\Http\Controllers\MedicionProgresoController::class, 'destroy']);
    Route::get('informacion', [\App\Http\Controllers\InformacionController::class, 'index'])->name('informacion.index');
    Route::get('informacion/edit', [\App\Http\Controllers\InformacionController::class, 'edit'])->name('informacion.edit');
    Route::put('informacion', [\App\Http\Controllers\InformacionController::class, 'update'])->name('informacion.update');

    // Rutas de QR y Asistencia
    Route::get('mi-qr', [\App\Http\Controllers\QrController::class, 'miQr'])->name('qr.mi-qr');
    Route::get('asistencias/registrar', [\App\Http\Controllers\AsistenciaController::class, 'index'])->name('asistencias.registrar');
    Route::post('asistencias/registrar', [\App\Http\Controllers\AsistenciaController::class, 'registrar'])->name('asistencias.store');

    // Rutas de PagoFácil (Protegidas)
    Route::get('pagos/qr', [\App\Http\Controllers\PagoFacilController::class, 'index'])->name('pagofacil.index');
    Route::post('pagos/generar-qr', [\App\Http\Controllers\PagoFacilController::class, 'generarQr'])->name('pagofacil.generar');
    Route::get('pagos/generar-cuota/{pago}', [\App\Http\Controllers\PagoFacilController::class, 'generarQrCuota'])->name('pagofacil.generar-cuota');
    Route::get('pagos/consultar/{id}', [\App\Http\Controllers\PagoFacilController::class, 'consultarEstado'])->name('pagofacil.consultar');
});

// Ruta Callback PagoFácil (Pública y sin CSRF - configurar en bootstrap/app.php)
Route::post('payment/callback', [\App\Http\Controllers\PagoFacilController::class, 'callback'])->name('pagofacil.callback');

require __DIR__ . '/settings.php';
