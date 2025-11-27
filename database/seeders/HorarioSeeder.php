<?php

namespace Database\Seeders;

use App\Models\Horario;
use Illuminate\Database\Seeder;

class HorarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $horarios = [
            [
                'dia_semana' => [1, 3, 5], // Lunes, Miércoles, Viernes
                'hora_inicio' => '06:00',
                'hora_fin' => '08:00',
            ],
            [
                'dia_semana' => [2, 4, 6], // Martes, Jueves, Sábado
                'hora_inicio' => '06:00',
                'hora_fin' => '08:00',
            ],
            [
                'dia_semana' => [1, 2, 3, 4, 5], // Lunes a Viernes
                'hora_inicio' => '09:00',
                'hora_fin' => '11:00',
            ],
            [
                'dia_semana' => [1, 2, 3, 4, 5], // Lunes a Viernes
                'hora_inicio' => '14:00',
                'hora_fin' => '16:00',
            ],
            [
                'dia_semana' => [1, 3, 5], // Lunes, Miércoles, Viernes
                'hora_inicio' => '17:00',
                'hora_fin' => '19:00',
            ],
            [
                'dia_semana' => [2, 4, 6], // Martes, Jueves, Sábado
                'hora_inicio' => '17:00',
                'hora_fin' => '19:00',
            ],
            [
                'dia_semana' => [1, 2, 3, 4, 5, 6], // Lunes a Sábado
                'hora_inicio' => '19:00',
                'hora_fin' => '21:00',
            ],
        ];

        foreach ($horarios as $horario) {
            Horario::create($horario);
        }
    }
}
