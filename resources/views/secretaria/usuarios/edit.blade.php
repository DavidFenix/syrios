@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Usuário</h1>

    <form method="POST" action="{{ route('secretaria.usuarios.update', $usuario) }}">
        @csrf
        @method('PUT')

        {{-- Nome --}}
        <div class="mb-3">
            <label for="nome_u" class="form-label">Nome</label>
            <input type="text" name="nome_u" id="nome_u"
                   class="form-control"
                   value="{{ old('nome_u', $usuario->nome_u) }}" required>
        </div>

        {{-- CPF --}}
        <div class="mb-3">
            <label for="cpf" class="form-label">CPF</label>
            <input type="text" name="cpf" id="cpf"
                   class="form-control"
                   value="{{ old('cpf', $usuario->cpf) }}" required>
        </div>

        {{-- Senha (opcional) --}}
        <div class="mb-3">
            <label for="senha" class="form-label">Senha (preencha apenas se quiser alterar)</label>
            <input type="password" name="senha" id="senha" class="form-control">
        </div>

        {{-- Status --}}
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select" required>
                <option value="1" {{ old('status', $usuario->status) == 1 ? 'selected' : '' }}>Ativo</option>
                <option value="0" {{ old('status', $usuario->status) == 0 ? 'selected' : '' }}>Inativo</option>
            </select>
        </div>

        {{-- Escola (fixa da secretaria logada) --}}
        <div class="mb-3">
            <label class="form-label">Secretaria Vinculada</label>
            <input type="text" class="form-control" value="{{ $secretaria->nome_e }}" disabled>
            <input type="hidden" name="school_id" value="{{ $secretaria->id }}">
        </div>
        
        <div class="mb-3">
            <label for="school_id" class="form-label">Escola Vinculada</label>
            <select name="school_id" id="school_id" class="form-select" required>
                @foreach($escolas as $e)
                    <option value="{{ $e->id }}" {{ old('school_id', $usuario->school_id ?? '') == $e->id ? 'selected' : '' }}>
                        {{ $e->nome_e }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Roles (sem master/secretaria) --}}
        <div class="mb-3">
            <label class="form-label">Roles</label>
            <div class="form-check">
                @foreach($roles as $role)
                    @if(!in_array($role->role_name, ['master','secretaria']))
                        <div>
                            <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                   id="role_{{ $role->id }}"
                                   class="form-check-input"
                                   {{ $usuario->roles->contains($role->id) ? 'checked' : '' }}>
                            <label for="role_{{ $role->id }}" class="form-check-label">
                                {{ $role->role_name }}
                            </label>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="{{ route('secretaria.usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection



{{--
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
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select" required>
                <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Ativo</option>
                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inativo</option>
            </select>
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
--}}