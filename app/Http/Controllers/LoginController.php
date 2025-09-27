<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view("login");
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            "email" => ["required", "email"],
            "password" => ["required"]
        ]);

        $user = User::where("email", $credentials["email"])->first();

        if (!$user) {
            return back()
                ->withErrors(["email" => "Las credenciales proporcionadas no coinciden con nuestros registros."])
                ->withInput();
        }

        // Primero verificamos si es una contraseña sin encriptar
        if ($user->password === $credentials["password"]) {
            // Actualizamos la contraseña con versión encriptada
            $user->password = Hash::make($credentials["password"]);
            $user->save();
            
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->intended("dashboard");
        }
        
        // Si no coincide, intentamos verificar si está encriptada
        try {
            if (Hash::check($credentials["password"], $user->password)) {
                Auth::login($user);
                $request->session()->regenerate();
                return redirect()->intended("dashboard");
            }
        } catch (\RuntimeException $e) {
            // Si hay un error al verificar el hash, significa que la contraseña
            // no está en formato válido de hash
            Log::warning("Contraseña en formato inválido para usuario: " . $user->email);
        }

        return back()
            ->withErrors(["email" => "Las credenciales proporcionadas no coinciden con nuestros registros."])
            ->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect("/");
    }
}
