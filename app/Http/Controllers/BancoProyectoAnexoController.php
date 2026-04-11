<?php

namespace App\Http\Controllers;

use App\Models\BancoProyecto;
use App\Models\BancoProyectoAnexo;
use App\Models\BancoProyectoHistorial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BancoProyectoAnexoController extends Controller
{
    private const TIPOS = [
        'documento_proyecto', 'presupuesto', 'carta_aval', 'cronograma', 'imagen_plano',
        'soporte_adicional', 'certificado_cumplimiento',
    ];

    public function store(Request $request, BancoProyecto $bancoProyecto)
    {
        $request->validate([
            'archivo' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
            'tipo_anexo' => 'required|in:'.implode(',', self::TIPOS),
            'notas' => 'nullable|string',
        ]);

        $file = $request->file('archivo');
        $tipo = $request->tipo_anexo;

        DB::transaction(function () use ($bancoProyecto, $file, $tipo, $request) {
            $maxVersion = (int) BancoProyectoAnexo::where('banco_proyecto_id', $bancoProyecto->id)
                ->where('tipo_anexo', $tipo)
                ->max('version');
            $version = $maxVersion + 1;

            BancoProyectoAnexo::where('banco_proyecto_id', $bancoProyecto->id)
                ->where('tipo_anexo', $tipo)
                ->where('is_current', true)
                ->update(['is_current' => false]);

            $path = $file->store("banco-proyectos/{$bancoProyecto->id}/anexos", 'public');

            BancoProyectoAnexo::create([
                'banco_proyecto_id' => $bancoProyecto->id,
                'tipo_anexo' => $tipo,
                'nombre_original' => $file->getClientOriginalName(),
                'ruta_archivo' => $path,
                'tipo_archivo' => $file->getMimeType() ?? 'application/octet-stream',
                'tamano_bytes' => $file->getSize(),
                'version' => $version,
                'notas' => $request->notas,
                'uploaded_by' => Auth::id(),
                'uploaded_at' => now(),
                'is_current' => true,
            ]);

            BancoProyectoHistorial::create([
                'banco_proyecto_id' => $bancoProyecto->id,
                'accion' => 'anexo_subido',
                'descripcion' => 'Subido: '.$file->getClientOriginalName().' (v'.$version.')',
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'created_at' => now(),
            ]);
        });

        return back()->with('success', 'Anexo cargado.');
    }

    public function download(BancoProyecto $bancoProyecto, BancoProyectoAnexo $anexo): StreamedResponse
    {
        abort_unless($anexo->banco_proyecto_id === $bancoProyecto->id, 404);

        return Storage::disk('public')->download($anexo->ruta_archivo, $anexo->nombre_original);
    }

    public function destroy(BancoProyecto $bancoProyecto, BancoProyectoAnexo $anexo)
    {
        abort_unless($anexo->banco_proyecto_id === $bancoProyecto->id, 404);
        if (! $anexo->is_current) {
            return back()->withErrors(['error' => 'Solo puede eliminar la versión vigente desde esta acción.']);
        }
        $nombre = $anexo->nombre_original;
        $anexo->delete();
        BancoProyectoHistorial::create([
            'banco_proyecto_id' => $bancoProyecto->id,
            'accion' => 'anexo_eliminado',
            'descripcion' => 'Eliminado registro de anexo: '.$nombre,
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'created_at' => now(),
        ]);

        return back()->with('success', 'Anexo eliminado del listado.');
    }

    public function restore(Request $request, BancoProyecto $bancoProyecto, BancoProyectoAnexo $anexo)
    {
        abort_unless($anexo->banco_proyecto_id === $bancoProyecto->id, 404);

        DB::transaction(function () use ($bancoProyecto, $anexo) {
            BancoProyectoAnexo::where('banco_proyecto_id', $bancoProyecto->id)
                ->where('tipo_anexo', $anexo->tipo_anexo)
                ->update(['is_current' => false]);

            $anexo->update(['is_current' => true]);

            BancoProyectoHistorial::create([
                'banco_proyecto_id' => $bancoProyecto->id,
                'accion' => 'anexo_restaurado',
                'descripcion' => 'Versión restaurada: '.$anexo->nombre_original.' v'.$anexo->version,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'created_at' => now(),
            ]);
        });

        return back()->with('success', 'Versión restaurada como vigente.');
    }
}
