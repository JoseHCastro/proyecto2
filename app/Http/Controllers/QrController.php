<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class QrController extends Controller
{
    public function miQr()
    {
        $user = auth()->user();

        // Permitir a cualquier usuario autenticado ver su QR
        // El QR es útil para registrar asistencia

        return Inertia::render('Qr/MiQr', [
            'usuario' => $user,
        ]);
    }
}
