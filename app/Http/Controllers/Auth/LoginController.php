<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'cpf' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // tenta autenticar usando o campo CPF
        if (Auth::attempt(['cpf' => $credentials['cpf'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            return redirect()->intended('/master/dashboard');
        }

        return back()->withErrors([
            'cpf' => 'As credenciais não conferem.',
        ])->onlyInput('cpf');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
