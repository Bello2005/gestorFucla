<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Audit;
use Carbon\Carbon;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = Audit::with('user')
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('table')) {
            $query->where('table_name', $request->table);
        }

        if ($request->filled('operation')) {
            $query->where('operation', $request->operation);
        }

        if ($request->filled('user')) {
            $query->where('changed_by', $request->user);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Obtener datos para los filtros
        $tables = Audit::distinct()->pluck('table_name');
        $operations = Audit::distinct()->pluck('operation');
        $audits = $query->paginate(25);

        return view('audit.index', compact('audits', 'tables', 'operations'));
    }

    public function show(Audit $audit)
    {
        return view('audit.show', compact('audit'));
    }

    public function export(Request $request)
    {
        $query = Audit::with('user')
            ->orderBy('created_at', 'desc');

        // Aplicar los mismos filtros que en index
        if ($request->has('table')) {
            $query->where('table_name', $request->table);
        }

        if ($request->has('operation')) {
            $query->where('operation', $request->operation);
        }

        if ($request->has('user')) {
            $query->where('changed_by', $request->user);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $audits = $query->get();
        
        $filename = 'auditoria_' . Carbon::now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $callback = function() use ($audits) {
            $file = fopen('php://output', 'w');
            
            // Cabeceras CSV
            fputcsv($file, [
                'ID',
                'Tabla',
                'Operación',
                'ID Registro',
                'Usuario',
                'Dirección IP',
                'Navegador',
                'Valores Anteriores',
                'Valores Nuevos',
                'Fecha'
            ]);

            foreach ($audits as $audit) {
                fputcsv($file, [
                    $audit->id,
                    $audit->table_name,
                    $audit->operation,
                    $audit->record_id,
                    $audit->user_name,
                    $audit->ip_address,
                    $audit->user_agent,
                    $audit->old_values,
                    $audit->new_values,
                    $audit->created_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
