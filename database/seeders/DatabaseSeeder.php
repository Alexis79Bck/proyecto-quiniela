<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            //  RolesAndPermissionsSeeder::class,
            EtapaSeeder::class,
            GrupoSeeder::class,
            EquipoSeeder::class,
            JuegoSeeder::class,
        ]);

        // Crear usuarios de prueba si no existen
        // if (!Usuario::where('correo_electronico', 'admin@app.com')->exists()) {
        //     Usuario::factory()->create([
        //         'nombre_completo' => 'Admin User',
        //         'nombre_usuario' => 'admin',
        //         'correo_electronico' => 'admin@app.com',
        //     ])->assignRole('admin');
        // }

        // if (!Usuario::where('correo_electronico', 'organizador@example.com')->exists()) {
        //     Usuario::factory()->create([
        //         'nombre_completo' => 'Organizador Test',
        //         'nombre_usuario' => 'organizador',
        //         'correo_electronico' => 'organizador@example.com',
        //     ])->assignRole('organizador');
        // }

        // if (!Usuario::where('correo_electronico', 'jugador@example.com')->exists()) {
        //     Usuario::factory()->create([
        //         'nombre_completo' => 'Jugador Test',
        //         'nombre_usuario' => 'jugador',
        //         'correo_electronico' => 'jugador@example.com',
        //     ])->assignRole('jugador');
        // }
    }
}
