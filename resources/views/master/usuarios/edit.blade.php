@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Usuário</h1>

    <form method="POST" action="{{ route('master.usuarios.update', $usuario) }}">
        @csrf @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nome*</label>
            <input type="text" name="nome_u" class="form-control" value="{{ $usuario->nome_u }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">CPF*</label>
            <input type="text" name="cpf" class="form-control" value="{{ $usuario->cpf }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Senha (preencha apenas se quiser trocar)</label>
            <input type="password" name="senha" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Escola*</label>
            <select name="school_id" class="form-control">
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
                <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                    {{ in_array($role->id, $rolesUsuario) ? 'checked' : '' }}>
                {{ $role->role_name }}<br>
            @endforeach
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="1" {{ $usuario->status ? 'selected' : '' }}>Ativo</option>
                <option value="0" {{ !$usuario->status ? 'selected' : '' }}>Inativo</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Salvar Alterações</button>
        <a href="{{ route('master.usuarios.index') }}" class="btn btn-secondary">Voltar</a>
    </form>
</div>
@endsection
