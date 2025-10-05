<?php

namespace App\Http\Controllers\Secretaria;

use App\Http\Controllers\Controller;
use App\Models\Escola;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class EscolaController extends Controller
{
    
    public function index()
    {
        // obtém ID da escola atual da sessão
        $currentSchoolId = session('current_school_id');

        // verifica se há uma escola em contexto
        if (!$currentSchoolId) {
            return redirect()->route('home')->with('error', 'Nenhuma escola selecionada no momento.');
        }

        // busca a escola atual no banco
        $secretaria = Escola::find($currentSchoolId);

        // se não existir (por exemplo, ID inválido)
        if (!$secretaria) {
            return redirect()->route('home')->with('error', 'Escola atual não encontrada.');
        }

        // filhas da secretaria atual
        $escolas = $secretaria->filhas()->get();

        return view('secretaria.escolas.index', compact('escolas', 'secretaria'));
    }


    /*public function index()
    {
        // secretaria logada
        $secretaria = auth()->user()->escola;

        // se não tiver secretaria vinculada
        if (!$secretaria) {
            return redirect()->route('home')->with('error', 'Nenhuma secretaria vinculada a este usuário.');
        }

        // filhas da secretaria logada
        $escolas = $secretaria->filhas()->get();

        return view('secretaria.escolas.index', compact('escolas','secretaria'));
    }*/

    /*public function index()
    {
        $secretaria = Auth::user()->escola;

        // só filhas da secretaria logada
        $escolas = Escola::where('secretaria_id', $secretaria->id)->get();

        return view('secretaria.escolas.index', compact('secretaria','escolas'));
    }*/

    public function create()
    {
        return view('secretaria.escolas.create');
    }

    public function store(Request $request)
    {
        $secretaria = Auth::user()->escola;

        Escola::create([
            'nome_e' => $request->nome_e,
            'inep'   => $request->inep,
            'cnpj'   => $request->cnpj,
            'secretaria_id' => $secretaria->id,
        ]);

        return redirect()->route('secretaria.escolas.index')->with('success','Escola criada');
    }

    public function edit(Escola $escola)
    {
        return view('secretaria.escolas.edit', compact('escola'));
    }

    public function update(Request $request, Escola $escola)
    {
        $escola->update($request->only('nome_e','inep','cnpj'));
        return redirect()->route('secretaria.escolas.index')->with('success','Escola atualizada');
    }

    public function destroy(Escola $escola)
    {
        // 🔒 Impede exclusão da escola principal
        if ($escola->is_master) {
            return redirect()->back()->with('error', 'A escola principal não pode ser excluída.');
        }

        // Exclui a escola
        $escola->delete();

        return redirect()->route('secretaria.escolas.index')
            ->with('success', 'Escola excluída com sucesso!');
    }


    // public function destroy(Escola $escola)
    // {
    //     $escola->delete();
    //     return redirect()->route('secretaria.escolas.index')->with('success','Escola excluída');
    // }
}
