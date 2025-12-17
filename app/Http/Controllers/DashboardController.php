<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Paquete;
use App\Models\Suscripcion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard');
    }

    /**
     * Obtener estadísticas de ingresos por paquete
     */
    public function ingresosPorPaquete(Request $request)
    {
        $periodo = $request->input('periodo', 'semana');
        
        $fechaInicio = match($periodo) {
            'semana' => Carbon::now()->subDays(7),
            'mes' => Carbon::now()->subDays(30),
            'año' => Carbon::now()->subDays(365),
            default => Carbon::now()->subDays(7),
        };

        // Obtener todos los paquetes
        $paquetes = Paquete::where('activo', true)->get();
        
        $data = [];
        
        foreach ($paquetes as $paquete) {
            // Sumar pagos de suscripciones a este paquete en el periodo
            $ingresos = Pago::whereHas('suscripcion', function ($query) use ($paquete) {
                    $query->where('paquete_id', $paquete->id);
                })
                ->where('estado', 'pagada')
                ->where('fecha', '>=', $fechaInicio)
                ->sum('monto');
            
            $data[] = [
                'paquete' => $paquete->nombre,
                'ingresos' => (float) $ingresos,
            ];
        }

        return response()->json($data);
    }

    /**
     * Obtener estadísticas de suscripciones por paquete
     */
    public function suscripcionesPorPaquete(Request $request)
    {
        $periodo = $request->input('periodo', 'semana');
        
        $fechaInicio = match($periodo) {
            'semana' => Carbon::now()->subDays(7),
            'mes' => Carbon::now()->subDays(30),
            'año' => Carbon::now()->subDays(365),
            default => Carbon::now()->subDays(7),
        };

        // Obtener todos los paquetes con conteo de suscripciones activas en el periodo
        $paquetes = Paquete::where('activo', true)->get();
        
        $data = [];
        
        foreach ($paquetes as $paquete) {
            $cantidad = Suscripcion::where('paquete_id', $paquete->id)
                ->where('created_at', '>=', $fechaInicio)
                ->count();
            
            if ($cantidad > 0) {
                $data[] = [
                    'paquete' => $paquete->nombre,
                    'cantidad' => $cantidad,
                ];
            }
        }

        // Si no hay datos, devolver los paquetes con 0
        if (empty($data)) {
            foreach ($paquetes as $paquete) {
                $data[] = [
                    'paquete' => $paquete->nombre,
                    'cantidad' => 0,
                ];
            }
        }

        return response()->json($data);
    }
}
