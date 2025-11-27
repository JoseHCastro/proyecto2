<?php

namespace Database\Seeders;

use App\Models\Disciplina;
use Illuminate\Database\Seeder;

class DisciplinaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $disciplinas = [
            [
                'nombre' => 'Aparatos',
                'descripcion' => 'Entrenamiento con máquinas y equipos de gimnasio para desarrollo muscular y fuerza.',
            ],
            [
                'nombre' => 'Cardio',
                'descripcion' => 'Ejercicios cardiovasculares para mejorar la resistencia y salud del corazón.',
            ]
        ];

        foreach ($disciplinas as $disciplina) {
            Disciplina::create($disciplina);
        }
    }
}
