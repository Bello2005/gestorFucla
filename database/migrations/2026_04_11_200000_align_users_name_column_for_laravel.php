<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Align legacy `users` tables with Laravel (expects a `name` column).
     * Some databases used `nombre` instead.
     */
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        if (Schema::hasColumn('users', 'name')) {
            return;
        }

        if (Schema::hasColumn('users', 'nombre')) {
            $driver = Schema::getConnection()->getDriverName();
            if ($driver === 'pgsql') {
                DB::statement('ALTER TABLE users RENAME COLUMN nombre TO name');
            } else {
                Schema::table('users', function (Blueprint $table) {
                    $table->renameColumn('nombre', 'name');
                });
            }

            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        if (Schema::hasColumn('users', 'name') && ! Schema::hasColumn('users', 'nombre')) {
            $driver = Schema::getConnection()->getDriverName();
            if ($driver === 'pgsql') {
                DB::statement('ALTER TABLE users RENAME COLUMN name TO nombre');
            } else {
                Schema::table('users', function (Blueprint $table) {
                    $table->renameColumn('name', 'nombre');
                });
            }
        }
    }
};
