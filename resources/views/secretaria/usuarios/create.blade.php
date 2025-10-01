@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Novo Usuário</h1>

    <form method="POST" action="{{ route('secretaria.usuarios.store') }}">
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
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select" required>
                <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Ativo</option>
                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inativo</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="school_id" class="form-label">Escola vinculada</label>
            <select name="school_id" id="school_id" class="form-select" required>
                @foreach($escolas as $e)
                    <option value="{{ $e->id }}" {{ old('school_id', $usuario->school_id ?? '') == $e->id ? 'selected' : '' }}>
                        {{ $e->nome_e }}
                    </option>
                @endforeach
            </select>
        </div>

        {{--
        <div class="mb-3">
            <label class="form-label">Escola</label>
            <select name="school_id" class="form-select" required>
                <option value="">-- Escolha --</option>
                @foreach($escolas as $escola)
                    <option value="{{ $escola->id }}">{{ $escola->nome_e }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="mb-3">
            <label for="school_id" class="form-label">Escola</label>
            <select name="school_id" id="school_id" class="form-select" required>
                @foreach($escolas as $escola)
                    <option value="{{ $escola->id }}" {{ old('school_id', $usuario->school_id ?? '') == $escola->id ? 'selected' : '' }}>
                        {{ $escola->nome_e }}
                    </option>
                @endforeach
            </select>
        </div>
        --}}

        <div class="mb-3">
            <label class="form-label">Roles</label><br>
            @foreach($roles as $role)
                <label class="me-3">
                    <input type="checkbox" name="roles[]" value="{{ $role->id }}"> {{ $role->role_name }}
                </label>
            @endforeach
        </div>

        <button type="submit" class="btn btn-success">Salvar</button>
        <a href="{{ route('secretaria.usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection

