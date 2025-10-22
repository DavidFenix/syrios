<?php

namespace App\Http\Controllers\Escola;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\{
    Professor,
    Aluno,
    Turma,
    Disciplina,
    Enturmacao,
    ModeloMotivo,
    Ocorrencia,
    Escola
};

class DashboardController extends Controller
{
    public function index()
    {
        $schoolId = session('current_school_id');

        // 👨‍🏫 Conta total de professores
        $totalProfessores = Professor::where('school_id', $schoolId)->count();

        // 🎓 Conta total de alunos
        $totalAlunos = Aluno::where('school_id', $schoolId)->count();

        // 🏷️ Conta total de turmas
        $totalTurmas = Turma::where('school_id', $schoolId)->count();

        // 📚 Conta total de disciplinas
        $totalDisciplinas = Disciplina::where('school_id', $schoolId)->count();

        // 🧮 Conta total de enturmações
        $totalEnturmacoes = Enturmacao::where('school_id', $schoolId)->count();

        // 🧩 Conta total de motivos de ocorrência
        $totalMotivos = ModeloMotivo::where('school_id', $schoolId)->count();

        // ⚠️ Ocorrências (ativas / arquivadas / anuladas)
        $totalOcorrenciasAtivas = Ocorrencia::where('school_id', $schoolId)->where('status', 1)->count();
        $totalOcorrenciasArquivadas = Ocorrencia::where('school_id', $schoolId)->where('status', 0)->count();
        $totalOcorrenciasAnuladas = Ocorrencia::where('school_id', $schoolId)->where('status', 2)->count();
        $totalOcorrencias = $totalOcorrenciasAtivas + $totalOcorrenciasArquivadas + $totalOcorrenciasAnuladas;

        // 🏫 Verifica se há regimento cadastrado
        $temRegimento = DB::table(prefix('regimento'))
            ->where('school_id', $schoolId)
            ->exists();

        // 🏫 Identidade escolar
        $escola = Escola::find($schoolId);

        // 📅 Ocorrências aplicadas hoje
        $totalOcorrenciasHoje = Ocorrencia::where('school_id', $schoolId)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        // 📊 Envia tudo para a view
        return view('escola.dashboard', compact(
            'totalProfessores',
            'totalAlunos',
            'totalTurmas',
            'totalDisciplinas',
            'totalEnturmacoes',
            'totalMotivos',
            'totalOcorrencias',
            'totalOcorrenciasAtivas',
            'totalOcorrenciasArquivadas',
            'totalOcorrenciasAnuladas',
            'totalOcorrenciasHoje',
            'temRegimento',
            'escola'
        ));
    }
}




/*
namespace App\Http\Controllers\Escola;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\{
    Professor,
    Aluno,
    Turma,
    Disciplina,
    Enturmacao,
    ModeloMotivo,
    Escola
};

class DashboardController extends Controller
{
    public function index()
    {

        $schoolId = session('current_school_id');

        // 👨‍🏫 Conta total de professores
        $totalProfessores = Professor::where('school_id', $schoolId)->count();

        // 🎓 Conta total de alunos
        $totalAlunos = Aluno::where('school_id', $schoolId)->count();

        // 🏷️ Conta total de turmas
        $totalTurmas = Turma::where('school_id', $schoolId)->count();

        // 📚 Conta total de disciplinas
        $totalDisciplinas = Disciplina::where('school_id', $schoolId)->count();

        // 🧮 Conta total de enturmações
        $totalEnturmacoes = Enturmacao::where('school_id', $schoolId)->count();

        // 🧩 Conta total de motivos de ocorrência
        $totalMotivos = ModeloMotivo::where('school_id', $schoolId)->count();

        // 🏫 Verifica se há regimento cadastrado
        $temRegimento = DB::table(prefix('regimento'))
            ->where('school_id', $schoolId)
            ->exists();

        // 🏫 Identidade escolar (opcional)
        $escola = Escola::find($schoolId);

        // 📊 Envia tudo para a view
        return view('escola.dashboard', compact(
            'totalProfessores',
            'totalAlunos',
            'totalTurmas',
            'totalDisciplinas',
            'totalEnturmacoes',
            'totalMotivos',
            'temRegimento',
            'escola'
        ));
    }
}
*/

/*
namespace App\Http\Controllers\Escola;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
        $escola = $usuario->escola; // escola do usuário logado

        return view('escola.dashboard', compact('usuario', 'escola'));
    }
}
*/