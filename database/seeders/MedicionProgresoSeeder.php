<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MedicionProgreso;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MedicionProgresoSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener todos los usuarios con rol Cliente
        $clientes = User::role('Cliente')->get();

        if ($clientes->isEmpty()) {
            $this->command->warn('No hay usuarios con rol Cliente para crear mediciones');
            return;
        }

        foreach ($clientes as $cliente) {
            // Crear mediciones retroactivas para simular progreso en el tiempo
            $mediciones = [
                [
                    'socio_id' => $cliente->id,
                    'medido_en' => Carbon::now()->subMonths(3),
                    'peso_kg' => rand(65, 90) + (rand(0, 9) / 10),
                    'estatura_cm' => rand(160, 185) + (rand(0, 9) / 10),
                    'grasa_corporal' => rand(18, 28) + (rand(0, 9) / 10),
                    'notas' => 'Medición inicial - inicio del programa de entrenamiento',
                ],
                [
                    'socio_id' => $cliente->id,
                    'medido_en' => Carbon::now()->subMonths(2),
                    'peso_kg' => rand(63, 88) + (rand(0, 9) / 10),
                    'estatura_cm' => rand(160, 185) + (rand(0, 9) / 10),
                    'grasa_corporal' => rand(16, 26) + (rand(0, 9) / 10),
                    'notas' => 'Ligera mejora en composición corporal',
                ],
                [
                    'socio_id' => $cliente->id,
                    'medido_en' => Carbon::now()->subMonth(),
                    'peso_kg' => rand(62, 86) + (rand(0, 9) / 10),
                    'estatura_cm' => rand(160, 185) + (rand(0, 9) / 10),
                    'grasa_corporal' => rand(15, 24) + (rand(0, 9) / 10),
                    'notas' => 'Progreso notable, continuar con el plan actual',
                ],
                [
                    'socio_id' => $cliente->id,
                    'medido_en' => Carbon::now()->subWeeks(2),
                    'peso_kg' => rand(61, 85) + (rand(0, 9) / 10),
                    'estatura_cm' => rand(160, 185) + (rand(0, 9) / 10),
                    'grasa_corporal' => rand(14, 23) + (rand(0, 9) / 10),
                    'notas' => null,
                ],
            ];

            foreach ($mediciones as $medicion) {
                MedicionProgreso::create($medicion);
            }
        }

        $this->command->info('Mediciones de progreso creadas exitosamente para ' . $clientes->count() . ' clientes');
    }
}
