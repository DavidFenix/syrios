<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\{
    Ocorrencia,
    ModeloMotivo,
    Oferta,
    Aluno,
    Escola
};
use Barryvdh\DomPDF\Facade\Pdf;


class OcorrenciaController extends Controller
{
    
    public function gerarPdf($alunoId)
    {
        $aluno = Aluno::findOrFail($alunoId);
        $turma = $aluno->turma()->first();
        $escola = Escola::find(session('current_school_id'));

        $fotoPath = public_path('storage/img-user/' . $aluno->matricula . '.png');
        $fotoUrl = file_exists($fotoPath)
            ? asset('storage/img-user/' . $aluno->matricula . '.png')
            : asset('storage/img-user/padrao.png');

        $ocorrencias = Ocorrencia::with(['motivos', 'oferta.disciplina', 'professor'])
            ->where('aluno_id', $aluno->id)
            ->orderByDesc('created_at')
            ->get();

        // // Gera o PDF com base na mesma view
        // $pdf = Pdf::loadView('professor.ocorrencias.historico_resumido', [
        //     'aluno' => $aluno,
        //     'turma' => $turma,
        //     'escola' => $escola,
        //     'fotoUrl' => $fotoUrl,
        //     'ocorrencias' => $ocorrencias,
        // ]);

        // // Configurações opcionais
        // $pdf->setPaper('a4', 'portrait');

        // $nomeArquivo = 'Historico_' . preg_replace('/\s+/', '_', $aluno->nome_a) . '.pdf';

        // // Faz download direto
        // return $pdf->download($nomeArquivo);

        $pdf = \PDF::loadView('professor.ocorrencias.pdf_historico', [
            'escola' => $escola,
            'aluno' => $aluno,
            'ocorrencias' => $ocorrencias,
            'fotoUrl' => $fotoUrl,
        ]);

        $pdf->setPaper('a4');
        return $pdf->download('historico_ocorrencias_'.$aluno->matricula.'.pdf');

    }


    public function historicoResumido($alunoId)
    {
        $aluno = Aluno::findOrFail($alunoId);
        $turma = $aluno->turma()->first();
        $escola = Escola::find(session('current_school_id'));

        $fotoPath = public_path('storage/img-user/' . $aluno->matricula . '.png');
        $fotoUrl = file_exists($fotoPath)
            ? asset('storage/img-user/' . $aluno->matricula . '.png')
            : asset('storage/img-user/padrao.png');

        $ocorrencias = Ocorrencia::with(['motivos', 'oferta.disciplina', 'professor'])
            ->where('aluno_id', $aluno->id)
            ->orderByDesc('created_at')
            ->get();

        return view('professor.ocorrencias.historico_resumido', compact(
            'aluno', 'turma', 'escola', 'fotoUrl', 'ocorrencias'
        ));
    }


    public function historico($alunoId)
    {
        $schoolId = session('current_school_id');

        $aluno = \App\Models\Aluno::with('turma')
            ->where('id', $alunoId)
            ->firstOrFail();

        // $ocorrencias = \App\Models\Ocorrencia::with(['professor.usuario', 'oferta.disciplina', 'motivos.modelo'])
        //     ->where('aluno_id', $alunoId)
        //     ->where('school_id', $schoolId)
        //     ->orderByDesc('created_at')
        //     ->get();

        $ocorrencias = Ocorrencia::with([
            'professor.usuario',
            'oferta.disciplina',
            'oferta.turma',
            'motivos' // ✅ sem .modelo
        ])
        ->where('aluno_id', $alunoId)
        ->where('school_id', $schoolId)
        ->orderByDesc('created_at')
        ->get();


        return view('professor.ocorrencias.historico', compact('aluno', 'ocorrencias'));
    }


    /**
     * Exibe o formulário de criação de ocorrência
     * para um ou mais alunos.
     */
    public function create(Request $request, $ofertaId)
    {
        $schoolId = session('current_school_id');
        $anoLetivo = session('ano_letivo_atual') ?? date('Y');

        $oferta = Oferta::with(['disciplina', 'turma'])->findOrFail($ofertaId);

        $alunosIds = $request->input('alunos', []);
        $alunos = Aluno::whereIn('id', $alunosIds)->get();

        $motivos = ModeloMotivo::daEscolaAtual()->orderBy('categoria')->get();

        return view('professor.ocorrencias.create', compact(
            'oferta',
            'alunos',
            'motivos',
            'anoLetivo',
            'schoolId'
        ));
    }

    /**
     * Grava a ocorrência no banco de dados
     * (para um ou mais alunos simultaneamente).
     */
    public function store(Request $request)
    {
        $schoolId = session('current_school_id');
        $anoLetivo = session('ano_letivo_atual') ?? date('Y');

        $request->validate([
            'alunos' => 'required|array|min:1',
            'oferta_id' => 'nullable|integer|exists:' . prefix() . 'oferta,id',
            'motivos' => 'nullable|array',
            'descricao' => 'nullable|string',
            'local' => 'nullable|string|max:100',
            'atitude' => 'nullable|string|max:100',
            'outra_atitude' => 'nullable|string|max:150',
            'comportamento' => 'nullable|string|max:100',
            'sugestao' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->alunos as $alunoId) {
                $ocorrencia = Ocorrencia::create([
                    'school_id' => $schoolId,
                    'ano_letivo' => $anoLetivo,
                    'vigente' => true,
                    'aluno_id' => $alunoId,
                    'professor_id' => auth()->id(),
                    'oferta_id' => $request->oferta_id,
                    'descricao' => $request->descricao,
                    'local' => $request->local,
                    'atitude' => $request->atitude,
                    'outra_atitude' => $request->outra_atitude,
                    'comportamento' => $request->comportamento,
                    'sugestao' => $request->sugestao,
                    'nivel_gravidade' => 1,
                ]);

                // associa motivos selecionados
                // if ($request->has('motivos')) {
                //     $ocorrencia->motivos()->sync($request->motivos);
                // }

                // Salva os motivos relacionados sem sobrescrever os existentes
                if (!empty($request->motivos)) {
                    $ocorrencia->motivos()->syncWithoutDetaching($request->motivos);
                }

                Log::info('📘 Ocorrência registrada', [
                    'professor_id' => auth()->id(),
                    'aluno_id' => $alunoId,
                    'motivos' => $request->motivos,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('professor.ofertas.index')
                ->with('success', '✅ Ocorrência registrada com sucesso.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('❌ Erro ao registrar ocorrência', [
                'erro' => $e->getMessage(),
                'linha' => $e->getLine()
            ]);

            return back()->with('error', 'Ocorreu um erro ao registrar a ocorrência.');
        }
    }

    /**
     * Lista todas as ocorrências do professor logado
     * (filtrando por escola e ano letivo).
     */
    public function index()
    {
        $schoolId = session('current_school_id');
        $anoLetivo = session('ano_letivo_atual') ?? date('Y');
        $professorId = auth()->id();

        $ocorrencias = Ocorrencia::with(['aluno', 'oferta.disciplina', 'oferta.turma', 'motivos'])
            ->where('professor_id', $professorId)
            ->where('school_id', $schoolId)
            ->where('ano_letivo', $anoLetivo)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('professor.ocorrencias.index', compact('ocorrencias'));
    }

    /**
     * Exibe detalhes de uma ocorrência específica.
     */
    public function show($id)
    {
        $ocorrencia = Ocorrencia::with(['aluno', 'professor', 'oferta', 'motivos'])
            ->daEscolaAtual()
            ->findOrFail($id);

        return view('professor.ocorrencias.show', compact('ocorrencia'));
    }

    /**
     * Arquiva ou anula uma ocorrência.
     */
    public function updateStatus($id, Request $request)
    {
        $ocorrencia = Ocorrencia::daEscolaAtual()->findOrFail($id);

        $novoStatus = $request->input('status', 0); // 0=arquivada, 2=anulada
        $ocorrencia->update(['status' => $novoStatus, 'vigente' => false]);

        return back()->with('success', 'Ocorrência atualizada com sucesso.');
    }
}


/*
namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Ocorrencia;
use App\Models\ModeloMotivo;
use App\Models\Aluno;
use App\Models\Oferta;
use App\Models\OcorrenciaMotivo;

class OcorrenciaController extends Controller
{
    
    // * Lista geral (opcional)
    public function index()
    {
        $professorId = auth()->user()->id;
        $schoolId = session('current_school_id');
        $anoLetivo = session('ano_letivo_atual') ?? date('Y');

        $ocorrencias = Ocorrencia::with(['aluno', 'oferta.disciplina', 'oferta.turma'])
            ->where('professor_id', $professorId)
            ->where('school_id', $schoolId)
            ->where('ano_letivo', $anoLetivo)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('professor.ocorrencias.index', compact('ocorrencias'));
    }

    
    // * Formulário de criação (recebe alunos e oferta)
    public function create(Request $request)
    {
        $alunoIds = $request->input('alunos', []);
        $ofertaId = $request->input('oferta_id');

        $alunos = Aluno::whereIn('id', $alunoIds)->get();
        $oferta = Oferta::with(['disciplina', 'turma'])->findOrFail($ofertaId);
        $motivos = ModeloMotivo::orderBy('descr_r')->get();

        return view('professor.ocorrencias.create', compact('alunos', 'oferta', 'motivos'));
    }

    
    // * Persiste a ocorrência (vários alunos de uma vez)
    public function store(Request $request)
    {
        $schoolId = session('current_school_id');
        $anoLetivo = session('ano_letivo_atual') ?? date('Y');
        $professor = auth()->user();

        $request->validate([
            'oferta_id' => 'required|integer|exists:' . prefix() . 'oferta,id',
            'alunos' => 'required|array|min:1',
            'motivos' => 'nullable|array',
            'descricao_extra' => 'nullable|string|max:500',
            'local' => 'nullable|string|max:100',
            'atitude' => 'nullable|string|max:100',
            'outra_atitude' => 'nullable|string|max:100',
            'comportamento' => 'nullable|string|max:100',
            'sugestao' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->alunos as $alunoId) {
                $ocorrencia = Ocorrencia::create([
                    'school_id'     => $schoolId,
                    'ano_letivo'    => $anoLetivo,
                    'professor_id'  => $professor->id,
                    'aluno_id'      => $alunoId,
                    'oferta_id'     => $request->oferta_id,
                    'descricao'     => $request->descricao_extra,
                    'local'         => $request->local,
                    'atitude'       => $request->atitude,
                    'outra_atitude' => $request->outra_atitude,
                    'comportamento' => $request->comportamento,
                    'sugestao'      => $request->sugestao,
                    'vigente'       => true,
                    'status'        => 1,
                ]);

                // 🔗 Associa motivos (pivot)
                if (!empty($request->motivos)) {
                    foreach ($request->motivos as $motivoId) {
                        OcorrenciaMotivo::create([
                            'ocorrencia_id' => $ocorrencia->id,
                            'modelo_motivo_id' => $motivoId,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('professor.ofertas.index')
                ->with('success', '✅ Ocorrência aplicada com sucesso aos alunos selecionados.');

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Erro ao aplicar ocorrência', [
                'user_id' => $professor->id ?? null,
                'mensagem' => $e->getMessage(),
                'linha' => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with('warning', '❌ Erro ao registrar ocorrência. Tente novamente.');
        }
    }

   
    // * Exibe o histórico de um aluno (opcional)
    public function show($id)
    {
        $ocorrencia = Ocorrencia::with(['aluno', 'oferta.disciplina', 'oferta.turma', 'motivos'])
            ->findOrFail($id);

        return view('professor.ocorrencias.show', compact('ocorrencia'));
    }

    
    // * Remove (arquiva) uma ocorrência
    public function destroy($id)
    {
        $ocorrencia = Ocorrencia::findOrFail($id);
        $ocorrencia->update(['vigente' => false]);

        return back()->with('success', '🗂 Ocorrência arquivada com sucesso.');
    }
}
*/