<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Escola;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        $filtro = request('tipo');
        $usuarios = Usuario::with(['escola','roles'])->filtrarPorEscola($filtro)->get();

        //$usuarios = Usuario::with(['escola', 'roles'])->get();
        return view('master.usuarios.index', compact('usuarios','filtro'));
    }

    public function create()
    {
        $escolas = Escola::all();
        $roles   = Role::all();
        return view('master.usuarios.create', compact('escolas', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome_u'   => 'required|string|max:100',
            'cpf'      => 'required|string|max:20',
            'senha'    => 'required|string|min:4',
            'school_id'=> 'required|integer',
        ]);

        $usuario = Usuario::create([
            'nome_u'     => $request->nome_u,
            'cpf'        => $request->cpf,
            'senha_hash' => Hash::make($request->senha),
            'status'     => 1,
            'school_id'  => $request->school_id,
        ]);

        // Sincronizar roles com school_id
        $rolesSync = [];
        if ($request->has('roles')) {
            foreach ($request->roles as $role_id) {
                $rolesSync[$role_id] = ['school_id' => $request->school_id];
            }
        }
        $usuario->roles()->sync($rolesSync);

        return redirect()->route('master.usuarios.index')
            ->with('success', 'Usuário criado com sucesso!');
    }

    public function edit(Usuario $usuario)
    {
        $escolas = Escola::all();
        $roles   = Role::all();
        $rolesUsuario = $usuario->roles->pluck('id')->toArray();

        return view('master.usuarios.edit', compact('usuario', 'escolas', 'roles', 'rolesUsuario'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        $request->validate([
            'nome_u'   => 'required|string|max:100',
            'cpf'      => 'required|string|max:20',
            'school_id'=> 'required|integer',
            'status'   => 'required|in:0,1',
        ]);

        $usuario->update([
            'nome_u'   => $request->nome_u,
            'cpf'      => $request->cpf,
            'status'   => $request->status,
            'school_id'=> $request->school_id,
        ]);

        if ($request->filled('senha')) {
            $usuario->update(['senha_hash' => Hash::make($request->senha)]);
        }

        // Atualizar roles com school_id
        $rolesSync = [];
        if ($request->has('roles')) {
            foreach ($request->roles as $role_id) {
                $rolesSync[$role_id] = ['school_id' => $request->school_id];
            }
        }
        $usuario->roles()->sync($rolesSync);

        return redirect()->route('master.usuarios.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(Usuario $usuario)
    {
        // Primeiro remove vínculos na pivot
        $usuario->roles()->detach();

        // Depois exclui usuário
        $usuario->delete();

        return redirect()->route('master.usuarios.index')
            ->with('success', 'Usuário excluído com sucesso!');
    }
}
