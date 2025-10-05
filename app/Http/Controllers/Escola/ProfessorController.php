<?php

namespace App\Http\Controllers\Escola;

use App\Http\Controllers\Controller;
use App\Models\Professor;
use App\Models\Usuario;
use App\Models\Role;
use Illuminate\Http\Request;

class ProfessorController extends Controller
{
    public function index()
    {
        $schoolId = session('current_school_id');

        // 🔹 Role professor
        $roleProfessorId = Role::where('role_name', 'professor')->first()->id;

        // 🔹 Busca todos os usuários com role professor nesta escola
        $usuariosComRole = Usuario::whereHas('roles', function($q) use ($roleProfessorId, $schoolId) {
            $q->where('syrios_usuario_role.role_id', $roleProfessorId)
              ->where('syrios_usuario_role.school_id', $schoolId);
        })->get();


        // 🔹 Conta quantos estavam faltando
        $sincronizados = 0;

        foreach ($usuariosComRole as $usuario) {
            $criado = Professor::firstOrCreate([
                'usuario_id' => $usuario->id,
                'school_id'  => $schoolId
            ]);

            if ($criado->wasRecentlyCreated) {
                $sincronizados++;
            }
        }

        // 🔹 Carrega professores com suas escolas de origem
        $professores = Professor::with(['usuario.escola'])
            ->where('school_id', $schoolId)
            ->get();

        $mensagem = $sincronizados > 0 
            ? "Sincronização automática: $sincronizados professor(es) adicionados."
            : null;

        return view('escola.professores.index', compact('professores', 'mensagem'));
    }

    public function create()
    {
        return view('escola.professores.create');
    }

    public function store(Request $request)
    {
        $schoolId = session('current_school_id');

        $request->validate([
            'usuario_id' => 'required|integer',
        ]);

        Professor::create([
            'usuario_id' => $request->usuario_id,
            'school_id'  => $schoolId,
        ]);

        return redirect()->route('escola.professores.index')->with('success', 'Professor criado!');
    }

    public function edit(Professor $professor)
    {
        return view('escola.professores.edit', compact('professor'));
    }

    public function update(Request $request, Professor $professor)
    {
        $request->validate([
            'usuario_id' => 'required|integer',
        ]);

        $professor->update($request->only('usuario_id'));

        return redirect()->route('escola.professores.index')->with('success', 'Professor atualizado!');
    }

    public function destroy($id)
    {
        $schoolId = session('current_school_id');
        $professor = Professor::where('school_id', $schoolId)->findOrFail($id);
        $professor->delete();

        return redirect()->route('escola.professores.index')->with('success','Professor removido!');
    }
}




/*
namespace App\Http\Controllers\Escola;

use App\Http\Controllers\Controller;
use App\Models\Professor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfessorController extends Controller
{
    public function index()
    {
        $schoolId = session('current_school_id');
        //$professores = Professor::where('school_id', $schoolId)->with('usuario')->get();

        //Assim, cada $p trará automaticamente o usuário e a escola de origem ($p->usuario->escola).
        $professores = Professor::with(['usuario.escola'])
        ->where('school_id', $schoolId)
        ->get();

        return view('escola.professores.index', compact('professores'));
    }

    // public function index()
    // {
    //     $escola = Auth::user()->escola;
    //     $professores = Professor::where('school_id', $escola->id)->get();

    //     return view('escola.professores.index', compact('professores', 'escola'));
    // }

    public function create()
    {
        return view('escola.professores.create');
    }

    public function store(Request $request)
    {
        $schoolId = session('current_school_id');
        //$escola = Auth::user()->escola;

        $request->validate([
            'usuario_id' => 'required|integer',
        ]);

        Professor::create([
            'usuario_id' => $request->usuario_id,
            'school_id'  => $schoolId,
        ]);

        return redirect()->route('escola.professores.index')->with('success', 'Professor criado!');
    }

    public function edit(Professor $professor)
    {
        return view('escola.professores.edit', compact('professor'));
    }

    public function update(Request $request, Professor $professor)
    {
        $request->validate([
            'usuario_id' => 'required|integer',
        ]);

        $professor->update($request->only('usuario_id'));

        return redirect()->route('escola.professores.index')->with('success', 'Professor atualizado!');
    }

    public function destroy($id)
    {
        $schoolId = session('current_school_id');
        $professor = Professor::where('school_id', $schoolId)->findOrFail($id);
        $professor->delete();

        return redirect()->route('escola.professores.index')->with('success','Professor removido!');
    }
    
    // public function destroy(Professor $professor)
    // {
    //     $professor->delete();
    //     return redirect()->route('escola.professores.index')->with('success', 'Professor excluído!');
    // }
}
*/