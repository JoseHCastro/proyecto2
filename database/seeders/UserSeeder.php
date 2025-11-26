<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('12345678');

        // 1 Propietario
        $propietario = User::factory()->create([
            'name' => 'Propietario',
            'email' => 'propietario@gmail.com',
            'password' => $password,
            'estado' => 'activo',
        ]);
        $propietario->assignRole('Propietario');

        // 2 Secretarias
        for ($i = 1; $i <= 2; $i++) {
            $secretaria = User::factory()->create([
                'name' => "Secretaria $i",
                'email' => "secretaria$i@gmail.com",
                'password' => $password,
                'estado' => 'activo',
            ]);
            $secretaria->assignRole('Secretaria');
        }

        // 4 Instructores
        for ($i = 1; $i <= 4; $i++) {
            $instructor = User::factory()->create([
                'name' => "Instructor $i",
                'email' => "instructor$i@gmail.com",
                'password' => $password,
                'estado' => 'activo',
            ]);
            $instructor->assignRole('Instructor');
        }

        // 8 Clientes
        for ($i = 1; $i <= 8; $i++) {
            $cliente = User::factory()->create([
                'name' => "Cliente $i",
                'email' => "cliente$i@gmail.com",
                'password' => $password,
                'estado' => 'activo',
            ]);
            $cliente->assignRole('Cliente');
        }
    }
}
