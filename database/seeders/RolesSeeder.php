<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run()
    {
        // Deshabilitar restricciones de clave foránea
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Limpiar tablas existentes
        DB::table('role_user')->truncate();
        DB::table('roles')->truncate();
        
        // Habilitar restricciones de clave foránea
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Insertar roles básicos
        DB::table('roles')->insert([
            [
                'name' => 'admin',
                'slug' => 'admin',
                'description' => 'Administrador del sistema',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'supervisor',
                'slug' => 'supervisor',
                'description' => 'Supervisor de proyectos',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'usuario',
                'slug' => 'usuario',
                'description' => 'Usuario regular',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Asignar rol de admin al primer usuario de prueba
        DB::table('role_user')->insert([
            [
                'user_id' => DB::table('users')->where('email', 'test1@uniclaretiana.edu.co')->value('id'),
                'role_id' => DB::table('roles')->where('name', 'admin')->value('id'),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Asignar rol de supervisor al segundo usuario de prueba
        DB::table('role_user')->insert([
            [
                'user_id' => DB::table('users')->where('email', 'test2@uniclaretiana.edu.co')->value('id'),
                'role_id' => DB::table('roles')->where('name', 'supervisor')->value('id'),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Asignar rol de usuario al tercer usuario de prueba
        DB::table('role_user')->insert([
            [
                'user_id' => DB::table('users')->where('email', 'test3@uniclaretiana.edu.co')->value('id'),
                'role_id' => DB::table('roles')->where('name', 'usuario')->value('id'),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}