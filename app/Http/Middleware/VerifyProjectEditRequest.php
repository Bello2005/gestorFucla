<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerifyProjectEditRequest
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->is('api/*') && !$request->ajax() && !$request->wantsJson()) {
            $request->headers->set('Accept', 'text/html');
        }

        Log::info('VerifyProjectEditRequest middleware iniciado', [
            'method' => $request->method(),
            'url' => $request->url(),
            'is_edit' => $request->has('is_edit'),
            'proyecto_id' => $request->input('proyecto_id'),
            'route_proyecto' => $request->route('proyecto'),
            'accepts_json' => $request->expectsJson()
        ]);

        if (!$request->has('is_edit')) {
            Log::warning('Intento de actualización sin campo is_edit', [
                'url' => $request->url(),
                'method' => $request->method()
            ]);
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Solicitud de edición inválida'], 422);
            }
            return redirect()->back()->with('error', 'Solicitud de edición inválida');
        }
        
        // Obtener el ID del proyecto de la ruta
        $routeProject = $request->route('proyecto');
        $routeId = is_object($routeProject) ? $routeProject->id : $routeProject;
        
        // Obtener el ID del formulario
        $formId = $request->input('proyecto_id');
        
        Log::info('Comparación de IDs de proyecto', [
            'route_project_raw' => $request->route('proyecto'),
            'route_id' => $routeId,
            'form_id' => $formId,
            'types' => [
                'route_id_type' => gettype($routeId),
                'form_id_type' => gettype($formId)
            ]
        ]);

        if ($routeId != $formId) {
            Log::warning('ID de proyecto no coincide en la solicitud de edición', [
                'route_id' => $routeId,
                'form_id' => $formId,
                'method' => $request->method(),
                'path' => $request->path(),
                'full_url' => $request->fullUrl()
            ]);
            if ($request->expectsJson()) {
                return response()->json(['message' => 'ID de proyecto no válido'], 422);
            }
            return redirect()->back()->with('error', 'ID de proyecto no válido');
        }

        return $next($request);
    }
}