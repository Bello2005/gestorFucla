<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabla de módulos de la aplicación
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->unique();
            $table->string('label', 100);
            $table->smallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // 2. Tabla de permisos por usuario/módulo
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->boolean('can_view')->default(false);
            $table->boolean('can_edit')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'module_id']);
        });

        // 3. Insertar los 7 módulos de la app
        $now = now();
        DB::table('modules')->insert([
            ['slug' => 'proyectos',   'label' => 'Proyectos Activos',  'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'banco',       'label' => 'Banco de Proyectos', 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'estadistica', 'label' => 'Estadísticas',       'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'auditoria',   'label' => 'Auditoría',          'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'usuarios',    'label' => 'Usuarios',           'sort_order' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'solicitudes', 'label' => 'Solicitudes',        'sort_order' => 6, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'catalogos',   'label' => 'Catálogos',          'sort_order' => 7, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 4. Asegurar que existe el rol 'user'
        $userRoleExists = DB::table('roles')->where('slug', 'user')->exists();
        if (!$userRoleExists) {
            DB::table('roles')->insert([
                'name'        => 'Usuario',
                'slug'        => 'user',
                'description' => 'Acceso configurado por matriz de permisos',
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }

        // 5. Migrar usuarios con roles 'supervisor' o 'usuario' al nuevo rol 'user'
        $userRoleId     = DB::table('roles')->where('slug', 'user')->value('id');
        $supervisorRole = DB::table('roles')->where('slug', 'supervisor')->first();
        $usuarioRole    = DB::table('roles')->where('slug', 'usuario')->first();

        $oldRoleIds = array_filter([
            $supervisorRole?->id,
            $usuarioRole?->id,
        ]);

        if (!empty($oldRoleIds)) {
            // Obtener usuarios afectados (que tienen rol supervisor o usuario)
            $affectedUserIds = DB::table('role_user')
                ->whereIn('role_id', $oldRoleIds)
                ->pluck('user_id')
                ->unique()
                ->values();

            if ($affectedUserIds->isNotEmpty()) {
                // Eliminar los role_user con roles viejos
                DB::table('role_user')
                    ->whereIn('user_id', $affectedUserIds)
                    ->whereIn('role_id', $oldRoleIds)
                    ->delete();

                // Asignar rol 'user' a cada uno (si no lo tienen ya)
                $existing = DB::table('role_user')
                    ->where('role_id', $userRoleId)
                    ->whereIn('user_id', $affectedUserIds)
                    ->pluck('user_id')
                    ->toArray();

                $toInsert = $affectedUserIds
                    ->reject(fn($uid) => in_array($uid, $existing))
                    ->map(fn($uid) => [
                        'user_id'    => $uid,
                        'role_id'    => $userRoleId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])
                    ->values()
                    ->toArray();

                if (!empty($toInsert)) {
                    DB::table('role_user')->insert($toInsert);
                }
            }

            // 6. Eliminar los roles viejos
            DB::table('roles')->whereIn('id', $oldRoleIds)->delete();
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
        Schema::dropIfExists('modules');
        // Nota: no se restauran los roles supervisor/usuario automáticamente
    }
};
