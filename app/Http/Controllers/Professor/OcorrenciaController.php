<?php

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
    /**
     * Lista geral (opcional)
     */
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

    /**
     * Formulário de criação (recebe alunos e oferta)
     */
    public function create(Request $request)
    {
        $alunoIds = $request->input('alunos', []);
        $ofertaId = $request->input('oferta_id');

        $alunos = Aluno::whereIn('id', $alunoIds)->get();
        $oferta = Oferta::with(['disciplina', 'turma'])->findOrFail($ofertaId);
        $motivos = ModeloMotivo::orderBy('descr_r')->get();

        return view('professor.ocorrencias.create', compact('alunos', 'oferta', 'motivos'));
    }

    /**
     * Persiste a ocorrência (vários alunos de uma vez)
     */
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

    /**
     * Exibe o histórico de um aluno (opcional)
     */
    public function show($id)
    {
        $ocorrencia = Ocorrencia::with(['aluno', 'oferta.disciplina', 'oferta.turma', 'motivos'])
            ->findOrFail($id);

        return view('professor.ocorrencias.show', compact('ocorrencia'));
    }

    /**
     * Remove (arquiva) uma ocorrência
     */
    public function destroy($id)
    {
        $ocorrencia = Ocorrencia::findOrFail($id);
        $ocorrencia->update(['vigente' => false]);

        return back()->with('success', '🗂 Ocorrência arquivada com sucesso.');
    }
}
