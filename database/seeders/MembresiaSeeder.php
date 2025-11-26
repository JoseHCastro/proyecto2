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
                'nombre' => 'Membresía Mensual',
                'descripcion' => 'Acceso completo al gimnasio durante 30 días. Incluye acceso a todas las máquinas y áreas de entrenamiento.',
                'duracion_dias' => 30,
                'activo' => true,
            ],
            [
                'nombre' => 'Membresía Trimestral',
                'descripcion' => 'Acceso completo al gimnasio durante 90 días. Incluye acceso a todas las máquinas, áreas de entrenamiento y una sesión de evaluación física.',
                'duracion_dias' => 90,
                'activo' => true,
            ],
            [
                'nombre' => 'Membresía Semestral',
                'descripcion' => 'Acceso completo al gimnasio durante 180 días. Incluye acceso a todas las máquinas, áreas de entrenamiento, evaluación física mensual y plan de entrenamiento personalizado.',
                'duracion_dias' => 180,
                'activo' => true,
            ],
            [
                'nombre' => 'Membresía Anual',
                'descripcion' => 'Acceso completo al gimnasio durante 365 días. Incluye acceso a todas las máquinas, áreas de entrenamiento, evaluación física mensual, plan de entrenamiento personalizado y descuentos en clases especiales.',
                'duracion_dias' => 365,
                'activo' => true,
            ],
            [
                'nombre' => 'Membresía Semanal',
                'descripcion' => 'Acceso completo al gimnasio durante 7 días. Ideal para visitantes o quienes desean probar nuestras instalaciones.',
                'duracion_dias' => 7,
                'activo' => true,
            ],
        ];

        foreach ($membresias as $membresia) {
            Membresia::create($membresia);
        }
    }
}
