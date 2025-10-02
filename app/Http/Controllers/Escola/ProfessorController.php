<?php

namespace App\Http\Controllers\Escola;

use App\Http\Controllers\Controller;
use App\Models\Professor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfessorController extends Controller
{
    public function index()
    {
        $escola = Auth::user()->escola;
        $professores = Professor::where('school_id', $escola->id)->get();

        return view('escola.professores.index', compact('professores', 'escola'));
    }

    public function create()
    {
        return view('escola.professores.create');
    }

    public function store(Request $request)
    {
        $escola = Auth::user()->escola;

        $request->validate([
            'usuario_id' => 'required|integer',
        ]);

        Professor::create([
            'usuario_id' => $request->usuario_id,
            'school_id'  => $escola->id,
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

    public function destroy(Professor $professor)
    {
        $professor->delete();
        return redirect()->route('escola.professores.index')->with('success', 'Professor excluído!');
    }
}
