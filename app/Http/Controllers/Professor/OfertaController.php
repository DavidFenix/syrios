<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Oferta;
use App\Models\Enturmacao;
use App\Models\Aluno;

class OfertaController extends Controller
{
    /**
     * Lista todas as ofertas do professor logado
     */
    public function index()
    {
        $user = auth()->user();
        $schoolId = session('current_school_id');
        $anoLetivo = session('ano_letivo_atual') ?? date('Y');

        // 🔍 Localiza o vínculo de professor na escola atual
        $professor = $user->professor()->where('school_id', $schoolId)->first();

        if (!$professor) {
            return redirect()
                ->route('professor.dashboard')
                ->with('warning', '⚠️ Seu usuário não está vinculado como professor nesta escola.');
        }

        // 📚 Carrega as ofertas do professor para o ano vigente
        $ofertas = Oferta::with(['disciplina', 'turma'])
            ->where('professor_id', $professor->id)
            ->where('school_id', $schoolId)
            ->where('ano_letivo', $anoLetivo)
            ->where('vigente', true)
            ->orderByDesc('id')
            ->get();

        // 🔢 Calcula contagem de ocorrências simulada (substituir depois por consulta real)
        foreach ($ofertas as $oferta) {
            $oferta->qtd1 = rand(0, 5);
            $oferta->qtd2 = rand(0, 5);
            $oferta->qtd3 = rand(0, 5);
            $oferta->qtd4 = rand(0, 5);
            $oferta->qtd5 = rand(0, 5);
        }

        return view('professor.ofertas.index', compact('ofertas'));
    }

    /**
     * Exibe os alunos da turma vinculada a uma oferta específica
     */
    public function alunos($ofertaId)
    {
        $schoolId = session('current_school_id');
        $anoLetivo = session('ano_letivo_atual') ?? date('Y');

        $oferta = Oferta::with(['disciplina', 'turma'])
            ->where('school_id', $schoolId)
            ->where('ano_letivo', $anoLetivo)
            ->findOrFail($ofertaId);

        // 🔍 Alunos enturmados na turma dessa oferta
        $alunos = Aluno::select('id', 'matricula', 'nome_a', 'school_id')
            ->whereHas('enturmacao', function ($q) use ($oferta, $schoolId, $anoLetivo) {
                $q->where('turma_id', $oferta->turma_id)
                  ->where('school_id', $schoolId)
                  ->where('ano_letivo', $anoLetivo);
            })
            ->orderBy('nome_a')
            ->get();

        // 🔢 Contagem simulada de ocorrências (substituir futuramente)
        foreach ($alunos as $a) {
            $a->total_ocorrencias = rand(0, 8);
        }

        return view('professor.ofertas.alunos', compact('oferta', 'alunos'));
    }

    public function alunosPost(Request $request, Oferta $oferta)
    {
        $alunosSelecionados = $request->input('alunos', []);

        if (empty($alunosSelecionados)) {
            return back()->with('warning', 'Selecione ao menos um aluno.');
        }

        return redirect()->route('professor.ocorrencias.create', [
            'oferta_id' => $oferta->id,
            'alunos' => $alunosSelecionados
        ]);
    }





    
}
