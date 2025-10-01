@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Usuário</h1>

    <form method="POST" action="{{ route('secretaria.usuarios.update', $usuario) }}">
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
            <label class="form-label">Senha (deixe em branco para não alterar)</label>
            <input type="password" name="senha" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Escola</label>
            <select name="school_id" class="form-select" required>
                @foreach($escolas as $escola)
                    <option value="{{ $escola->id }}" {{ $usuario->school_id == $escola->id ? 'selected' : '' }}>
                        {{ $escola->nome_e }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Roles</label><br>
            @foreach($roles as $role)
                <label class="me-3">
                    <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                        {{ $usuario->roles->contains($role->id) ? 'checked' : '' }}>
                    {{ $role->role_name }}
                </label>
            @endforeach
        </div>

        <button type="submit" class="btn btn-success">Salvar alterações</button>
        <a href="{{ route('secretaria.usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
