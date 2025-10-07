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

        //$escolas = $query->with('mae')->orderBy('nome_e')->get();
        
        $escolas = Escola::with('mae')->filtrar($filtro)->get();
        
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
        
        $auth = auth()->user();

        // 🔒 Proteção 1: regra:impede que qualquer usuário não super master edite a escola master
        if ($escola->is_master && !$auth->is_super_master) {
            return redirect()
                ->route('master.escolas.index')
                ->with('error', 'Apenas o Super Master pode editar a escola principal.');
        }

        // 🧩 regra:Bloqueia edição da escola master por não-super_master
        // if ($escola->is_master) {
        //     $usuario = auth()->user();

        //     if (!$usuario || !$usuario->is_super_master) {
        //         return redirect()
        //             ->route('master.escolas.index')
        //             ->with('error', 'A escola principal só pode ser editada pelo Super Master.');
        //     }
        // }

        $maes = Escola::whereNull('secretaria_id')
            ->where('id', '<>', $escola->id)
            ->orderBy('nome_e')->get();

        return view('master.escolas.edit', compact('escola', 'maes'));
    }

    public function update(Request $request, Escola $escola)
    {
        
        $auth = auth()->user();

        // 🔒 regra:Bloqueia alteração na escola master, exceto pelo Super Master
        if ($escola->is_master && !$auth->is_super_master) {
            return redirect()
                ->route('master.escolas.index')
                ->with('error', 'Apenas o Super Master pode atualizar a escola principal.');
        }

        // 🧩 regra:Bloqueia atualização da escola master por não-super_master
        // if ($escola->is_master) {
        //     $usuario = auth()->user();

        //     if (!$usuario || !$usuario->is_super_master) {
        //         return redirect()
        //             ->route('master.escolas.index')
        //             ->with('error', 'A escola principal só pode ser alterada pelo Super Master.');
        //     }
        // }

        //regra:validar os dados
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

        //regra:secretaria_id não pode ser igual ao escola.id
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
        $auth = auth()->user();

        // 🔒 Impede excluir a escola master (qualquer usuário)
        if ($escola->is_master) {
            return redirect()
                ->route('master.escolas.index')
                ->with('error', 'A escola principal não pode ser excluída.');
        }
        
        // regra:DELETE SEGURO; evita quebrar FKs
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

        //regra:não excluir escola quando possui vínculos
        if (!empty($bloqs)) {
            $lista = [];
            foreach ($bloqs as $tabela => $qtd) {
                $lista[] = "$tabela: $qtd";
            }
            return redirect()->route('master.escolas.index')
                ->with('error', 'Não é possível excluir. Existem vínculos → '.implode(', ', $lista));
        }

        // 🔒 regra:Impede excluir a escola master
        if ($escola->is_master) {
            return redirect()
                ->route('master.escolas.index')
                ->with('error', 'A escola master não pode ser excluída.');
        }

        $escola->delete();

        return redirect()
            ->route('master.escolas.index')
            ->with('success', 'Escola excluída!');

    }

    public function associarFilha(Request $request)
    {
        $request->validate([
            'mae_id' => 'required|exists:syrios_escola,id',
            'filha_id' => 'required|exists:syrios_escola,id',
        ]);

        $filha = Escola::findOrFail($request->filha_id);
        $filha->secretaria_id = $request->mae_id;
        $filha->save();

        return redirect()->route('master.escolas.associacoes')
                         ->with('success', 'Escola filha associada com sucesso!');
    }

    public function associacoes()
    {
        // escolas mãe = secretaria_id NULL
        $escolasMae = Escola::whereNull('secretaria_id')->get();

        // pega o ID da mãe selecionada (se houver na URL ?mae_id=)
        $maeSelecionada = request('mae_id');

        $escolasFilhas = collect();
        $nomeMae = null;

        if ($maeSelecionada) {
            $mae = Escola::find($maeSelecionada);
            if ($mae) {
                $nomeMae = $mae->nome_e;
                $escolasFilhas = $mae->filhas; // usa o relacionamento
            }
        }

        return view('master.escolas.associacoes', compact(
            'escolasMae',
            'maeSelecionada',
            'escolasFilhas',
            'nomeMae'
        ));
    }

    //passo 2: esta função foi chamada pela rota ../master/escolas-associacoes2
    //ao terminar vai retornar compact(dados) para a view /master/escolas/associacoes2.blade.php
    public function associacoes2()
    {
        // escolas mãe = secretaria_id NULL
        $escolasMae = Escola::whereNull('secretaria_id')->get();

        // pega o ID da mãe selecionada (se houver na URL ?mae_id=)
        $maeSelecionada = request('mae_id');

        $escolasFilhas = collect();
        $nomeMae = null;

        if ($maeSelecionada) {
            $mae = Escola::find($maeSelecionada);
            if ($mae) {
                $nomeMae = $mae->nome_e;
                $escolasFilhas = $mae->filhas; // usa o relacionamento
            }
        }

        //os resultados em compact vai para a view master/escolas/associacoes2.php
        return view('master.escolas.associacoes2', compact('escolasMae', 'maeSelecionada', 'escolasFilhas', 'nomeMae'));
    }


}
