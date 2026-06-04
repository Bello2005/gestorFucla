<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->boolean('read_only')->default(false)->after('sort_order');
        });

        // Módulos donde "Editar" no aplica para usuarios regulares:
        // las rutas de escritura de estos módulos son exclusivas de admin
        DB::table('modules')
            ->whereIn('slug', ['auditoria', 'estadistica', 'usuarios', 'solicitudes', 'catalogos'])
            ->update(['read_only' => true]);
    }

    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn('read_only');
        });
    }
};
