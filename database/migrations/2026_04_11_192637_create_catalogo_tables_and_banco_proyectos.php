<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalogo_programas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('facultad')->nullable();
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });

        Schema::create('catalogo_tipos_proyecto', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });

        Schema::create('catalogo_lineas_investigacion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('area')->nullable();
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });

        Schema::create('banco_proyectos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('titulo');
            $table->string('linea_investigacion')->nullable();
            $table->string('area_facultad')->nullable();
            $table->string('tipo_proyecto', 100)->nullable();
            $table->string('convocatoria')->nullable();
            $table->date('fecha_registro')->useCurrent();
            $table->string('estado', 32)->default('borrador');

            $table->text('resumen_ejecutivo')->nullable();
            $table->text('problema_necesidad')->nullable();
            $table->text('objetivo_general')->nullable();
            $table->text('justificacion')->nullable();
            $table->text('alcance')->nullable();
            $table->text('poblacion_objetivo')->nullable();
            $table->string('cobertura_geografica')->nullable();

            $table->decimal('presupuesto_estimado', 15, 2)->nullable();
            $table->string('fuente_financiacion')->nullable();
            $table->decimal('cofinanciacion', 15, 2)->nullable();
            $table->unsignedInteger('duracion_meses')->nullable();

            $table->json('autores')->nullable();
            $table->string('tutor_director')->nullable();
            $table->string('programa_departamento')->nullable();
            $table->string('entidad_aliada')->nullable();
            $table->string('evaluador_asignado')->nullable();

            $table->string('certificado_cumplimiento')->nullable();
            $table->date('certificado_fecha')->nullable();
            $table->text('certificado_observaciones')->nullable();

            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('banco_proyecto_anexos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('banco_proyecto_id')->constrained('banco_proyectos')->cascadeOnDelete();
            $table->string('tipo_anexo', 40);
            $table->string('nombre_original');
            $table->string('ruta_archivo', 500);
            $table->string('tipo_archivo', 50);
            $table->unsignedBigInteger('tamano_bytes');
            $table->integer('version')->default(1);
            $table->text('notas')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('uploaded_at')->useCurrent();
            $table->boolean('is_current')->default(true);
        });

        Schema::create('banco_proyecto_historial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('banco_proyecto_id')->constrained('banco_proyectos')->cascadeOnDelete();
            $table->string('accion', 100);
            $table->string('campo_modificado', 100)->nullable();
            $table->text('valor_anterior')->nullable();
            $table->text('valor_nuevo')->nullable();
            $table->text('descripcion')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('user_name', 255);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banco_proyecto_historial');
        Schema::dropIfExists('banco_proyecto_anexos');
        Schema::dropIfExists('banco_proyectos');
        Schema::dropIfExists('catalogo_lineas_investigacion');
        Schema::dropIfExists('catalogo_tipos_proyecto');
        Schema::dropIfExists('catalogo_programas');
    }
};
