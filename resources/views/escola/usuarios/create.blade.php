@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Novo Usuário</h1>

    <form method="POST" action="{{ route('escola.usuarios.store') }}">
        @csrf

        <div class="mb-3">
            <label>Nome</label>
            <input type="text" name="nome_u" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>CPF</label>
            <input type="text" name="cpf" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Senha</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="1">Ativo</option>
                <option value="0">Inativo</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Roles</label><br>
            @foreach($roles as $role)
                <label><input type="checkbox" name="roles[]" value="{{ $role->id }}"> {{ $role->role_name }}</label><br>
            @endforeach
        </div>

        <button class="btn btn-success">Salvar</button>
    </form>
</div>
@endsection


{{--
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Novo Usuário</h1>

    <form method="POST" action="{{ route('escola.usuarios.store') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="nome_u" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">CPF</label>
            <input type="text" name="cpf" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Senha</label>
            <input type="password" name="senha" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Roles</label>
            @foreach($roles as $role)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}">
                    <label class="form-check-label">{{ $role->role_name }}</label>
                </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-success">Salvar</button>
        <a href="{{ route('escola.usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
--}}