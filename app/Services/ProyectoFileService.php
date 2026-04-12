<?php

namespace App\Services;

use App\Models\Proyecto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProyectoFileService
{
    // =====================================================================
    //  Store (crear proyecto)
    // =====================================================================

    /**
     * Procesa y almacena los archivos al crear un proyecto.
     * Retorna los campos a mergear en $data antes del create().
     */
    public function procesarArchivosStore(array $files): array
    {
        $data = [];

        if (isset($files['archivo_proyecto'])) {
            $data['cargar_archivo_proyecto'] = $files['archivo_proyecto']
                ->store('proyectos/archivos', 'public');
        }

        if (isset($files['archivo_contrato'])) {
            $data['cargar_contrato_o_convenio'] = $files['archivo_contrato']
                ->store('proyectos/contratos', 'public');
        }

        if (isset($files['evidencias'])) {
            $data['cargar_evidencias'] = $this->almacenarEvidencias($files['evidencias']);
        }

        return $data;
    }

    // =====================================================================
    //  Update (actualizar proyecto)
    // =====================================================================

    /**
     * Procesa y almacena los archivos al actualizar un proyecto.
     * Elimina el archivo anterior si existe antes de guardar el nuevo.
     * Retorna los campos a mergear en $validated antes del update().
     */
    public function procesarArchivosUpdate(array $files, Proyecto $proyecto): array
    {
        $data = [];

        if (isset($files['archivo_proyecto'])) {
            $this->eliminarSiExiste($proyecto->cargar_archivo_proyecto);
            $data['cargar_archivo_proyecto'] = $files['archivo_proyecto']
                ->store('proyectos/archivos', 'public');
        }

        if (isset($files['archivo_contrato'])) {
            $this->eliminarSiExiste($proyecto->cargar_contrato_o_convenio);
            $data['cargar_contrato_o_convenio'] = $files['archivo_contrato']
                ->store('proyectos/contratos', 'public');
        }

        if (isset($files['evidencias'])) {
            $existentes = $proyecto->cargar_evidencias ?? [];
            $nuevas = $this->almacenarEvidencias($files['evidencias']);
            $data['cargar_evidencias'] = array_merge($existentes, $nuevas);
        }

        return $data;
    }

    // =====================================================================
    //  Eliminación individual
    // =====================================================================

    public function eliminarEvidencia(Proyecto $proyecto, int $indice): bool
    {
        $evidencias = is_array($proyecto->cargar_evidencias) ? $proyecto->cargar_evidencias : [];

        if (! isset($evidencias[$indice])) {
            return false;
        }

        $this->eliminarSiExiste($evidencias[$indice]);
        unset($evidencias[$indice]);

        $proyecto->cargar_evidencias = array_values($evidencias);
        $proyecto->save();

        return true;
    }

    public function eliminarContrato(Proyecto $proyecto): bool
    {
        if (! $proyecto->cargar_contrato_o_convenio) {
            return false;
        }

        $this->eliminarSiExiste($proyecto->cargar_contrato_o_convenio);
        $proyecto->cargar_contrato_o_convenio = null;
        $proyecto->save();

        return true;
    }

    public function eliminarArchivoProyecto(Proyecto $proyecto): bool
    {
        if (! $proyecto->cargar_archivo_proyecto) {
            return false;
        }

        $this->eliminarSiExiste($proyecto->cargar_archivo_proyecto);
        $proyecto->cargar_archivo_proyecto = null;
        $proyecto->save();

        return true;
    }

    /**
     * Elimina todos los archivos asociados a un proyecto (usado en destroy).
     */
    public function eliminarTodosLosArchivos(Proyecto $proyecto): void
    {
        $this->eliminarSiExiste($proyecto->cargar_archivo_proyecto);
        $this->eliminarSiExiste($proyecto->cargar_contrato_o_convenio);

        foreach ($proyecto->cargar_evidencias ?? [] as $evidencia) {
            $this->eliminarSiExiste($evidencia);
        }

        $this->eliminarSiExiste($proyecto->certificado_cumplimiento);
    }

    // =====================================================================
    //  Certificado
    // =====================================================================

    public function almacenarCertificado(UploadedFile $archivo, Proyecto $proyecto): string
    {
        $this->eliminarSiExiste($proyecto->certificado_cumplimiento);

        return $archivo->store("proyectos/certificados/{$proyecto->id}", 'public');
    }

    public function eliminarCertificadoArchivo(Proyecto $proyecto): void
    {
        $this->eliminarSiExiste($proyecto->certificado_cumplimiento);
    }

    // =====================================================================
    //  Helpers privados
    // =====================================================================

    private function almacenarEvidencias(array $archivos): array
    {
        $paths = [];
        foreach ($archivos as $archivo) {
            $paths[] = $archivo->store('proyectos/evidencias', 'public');
        }

        return $paths;
    }

    private function eliminarSiExiste(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
