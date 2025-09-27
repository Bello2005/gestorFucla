<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\EmailVerification;
use App\Models\User;
use App\Mail\EmailChangeVerificationMail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'new_email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
                'current_password' => [
                    'required_with:new_password,new_email',
                    function ($attribute, $value, $fail) use ($user) {
                        if ($value && !Hash::check($value, $user->password)) {
                            $fail('La contraseña actual es incorrecta.');
                        }
                    },
                ],
                'new_password' => ['nullable', 'confirmed', Password::defaults()],
            ]);

            $response = ['success' => true];

            // Actualizar nombre
            if ($user->name !== $validated['name']) {
                $user->name = $validated['name'];
                $response['profile_updated'] = true;
            }

            // Actualizar contraseña si se proporcionó una nueva
            if (!empty($validated['new_password'])) {
                $user->password = Hash::make($validated['new_password']);
                $response['profile_updated'] = true;
            }

            // Guardar los cambios del usuario
            $user->save();

                // Gestionar cambio de correo si se solicitó
            if (!empty($validated['new_email'])) {
                $verification = EmailVerification::createVerification(
                    $user->id,
                    $user->email,
                    $validated['new_email']
                );

                // Enviar notificación directamente al nuevo correo
                try {
                    Log::info('Iniciando proceso de envío de correo de verificación', [
                        'user_id' => $user->id,
                        'current_email' => $user->email,
                        'new_email' => $validated['new_email']
                    ]);

                    Log::info('Preparando correo de verificación', [
                        'to' => $validated['new_email'],
                        'verification_id' => $verification->id,
                        'token' => $verification->token
                    ]);

                    // Enviar correo usando el mismo método que funcionó en la prueba
                    Mail::raw("Por favor verifica tu cambio de correo electrónico haciendo clic en el siguiente enlace:\n\n" . 
                             url("/email/verify/{$verification->token}") . 
                             "\n\nEste enlace expirará en 24 horas.", function($message) use ($validated, $verification) {
                        $message->to($validated['new_email'])
                                ->subject('Verifica tu cambio de correo electrónico');
                    });
                    
                    Log::info('Correo de verificación enviado exitosamente', [
                        'to' => $validated['new_email'],
                        'user_id' => $user->id,
                        'verification_id' => $verification->id
                    ]);
                    
                    $response['email_verification_sent'] = true;
                } catch (\Exception $e) {
                    Log::error('Error al enviar correo de verificación', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'user_id' => $user->id,
                        'new_email' => $validated['new_email']
                    ]);
                    throw $e;
                }
            }            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('Error en actualización de perfil: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el perfil: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verifyEmail($token)
    {
        $verification = EmailVerification::where('token', $token)
            ->where('verified', false)
            ->first();

        if (!$verification) {
            return redirect()->route('home')
                ->with('error', 'El enlace de verificación es inválido o ya ha sido usado.');
        }

        if ($verification->isExpired()) {
            return redirect()->route('home')
                ->with('error', 'El enlace de verificación ha expirado.');
        }

        // Verificar que el nuevo correo sigue siendo único
        if (User::where('email', $verification->new_email)
            ->where('id', '!=', $verification->user_id)
            ->exists()) {
            return redirect()->route('home')
                ->with('error', 'Este correo electrónico ya está en uso por otro usuario.');
        }

        // Actualizar el correo del usuario
        $user = $verification->user;
        $user->email = $verification->new_email;
        $user->save();

        // Marcar la verificación como completada
        $verification->verified = true;
        $verification->verified_at = now();
        $verification->save();

        return view('auth.email-verification-success');
    }
}
