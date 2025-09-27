<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProyectosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proyectos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_del_proyecto');
            $table->string('objeto_contractual')->nullable();
            $table->text('lineas_de_accion')->nullable();
            $table->string('cobertura')->nullable();
            $table->string('entidad_contratante')->nullable();
            $table->date('fecha_de_ejecucion')->nullable();
            $table->decimal('plazo', 8, 2)->nullable(); // En meses o días
            $table->decimal('valor_total', 15, 2)->nullable();
            $table->string('cargar_archivo_proyecto')->nullable(); // Ruta del archivo
            $table->string('cargar_contrato_o_convenio')->nullable(); // Ruta del archivo
            $table->text('cargar_evidencias')->nullable(); // JSON para múltiples archivos
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proyectos');
    }
}