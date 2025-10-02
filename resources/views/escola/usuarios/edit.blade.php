@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Usuário</h1>

    <form method="POST" action="{{ route('escola.usuarios.update', $usuario) }}">
        @csrf @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="nome_u" class="form-control" value="{{ $usuario->nome_u }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">CPF</label>
            <input type="text" name="cpf" class="form-control" value="{{ $usuario->cpf }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nova Senha (deixe em branco para não alterar)</label>
            <input type="password" name="senha" class="form-control">
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select" required>
                <option value="1" {{ $usuario->status == 1 ? 'selected' : '' }}>Ativo</option>
                <option value="0" {{ $usuario->status == 0 ? 'selected' : '' }}>Inativo</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Roles</label>
            @foreach($roles as $role)
                <div class="form-check">
                    <input class="form-check-input"
                           type="checkbox"
                           name="roles[]"
                           value="{{ $role->id }}"
                           {{ $usuario->roles->contains($role->id) ? 'checked' : '' }}>
                    <label class="form-check-label">{{ $role->role_name }}</label>
                </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-success">Salvar Alterações</button>
        <a href="{{ route('escola.usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
