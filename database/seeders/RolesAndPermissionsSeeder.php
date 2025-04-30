<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Resetea el caché de roles y permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos por módulo
        $permissions = [
            // Permisos para sucursales
            'ver sucursales',
            'crear sucursales',
            'editar sucursales',
            'eliminar sucursales',
            
            // Permisos para empresas
            'ver empresas',
            'crear empresas',
            'editar empresas',
            'eliminar empresas',
            
            // Permisos para departamentos
            'ver departamentos',
            'crear departamentos',
            'editar departamentos',
            'eliminar departamentos',
            
            // Permisos para municipios
            'ver municipios',
            'crear municipios',
            'editar municipios',
            'eliminar municipios',
            
            // Permisos para encargados
            'ver encargados',
            'crear encargados',
            'editar encargados',
            'eliminar encargados',
        ];

        // Crear los permisos
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear el rol de administrador y asignarle todos los permisos
        $adminRole = Role::create(['name' => 'Administrador']);
        $adminRole->givePermissionTo(Permission::all());

        // Crear el rol de usuario normal
        $userRole = Role::create(['name' => 'Usuario']);
        $userRole->givePermissionTo(['ver sucursales']);

        // Crear un usuario administrador por defecto
        $admin = User::where('email', 'admin@mail.com')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Administrador',
                'email' => 'admin@mail.com',
                'password' => bcrypt('admin123'),
            ]);
        }
        
        $admin->assignRole('Administrador');

        // Si hay otros usuarios, asignarles el rol de usuario normal
        $users = User::where('email', '!=', 'admin@mail.com')->get();
        foreach ($users as $user) {
            $user->assignRole('Usuario');
        }
    }
}