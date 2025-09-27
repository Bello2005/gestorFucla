<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('proyectos', function (Blueprint $table) {
            // Verificar si la columna no existe antes de agregarla
            if (!Schema::hasColumn('proyectos', 'estado')) {
                $table->enum('estado', ['activo', 'cerrado', 'inactivo'])->default('activo')->after('cargar_evidencias');
            }
        });
    }

    public function down()
    {
        Schema::table('proyectos', function (Blueprint $table) {
            if (Schema::hasColumn('proyectos', 'estado')) {
                $table->dropColumn('estado');
            }
        });
    }
};