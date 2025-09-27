<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\PasswordResetHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\PasswordReset;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::with('roles')
            ->addSelect(['last_password_reset' => DB::table('password_reset_tokens')
                ->select('created_at')
                ->whereColumn('email', 'users.email')
                ->latest()
                ->limit(1)
            ])
            ->paginate(10);
        $roles = Role::all();

        return view('users.index', compact('usuarios', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'roles' => 'required|array'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_temporary_password' => false
        ]);

        $user->roles()->sync($request->roles);

        return response()->json(['message' => 'Usuario creado exitosamente']);
    }

    public function show(User $user)
    {
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->roles
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'roles' => 'required|array'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->is_temporary_password = false;
        }

        $user->save();
        $user->roles()->sync($request->roles);

        return response()->json(['message' => 'Usuario actualizado exitosamente']);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'No puedes eliminar tu propio usuario'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'Usuario eliminado exitosamente']);
    }

    public function resetPassword(Request $request, User $user)
    {
        try {
            \Log::info('Reset password request received:', $request->all());
            
            $validated = $request->validate([
                'reset_type' => 'required|in:email,temporal',
                'motivo' => 'required_if:reset_type,temporal|nullable|string',
                'force_change' => 'required|in:0,1',
                'invalidate_sessions' => 'required|in:0,1'
            ]);

            if ($request->reset_type === 'temporal') {
                // Generar contraseña temporal
                $temporalPassword = Str::random(12);
                $user->password = Hash::make($temporalPassword);
                $user->is_temporary_password = (bool)$request->force_change;
                $user->save();

                // Registrar historial
                PasswordResetHistory::create([
                    'user_id' => $user->id,
                    'reset_by' => auth()->id(),
                    'reason' => $request->motivo,
                    'method' => 'temporal'
                ]);

                // Si se solicita invalidar sesiones
                if ($request->invalidate_sessions == 1) {
                    DB::table('sessions')->where('user_id', $user->id)->delete();
                }

                return response()->json([
                    'success' => true,
                    'message' => "Contraseña temporal: {$temporalPassword}. Por favor, compártela de forma segura con el usuario."
                ]);
            } else {
                // Enviar enlace por correo
                $token = Str::random(60);
                
                // Guardar token en la base de datos
                DB::table('password_reset_tokens')->updateOrInsert(
                    ['email' => $user->email],
                    [
                        'token' => Hash::make($token),
                        'created_at' => Carbon::now()
                    ]
                );

                try {
                    // Enviar correo con el token y el usuario
                    Mail::to($user->email)->send(new PasswordReset($user, $token));
                } catch (\Exception $e) {
                    \Log::error('Error sending password reset email:', ['error' => $e->getMessage()]);
                    throw new \Exception('Error al enviar el correo de restablecimiento: ' . $e->getMessage());
                }

                // Registrar historial
                PasswordResetHistory::create([
                    'user_id' => $user->id,
                    'reset_by' => auth()->id(),
                    'reason' => $request->motivo,
                    'method' => 'email'
                ]);

                if ($request->invalidate_sessions == 1) {
                    DB::table('sessions')->where('user_id', $user->id)->delete();
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Se ha enviado un enlace de restablecimiento al correo del usuario'
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error resetting password:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al restablecer la contraseña: ' . $e->getMessage()
            ], 500);
        }
    }
}