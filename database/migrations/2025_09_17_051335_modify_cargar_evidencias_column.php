<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Doctrine\DBAL\Types\Type;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Crear una copia de seguridad de los datos existentes
        $datos = DB::table('proyectos')->select('id', 'cargar_evidencias')->get();
        
        // Eliminar la columna existente
        Schema::table('proyectos', function (Blueprint $table) {
            $table->dropColumn('cargar_evidencias');
        });
        
        // Crear la nueva columna como JSON
        Schema::table('proyectos', function (Blueprint $table) {
            $table->json('cargar_evidencias')->nullable();
        });
        
        // Restaurar los datos, asegurando que sean JSON válido
        foreach ($datos as $dato) {
            $evidencias = $dato->cargar_evidencias;
            if (empty($evidencias)) {
                $evidencias = '[]';
            } elseif (!is_string($evidencias)) {
                $evidencias = json_encode($evidencias);
            } elseif (!json_decode($evidencias)) {
                $evidencias = '[]';
            }
            
            DB::table('proyectos')
                ->where('id', $dato->id)
                ->update(['cargar_evidencias' => $evidencias]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->text('cargar_evidencias')->nullable()->change();
        });
    }
};
