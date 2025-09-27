<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyecto;

class EstadisticaController extends Controller
{
    public function index(Request $request)
    {
        // Obtener cantidad de proyectos por estado
        $proyectosPorEstado = Proyecto::select('estado')
            ->groupBy('estado')
            ->selectRaw('estado, COUNT(*) as total')
            ->get();

        // Calcular totales generales
        $totalProyectos = Proyecto::count();
        $valorTotal = Proyecto::sum('valor_total');
        $proyectosActivos = Proyecto::where('estado', 'activo')->count();
        $proyectosInactivos = Proyecto::where('estado', 'inactivo')->count();
        $proyectosCerrados = Proyecto::where('estado', 'cerrado')->count();

        // Porcentaje de activos
        $porcentajeActivos = $totalProyectos > 0 ? round(($proyectosActivos / $totalProyectos) * 100, 1) : 0;

        // Tasa de éxito (ejemplo: cerrados / total)
        $tasaExito = $totalProyectos > 0 ? round(($proyectosCerrados / $totalProyectos) * 100, 1) : 0;

        // Crecimiento mensual simulado (puedes reemplazarlo por lógica real si tienes fechas)
        $crecimiento = 8.2; // %
        $crecimientoProyectos = 12; // %

        return view('estadistica', [
            'proyectosPorEstado' => $proyectosPorEstado,
            'totalProyectos' => $totalProyectos,
            'valorTotal' => $valorTotal,
            'proyectosActivos' => $proyectosActivos,
            'proyectosInactivos' => $proyectosInactivos,
            'proyectosCerrados' => $proyectosCerrados,
            'porcentajeActivos' => $porcentajeActivos,
            'tasaExito' => $tasaExito,
            'crecimiento' => $crecimiento,
            'crecimientoProyectos' => $crecimientoProyectos
        ]);
    }
}
