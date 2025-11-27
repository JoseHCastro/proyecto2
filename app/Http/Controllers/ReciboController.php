<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Informacion;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class ReciboController extends Controller
{
    public function ver($pagoId)
    {
        $pago = Pago::with(['usuario', 'suscripcion.paquete.membresia'])->findOrFail($pagoId);
        $informacion = Informacion::first();

        return Inertia::render('Recibos/Ver', [
            'pago' => $pago,
            'informacion' => $informacion,
        ]);
    }

    public function descargar($pagoId)
    {
        $pago = Pago::with(['usuario', 'suscripcion.paquete.membresia'])->findOrFail($pagoId);
        $informacion = Informacion::first();

        $pdf = Pdf::loadView('pdf.recibo', [
            'pago' => $pago,
            'informacion' => $informacion,
        ]);

        return $pdf->download('recibo-pago-' . $pago->id . '.pdf');
    }
}
