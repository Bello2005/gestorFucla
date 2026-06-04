<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequireModulePermission
{
    public function handle(Request $request, Closure $next, string $module, string $level = 'view')
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $allowed = $level === 'edit'
            ? $user->canEdit($module)
            : $user->canView($module);

        if (!$allowed) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
