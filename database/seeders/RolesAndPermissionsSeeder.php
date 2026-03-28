<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $permissions = [
            'manage-users',
            'manage-quinielas',
            'manage-matches',
            'manage-teams',
            'make-predictions',
            'view-results',
            'view-leaderboard',
            'view-audit-logs',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear rol admin con todos los permisos
        $admin = Role::create(['name' => 'admin'])
            ->givePermissionTo($permissions);

        // Crear rol organizador con permisos específicos
        $organizer = Role::create(['name' => 'organizador'])
            ->givePermissionTo([
                'manage-quinielas',
                'manage-matches',
                'manage-teams',
                'view-results',
                'view-leaderboard',
            ]);

        // Crear rol jugador con permisos básicos
        $player = Role::create(['name' => 'jugador'])
            ->givePermissionTo([
                'make-predictions',
                'view-results',
                'view-leaderboard',
            ]);
    }
}
