<?php

namespace Database\Seeders;

use App\Models\Paquete;
use App\Models\Membresia;
use App\Models\Sesion;
use Illuminate\Database\Seeder;

class PaqueteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar que existan membresías y sesiones
        $membresias = Membresia::all();
        $sesiones = Sesion::all();

        if ($membresias->isEmpty() || $sesiones->isEmpty()) {
            $this->command->error('Deben existir membresías y sesiones antes de crear paquetes.');
            return;
        }

        // Obtener membresía mensual
        $mensual = Membresia::where('nombre', 'Mensual')->first();
        
        if (!$mensual) {
            $this->command->error('La membresía Mensual debe existir.');
            return;
        }

        // Obtener sesiones de Aparatos y Cardio
        $aparatos = Sesion::whereHas('disciplina', function ($q) {
            $q->where('nombre', 'Aparatos');
        })->first();

        $cardio = Sesion::whereHas('disciplina', function ($q) {
            $q->where('nombre', 'Cardio');
        })->first();

        if (!$aparatos || !$cardio) {
            $this->command->error('Deben existir sesiones de Aparatos y Cardio.');
            return;
        }

        // Paquete Básico: Solo Aparatos
        $paqueteBasico = Paquete::create([
            'nombre' => 'Paquete Básico',
            'descripcion' => 'Acceso completo a zona de aparatos durante todo el horario.',
            'precio' => 299.99,
            'membresia_id' => $mensual->id,
            'activo' => true,
        ]);
        $paqueteBasico->sesiones()->attach([$aparatos->id]);

        // Paquete Cardio: Solo Cardio
        $paqueteCardio = Paquete::create([
            'nombre' => 'Paquete Cardio',
            'descripcion' => 'Acceso a zona de cardio para mejorar tu resistencia.',
            'precio' => 249.99,
            'membresia_id' => $mensual->id,
            'activo' => true,
        ]);
        $paqueteCardio->sesiones()->attach([$cardio->id]);

        // Paquete Completo: Aparatos + Cardio
        $paqueteCompleto = Paquete::create([
            'nombre' => 'Paquete Completo',
            'descripcion' => 'Acceso total a aparatos y cardio. La mejor opción para tu entrenamiento.',
            'precio' => 499.99,
            'membresia_id' => $mensual->id,
            'activo' => true,
        ]);
        $paqueteCompleto->sesiones()->attach([$aparatos->id, $cardio->id]);

        $this->command->info('Paquetes creados correctamente.');
    }
}
