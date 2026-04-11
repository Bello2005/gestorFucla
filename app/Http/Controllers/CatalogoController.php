<?php

namespace App\Http\Controllers;

use App\Models\CatalogoLineaInvestigacion;
use App\Models\CatalogoPrograma;
use App\Models\CatalogoTipoProyecto;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function index()
    {
        return view('catalogos.index', [
            'programas' => CatalogoPrograma::orderBy('orden')->orderBy('nombre')->get(),
            'tipos' => CatalogoTipoProyecto::orderBy('orden')->orderBy('nombre')->get(),
            'lineas' => CatalogoLineaInvestigacion::orderBy('orden')->orderBy('nombre')->get(),
        ]);
    }

    public function storePrograma(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'facultad' => 'nullable|string|max:255',
        ]);
        $data['orden'] = (CatalogoPrograma::max('orden') ?? 0) + 1;
        CatalogoPrograma::create($data);

        return back()->with('success', 'Programa creado.');
    }

    public function updatePrograma(Request $request, CatalogoPrograma $programa)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'facultad' => 'nullable|string|max:255',
            'activo' => 'sometimes|boolean',
            'orden' => 'sometimes|integer',
        ]);
        $programa->update($data);

        return back()->with('success', 'Programa actualizado.');
    }

    public function destroyPrograma(CatalogoPrograma $programa)
    {
        $programa->delete();

        return back()->with('success', 'Programa eliminado.');
    }

    public function storeTipo(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
        ]);
        $data['orden'] = (CatalogoTipoProyecto::max('orden') ?? 0) + 1;
        CatalogoTipoProyecto::create($data);

        return back()->with('success', 'Tipo creado.');
    }

    public function updateTipo(Request $request, CatalogoTipoProyecto $tipo)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'activo' => 'sometimes|boolean',
            'orden' => 'sometimes|integer',
        ]);
        $tipo->update($data);

        return back()->with('success', 'Tipo actualizado.');
    }

    public function destroyTipo(CatalogoTipoProyecto $tipo)
    {
        $tipo->delete();

        return back()->with('success', 'Tipo eliminado.');
    }

    public function storeLinea(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'area' => 'nullable|string|max:255',
        ]);
        $data['orden'] = (CatalogoLineaInvestigacion::max('orden') ?? 0) + 1;
        CatalogoLineaInvestigacion::create($data);

        return back()->with('success', 'Línea creada.');
    }

    public function updateLinea(Request $request, CatalogoLineaInvestigacion $linea)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'area' => 'nullable|string|max:255',
            'activo' => 'sometimes|boolean',
            'orden' => 'sometimes|integer',
        ]);
        $linea->update($data);

        return back()->with('success', 'Línea actualizada.');
    }

    public function destroyLinea(CatalogoLineaInvestigacion $linea)
    {
        $linea->delete();

        return back()->with('success', 'Línea eliminada.');
    }
}
