<?php

namespace App\Http\Controllers\Escola;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
        $escola = $usuario->escola; // escola do usuário logado

        return view('escola.dashboard', compact('usuario', 'escola'));
    }
}
