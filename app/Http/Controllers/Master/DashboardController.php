<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Escola;
use App\Models\Usuario;
use App\Models\Role;

class DashboardController extends Controller
{
    public function index()
    {
        // todas as escolas
        $escolas = Escola::all();

        // todos os usuários já com escola e roles carregados
        $usuarios = Usuario::with(['escola', 'roles'])->get();

        // todas as roles
        $roles = Role::all();

        // filtro padrão (para compatibilidade com index.blade.php de escolas)
        $filtro = null;

        return view('master.dashboard', compact('escolas', 'usuarios', 'roles', 'filtro'));
    }
}
