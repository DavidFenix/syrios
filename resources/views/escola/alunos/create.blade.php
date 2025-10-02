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
            <input type="text" name="matricula" class="form-control" required>
        </div>
        <button class="btn btn-success">Salvar</button>
        <a href="{{ route('escola.alunos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
