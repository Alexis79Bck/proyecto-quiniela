<?php

namespace Database\Seeders;

use App\Enums\Permission;
use App\Enums\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Permission\Models\Role as SpatieRole;
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
        foreach (Permission::values() as $permiso) {
            SpatiePermission::firstOrCreate(['name' => $permiso]);
        }

        // Crear roles y asignar permisos según el Enum
        foreach (Role::cases() as $rol) {
            $roleModel = SpatieRole::firstOrCreate(['name' => $rol->value]);
            $roleModel->syncPermissions($rol->permisos());
        }
        $this->command->info('✅ Roles y permisos creados exitosamente.');
    }
}
