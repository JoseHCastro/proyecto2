<?php

namespace Database\Seeders;

use App\Models\Sesion;
use App\Models\Disciplina;
use App\Models\Horario;
use App\Models\User;
use Illuminate\Database\Seeder;

class SesionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener las disciplinas
        $aparatos = Disciplina::where('nombre', 'Aparatos')->first();
        $cardio = Disciplina::where('nombre', 'Cardio')->first();

        // Verificar que existan las disciplinas
        if (!$aparatos || !$cardio) {
            $this->command->error('Las disciplinas Aparatos y Cardio deben existir. Ejecuta DisciplinaSeeder primero.');
            return;
        }

        // Buscar o crear horario de 06:00 a 22:00, Lunes a Sábado
        $horario = Horario::where('hora_inicio', '06:00')
            ->where('hora_fin', '22:00')
            ->first();

        if (!$horario) {
            $horario = Horario::create([
                'hora_inicio' => '06:00',
                'hora_fin' => '22:00',
                'dia_semana' => [1, 2, 3, 4, 5, 6], // Lunes a Sábado
            ]);
        }

        // Obtener un instructor (el primer usuario con rol Instructor)
        $instructor = User::role('Instructor')->first();

        if (!$instructor) {
            $this->command->error('Debe existir al menos un usuario con rol Instructor.');
            return;
        }

        // Crear las sesiones
        $sesiones = [
            [
                'disciplina_id' => $aparatos->id,
                'horario_id' => $horario->id,
                'instructor_id' => $instructor->id,
            ],
            [
                'disciplina_id' => $cardio->id,
                'horario_id' => $horario->id,
                'instructor_id' => $instructor->id,
            ],
        ];

        foreach ($sesiones as $sesion) {
            // Verificar que no exista ya
            $exists = Sesion::where('disciplina_id', $sesion['disciplina_id'])
                ->where('horario_id', $sesion['horario_id'])
                ->exists();

            if (!$exists) {
                Sesion::create($sesion);
            }
        }

        $this->command->info('Sesiones de Aparatos y Cardio creadas correctamente.');
    }
}
