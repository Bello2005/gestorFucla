<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Audit;
use App\Models\PasswordResetHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\PasswordReset;
use Illuminate\Validation\Rules\Password;

class UsuarioController extends Controller
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
            ->get();
        $roles = Role::all();

        return view('usuarios.index', compact('usuarios', 'roles'));
    }
    
    public function resetPassword(Request $request, User $usuario)
    {
        $validator = Validator::make($request->all(), [
            'reset_type' => 'required|in:email,temporal',
            'motivo' => 'required_if:reset_type,temporal',
            'force_change' => 'boolean',
            'invalidate_sessions' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            // Registrar en auditoría e historial
            $resetType = $request->reset_type;
            $token = null;
            
            // Crear registro en el historial de restablecimientos
            if ($resetType === 'email') {
                $token = Str::random(64);
                PasswordResetHistory::createReset($usuario->id, 'email', $request->motivo, $token);
            } else {
                PasswordResetHistory::createReset($usuario->id, 'temporal', $request->motivo);
            }
            Audit::create([
                'table_name' => 'users',
                'operation' => 'UPDATE',
                'record_id' => $usuario->id,
                'old_values' => json_encode(['action' => 'password_reset']),
                'new_values' => json_encode([
                    'reset_type' => $request->reset_type,
                    'reason' => $request->motivo,
                    'force_change' => $request->force_change,
                    'invalidate_sessions' => $request->invalidate_sessions
                ]),
                'changed_by' => Auth::id(),
                'user_name' => Auth::user()->name,
                'ip_address' => $request->ip()
            ]);

            if ($request->invalidate_sessions) {
                // Invalidar todas las sesiones del usuario
                DB::table('sessions')->where('user_id', $usuario->id)->delete();
            }

            if ($request->reset_type === 'email') {
                // Enviar enlace de restablecimiento
                $token = Str::random(64);
                DB::table('password_reset_tokens')->updateOrInsert(
                    ['email' => $usuario->email],
                    [
                        'token' => Hash::make($token),
                        'created_at' => now()
                    ]
                );

                Mail::to($usuario)->send(new PasswordReset($usuario, $token));
                
                $message = 'Se ha enviado un enlace de restablecimiento al correo del usuario.';
            } else {
                // Generar contraseña temporal
                $temporalPassword = Str::random(12);
                $usuario->update([
                    'password' => Hash::make($temporalPassword),
                    'is_temporary_password' => true
                ]);

                if ($request->force_change) {
                    $usuario->update(['force_password_change' => true]);
                }

                $message = 'Se ha generado una contraseña temporal: ' . $temporalPassword;
            }

            DB::commit();
            return response()->json(['message' => $message]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al restablecer la contraseña: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Password::defaults()],
            'roles' => ['required', 'array']
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $usuario = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $usuario->roles()->sync($request->roles);

        return response()->json([
            'message' => 'Usuario creado exitosamente',
            'user' => $usuario->load('roles')
        ]);
    }

    public function update(Request $request, User $usuario)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $usuario->id],
            'roles' => ['required', 'array'],
            'password' => $request->has('password') ? ['required', Password::defaults()] : []
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $usuario->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $usuario->update(['password' => Hash::make($request->password)]);
        }

        $usuario->roles()->sync($request->roles);

        return response()->json([
            'message' => 'Usuario actualizado exitosamente',
            'user' => $usuario->load('roles')
        ]);
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === Auth::id()) {
            return response()->json(['message' => 'No puedes eliminar tu propio usuario'], 403);
        }

        $usuario->delete();
        return response()->json(['message' => 'Usuario eliminado exitosamente']);
    }
}
