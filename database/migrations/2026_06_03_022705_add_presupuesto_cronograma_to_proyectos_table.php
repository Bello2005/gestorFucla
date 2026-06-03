<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->string('cargar_presupuesto')->nullable()->after('cargar_contrato_o_convenio');
            $table->string('cargar_cronograma')->nullable()->after('cargar_presupuesto');
        });
    }

    public function down(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->dropColumn(['cargar_presupuesto', 'cargar_cronograma']);
        });
    }
};
