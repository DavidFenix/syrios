<?php

namespace App\Http\Controllers\Escola;

use App\Http\Controllers\Controller;
use App\Models\Disciplina;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisciplinaController extends Controller
{
    public function index()
    {
        $escola = Auth::user()->escola;
        $disciplinas = Disciplina::where('school_id', $escola->id)->get();

        return view('escola.disciplinas.index', compact('disciplinas', 'escola'));
    }

    public function create()
    {
        return view('escola.disciplinas.create');
    }

    public function store(Request $request)
    {
        $escola = Auth::user()->escola;

        $request->validate([
            'abr'     => 'required|string|max:10',
            'descr_d' => 'required|string|max:100',
        ]);

        Disciplina::create([
            'abr'       => $request->abr,
            'descr_d'   => $request->descr_d,
            'school_id' => $escola->id,
        ]);

        return redirect()->route('escola.disciplinas.index')->with('success', 'Disciplina criada!');
    }

    public function edit(Disciplina $disciplina)
    {
        return view('escola.disciplinas.edit', compact('disciplina'));
    }

    public function update(Request $request, Disciplina $disciplina)
    {
        $request->validate([
            'abr'     => 'required|string|max:10',
            'descr_d' => 'required|string|max:100',
        ]);

        $disciplina->update($request->only('abr','descr_d'));

        return redirect()->route('escola.disciplinas.index')->with('success', 'Disciplina atualizada!');
    }

    public function destroy(Disciplina $disciplina)
    {
        $disciplina->delete();
        return redirect()->route('escola.disciplinas.index')->with('success', 'Disciplina excluída!');
    }
}
