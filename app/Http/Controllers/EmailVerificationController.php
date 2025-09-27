<?php

namespace App\Http\Controllers;

use App\Models\EmailVerification;
use App\Models\User;
use App\Models\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class EmailVerificationController extends Controller
{
    public function verify(Request $request, $token)
    {
        Log::info('Iniciando proceso de verificación', ['token' => $token]);

        $verification = EmailVerification::where('token', $token)
            ->where('verified', false)
            ->first();
            
        Log::info('Resultado de búsqueda de verificación', [
            'token' => $token,
            'encontrado' => $verification ? 'sí' : 'no'
        ]);

        if (!$verification) {
            Log::warning('Verificación no encontrada o ya usada', ['token' => $token]);
            return response()->view('auth.email-verified', [
                'error' => 'El enlace de verificación es inválido o ya ha sido usado.'
            ]);
        }

        if ($verification->isExpired()) {
            Log::warning('Verificación expirada', ['id' => $verification->id]);
            return response()->view('auth.email-verified', [
                'error' => 'El enlace de verificación ha expirado.'
            ]);
        }

        try {
            DB::beginTransaction();

            // Actualizar el correo del usuario
            $user = User::find($verification->user_id);
            
            if (!$user) {
                throw new \Exception('Usuario no encontrado');
            }

            // Verificar que el nuevo correo sigue siendo único
            if (User::where('email', $verification->new_email)
                ->where('id', '!=', $verification->user_id)
                ->exists()) {
                throw new \Exception('Este correo electrónico ya está en uso por otro usuario.');
            }

            // Guardar los valores antiguos antes de la actualización
            $oldValues = $user->toArray();
            
            $user->email = $verification->new_email;
            $user->save();

            // Marcar la verificación como completada
            $verification->verified = true;
            $verification->verified_at = now();
            $verification->save();

            // Registrar la auditoría del cambio de correo
            $audit = new Audit();
            $audit->table_name = 'users';
            $audit->operation = 'UPDATE';
            $audit->record_id = (string) $user->id;
            $audit->old_values = $oldValues;
            $audit->new_values = $user->toArray();
            $audit->changed_by = $user->id;
            $audit->user_name = $user->name;
            $audit->ip_address = $request->ip();
            $audit->save();

            DB::commit();

            Log::info('Cambio de correo completado exitosamente', [
                'user_id' => $user->id,
                'new_email' => $verification->new_email
            ]);

            return response()->view('auth.email-verified', [
                'success' => true,
                'message' => 'Tu correo electrónico ha sido verificado y actualizado exitosamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar el correo', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->view('auth.email-verified', [
                'error' => 'Error al actualizar el correo electrónico: ' . $e->getMessage()
            ]);
        }
    }
}