<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class QrController extends Controller
{
    public function miQr()
    {
        $user = auth()->user();

        // Solo clientes pueden ver su QR
        if (!$user->hasRole('Cliente')) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Qr/MiQr', [
            'usuario' => $user,
        ]);
    }
}
