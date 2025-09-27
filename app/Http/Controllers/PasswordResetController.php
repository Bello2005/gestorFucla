<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PasswordResetController extends Controller
{
    public function showRequestForm()
    {
        return view('auth.passwords.request');
    }

    public function sendResetLink(Request $request)
    {
        Log::info('Solicitud de reset de contraseña para: ' . $request->email);
        
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email'
            ]);

            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                Log::warning('Email no encontrado en la base de datos: ' . $request->email);
                return back()
                    ->withErrors(['email' => 'No encontramos una cuenta con ese correo electrónico.'])
                    ->withInput();
            }

            $token = Str::random(64);
            
            // Eliminar tokens antiguos para este email
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            
            // Crear nuevo token
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => Carbon::now()
            ]);

            Mail::to($user)->send(new PasswordReset($user, $token));
            Log::info('Correo de reset enviado exitosamente a: ' . $request->email);
            
            return redirect()
                ->route('login')
                ->with('success', 'Se ha enviado un correo con las instrucciones para restablecer tu contraseña.');
                
        } catch (\Exception $e) {
            Log::error('Error en reset de contraseña: ' . $e->getMessage());
            return back()
                ->withErrors(['email' => 'Hubo un problema al procesar tu solicitud. Por favor, intenta más tarde.'])
                ->withInput();
        }
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('auth.passwords.reset', ['token' => $token, 'email' => $request->email]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed'
        ]);

        try {
            $tokenRecord = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();

            if (!$tokenRecord || !Hash::check($request->token, $tokenRecord->token)) {
                Log::warning('Token inválido para email: ' . $request->email);
                return back()->withErrors(['email' => 'Token inválido o expirado.']);
            }

            if (Carbon::parse($tokenRecord->created_at)->addHour()->isPast()) {
                DB::table('password_reset_tokens')->where('email', $request->email)->delete();
                Log::warning('Token expirado para email: ' . $request->email);
                return back()->withErrors(['email' => 'El token ha expirado.']);
            }

            $user = User::where('email', $request->email)->first();
            $newHashedPassword = Hash::make($request->password);
            
            // Verificar que la nueva contraseña se puede validar
            if (!Hash::check($request->password, $newHashedPassword)) {
                Log::error('Error al hashear nueva contraseña para: ' . $request->email);
                throw new \Exception('Error al encriptar la contraseña');
            }

            $user->password = $newHashedPassword;
            $user->is_temporary_password = false;
            $user->save();

            Log::info('Contraseña actualizada exitosamente para: ' . $request->email);

            // Eliminar el token usado
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return view('auth.verification-success');
        } catch (\Exception $e) {
            Log::error('Error en reset de contraseña: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Error al actualizar la contraseña. Por favor, intenta nuevamente.']);
        }
    }
}
