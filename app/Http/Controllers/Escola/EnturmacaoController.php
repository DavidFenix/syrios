<?php

namespace App\Http\Controllers\Escola;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enturmacao;
use App\Models\Aluno;
use App\Models\Turma;
use Illuminate\Support\Facades\Log;

class EnturmacaoController extends Controller
{
    public function index()
    {
        $schoolId = session('current_school_id');
        $anoLetivo = session('ano_letivo_atual') ?? date('Y');

        $enturmacoes = Enturmacao::with(['aluno', 'turma'])
            ->where('school_id', $schoolId)
            ->where('ano_letivo', $anoLetivo)
            ->orderByDesc('id')
            ->get();

        return view('escola.enturmacao.index', compact('enturmacoes', 'anoLetivo'));
    }

    public function create()
    {
        $schoolId = session('current_school_id');
        $alunos = Aluno::where('school_id', $schoolId)->orderBy('nome_a')->get();
        $turmas = Turma::where('school_id', $schoolId)->orderBy('serie_turma')->get();

        return view('escola.enturmacao.create', compact('alunos', 'turmas'));
    }

    public function store(Request $request)
    {
        $schoolId = session('current_school_id');
        $anoLetivo = session('ano_letivo_atual') ?? date('Y');

        $request->validate([
            'aluno_id' => 'required|integer|exists:' . prefix() . 'aluno,id',
            'turma_id' => 'required|integer|exists:' . prefix() . 'turma,id',
        ]);

        // Evita duplicação do vínculo no mesmo ano e escola
        $jaExiste = Enturmacao::where([
            'school_id' => $schoolId,
            'aluno_id' => $request->aluno_id,
            'ano_letivo' => $anoLetivo,
        ])->exists();

        if ($jaExiste) {
            return redirect()->route('escola.enturmacao.index')
                ->with('warning', '⚠️ Este aluno já está enturmado neste ano letivo.');
        }

        Enturmacao::create([
            'school_id' => $schoolId,
            'aluno_id' => $request->aluno_id,
            'turma_id' => $request->turma_id,
            'ano_letivo' => $anoLetivo,
            'vigente' => true,
        ]);

        return redirect()->route('escola.enturmacao.index')
            ->with('success', '✅ Aluno enturmado com sucesso!');
    }

    public function destroy($id)
    {
        $schoolId = session('current_school_id');

        $enturmacao = Enturmacao::where('school_id', $schoolId)->findOrFail($id);
        $enturmacao->delete();

        return redirect()->route('escola.enturmacao.index')
            ->with('success', '🔗 Enturmação removida com sucesso.');
    }
}
