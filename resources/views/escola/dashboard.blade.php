@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Dashboard da Escola</h1>
    <p>Bem-vindo, {{ Auth::user()->nome_u ?? 'Usuário' }}!</p>

    <div class="row">
        <div class="col-md-3 mb-3">
            <a href="{{ route('escola.professores.index') }}" class="btn btn-primary w-100">
                👨‍🏫 Professores
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('escola.alunos.index') }}" class="btn btn-success w-100">
                👩‍🎓 Alunos
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('escola.disciplinas.index') }}" class="btn btn-warning w-100">
                📚 Disciplinas
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('escola.turmas.index') }}" class="btn btn-info w-100">
                🏫 Turmas
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('escola.enturmacao.index') }}" class="btn btn-warning w-100">
                📚 Enturmação
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('escola.lotacao.index') }}" class="btn btn-warning w-100">
                📚 Lotação
            </a>
        </div>
    </div>
</div>
@endsection
