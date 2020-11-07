<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $permissions = [
           ['name' => 'users.index', 'display_name' => 'Navegar Usuarios'],
           ['name' => 'users.create', 'display_name' => 'Crear Usuarios'],
           ['name' => 'users.show', 'display_name' => 'Ver Usuarios'],
           ['name' => 'users.edit', 'display_name' => 'Modificar Usuarios'],
           ['name' => 'users.activate', 'display_name' => 'Activar Usuarios'],
           ['name' => 'users.deactivate', 'display_name' => 'Desactivar Usuarios'],
        ];

        foreach ($permissions as $p) {
            $permission = Permission::updateOrCreate(
                ['name' => $p['name']],
                ['display_name' => $p['display_name']]
            );
        }
    }
}
