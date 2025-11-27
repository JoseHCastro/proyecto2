<?php

namespace Database\Seeders;

use App\Models\Membresia;
use Illuminate\Database\Seeder;

class MembresiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $membresias = [
            [
                'nombre' => 'Mensual',
                'descripcion' => 'Acceso completo al gimnasio durante 30 días.',
                'duracion_dias' => 30,
                'activo' => true,
            ],
            [
                'nombre' => 'Trimestral',
                'descripcion' => 'Acceso completo al gimnasio durante 90 días.',
                'duracion_dias' => 90,
                'activo' => true,
            ],
            [
                'nombre' => 'Semestral',
                'descripcion' => 'Acceso completo al gimnasio durante 180 días.',
                'duracion_dias' => 180,
                'activo' => true,
            ],
            [
                'nombre' => 'Anual',
                'descripcion' => 'Acceso completo al gimnasio durante 365 días.',
                'duracion_dias' => 365,
                'activo' => true,
            ],
            [
                'nombre' => 'Semanal',
                'descripcion' => 'Acceso completo al gimnasio durante 7 días.',
                'duracion_dias' => 7,
                'activo' => true,
            ],
        ];

        foreach ($membresias as $membresia) {
            Membresia::create($membresia);
        }
    }
}
