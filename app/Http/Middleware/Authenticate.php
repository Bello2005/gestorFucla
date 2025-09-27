<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class Authenticate
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            Log::info('Usuario no autenticado, redirigiendo a login');
            return redirect()->route('login');
        }
        Log::info('Usuario autenticado correctamente', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email
        ]);
        return $next($request);
    }
}
