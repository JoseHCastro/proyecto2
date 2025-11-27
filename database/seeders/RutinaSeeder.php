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

        $ejercicios = [
            ['ejercicio' => 'Press de banca', 'series' => 4, 'repeticiones' => 12],
            ['ejercicio' => 'Sentadillas', 'series' => 4, 'repeticiones' => 15],
            ['ejercicio' => 'Peso muerto', 'series' => 3, 'repeticiones' => 10],
            ['ejercicio' => 'Dominadas', 'series' => 3, 'repeticiones' => 8],
            ['ejercicio' => 'Curl de bíceps', 'series' => 3, 'repeticiones' => 12],
            ['ejercicio' => 'Press militar', 'series' => 3, 'repeticiones' => 10],
            ['ejercicio' => 'Remo con barra', 'series' => 4, 'repeticiones' => 12],
        ];

        $totalRutinas = 0;

        // Crear rutinas para cada cliente
        foreach ($clientes as $index => $cliente) {
            // Asignar un instructor (rotar entre los disponibles)
            $instructor = $instructores[$index % $instructores->count()];

            // Crear 3-5 rutinas aleatorias por cliente
            $numRutinas = rand(3, 5);
            
            for ($i = 0; $i < $numRutinas; $i++) {
                $ejercicio = $ejercicios[array_rand($ejercicios)];
                
                Rutina::create([
                    'socio_id' => $cliente->id,
                    'instructor_id' => $instructor->id,
                    'ejercicio' => $ejercicio['ejercicio'],
                    'series' => $ejercicio['series'],
                    'repeticiones' => $ejercicio['repeticiones'],
                    'creada_en' => now()->subDays(rand(1, 30)),
                ]);
                
                $totalRutinas++;
            }
        }

        $this->command->info("Rutinas creadas correctamente: {$totalRutinas} rutinas para {$clientes->count()} clientes.");
    }
}
