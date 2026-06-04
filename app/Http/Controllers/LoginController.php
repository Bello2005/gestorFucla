<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            "email"    => ["required", "email"],
            "password" => ["required"],
        ]);

        $remember = $request->boolean("remember");

        $user = User::where("email", $credentials["email"])->first();

        if (!$user || !Hash::check($credentials["password"], $user->password)) {
            return back()
                ->withErrors(["email" => "Las credenciales proporcionadas no coinciden con nuestros registros."])
                ->withInput();
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();
        return redirect()->intended("dashboard");
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect("/");
    }
}
