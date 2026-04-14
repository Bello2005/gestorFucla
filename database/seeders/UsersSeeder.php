<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->upsert(
            [
                ['name' => 'Usuario Prueba 1', 'email' => 'test1@uniclaretiana.edu.co', 'password' => Hash::make('password'), 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Usuario Prueba 2', 'email' => 'test2@uniclaretiana.edu.co', 'password' => Hash::make('password'), 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Usuario Prueba 3', 'email' => 'test3@uniclaretiana.edu.co', 'password' => Hash::make('password'), 'created_at' => now(), 'updated_at' => now()],
            ],
            ['email'],
            ['name', 'password', 'updated_at']
        );
    }
}
