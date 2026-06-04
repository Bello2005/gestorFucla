<?php

namespace App\Http\Controllers;

use App\Models\Convocatoria;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ConvocatoriaController extends Controller
{
    public function index()
    {
        $convocatorias = Convocatoria::with('creadoPor')
            ->latest()
            ->get();

        return response()->json($convocatorias);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'           => 'required|string|max:255',
            'entidad'          => 'required|string|max:255',
            'descripcion'      => 'nullable|string',
            'fecha_inicio'     => 'nullable|date',
            'fecha_cierre'     => 'nullable|date|after_or_equal:fecha_inicio',
            'fecha_resultados' => 'nullable|date',
            'responsable'      => 'nullable|string|max:255',
            'enlace'           => 'nullable|url|max:500',
            'correo_contacto'  => 'nullable|email|max:255',
            'observacion'      => 'nullable|string',
        ]);

        $data['created_by'] = auth()->id();

        $convocatoria = Convocatoria::create($data);

        return response()->json([
            'message'       => 'Convocatoria registrada exitosamente.',
            'convocatoria'  => $convocatoria->load('creadoPor'),
        ], 201);
    }

    public function show(Convocatoria $convocatoria)
    {
        return response()->json($convocatoria->load('creadoPor'));
    }

    public function update(Request $request, Convocatoria $convocatoria)
    {
        $data = $request->validate([
            'nombre'           => 'required|string|max:255',
            'entidad'          => 'required|string|max:255',
            'descripcion'      => 'nullable|string',
            'fecha_inicio'     => 'nullable|date',
            'fecha_cierre'     => 'nullable|date|after_or_equal:fecha_inicio',
            'fecha_resultados' => 'nullable|date',
            'responsable'      => 'nullable|string|max:255',
            'enlace'           => 'nullable|url|max:500',
            'correo_contacto'  => 'nullable|email|max:255',
            'observacion'      => 'nullable|string',
        ]);

        $convocatoria->update($data);

        return response()->json([
            'message'      => 'Convocatoria actualizada exitosamente.',
            'convocatoria' => $convocatoria->fresh('creadoPor'),
        ]);
    }

    public function destroy(Convocatoria $convocatoria)
    {
        $convocatoria->delete();

        return response()->json(['message' => 'Convocatoria eliminada exitosamente.']);
    }
}
