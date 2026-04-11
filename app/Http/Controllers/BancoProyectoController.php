<?php

namespace App\Http\Controllers;

use App\Models\BancoProyecto;
use App\Models\BancoProyectoHistorial;
use App\Models\CatalogoLineaInvestigacion;
use App\Models\CatalogoPrograma;
use App\Models\CatalogoTipoProyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BancoProyectoController extends Controller
{
    private const ESTADOS = [
        'borrador', 'en_evaluacion', 'aprobado', 'rechazado', 'en_ejecucion', 'cerrado', 'suspendido',
    ];

    public function index(Request $request)
    {
        $q = BancoProyecto::query()->with('creador');

        if ($s = $request->get('estado')) {
            $q->where('estado', $s);
        }
        if ($request->filled('q')) {
            $term = '%'.$request->get('q').'%';
            $q->where(function ($qq) use ($term) {
                $qq->where('titulo', 'like', $term)
                    ->orWhere('codigo', 'like', $term);
            });
        }

        $proyectos = $q->orderByDesc('created_at')->get();

        $counts = BancoProyecto::query()
            ->selectRaw('estado, count(*) as c')
            ->groupBy('estado')
            ->pluck('c', 'estado');

        $total = BancoProyecto::count();

        return view('banco-proyectos.index', [
            'proyectos' => $proyectos,
            'counts' => $counts,
            'total' => $total,
        ]);
    }

    public function create()
    {
        return view('banco-proyectos.create', $this->catalogos());
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['autores'] = $this->parseAutores($request);
        $data['created_by'] = Auth::id();
        $data['estado'] = 'borrador';

        $bp = DB::transaction(function () use ($data) {
            $p = BancoProyecto::create($data);
            $this->log($p, 'creado', null, null, 'Proyecto registrado en el banco');

            return $p;
        });

        return redirect()->route('banco.show', $bp)->with('success', 'Proyecto creado.');
    }

    public function show(BancoProyecto $bancoProyecto)
    {
        $bancoProyecto->load(['creador', 'anexos' => fn ($q) => $q->orderByDesc('uploaded_at')]);

        $historial = $bancoProyecto->historial()->limit(200)->get();

        return view('banco-proyectos.show', compact('bancoProyecto', 'historial'));
    }

    public function edit(BancoProyecto $bancoProyecto)
    {
        return view('banco-proyectos.edit', array_merge(
            ['bancoProyecto' => $bancoProyecto],
            $this->catalogos()
        ));
    }

    public function update(Request $request, BancoProyecto $bancoProyecto)
    {
        $data = $this->validated($request);
        $data['autores'] = $this->parseAutores($request);
        unset($data['created_by']);

        DB::transaction(function () use ($bancoProyecto, $data) {
            $bancoProyecto->update($data);
            $this->log($bancoProyecto, 'editado', null, null, null, 'Ficha actualizada');
        });

        return redirect()->route('banco.show', $bancoProyecto)->with('success', 'Cambios guardados.');
    }

    public function destroy(BancoProyecto $bancoProyecto)
    {
        if (! in_array($bancoProyecto->estado, ['borrador', 'rechazado'], true) && ! Auth::user()->hasRole('admin')) {
            return back()->withErrors(['error' => 'No se puede eliminar en este estado.']);
        }
        $bancoProyecto->delete();

        return redirect()->route('banco.index')->with('success', 'Proyecto eliminado.');
    }

    public function cambiarEstado(Request $request, BancoProyecto $bancoProyecto)
    {
        $request->validate([
            'estado' => 'required|in:'.implode(',', self::ESTADOS),
        ]);
        $prev = $bancoProyecto->estado;
        $bancoProyecto->update(['estado' => $request->estado]);
        $this->log($bancoProyecto, 'estado_cambiado', 'estado', $prev, $request->estado, 'Estado actualizado');

        return back()->with('success', 'Estado actualizado.');
    }

    public function subirCertificado(Request $request, BancoProyecto $bancoProyecto)
    {
        $request->validate([
            'certificado' => 'required|file|mimes:pdf|max:10240',
            'certificado_fecha' => 'nullable|date',
            'certificado_observaciones' => 'nullable|string',
        ]);
        $path = $request->file('certificado')->store("banco-proyectos/{$bancoProyecto->id}/certificado", 'public');
        $bancoProyecto->update([
            'certificado_cumplimiento' => $path,
            'certificado_fecha' => $request->certificado_fecha,
            'certificado_observaciones' => $request->certificado_observaciones,
        ]);
        $this->log($bancoProyecto, 'certificado_subido', null, null, 'Certificado de cumplimiento cargado');

        return back()->with('success', 'Certificado guardado.');
    }

    public function eliminarCertificado(BancoProyecto $bancoProyecto)
    {
        if ($bancoProyecto->certificado_cumplimiento) {
            Storage::disk('public')->delete($bancoProyecto->certificado_cumplimiento);
        }
        $bancoProyecto->update([
            'certificado_cumplimiento' => null,
            'certificado_fecha' => null,
            'certificado_observaciones' => null,
        ]);
        $this->log($bancoProyecto, 'editado', 'certificado', 'presente', 'eliminado');

        return back()->with('success', 'Certificado eliminado.');
    }

    public function historialJson(BancoProyecto $bancoProyecto)
    {
        return response()->json($bancoProyecto->historial()->orderByDesc('created_at')->get());
    }

    public function exportExcel()
    {
        return redirect()->route('banco.index')->with('info', 'Exportación Excel en preparación.');
    }

    public function exportPdf()
    {
        return redirect()->route('banco.index')->with('info', 'Exportación PDF en preparación.');
    }

    private function catalogos(): array
    {
        return [
            'programas' => CatalogoPrograma::where('activo', true)->orderBy('orden')->orderBy('nombre')->get(),
            'tipos' => CatalogoTipoProyecto::where('activo', true)->orderBy('orden')->orderBy('nombre')->get(),
            'lineas' => CatalogoLineaInvestigacion::where('activo', true)->orderBy('orden')->orderBy('nombre')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'titulo' => 'required|string|max:255',
            'linea_investigacion' => 'nullable|string|max:255',
            'area_facultad' => 'nullable|string|max:255',
            'tipo_proyecto' => 'nullable|string|max:100',
            'convocatoria' => 'nullable|string|max:255',
            'fecha_registro' => 'nullable|date',
            'resumen_ejecutivo' => 'nullable|string',
            'problema_necesidad' => 'nullable|string',
            'objetivo_general' => 'nullable|string',
            'justificacion' => 'nullable|string',
            'alcance' => 'nullable|string',
            'poblacion_objetivo' => 'nullable|string',
            'cobertura_geografica' => 'nullable|string|max:255',
            'presupuesto_estimado' => 'nullable|numeric|min:0',
            'fuente_financiacion' => 'nullable|string|max:255',
            'cofinanciacion' => 'nullable|numeric|min:0',
            'duracion_meses' => 'nullable|integer|min:1',
            'autores' => 'nullable|array',
            'autores.*' => 'string',
            'tutor_director' => 'nullable|string|max:255',
            'programa_departamento' => 'nullable|string|max:255',
            'entidad_aliada' => 'nullable|string|max:255',
            'evaluador_asignado' => 'nullable|string|max:255',
        ]);
    }

    private function parseAutores(Request $request): ?array
    {
        if (! $request->filled('autores_text')) {
            return $request->has('autores') ? (array) $request->input('autores') : null;
        }
        $lines = preg_split('/\r\n|\r|\n/', (string) $request->input('autores_text', ''));

        return array_values(array_filter(array_map('trim', $lines)));
    }

    private function log(
        BancoProyecto $p,
        string $accion,
        ?string $campo,
        ?string $antes,
        ?string $despues,
        ?string $descripcion = null
    ): void {
        BancoProyectoHistorial::create([
            'banco_proyecto_id' => $p->id,
            'accion' => $accion,
            'campo_modificado' => $campo,
            'valor_anterior' => $antes !== null ? (string) $antes : null,
            'valor_nuevo' => $despues !== null ? (string) $despues : null,
            'descripcion' => $descripcion,
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'created_at' => now(),
        ]);
    }
}
