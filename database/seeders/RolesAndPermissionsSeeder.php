<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guard = config('auth.defaults.guard', 'web');

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
            Permission::findOrCreate($permission, $guard);
        }

        $roles = [
            'admin' => $permissions,
            'organizador' => [
                'manage-quinielas',
                'manage-matches',
                'manage-teams',
                'view-results',
                'view-leaderboard',
            ],
            'jugador' => [
                'make-predictions',
                'view-results',
                'view-leaderboard',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::findOrCreate($roleName, $guard);
            $role->syncPermissions($rolePermissions);
        }
    }
}
