<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Resetea el caché de roles y permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear el rol Super Admin
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);

        // Crear un usuario super admin por defecto
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@mail.com'],
            [
                'name' => 'Super Administrador',
                'email' => 'admin@mail.com',
                'password' => bcrypt('admin123'),
            ]
        );

        // Asignar el rol de Super Admin al usuario
        if (!$superAdmin->hasRole('super_admin')) {
            $superAdmin->assignRole('super_admin');
        }
    }
}
