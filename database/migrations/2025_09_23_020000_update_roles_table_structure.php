<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // Agregar la columna name si no existe
            if (!Schema::hasColumn('roles', 'name')) {
                $table->string('name')->unique();
            }
            
            // Agregar la columna slug si no existe
            if (!Schema::hasColumn('roles', 'slug')) {
                $table->string('slug')->unique();
            }
            
            // Agregar la columna description si no existe
            if (!Schema::hasColumn('roles', 'description')) {
                $table->text('description')->nullable();
            }
        });

        // Asegurarnos que la tabla pivot tenga los campos correctos
        Schema::table('role_user', function (Blueprint $table) {
            if (!Schema::hasColumn('role_user', 'user_id')) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('role_user', 'role_id')) {
                $table->foreignId('role_id')->constrained()->onDelete('cascade');
            }
        });

        // Insertar roles básicos si no existen
        $roles = [
            ['name' => 'Administrador', 'slug' => 'admin', 'description' => 'Administrador del sistema'],
            ['name' => 'Usuario', 'slug' => 'user', 'description' => 'Usuario regular']
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['slug' => $role['slug']],
                $role
            );
        }
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['name', 'slug', 'description']);
        });

        Schema::table('role_user', function (Blueprint $table) {
            $table->dropForeign(['user_id', 'role_id']);
            $table->dropColumn(['user_id', 'role_id']);
        });
    }
};