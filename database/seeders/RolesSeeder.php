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
                'name'        => 'Administrador',
                'slug'        => 'admin',
                'description' => 'Acceso total al sistema',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Usuario',
                'slug'        => 'user',
                'description' => 'Acceso configurado por matriz de permisos',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);

        $ridAdmin = DB::table('roles')->where('slug', 'admin')->value('id');
        $ridUser  = DB::table('roles')->where('slug', 'user')->value('id');

        $uid1 = DB::table('users')->where('email', 'test1@uniclaretiana.edu.co')->value('id');
        $uid2 = DB::table('users')->where('email', 'test2@uniclaretiana.edu.co')->value('id');
        $uid3 = DB::table('users')->where('email', 'test3@uniclaretiana.edu.co')->value('id');

        if ($uid1 && $ridAdmin) {
            DB::table('role_user')->insert([
                ['user_id' => $uid1, 'role_id' => $ridAdmin, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
        if ($uid2 && $ridUser) {
            DB::table('role_user')->insert([
                ['user_id' => $uid2, 'role_id' => $ridUser, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
        if ($uid3 && $ridUser) {
            DB::table('role_user')->insert([
                ['user_id' => $uid3, 'role_id' => $ridUser, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }
}
