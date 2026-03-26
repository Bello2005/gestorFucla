<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $usuario = Auth::user()?->fresh();
        $usuarioNombre = trim((string) ($usuario?->name ?? $usuario?->nombre ?? ''));

        if ($usuarioNombre === '') {
            $aliasCorreo = Str::before((string) ($usuario?->email ?? ''), '@');
            $usuarioNombre = $aliasCorreo !== '' ? $aliasCorreo : 'Usuario';
        }

        // --- Current period stats ---
        $proyectos = Proyecto::all();

        $stats = [
            'total'       => $proyectos->count(),
            'activos'     => $proyectos->where('estado', 'activo')->count(),
            'inactivos'   => $proyectos->where('estado', 'inactivo')->count(),
            'cerrados'    => $proyectos->where('estado', 'cerrado')->count(),
            'valor_total' => $proyectos->sum('valor_total'),
            'entidades'   => $proyectos->unique('entidad_contratante')->count(),
        ];

        // --- Previous month stats (for trend deltas) ---
        $now           = now();
        $prevMonth     = $now->copy()->subMonth();
        $prevYear      = $prevMonth->year;
        $prevMonthNum  = $prevMonth->month;

        $proyectosAnteriores = Proyecto::whereYear('created_at', $prevYear)
            ->whereMonth('created_at', '<=', $prevMonthNum)
            ->get();

        $statsAnterior = [
            'total'       => $proyectosAnteriores->count(),
            'activos'     => $proyectosAnteriores->where('estado', 'activo')->count(),
            'valor_total' => $proyectosAnteriores->sum('valor_total'),
            'entidades'   => $proyectosAnteriores->unique('entidad_contratante')->count(),
        ];

        // --- Recent projects (ordered by latest activity) ---
        $recientes = Proyecto::orderBy('updated_at', 'desc')->take(5)->get();

        // --- Status summary for full-width row ---
        $resumenEstados = collect([
            ['label' => 'Activos',   'count' => $stats['activos'],   'variant' => 'success', 'icon' => 'fa-check-circle'],
            ['label' => 'Inactivos', 'count' => $stats['inactivos'], 'variant' => 'warning', 'icon' => 'fa-pause-circle'],
            ['label' => 'Cerrados',  'count' => $stats['cerrados'],  'variant' => 'danger',  'icon' => 'fa-times-circle'],
        ]);

        return view('dashboard', compact(
            'stats',
            'statsAnterior',
            'recientes',
            'usuarioNombre',
            'resumenEstados'
        ));
    }
}
