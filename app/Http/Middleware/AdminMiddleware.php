<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            abort(403, 'No tienes permisos para acceder a esta página.');
        }

        // Por slug, no por role_id fijo: el id del rol admin puede no ser 1 según migraciones/seed.
        $isAdmin = Auth::user()->roles()->where('slug', 'admin')->exists();

        if (! $isAdmin) {
            abort(403, 'No tienes permisos para acceder a esta página.');
        }

        return $next($request);
    }
}
