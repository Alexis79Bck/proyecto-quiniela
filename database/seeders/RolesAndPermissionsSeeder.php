<?php

namespace Database\Seeders;

use App\Enums\Permiso;
use App\Enums\Rol;
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

         // Crear permisos desde el Enum
        foreach (Permiso::values() as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // Crear roles y asignar permisos según el Enum
        foreach (Rol::cases() as $rol) {
            $roleModel = Role::firstOrCreate(['name' => $rol->value]);
            $roleModel->syncPermissions($rol->permisos());
        }
            $this->command->info('✅ Roles y permisos creados exitosamente.');
    }
}
