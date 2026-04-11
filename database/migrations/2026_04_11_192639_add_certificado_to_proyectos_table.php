<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->string('certificado_cumplimiento')->nullable()->after('estado');
            $table->date('certificado_fecha')->nullable()->after('certificado_cumplimiento');
            $table->text('certificado_observaciones')->nullable()->after('certificado_fecha');
        });
    }

    public function down(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->dropColumn(['certificado_cumplimiento', 'certificado_fecha', 'certificado_observaciones']);
        });
    }
};
