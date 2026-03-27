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

        // Define permissions for Quiniela management
        $permissions = [
            // User management
            'users.create',
            'users.view',
            'users.edit',
            'users.delete',

            // Quiniela management
            'quinielas.create',
            'quinielas.view',
            'quinielas.edit',
            'quinielas.delete',
            'quinielas.publish',
            'quinielas.close',

            // Predictions management
            'predictions.create',
            'predictions.view',
            'predictions.edit',
            'predictions.delete',

            // Match management
            'matches.create',
            'matches.view',
            'matches.edit',
            'matches.delete',
            'matches.score',

            // Scoring
            'scoring.view',
            'scoring.calculate',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Create admin role with all permissions
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $adminRole->givePermissionTo($permissions);

        // Create user role with basic permissions
        $userRole = Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web',
        ]);
        $userRole->givePermissionTo([
            'quinielas.view',
            'predictions.create',
            'predictions.view',
            'predictions.edit',
            'scoring.view',
        ]);
    }
}
