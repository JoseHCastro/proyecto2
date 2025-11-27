<?php

namespace Database\Seeders;

use App\Models\Suscripcion;
use App\Models\Paquete;
use App\Models\Pago;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SuscripcionSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = User::role('Cliente')->get();
        $paquetes = Paquete::with('membresia')->where('activo', true)->get();

        if ($clientes->isEmpty() || $paquetes->isEmpty()) {
            $this->command->warn('No hay clientes o paquetes disponibles para crear suscripciones');
            return;
        }

        $totalSuscripciones = 0;
        $totalPagos = 0;

        // Crear suscripciones para algunos clientes
        foreach ($clientes->take(5) as $index => $cliente) {
            $paquete = $paquetes->random();
            
            // Fechas
            $fechaInicio = Carbon::now()->subDays(rand(0, 30));
            $fechaFin = $fechaInicio->copy()->addDays($paquete->membresia->duracion_dias);
            
            // Número de cuotas aleatorio (1-4)
            $cuotas = rand(1, 4);

            // Crear suscripción
            $suscripcion = Suscripcion::create([
                'usuario_id' => $cliente->id,
                'paquete_id' => $paquete->id,
                'estado' => 'activo',
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'renovacion_automatica' => rand(0, 1),
            ]);

            // Calcular monto por cuota
            $montoPorCuota = $paquete->precio / $cuotas;
            
            // Calcular días entre pagos
            $diasEntrePagos = floor($paquete->membresia->duracion_dias / $cuotas);
            
            // Crear motivo base
            $motivoBase = "{$paquete->nombre} - {$paquete->membresia->nombre} - Elevation Gym";

            // Crear pagos (cuotas)
            for ($i = 0; $i < $cuotas; $i++) {
                // Calcular fecha de vencimiento
                $fechaVence = $fechaInicio->copy()->addDays(1 + ($i * $diasEntrePagos));
                
                // Determinar estado del pago
                $hoy = Carbon::now();
                $estado = 'impaga';
                $fecha = null;
                $metodo = null;
                
                // Si la fecha de vencimiento ya pasó, algunos pagos estarán pagados
                if ($fechaVence->lt($hoy) && rand(0, 100) > 30) {
                    $estado = 'pagada';
                    $fecha = $fechaVence->copy()->subDays(rand(0, 3));
                    $metodo = ['QR', 'efectivo', 'tarjeta'][rand(0, 2)];
                }

                Pago::create([
                    'usuario_id' => $cliente->id,
                    'suscripcion_id' => $suscripcion->id,
                    'motivo' => "Cuota " . ($i + 1) . " - " . $motivoBase,
                    'monto' => round($montoPorCuota, 2),
                    'metodo' => $metodo,
                    'estado' => $estado,
                    'fecha' => $fecha,
                    'vence' => $fechaVence,
                ]);
                
                $totalPagos++;
            }

            $totalSuscripciones++;
        }

        $this->command->info("Suscripciones creadas correctamente: {$totalSuscripciones} suscripciones con {$totalPagos} pagos.");
    }
}
