<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            DisciplinaSeeder::class,
            MembresiaSeeder::class,
            HorarioSeeder::class,
            SesionSeeder::class,
            PaqueteSeeder::class,
            RutinaSeeder::class,
        ]);
    }
}
