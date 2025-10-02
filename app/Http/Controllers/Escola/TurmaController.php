<?php

namespace App\Http\Controllers\Escola;

use App\Http\Controllers\Controller;
use App\Models\Turma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TurmaController extends Controller
{
    public function index()
    {
        $escola = Auth::user()->escola;
        $turmas = Turma::where('school_id', $escola->id)->get();

        return view('escola.turmas.index', compact('turmas', 'escola'));
    }

    public function create()
    {
        return view('escola.turmas.create');
    }

    public function store(Request $request)
    {
        $escola = Auth::user()->escola;

        $request->validate([
            'serie_turma' => 'required|string|max:20',
            'turno'       => 'required|string|max:20',
        ]);

        Turma::create([
            'serie_turma' => $request->serie_turma,
            'turno'       => $request->turno,
            'school_id'   => $escola->id,
        ]);

        return redirect()->route('escola.turmas.index')->with('success', 'Turma criada!');
    }

    public function edit(Turma $turma)
    {
        return view('escola.turmas.edit', compact('turma'));
    }

    public function update(Request $request, Turma $turma)
    {
        $request->validate([
            'serie_turma' => 'required|string|max:20',
            'turno'       => 'required|string|max:20',
        ]);

        $turma->update($request->only('serie_turma','turno'));

        return redirect()->route('escola.turmas.index')->with('success', 'Turma atualizada!');
    }

    public function destroy(Turma $turma)
    {
        $turma->delete();
        return redirect()->route('escola.turmas.index')->with('success', 'Turma excluída!');
    }
}
