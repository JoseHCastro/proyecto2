<?php

namespace Database\Seeders;

use App\Models\Rutina;
use App\Models\User;
use Illuminate\Database\Seeder;

class RutinaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener clientes e instructores
        $clientes = User::role('Cliente')->get();
        $instructores = User::role('Instructor')->get();

        if ($clientes->isEmpty() || $instructores->isEmpty()) {
            $this->command->error('Deben existir usuarios con rol Cliente e Instructor.');
            return;
        }

        $cliente = $clientes->first();
        $instructor = $instructores->first();

        $rutinas = [
            [
                'socio_id' => $cliente->id,
                'instructor_id' => $instructor->id,
                'ejercicio' => 'Press de banca',
                'series' => 4,
                'repeticiones' => 12,
                'creada_en' => now()->subDays(7),
            ],
            [
                'socio_id' => $cliente->id,
                'instructor_id' => $instructor->id,
                'ejercicio' => 'Sentadillas',
                'series' => 4,
                'repeticiones' => 15,
                'creada_en' => now()->subDays(7),
            ],
            [
                'socio_id' => $cliente->id,
                'instructor_id' => $instructor->id,
                'ejercicio' => 'Peso muerto',
                'series' => 3,
                'repeticiones' => 10,
                'creada_en' => now()->subDays(5),
            ],
            [
                'socio_id' => $cliente->id,
                'instructor_id' => $instructor->id,
                'ejercicio' => 'Dominadas',
                'series' => 3,
                'repeticiones' => 8,
                'creada_en' => now()->subDays(3),
            ],
            [
                'socio_id' => $cliente->id,
                'instructor_id' => $instructor->id,
                'ejercicio' => 'Curl de bíceps',
                'series' => 3,
                'repeticiones' => 12,
                'creada_en' => now()->subDays(1),
            ],
        ];

        foreach ($rutinas as $rutina) {
            Rutina::create($rutina);
        }

        $this->command->info('Rutinas creadas correctamente.');
    }
}
