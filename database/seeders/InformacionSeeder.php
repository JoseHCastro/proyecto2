<?php

namespace Database\Seeders;

use App\Models\Informacion;
use Illuminate\Database\Seeder;

class InformacionSeeder extends Seeder
{
    public function run(): void
    {
        // Eliminar cualquier registro existente para asegurar un solo registro
        Informacion::truncate();
        
        Informacion::create([
            'nombre' => 'Elevation Gym',
            'direccion' => 'B/ San Lorenzo entre 4to y 5to anillo esquina Calle Cunumicita y Calle El Transnochado',
            'telefono' => '+591 71616738',
            'correo' => 'elevationgym@gmail.com',
        ]);

        $this->command->info('Información del gimnasio creada correctamente.');
    }
}
