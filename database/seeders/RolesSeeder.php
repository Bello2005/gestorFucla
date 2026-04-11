<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('roles') || ! Schema::hasTable('role_user')) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('role_user')->delete();
        DB::table('roles')->delete();
        Schema::enableForeignKeyConstraints();

        DB::table('roles')->insert([
            [
                'name' => 'admin',
                'slug' => 'admin',
                'description' => 'Administrador del sistema',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'supervisor',
                'slug' => 'supervisor',
                'description' => 'Supervisor de proyectos',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'usuario',
                'slug' => 'usuario',
                'description' => 'Usuario regular',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $uid1 = DB::table('users')->where('email', 'test1@uniclaretiana.edu.co')->value('id');
        $uid2 = DB::table('users')->where('email', 'test2@uniclaretiana.edu.co')->value('id');
        $uid3 = DB::table('users')->where('email', 'test3@uniclaretiana.edu.co')->value('id');
        $ridAdmin = DB::table('roles')->where('name', 'admin')->value('id');
        $ridSuper = DB::table('roles')->where('name', 'supervisor')->value('id');
        $ridUser = DB::table('roles')->where('name', 'usuario')->value('id');

        if ($uid1 && $ridAdmin) {
            DB::table('role_user')->insert([
                ['user_id' => $uid1, 'role_id' => $ridAdmin, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
        if ($uid2 && $ridSuper) {
            DB::table('role_user')->insert([
                ['user_id' => $uid2, 'role_id' => $ridSuper, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
        if ($uid3 && $ridUser) {
            DB::table('role_user')->insert([
                ['user_id' => $uid3, 'role_id' => $ridUser, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }
}
