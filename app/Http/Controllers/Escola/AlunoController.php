<?php

namespace App\Http\Controllers\Escola;

use App\Http\Controllers\Controller;
use App\Models\Aluno;
use App\Models\Enturmacao;
use App\Models\Turma;
use App\Models\Ocorrencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class AlunoController extends Controller
{
    
    
    // public function index()
        // {
        //     $schoolId = session('current_school_id');

        //     // Carrega alunos com suas turmas e escolas
        //     $alunos = Aluno::with(['turma', 'escola'])
        //         ->where('school_id', $schoolId)
        //         ->orderBy('nome_a')
        //         ->get();

        //     return view('escola.alunos.index', compact('alunos'));
        // }

    public function index()
    {
        $schoolId = session('current_school_id');

        // Alunos nativos da escola
        $nativos = Aluno::where('school_id', $schoolId)->pluck('id')->toArray();

        // Alunos vinculados via enturmação
        $vinculados = Enturmacao::where('school_id', $schoolId)
            ->pluck('aluno_id')
            ->toArray();

        // Combina ambos os grupos (sem duplicar)
        $ids = array_unique(array_merge($nativos, $vinculados));

        // Carrega todos os alunos correspondentes
        $alunos = Aluno::whereIn('id', $ids)
            ->orderBy('nome_a')
            ->get();

        return view('escola.alunos.index', compact('alunos'));
    }

    public function create()
    {
        $schoolId = session('current_school_id');

        if (!$schoolId) {
            return redirect()
                ->route('escola.dashboard')
                ->with('warning', '⚠️ Nenhuma escola selecionada no contexto atual.');
        }

        // Apenas turmas da escola atual
        $turmas = Turma::where('school_id', $schoolId)
            ->orderBy('serie_turma')
            ->get(['id', 'serie_turma', 'turno']);

        return view('escola.alunos.create', compact('turmas'));
    }

    // public function store(Request $request)
        // {
        //     $schoolId = session('current_school_id');

        //     $request->validate([
        //         'nome_a'   => 'required|string|max:100',
        //         'matricula'=> 'required|string|max:10',
        //         'turma_id' => 'nullable|integer|exists:'.prefix().'turma,id'
        //     ]);

        //     // 🔍 Verifica se já existe aluno com essa matrícula (em qualquer escola)
        //     $alunoExistente = Aluno::where('matricula', $request->matricula)->first();

        //     if ($alunoExistente) {
        //         // Se já existe e pertence a esta escola → apenas alerta
        //         if ($alunoExistente->school_id == $schoolId) {
        //             return redirect()->back()->with('warning', '⚠️ Este aluno já está cadastrado nesta escola.');
        //         }

        //         // Caso contrário → oferece opção de vincular à escola atual
        //         return redirect()
        //             ->route('escola.alunos.create')
        //             ->withInput()
        //             ->with([
        //                 'warning' => '⚠️ Aluno já existe. Você pode vinculá-lo à escola atual.',
        //                 'aluno_existente' => $alunoExistente->id
        //             ]);

        //     }

        //     // 👶 Cria novo aluno nesta escola
        //     $aluno = Aluno::create([
        //         'matricula' => $request->matricula,
        //         'school_id' => $schoolId,
        //         'nome_a'    => $request->nome_a,
        //     ]);

        //     // 🏫 Cria enturmação se houver turma
        //     if (!empty($request->turma_id)) {
        //         Enturmacao::firstOrCreate([
        //             'school_id' => $schoolId,
        //             'aluno_id'  => $aluno->id,
        //             'turma_id'  => $request->turma_id,
        //         ]);
        //     }

        //     return redirect()->route('escola.alunos.index')
        //         ->with('success', 'Aluno criado com sucesso!');
        // }

    public function store(Request $request)
    {
        
        $schoolId = session('current_school_id');

        $request->validate([
            'nome_a'   => 'required|string|max:100',
            'matricula'=> 'required|string|max:10',
            'turma_id' => 'nullable|integer|exists:' . prefix() . 'turma,id'
        ]);

        // 1️⃣ Verifica se já existe aluno com a mesma matrícula na MESMA escola
        $duplicado = Aluno::where('matricula', $request->matricula)
            ->where('school_id', $schoolId)
            ->exists();

        if ($duplicado) {
            return redirect()
                ->route('escola.alunos.index')
                ->with('warning', '🚫 Já existe um aluno com esta matrícula nesta escola.');
        }

        // 2️⃣ Verifica se matrícula já existe em OUTRA escola
        $alunoExistente = Aluno::where('matricula', $request->matricula)->first();

        if ($alunoExistente) {
            // Já existe em outra escola → oferece vínculo
            return redirect()
                ->route('escola.alunos.create')
                ->withInput()
                ->with([
                    'warning' => '⚠️ Aluno já existe em outra escola. Você pode vinculá-lo à escola atual.',
                    'aluno_existente' => $alunoExistente->id
                ]);
        }

        // 3️⃣ Cria novo aluno (não existe em lugar nenhum)
        $aluno = Aluno::create([
            'matricula' => $request->matricula,
            'school_id' => $schoolId,
            'nome_a'    => $request->nome_a,
        ]);

        // Enturma se selecionou turma
        if (!empty($request->turma_id)) {
            Enturmacao::firstOrCreate([
                'school_id' => $schoolId,
                'aluno_id'  => $aluno->id,
                'turma_id'  => $request->turma_id,
            ]);
        }

        return redirect()
            ->route('escola.alunos.index')
            ->with('success', '✅ Aluno criado com sucesso.');
    }

    public function vincular(Request $request, Aluno $aluno)
    {
        $schoolId = session('current_school_id');

        $request->validate([
            'turma_id' => 'nullable|integer|exists:' . prefix() . 'turma,id'
        ]);

        // 1️⃣ Confere se já há aluno com a mesma matrícula nesta escola
        $matriculaDuplicada = Aluno::where('matricula', $aluno->matricula)
            ->where('school_id', $schoolId)
            ->exists();

        if ($matriculaDuplicada) {
            return redirect()
                ->route('escola.alunos.index')
                ->with('warning', '🚫 Não é possível vincular. Já existe um aluno com a matrícula '
                    . $aluno->matricula . ' nesta escola.');
        }

        // 2️⃣ Verifica se já está vinculado
        $jaVinculado = Enturmacao::where('school_id', $schoolId)
            ->where('aluno_id', $aluno->id)
            ->exists();

        if ($jaVinculado) {
            return redirect()
                ->route('escola.alunos.index')
                ->with('warning', '⚠️ Este aluno já está vinculado a esta escola.');
        }

        // 3️⃣ Cria o vínculo (enturmação)
        Enturmacao::create([
            'school_id' => $schoolId,
            'aluno_id'  => $aluno->id,
            'turma_id'  => $request->turma_id ?? null,
        ]);

        return redirect()
            ->route('escola.alunos.index')
            ->with('success', '✅ Aluno vinculado à escola com sucesso!');
    }


    // public function vincular(Request $request, Aluno $aluno)
        // {
        //     $schoolId = session('current_school_id');

        //     $request->validate([
        //         'turma_id' => 'nullable|integer|exists:'.prefix().'turma,id'
        //     ]);

        //     // 🔍 Garante que o vínculo não exista
        //     $jaExiste = Enturmacao::where('school_id', $schoolId)
        //         ->where('aluno_id', $aluno->id)
        //         ->exists();

        //     if ($jaExiste) {
        //         return redirect()->route('escola.alunos.index')
        //             ->with('warning', '⚠️ Este aluno já está vinculado a esta escola.');
        //     }

        //     // 🔗 Cria enturmação (ou vínculo “sem turma”)
        //     Enturmacao::create([
        //         'school_id' => $schoolId,
        //         'aluno_id'  => $aluno->id,
        //         'turma_id'  => $request->turma_id ?? 0,
        //     ]);

        //     return redirect()->route('escola.alunos.index')
        //         ->with('success', 'Aluno vinculado à escola com sucesso!');
        // }

    // public function store(Request $request)
        // {
        //     $validated = $request->validate([
        //         'nome_a' => 'required|string|max:100',
        //         'matricula' => 'required|string|max:10',
        //         'turma_id' => 'nullable|integer|exists:' . prefix() . 'turma,id',
        //     ]);

        //     $schoolId = session('current_school_id');

        //     // 🔍 Busca aluno existente pela matrícula (única por escola)
        //     $aluno = \App\Models\Aluno::where('matricula', $validated['matricula'])
        //         ->where('school_id', $schoolId)
        //         ->first();

        //     if (!$aluno) {
        //         // 🆕 Cria aluno novo
        //         $aluno = \App\Models\Aluno::create([
        //             'matricula' => $validated['matricula'],
        //             'school_id' => $schoolId,
        //             'nome_a' => $validated['nome_a'],
        //         ]);
        //     }

        //     // 🏫 Se foi escolhida uma turma → cria enturmação
        //     if (!empty($validated['turma_id'])) {
        //         \App\Models\Enturmacao::firstOrCreate([
        //             'school_id' => $schoolId,
        //             'aluno_id' => $aluno->id,
        //             'turma_id' => $validated['turma_id'],
        //         ]);
        //     }

        //     return redirect()->route('escola.alunos.index')
        //         ->with('success', 'Aluno salvo e vinculado com sucesso.');
        // }

        // public function store(Request $request)
        // {
        //     //$escola = Auth::user()->escola;
        //     $schoolId = session('current_school_id');

        //     $request->validate([
        //         'nome_a'    => 'required|string|max:100',
        //         'matricula' => 'required|string|max:10',
        //     ]);

        //     Aluno::create([
        //         'nome_a'    => $request->nome_a,
        //         'matricula' => $request->matricula,
        //         'school_id' => $schoolId,
        //     ]);

        //     return redirect()->route('escola.alunos.index')->with('success', 'Aluno cadastrado com sucesso!');
        // }
    
    public function edit($id)
    {
        $schoolId = session('current_school_id');

        $aluno = Aluno::where(function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId)
                  ->orWhereHas('enturmacao', function ($q2) use ($schoolId) {
                      $q2->where('school_id', $schoolId);
                  });
            })
            ->with(['enturmacao.turma'])
            ->where('id', $id)
            ->firstOrFail();

        $isNativo = $aluno->school_id == $schoolId;

        $turmas = Turma::where('school_id', $schoolId)
            ->orderBy('serie_turma')
            ->get(['id', 'serie_turma', 'turno']);

        return view('escola.alunos.edit', compact('aluno', 'turmas', 'isNativo'));
    }


    // public function edit($id)
        // {
        //     $schoolId = session('current_school_id');

        //     // Busca o aluno, seja nativo ou vinculado via enturmacao
        //     $aluno = Aluno::where('school_id', $schoolId)
        //         ->orWhereHas('enturmacao', function ($q) use ($schoolId) {
        //             $q->where('school_id', $schoolId);
        //         })
        //         ->with(['enturmacao.turma'])
        //         ->findOrFail($id);

        //     // Verifica tipo de vínculo
        //     $isNativo = $aluno->school_id == $schoolId;

        //     // Lista de turmas disponíveis da escola atual
        //     $turmas = Turma::where('school_id', $schoolId)
        //         ->orderBy('serie_turma')
        //         ->get(['id', 'serie_turma', 'turno']);

        //     return view('escola.alunos.edit', compact('aluno', 'turmas', 'isNativo'));
        // }


    // public function edit($id)
        // {
        //     $schoolId = session('current_school_id');
        //     $aluno = Aluno::where('school_id', $schoolId)->findOrFail($id);

        //     return view('escola.alunos.edit', compact('aluno'));
        // }

    // public function edit(Aluno $aluno)
        // {
        //     return view('escola.alunos.edit', compact('aluno'));
        // }

    public function update(Request $request, $id)
    {
        $schoolId = session('current_school_id');
        $aluno = Aluno::findOrFail($id);
        $isNativo = $aluno->school_id == $schoolId;

        $request->validate([
            'nome_a' => 'required|string|max:100',
            'turma_id' => 'nullable|exists:' . prefix() . 'turma,id',
        ]);

        // Atualiza nome apenas se for nativo
        if ($isNativo) {
            $aluno->update(['nome_a' => $request->nome_a]);
        }

        // Atualiza enturmação (ou cria)
        if ($request->filled('turma_id')) {
            Enturmacao::updateOrCreate(
                ['aluno_id' => $aluno->id, 'school_id' => $schoolId],
                ['turma_id' => $request->turma_id]
            );
        } else {
            // Remove enturmação se desmarcar
            Enturmacao::where('aluno_id', $aluno->id)
                ->where('school_id', $schoolId)
                ->delete();
        }

        return redirect()
            ->route('escola.alunos.index')
            ->with('success', '✅ Dados do aluno atualizados com sucesso.');
    }


    // public function update(Request $request, $id)
        // {
        //     $schoolId = session('current_school_id');
        //     $aluno = Aluno::where('school_id', $schoolId)->findOrFail($id);

        //     $request->validate([
        //         'matricula' => 'required|string|max:10',
        //         'nome_a'    => 'required|string|max:100',
        //     ]);

        //     $aluno->update($request->only(['matricula','nome_a']));

        //     return redirect()->route('escola.alunos.index')->with('success','Aluno atualizado com sucesso!');
        // }

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

    //vamos proteger o destroi. como vc pode aplicar as regras que já sabemos?
    // public function destroy($id)
    // {
    //     $schoolId = session('current_school_id');
    //     $aluno = Aluno::where('school_id', $schoolId)->findOrFail($id);
    //     $aluno->delete();

    //     return redirect()->route('escola.alunos.index')->with('success','Aluno removido!');
    // }

    // public function destroy($id)
        // {
        //     $schoolId = session('current_school_id');

        //     // 🔍 Busca aluno (nativo ou vinculado)
        //     $aluno = Aluno::where('school_id', $schoolId)
        //         ->orWhereHas('enturmacao', function ($q) use ($schoolId) {
        //             $q->where('school_id', $schoolId);
        //         })
        //         ->with(['enturmacao', 'ocorrencias'])
        //         ->where('id', $id)
        //         ->firstOrFail();

        //     // 🔒 Proteção 1: aluno de outra escola sem vínculo
        //     $temVinculo = $aluno->enturmacao()->where('school_id', $schoolId)->exists();
        //     if ($aluno->school_id != $schoolId && !$temVinculo) {
        //         return redirect()->route('escola.alunos.index')
        //             ->with('warning', '🚫 Este aluno não pertence nem está vinculado a esta escola.');
        //     }

        //     // 🔒 Proteção 2: aluno com dependências
        //     $temOcorrencias = \App\Models\Ocorrencia::where('aluno_id', $aluno->id)->exists();
        //     if ($temOcorrencias) {
        //         return redirect()->route('escola.alunos.index')
        //             ->with('warning', '⚠️ Não é possível excluir. O aluno possui ocorrências registradas.');
        //     }

        //     $temEnturmacoes = \App\Models\Enturmacao::where('aluno_id', $aluno->id)->count();
        //     if ($temEnturmacoes > 1 || ($temEnturmacoes == 1 && $aluno->school_id != $schoolId)) {
        //         return redirect()->route('escola.alunos.index')
        //             ->with('warning', '⚠️ Este aluno está vinculado a mais de uma escola. Remova o vínculo antes de excluir.');
        //     }

        //     // 🔄 Caso seja vínculo (não nativo)
        //     if ($aluno->school_id != $schoolId && $temVinculo) {
        //         Enturmacao::where('aluno_id', $aluno->id)
        //             ->where('school_id', $schoolId)
        //             ->delete();

        //         return redirect()->route('escola.alunos.index')
        //             ->with('success', '🔗 Vínculo do aluno removido com sucesso.');
        //     }

        //     // 🧹 Caso seja nativo e sem dependências
        //     Enturmacao::where('aluno_id', $aluno->id)->delete();
        //     $aluno->delete();

        //     return redirect()->route('escola.alunos.index')
        //         ->with('success', '✅ Aluno removido com sucesso.');
        // }

    // public function destroy($id)
    // {
       
    //     $schoolId = session('current_school_id');

    //     // 🔍 Busca o aluno (nativo ou vinculado)
    //     $aluno = Aluno::with(['enturmacao', 'ocorrencias'])
    //         ->where(function ($query) use ($schoolId) {
    //             $query->where('school_id', $schoolId)
    //                   ->orWhereHas('enturmacao', function ($sub) use ($schoolId) {
    //                       $sub->where('school_id', $schoolId);
    //                   });
    //         })
    //         ->where('id', $id)
    //         ->firstOrFail();

    //     // 🔒 Proteção 1: aluno de outra escola sem vínculo
    //     $temVinculo = $aluno->enturmacao()->where('school_id', $schoolId)->exists();
    //     if ($aluno->school_id != $schoolId && !$temVinculo) {
    //         return redirect()->route('escola.alunos.index')
    //             ->with('warning', '🚫 Este aluno não pertence nem está vinculado a esta escola.');
    //     }

    //     // 🔒 Proteção 2: aluno com ocorrências
    //     $temOcorrencias = \App\Models\Ocorrencia::where('aluno_id', $aluno->id)->exists();
    //     if ($temOcorrencias) {
    //         return redirect()->route('escola.alunos.index')
    //             ->with('warning', '⚠️ Não é possível excluir. O aluno possui ocorrências registradas.');
    //     }

    //     // 🔒 Proteção 3: aluno com múltiplas enturmações (várias escolas)
    //     $qtdEnturmacoes = \App\Models\Enturmacao::where('aluno_id', $aluno->id)->count();
    //     if ($qtdEnturmacoes > 1 || ($qtdEnturmacoes == 1 && $aluno->school_id != $schoolId)) {
    //         return redirect()->route('escola.alunos.index')
    //             ->with('warning', '⚠️ Este aluno está vinculado a mais de uma escola. Remova o vínculo antes de excluir.');
    //     }

    //     // 🔄 Caso seja apenas vínculo (não nativo)
    //     if ($aluno->school_id != $schoolId && $temVinculo) {

    //         $enturmasRemovidas = Enturmacao::where('aluno_id', $aluno->id)
    //             ->where('school_id', $schoolId)
    //             ->delete();

    //         if ($enturmasRemovidas > 0) {
    //             return redirect()->route('escola.alunos.index')
    //                 ->with('success', '🔗 Vínculo do aluno removido com sucesso.');
    //         } else {
    //             return redirect()->route('escola.alunos.index')
    //                 ->with('warning', '⚠️ Nenhuma enturmação encontrada para remover.');
    //         }
    //     }

    //     // 🧹 Caso seja nativo e sem dependências
    //     $enturmasRemovidas = Enturmacao::where('aluno_id', $aluno->id)
    //         ->where('school_id', $schoolId)
    //         ->delete();

    //     // 🧾 Se não tiver mais vínculos com nenhuma escola, pode apagar o aluno
    //     $restaVinculo = Enturmacao::where('aluno_id', $aluno->id)->exists();

    //     if (!$restaVinculo && $aluno->school_id == $schoolId) {
    //         $aluno->delete();
    //     }

    //     return redirect()->route('escola.alunos.index')
    //         ->with('success', '✅ Aluno removido com sucesso.');
    // }

    // public function destroy($id)
    // {
    //     $schoolId = session('current_school_id');
    //     Log::info('🧭 [DEBUG] Início do destroy()', [
    //         'id_recebido' => $id,
    //         'school_id_sessao' => $schoolId
    //     ]);

    //     // 1️⃣ Busca o aluno (nativo ou vinculado)
    //     $aluno = Aluno::with(['enturmacao', 'ocorrencias'])
    //         ->where(function ($query) use ($schoolId) {
    //             $query->where('school_id', $schoolId)
    //                   ->orWhereHas('enturmacao', function ($sub) use ($schoolId) {
    //                       $sub->where('school_id', $schoolId);
    //                   });
    //         })
    //         ->where('id', $id)
    //         ->first();

    //     if (!$aluno) {
    //         Log::warning('⚠️ [DEBUG] Aluno não encontrado');
    //         dd('⚠️ Aluno não encontrado com o ID informado.');
    //     }

    //     Log::info('👤 [DEBUG] Aluno encontrado', $aluno->toArray());

    //     // 2️⃣ Checa vínculo na enturmação
    //     $temVinculo = $aluno->enturmacao()->where('school_id', $schoolId)->exists();
    //     Log::info('🔗 [DEBUG] Verificação de vínculo', [
    //         'tem_vinculo' => $temVinculo,
    //         'school_id_aluno' => $aluno->school_id,
    //         'school_id_sessao' => $schoolId
    //     ]);

    //     if ($aluno->school_id != $schoolId && !$temVinculo) {
    //         Log::warning('🚫 [DEBUG] Aluno não pertence nem está vinculado a esta escola');
    //         dd('🚫 Aluno não pertence nem está vinculado a esta escola.');
    //     }

    //     // 3️⃣ Verifica ocorrências
    //     $temOcorrencias = \App\Models\Ocorrencia::where('aluno_id', $aluno->id)->exists();
    //     Log::info('🧾 [DEBUG] Verificação de ocorrências', ['tem_ocorrencias' => $temOcorrencias]);

    //     if ($temOcorrencias) {
    //         Log::warning('⚠️ [DEBUG] Aluno tem ocorrências');
    //         dd('⚠️ Aluno possui ocorrências — não pode ser excluído.');
    //     }

    //     // 4️⃣ Conta enturmações
    //     $qtdEnturmacoes = \App\Models\Enturmacao::where('aluno_id', $aluno->id)->count();
    //     Log::info('📊 [DEBUG] Contagem de enturmações', ['qtd_enturmacoes' => $qtdEnturmacoes]);

    //     if ($qtdEnturmacoes > 1 || ($qtdEnturmacoes == 1 && $aluno->school_id != $schoolId)) {
    //         Log::warning('⚠️ [DEBUG] Aluno vinculado a múltiplas escolas');
    //         dd('⚠️ Este aluno está vinculado a mais de uma escola. Remova o vínculo antes de excluir.');
    //     }

    //     // 5️⃣ Caso seja apenas vínculo (não nativo)
    //     if ($aluno->school_id != $schoolId && $temVinculo) {
    //         Log::info('🧩 [DEBUG] Caso de vínculo detectado — removendo enturmação');
    //         $enturmasRemovidas = \App\Models\Enturmacao::where('aluno_id', $aluno->id)
    //             ->where('school_id', $schoolId)
    //             ->delete();

    //         Log::info('🧹 [DEBUG] Enturmações removidas', ['qtd' => $enturmasRemovidas]);

    //         if ($enturmasRemovidas > 0) {
    //             dd('✅ Vínculo do aluno removido com sucesso.');
    //         } else {
    //             dd('⚠️ Nenhuma enturmação encontrada para remover.');
    //         }
    //     }

    //     // 6️⃣ Caso seja nativo
    //     Log::info('🏫 [DEBUG] Aluno nativo — iniciando exclusão total');

    //     $enturmasRemovidas = \App\Models\Enturmacao::where('aluno_id', $aluno->id)
    //         ->where('school_id', $schoolId)
    //         ->delete();

    //     Log::info('🧹 [DEBUG] Enturmações deletadas', ['qtd' => $enturmasRemovidas]);

    //     $restaVinculo = \App\Models\Enturmacao::where('aluno_id', $aluno->id)->exists();
    //     Log::info('🔁 [DEBUG] Resta vínculo?', ['resta_vinculo' => $restaVinculo]);

    //     if (!$restaVinculo && $aluno->school_id == $schoolId) {
    //         $aluno->delete();
    //         Log::info('✅ [DEBUG] Aluno deletado definitivamente', ['id' => $aluno->id]);
    //         dd('✅ Aluno removido definitivamente.');
    //     }

    //     Log::warning('⚠️ [DEBUG] Nada foi removido — aluno ainda tem vínculos externos.');
    //     dd('⚠️ Nenhum vínculo removido. O aluno ainda tem enturmações em outras escolas.');
    // }

    public function destroy($id)
    {
        $schoolId = session('current_school_id');
        Log::info('🧭 Início do destroy()', [
            'id_recebido' => $id,
            'school_id_sessao' => $schoolId
        ]);

        // 1️⃣ Busca o aluno (nativo ou vinculado)
        $aluno = Aluno::with(['enturmacao', 'ocorrencias'])
            ->where(function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId)
                      ->orWhereHas('enturmacao', function ($sub) use ($schoolId) {
                          $sub->where('school_id', $schoolId);
                      });
            })
            ->where('id', $id)
            ->first();

        if (!$aluno) {
            Log::warning('⚠️ Aluno não encontrado', ['id' => $id]);
            return redirect()->route('escola.alunos.index')
                ->with('warning', '⚠️ Aluno não encontrado.');
        }

        // 2️⃣ Verifica vínculo com a escola logada
        $temVinculo = $aluno->enturmacao()->where('school_id', $schoolId)->exists();
        if ($aluno->school_id != $schoolId && !$temVinculo) {
            Log::warning('🚫 Aluno não pertence nem está vinculado à escola', [
                'aluno_school_id' => $aluno->school_id,
                'school_id_sessao' => $schoolId
            ]);
            return redirect()->route('escola.alunos.index')
                ->with('warning', '🚫 Este aluno não pertence nem está vinculado a esta escola.');
        }

        // 3️⃣ Bloqueia exclusão se tiver ocorrências
        if (\App\Models\Ocorrencia::where('aluno_id', $aluno->id)->exists()) {
            Log::warning('⚠️ Aluno com ocorrências detectado', ['id' => $aluno->id]);
            return redirect()->route('escola.alunos.index')
                ->with('warning', '⚠️ Não é possível excluir. O aluno possui ocorrências registradas.');
        }

        // 4️⃣ Se tiver vínculo (enturmado na escola atual)
        if ($temVinculo) {
            $removidas = \App\Models\Enturmacao::where('aluno_id', $aluno->id)
                ->where('school_id', $schoolId)
                ->delete();

            Log::info('🧹 Enturmações removidas desta escola', [
                'aluno_id' => $aluno->id,
                'removidas' => $removidas
            ]);

            // Verifica se ainda restam vínculos com outras escolas
            $restaVinculo = \App\Models\Enturmacao::where('aluno_id', $aluno->id)->exists();

            // Se o aluno for nativo e não tiver mais vínculos → pode excluir totalmente
            if (!$restaVinculo && $aluno->school_id == $schoolId) {
                $aluno->delete();
                Log::info('✅ Aluno nativo deletado definitivamente', ['id' => $aluno->id]);
                return redirect()->route('escola.alunos.index')
                    ->with('success', '✅ Aluno removido completamente, sem vínculos restantes.');
            }

            // Caso contrário, apenas o vínculo local foi removido
            return redirect()->route('escola.alunos.index')
                ->with('success', '🔗 Vínculo com esta escola removido com sucesso.');
        }

        // 5️⃣ Se for nativo e sem vínculos externos
        $restaVinculo = \App\Models\Enturmacao::where('aluno_id', $aluno->id)->exists();
        if (!$restaVinculo && $aluno->school_id == $schoolId) {
            $aluno->delete();
            Log::info('✅ Aluno nativo sem vínculos restantes — deletado', ['id' => $aluno->id]);
            return redirect()->route('escola.alunos.index')
                ->with('success', '✅ Aluno removido com sucesso.');
        }

        // 6️⃣ Nenhuma condição de exclusão atendida
        Log::warning('⚠️ Nenhuma exclusão realizada', [
            'aluno_id' => $aluno->id,
            'school_id_sessao' => $schoolId
        ]);
        return redirect()->route('escola.alunos.index')
            ->with('warning', '⚠️ Não foi possível excluir. O aluno ainda está vinculado a outras escolas.');
    }


}
