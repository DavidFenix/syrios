@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Novo Usuário</h1>

    @if(session('usuario_existente'))
        <div class="alert alert-warning">
            Usuário já existe no sistema.
            <form action="{{ route('escola.usuarios.vincular', session('usuario_existente')) }}" method="POST" class="mt-2">
                @csrf
                <label>Selecione os papéis para vincular:</label>
                @foreach($roles as $role)
                    <div>
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}">
                        <label for="role_{{ $role->id }}">{{ $role->role_name }}</label>
                    </div>
                @endforeach
                <button type="submit" class="btn btn-sm btn-primary mt-2">Vincular à minha escola</button>
            </form>
        </div>
    @endif


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
            <select name="status" class="form-select">
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
        <a href="{{ route('escola.usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection


{{--
@extends('layouts.app')

@section('content')
<div class="container">
    
    @if(session('usuario_existente'))
        <div class="alert alert-warning">
            Usuário já existe no sistema.
            <form action="{{ route('escola.usuarios.vincular', session('usuario_existente')) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-primary">Vincular este usuário à minha escola</button>
            </form>
        </div>
    @endif

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

--}}