<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProyectoRequest;
use App\Http\Requests\UpdateProyectoRequest;
use App\Models\Proyecto;
use App\Services\ProyectoExportService;
use App\Services\ProyectoFileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProyectoController extends Controller
{
    public function __construct(
        private ProyectoExportService $exportService,
        private ProyectoFileService $fileService,
    ) {}

    // =====================================================================
    //  Exportaciones
    // =====================================================================

    public function exportPdf(Request $request)
    {
        try {
            if ($request->has('id')) {
                $proyecto = Proyecto::findOrFail($request->id);
                return $this->exportService->exportarPdfUnico($proyecto);
            }

            return $this->exportService->exportarPdfTodos();
        } catch (\Exception $e) {
            Log::error('Error al exportar a PDF', ['error' => $e->getMessage()]);

            if ($request->ajax()) {
                return response()->json(['error' => 'Error al exportar a PDF: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Error al exportar a PDF: ' . $e->getMessage()]);
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            if ($request->has('id')) {
                $proyecto = Proyecto::findOrFail($request->id);
                return $this->exportService->exportarExcelUnico($proyecto);
            }

            return $this->exportService->exportarExcelTodos();
        } catch (\Exception $e) {
            Log::error('Error al exportar a Excel', ['error' => $e->getMessage()]);

            return redirect()->back()->withErrors(['error' => 'Error al exportar a Excel: ' . $e->getMessage()]);
        }
    }

    public function exportWord(Request $request)
    {
        try {
            if ($request->has('id')) {
                $proyecto = Proyecto::findOrFail($request->id);
                return $this->exportService->exportarWordUnico($proyecto);
            }

            return $this->exportService->exportarWordTodos();
        } catch (\Exception $e) {
            Log::error('Error al exportar a Word', ['error' => $e->getMessage()]);

            return redirect()->back()->withErrors(['error' => 'Error al exportar a Word: ' . $e->getMessage()]);
        }
    }

    // =====================================================================
    //  CRUD
    // =====================================================================

    public function index()
    {
        $order = request('order', 'desc');
        $q = Proyecto::query();

        if (request('cert') === 'con') {
            $q->whereNotNull('certificado_cumplimiento');
        } elseif (request('cert') === 'sin') {
            $q->whereNull('certificado_cumplimiento');
        }

        $proyectos = $q->orderBy('created_at', $order)->get();

        $entidades = Proyecto::distinct()
            ->whereNotNull('entidad_contratante')
            ->pluck('entidad_contratante')
            ->unique()
            ->values()
            ->toArray();

        return view('proyectos.index', compact('proyectos', 'entidades'));
    }

    public function create()
    {
        return view('proyectos.create');
    }

    public function store(StoreProyectoRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = array_merge(
                $request->validated(),
                $this->fileService->procesarArchivosStore($request->allFiles())
            );

            $proyecto = Proyecto::create($data);
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Proyecto creado exitosamente', 'id' => $proyecto->id]);
            }

            return redirect()->route('proyectos.index')->with('success', 'Proyecto creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear proyecto', ['error' => $e->getMessage()]);

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Error al crear el proyecto: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->withInput()->withErrors(['error' => 'Error al crear el proyecto: ' . $e->getMessage()]);
        }
    }

    public function show(Proyecto $proyecto)
    {
        return view('proyectos.show', compact('proyecto'));
    }

    public function edit(Proyecto $proyecto)
    {
        return view('proyectos.edit', compact('proyecto'));
    }

    public function update(UpdateProyectoRequest $request, Proyecto $proyecto)
    {
        DB::beginTransaction();
        try {
            $securityChecks = [
                'method'     => $request->input('_method') === 'PUT',
                'is_edit'    => $request->input('is_edit') === '1',
                'project_id' => $request->input('proyecto_id') == $proyecto->id,
            ];

            if (array_filter($securityChecks, fn ($check) => ! $check)) {
                Log::error('Falló la validación de seguridad', [
                    'proyecto_id'   => $proyecto->id,
                    'failed_checks' => array_keys(array_filter($securityChecks, fn ($c) => ! $c)),
                ]);
                throw new \Exception('Falló la validación de seguridad');
            }

            $data = array_merge(
                $request->validated(),
                $this->fileService->procesarArchivosUpdate($request->allFiles(), $proyecto)
            );

            $proyecto->update($data);
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Proyecto actualizado exitosamente']);
            }

            return redirect()->route('proyectos.show', $proyecto)->with('success', 'Proyecto actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar proyecto', ['proyecto_id' => $proyecto->id, 'error' => $e->getMessage()]);

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Error al actualizar el proyecto: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->withInput()->withErrors(['error' => 'Error al actualizar el proyecto: ' . $e->getMessage()]);
        }
    }

    public function destroy(Request $request, Proyecto $proyecto)
    {
        try {
            $this->fileService->eliminarTodosLosArchivos($proyecto);
            $proyecto->delete();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Proyecto eliminado exitosamente']);
            }

            return redirect()->route('proyectos.index')->with('success', 'Proyecto eliminado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar proyecto', ['proyecto_id' => $proyecto->id, 'error' => $e->getMessage()]);

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Error al eliminar el proyecto: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Error al eliminar el proyecto: ' . $e->getMessage()]);
        }
    }

    // =====================================================================
    //  Gestión de archivos individuales
    // =====================================================================

    public function deleteEvidenciaArchivo(Proyecto $proyecto, $indice)
    {
        try {
            $eliminada = $this->fileService->eliminarEvidencia($proyecto, (int) $indice);

            if (! $eliminada) {
                return response()->json(['success' => false, 'message' => 'No se encontró la evidencia para eliminar.'], 404);
            }

            return response()->json(['success' => true, 'message' => 'Evidencia eliminada exitosamente.']);
        } catch (\Exception $e) {
            Log::error('Error al eliminar evidencia', ['proyecto_id' => $proyecto->id, 'error' => $e->getMessage()]);

            return response()->json(['success' => false, 'message' => 'Error al eliminar la evidencia: ' . $e->getMessage()], 500);
        }
    }

    public function deleteContratoArchivo(Proyecto $proyecto)
    {
        try {
            $eliminado = $this->fileService->eliminarContrato($proyecto);

            if (! $eliminado) {
                return response()->json(['success' => false, 'message' => 'No se encontró el archivo de contrato/convenio para eliminar.'], 404);
            }

            return response()->json(['success' => true, 'message' => 'Archivo de contrato/convenio eliminado exitosamente.']);
        } catch (\Exception $e) {
            Log::error('Error al eliminar contrato', ['proyecto_id' => $proyecto->id, 'error' => $e->getMessage()]);

            return response()->json(['success' => false, 'message' => 'Error al eliminar el archivo: ' . $e->getMessage()], 500);
        }
    }

    public function deleteProyectoArchivo(Proyecto $proyecto)
    {
        try {
            $eliminado = $this->fileService->eliminarArchivoProyecto($proyecto);

            if (! $eliminado) {
                return response()->json(['success' => false, 'message' => 'No se encontró el archivo para eliminar.'], 404);
            }

            return response()->json(['success' => true, 'message' => 'Archivo del proyecto eliminado exitosamente.']);
        } catch (\Exception $e) {
            Log::error('Error al eliminar archivo del proyecto', ['proyecto_id' => $proyecto->id, 'error' => $e->getMessage()]);

            return response()->json(['success' => false, 'message' => 'Error al eliminar el archivo: ' . $e->getMessage()], 500);
        }
    }

    // =====================================================================
    //  Certificado de cumplimiento
    // =====================================================================

    public function subirCertificado(Request $request, Proyecto $proyecto)
    {
        $request->validate([
            'certificado'                  => 'required|file|mimes:pdf|max:10240',
            'certificado_fecha'            => 'nullable|date',
            'certificado_observaciones'    => 'nullable|string',
        ]);

        $path = $this->fileService->almacenarCertificado($request->file('certificado'), $proyecto);

        $proyecto->update([
            'certificado_cumplimiento'   => $path,
            'certificado_fecha'          => $request->certificado_fecha,
            'certificado_observaciones'  => $request->certificado_observaciones,
        ]);

        return redirect()->back()->with('success', 'Certificado de cumplimiento guardado.');
    }

    public function eliminarCertificado(Proyecto $proyecto)
    {
        $this->fileService->eliminarCertificadoArchivo($proyecto);

        $proyecto->update([
            'certificado_cumplimiento'  => null,
            'certificado_fecha'         => null,
            'certificado_observaciones' => null,
        ]);

        return redirect()->back()->with('success', 'Certificado eliminado.');
    }
}
