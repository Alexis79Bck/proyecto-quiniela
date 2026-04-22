<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        // Crear usuarios de prueba si no existen
        if (!User::where('email', 'admin@app.com')->exists()) {
            User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@app.com',
            ])->assignRole('admin');
        }

        if (!User::where('email', 'organizador@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Organizador Test',
                'email' => 'organizador@example.com',
            ])->assignRole('organizador');
        }

        if (!User::where('email', 'jugador@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Jugador Test',
                'email' => 'jugador@example.com',
            ])->assignRole('jugador');
        }
    }
}
