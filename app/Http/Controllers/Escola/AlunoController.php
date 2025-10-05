<?php

namespace App\Http\Controllers\Escola;

use App\Http\Controllers\Controller;
use App\Models\Aluno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlunoController extends Controller
{
    public function index()
    {
        //$escola = Auth::user()->escola;
        //$alunos = Aluno::where('school_id', $escola->id)->get();
        $schoolId = session('current_school_id');
        $alunos = Aluno::where('school_id', $schoolId)->get();

        return view('escola.alunos.index', compact('alunos'));
        //return view('escola.alunos.index', compact('alunos', 'escola'));
    }

    public function create()
    {
        return view('escola.alunos.create');
    }

    public function store(Request $request)
    {
        //$escola = Auth::user()->escola;
        $schoolId = session('current_school_id');

        $request->validate([
            'nome_a'    => 'required|string|max:100',
            'matricula' => 'required|string|max:10',
        ]);

        Aluno::create([
            'nome_a'    => $request->nome_a,
            'matricula' => $request->matricula,
            'school_id' => $schoolId,
        ]);

        return redirect()->route('escola.alunos.index')->with('success', 'Aluno cadastrado com sucesso!');
    }

    public function edit($id)
    {
        $schoolId = session('current_school_id');
        $aluno = Aluno::where('school_id', $schoolId)->findOrFail($id);

        return view('escola.alunos.edit', compact('aluno'));
    }

    // public function edit(Aluno $aluno)
    // {
    //     return view('escola.alunos.edit', compact('aluno'));
    // }

    public function update(Request $request, $id)
    {
        $schoolId = session('current_school_id');
        $aluno = Aluno::where('school_id', $schoolId)->findOrFail($id);

        $request->validate([
            'matricula' => 'required|string|max:10',
            'nome_a'    => 'required|string|max:100',
        ]);

        $aluno->update($request->only(['matricula','nome_a']));

        return redirect()->route('escola.alunos.index')->with('success','Aluno atualizado com sucesso!');
    }

    // public function update(Request $request, Aluno $aluno)
    // {
    //     $request->validate([
    //         'nome_a'    => 'required|string|max:100',
    //         'matricula' => 'required|string|max:10',
    //     ]);

    //     $aluno->update($request->only('nome_a','matricula'));

    //     return redirect()->route('escola.alunos.index')->with('success', 'Aluno atualizado!');
    // }

    // public function destroy(Aluno $aluno)
    // {
    //     $aluno->delete();
    //     return redirect()->route('escola.alunos.index')->with('success', 'Aluno excluído!');
    // }

    public function destroy($id)
    {
        $schoolId = session('current_school_id');
        $aluno = Aluno::where('school_id', $schoolId)->findOrFail($id);
        $aluno->delete();

        return redirect()->route('escola.alunos.index')->with('success','Aluno removido!');
    }
}
