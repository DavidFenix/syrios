<?php

namespace App\Http\Controllers;

use App\Models\Escola;
use App\Models\Usuario;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MasterController extends Controller
{
    // Página principal
    public function index()
    {
        $escolas  = Escola::all();
        $roles    = Role::all();
        $usuarios = Usuario::with('roles','escola')->get();

        return view('master.index', compact('escolas','roles','usuarios'));
    }

    // Criar nova escola
    public function storeEscola(Request $request)
    {
        $request->validate([
            'nome_e' => 'required|string|max:150',
        ]);

        Escola::create($request->all());

        return redirect()->route('master.index')->with('success','Escola criada com sucesso!');
    }

    // Criar nova role
    public function storeRole(Request $request)
    {
        $request->validate([
            'role_name' => 'required|string|max:20|unique:syrios_role,role_name',
        ]);

        Role::create($request->all());

        return redirect()->route('master.index')->with('success','Role criada com sucesso!');
    }

    // Criar novo usuário
    public function storeUsuario(Request $request)
    {
        $request->validate([
            'nome_u' => 'required|string|max:100',
            'cpf'    => 'required|string|max:11',
            'senha'  => 'required|string|min:4',
            'school_id' => 'required|exists:syrios_escola,id'
        ]);

        $usuario = Usuario::create([
            'school_id'  => $request->school_id,
            'cpf'        => $request->cpf,
            'senha_hash' => Hash::make($request->senha),
            'nome_u'     => $request->nome_u,
            'status'     => 1,
        ]);

        if ($request->roles) {
            $usuario->roles()->attach($request->roles, ['school_id' => $request->school_id]);
        }

        return redirect()->route('master.index')->with('success','Usuário criado com sucesso!');
    }

    // Excluir escola
    public function destroyEscola($id)
    {
        $escola = Escola::findOrFail($id);

        // 🔒 regra:Impede exclusão da escola principal
        if ($escola->is_master) {
            return redirect()->back()->with('error', 'A escola principal não pode ser excluída.');
        }

        $escola->delete();

        return redirect()->route('master.index')
            ->with('success', 'Escola excluída com sucesso!');
    }

    // // Excluir escola
    // public function destroyEscola($id)
    // {
    //     Escola::findOrFail($id)->delete();
    //     return redirect()->route('master.index')->with('success','Escola excluída!');
    // }

    public function destroyUsuario($id)
    {
        $usuario = Usuario::findOrFail($id);

        // 🔒 regra:Impede exclusão do super master
        if ($usuario->is_super_master) {
            return redirect()->back()->with('error', 'O usuário master principal não pode ser excluído.');
        }

        // 🔒 regra:Impede exclusão de usuários vinculados à escola principal (is_master = true)
        if ($usuario->escola && $usuario->escola->is_master) {
            return redirect()->back()->with('error', 'Usuários da escola principal não podem ser excluídos.');
        }

        // Remove vínculos de roles
        $usuario->roles()->detach();

        // Exclui usuário
        $usuario->delete();

        return redirect()->route('master.index')
            ->with('success', 'Usuário excluído com sucesso!');
    }


}
