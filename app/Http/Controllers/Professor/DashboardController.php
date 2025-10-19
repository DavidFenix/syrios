<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Oferta;
use App\Models\Ocorrencia;

class DashboardController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
        $schoolId = session('current_school_id');
        $ano = session('ano_letivo_atual') ?? date('Y');

        // ✅ Total de ofertas (disciplinas e turmas)
        $totalOfertas = Oferta::where('professor_id', $usuario->id)
            ->where('school_id', $schoolId)
            ->where('ano_letivo', $ano)
            ->count();

        // ✅ Total de ocorrências aplicadas
        $totalOcorrencias = Ocorrencia::where('professor_id', $usuario->id)
            ->where('school_id', $schoolId)
            ->count();

        // ✅ Ocorrências ativas
        $ocorrenciasAtivas = Ocorrencia::where('professor_id', $usuario->id)
            ->where('school_id', $schoolId)
            ->where('status', 1)
            ->count();

        // ✅ Ocorrências arquivadas
        $ocorrenciasArquivadas = Ocorrencia::where('professor_id', $usuario->id)
            ->where('school_id', $schoolId)
            ->where('status', 0)
            ->count();

        return view('professor.dashboard', compact(
            'usuario',
            'totalOfertas',
            'totalOcorrencias',
            'ocorrenciasAtivas',
            'ocorrenciasArquivadas',
            'ano'
        ));
    }
}


/*
namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Aqui pode ser customizado futuramente (avisos, agenda, etc.)
        return view('professor.dashboard');
    }
}
*/