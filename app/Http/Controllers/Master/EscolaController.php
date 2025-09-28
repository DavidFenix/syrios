<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Escola;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EscolaController extends Controller
{
    public function index(Request $request)
    {
        $filtro = $request->get('tipo'); // 'mae', 'filha', ou null
        $query  = Escola::query();

        if ($filtro === 'mae') {
            $query->whereNull('secretaria_id');
        } elseif ($filtro === 'filha') {
            $query->whereNotNull('secretaria_id');
        }

        $escolas = $query->with('mae')->orderBy('nome_e')->get();
        $maes    = Escola::whereNull('secretaria_id')->orderBy('nome_e')->get();

        return view('master.escolas.index', compact('escolas', 'maes', 'filtro'));
    }

    public function create()
    {
        $maes = Escola::whereNull('secretaria_id')->orderBy('nome_e')->get();
        return view('master.escolas.create', compact('maes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome_e'       => 'required|string|max:150',
            'inep'         => 'nullable|string|max:20',
            'cnpj'         => 'nullable|string|max:20',
            'cidade'       => 'nullable|string|max:100',
            'estado'       => 'nullable|string|max:100',
            'endereco'     => 'nullable|string|max:255',
            'telefone'     => 'nullable|string|max:20',
            'secretaria_id'=> 'nullable|integer|exists:syrios_escola,id',
        ]);

        Escola::create($data);
        return redirect()->route('master.escolas.index')
            ->with('success', 'Instituição criada!');
    }

    public function edit(Escola $escola)
    {
        $maes = Escola::whereNull('secretaria_id')
            ->where('id', '<>', $escola->id)
            ->orderBy('nome_e')->get();

        return view('master.escolas.edit', compact('escola', 'maes'));
    }

    public function update(Request $request, Escola $escola)
    {
        $data = $request->validate([
            'nome_e'       => 'required|string|max:150',
            'inep'         => 'nullable|string|max:20',
            'cnpj'         => 'nullable|string|max:20',
            'cidade'       => 'nullable|string|max:100',
            'estado'       => 'nullable|string|max:100',
            'endereco'     => 'nullable|string|max:255',
            'telefone'     => 'nullable|string|max:20',
            'secretaria_id'=> 'nullable|integer|exists:syrios_escola,id',
        ]);

        if (isset($data['secretaria_id']) && (int)$data['secretaria_id'] === (int)$escola->id) {
            return back()->withErrors(['secretaria_id' => 'Uma escola não pode ser sua própria secretaria.'])
                         ->withInput();
        }

        $escola->update($data);
        return redirect()->route('master.escolas.index')
            ->with('success', 'Instituição atualizada!');
    }

    public function destroy(Escola $escola)
    {
        // DELETE SEGURO: evita quebrar FKs
        $deps = [
            'usuarios'      => DB::table('syrios_usuario')->where('school_id', $escola->id)->count(),
            'professores'   => DB::table('syrios_professor')->where('school_id', $escola->id)->count(),
            'alunos'        => DB::table('syrios_aluno')->where('school_id', $escola->id)->count(),
            'turmas'        => DB::table('syrios_turma')->where('school_id', $escola->id)->count(),
            'disciplinas'   => DB::table('syrios_disciplina')->where('school_id', $escola->id)->count(),
            'ofertas'       => DB::table('syrios_oferta')->where('school_id', $escola->id)->count(),
            'registros'     => DB::table('syrios_registros')->where('school_id', $escola->id)->count(),
            'enturmacao'    => DB::table('syrios_enturmacao')->where('school_id', $escola->id)->count(),
            'notificacao'   => DB::table('syrios_notificacao')->where('school_id', $escola->id)->count(),
            'sessao'        => DB::table('syrios_sessao')->where('school_id', $escola->id)->count(),
            'visao_aluno'   => DB::table('syrios_visao_aluno')->where('school_id', $escola->id)->count(),
            'filhas'        => DB::table('syrios_escola')->where('secretaria_id', $escola->id)->count(),
        ];

        $bloqs = array_filter($deps, function ($c) { return $c > 0; });

        if (!empty($bloqs)) {
            $lista = [];
            foreach ($bloqs as $tabela => $qtd) {
                $lista[] = "$tabela: $qtd";
            }
            return redirect()->route('master.escolas.index')
                ->with('error', 'Não é possível excluir. Existem vínculos → '.implode(', ', $lista));
        }

        $escola->delete();
        return redirect()->route('master.escolas.index')->with('success', 'Escola excluída!');
    }
}
