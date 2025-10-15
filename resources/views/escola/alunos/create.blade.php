@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Novo Aluno</h1>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if(session('warning'))
    <div class="alert alert-warning">{{ session('warning') }}</div>
@endif

    {{-- ⚠️ Se já existir --}}
    @if(session('aluno_existente'))
        <div class="alert alert-warning">
            ⚠️ Este aluno já existe no sistema.
            <form action="{{ route('escola.alunos.vincular', session('aluno_existente')) }}" method="POST" class="mt-2">
                @csrf
                <label>Selecione a turma (opcional):</label>
                <select name="turma_id" class="form-select mb-3">
                    <option value="">— Sem turma —</option>
                    @foreach(\App\Models\Turma::where('school_id', session('current_school_id'))->orderBy('serie_turma')->get() as $t)
                        <option value="{{ $t->id }}">{{ $t->serie_turma }} — {{ $t->turno }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-primary">🔗 Vincular à escola</button>
            </form>
        </div>
    @endif

    {{-- Formulário de novo aluno --}}
    <form method="POST" action="{{ route('escola.alunos.store') }}">
        @csrf
        <div class="mb-3">
            <label>Nome</label>
            <input type="text" name="nome_a" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Matrícula</label>
            <input type="text" name="matricula" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Turma (opcional)</label>
            <select name="turma_id" class="form-select">
                <option value="">— Sem turma —</option>
                @foreach(\App\Models\Turma::where('school_id', session('current_school_id'))->orderBy('serie_turma')->get() as $t)
                    <option value="{{ $t->id }}">{{ $t->serie_turma }} — {{ $t->turno }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-success">Salvar</button>
        <a href="{{ route('escola.alunos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection













{{--
@extends('layouts.app')

    @section('content')
    <div class="container">
        <h1>Novo Aluno</h1>

        <form action="{{ route('escola.alunos.store') }}" method="post">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="nome_a" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Matrícula</label>
                <input type="text" name="matricula" class="form-control" maxlength="10" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Turma (opcional)</label>
                <select name="turma_id" class="form-select">
                    <option value="">— Sem turma —</option>
                    @foreach($turmas as $t)
                        <option value="{{ $t->id }}">
                            {{ $t->serie_turma }} — {{ $t->turno }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button class="btn btn-success">Salvar</button>
            <a href="{{ route('escola.alunos.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    @endsection
    --}}
