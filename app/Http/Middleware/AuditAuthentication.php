<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Audit;
use Illuminate\Support\Facades\Auth;

class AuditAuthentication
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
        $response = $next($request);

        try {
            // Si la ruta está relacionada con autenticación
            if ($this->isAuthenticationRoute($request)) {
                $user = Auth::user();
                $operation = $this->determineOperation($request);
                $routeName = $request->route()->getName();
                $email = $request->input('email') ?? ($user ? $user->email : null);

                if ($operation) {
                    Audit::create([
                        'table_name' => 'authentication',
                        'operation' => $operation,
                        'record_id' => $user ? $user->id : null,
                        'old_values' => json_encode(['action' => $routeName]),
                        'new_values' => json_encode([
                            'action' => $routeName,
                            'email' => $email,
                            'ip_address' => $request->ip(),
                            'user_agent' => $request->userAgent(),
                            'success' => $user !== null
                        ]),
                        'changed_by' => $user ? $user->id : null,
                        'user_name' => $user ? $user->name : $email,
                        'ip_address' => $request->ip()
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Log el error pero no interrumpir el flujo normal
            \Log::error('Error en auditoría de autenticación: ' . $e->getMessage());
        }

        return $response;
    }

    /**
     * Determina si la ruta actual está relacionada con autenticación
     */
    protected function isAuthenticationRoute(Request $request): bool
    {
        $authRoutes = [
            'login',
            'logout',
            'password.email',
            'password.reset',
            'password.update',
            'register',
            'email/verify'
        ];

        return in_array($request->route()->getName(), $authRoutes);
    }

    /**
     * Determina la operación basada en la ruta
     */
    protected function determineOperation(Request $request): ?string
    {
        $route = $request->route()->getName();

        // Mapeamos todas las operaciones a los valores permitidos del ENUM ('INSERT', 'UPDATE', 'DELETE')
        $operations = [
            'login' => 'INSERT',
            'logout' => 'UPDATE',
            'password.email' => 'UPDATE',
            'password.reset' => 'UPDATE',
            'password.update' => 'UPDATE',
            'register' => 'INSERT',
            'email/verify' => 'UPDATE'
        ];

        return $operations[$route] ?? 'UPDATE';
    }
}