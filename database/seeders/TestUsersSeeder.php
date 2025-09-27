<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TestUsersSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'Usuario Prueba 1',
                'email' => 'test1@uniclaretiana.edu.co',
                'password' => Hash::make('password123'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'is_temporary_password' => false
            ],
            [
                'name' => 'Usuario Prueba 2',
                'email' => 'test2@uniclaretiana.edu.co',
                'password' => Hash::make('password123'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'is_temporary_password' => false
            ],
            [
                'name' => 'Usuario Prueba 3',
                'email' => 'test3@uniclaretiana.edu.co',
                'password' => Hash::make('password123'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'is_temporary_password' => false
            ]
        ]);
    }
}