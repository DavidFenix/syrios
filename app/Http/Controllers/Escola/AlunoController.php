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
        $escola = Auth::user()->escola;
        $alunos = Aluno::where('school_id', $escola->id)->get();

        return view('escola.alunos.index', compact('alunos', 'escola'));
    }

    public function create()
    {
        return view('escola.alunos.create');
    }

    public function store(Request $request)
    {
        $escola = Auth::user()->escola;

        $request->validate([
            'nome_a'    => 'required|string|max:100',
            'matricula' => 'required|string|max:10',
        ]);

        Aluno::create([
            'nome_a'    => $request->nome_a,
            'matricula' => $request->matricula,
            'school_id' => $escola->id,
        ]);

        return redirect()->route('escola.alunos.index')->with('success', 'Aluno criado!');
    }

    public function edit(Aluno $aluno)
    {
        return view('escola.alunos.edit', compact('aluno'));
    }

    public function update(Request $request, Aluno $aluno)
    {
        $request->validate([
            'nome_a'    => 'required|string|max:100',
            'matricula' => 'required|string|max:10',
        ]);

        $aluno->update($request->only('nome_a','matricula'));

        return redirect()->route('escola.alunos.index')->with('success', 'Aluno atualizado!');
    }

    public function destroy(Aluno $aluno)
    {
        $aluno->delete();
        return redirect()->route('escola.alunos.index')->with('success', 'Aluno excluído!');
    }
}
