@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Aluno</h1>
    <form action="{{ route('escola.alunos.update', $aluno) }}" method="post">
        @csrf @method('PUT')
        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="nome_a" class="form-control" value="{{ $aluno->nome_a }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Matrícula</label>
            <input type="text" name="matricula" class="form-control" value="{{ $aluno->matricula }}" required>
        </div>
        <button class="btn btn-success">Atualizar</button>
        <a href="{{ route('escola.alunos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
