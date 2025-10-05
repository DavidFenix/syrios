@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">⚙️ Gerenciar Roles de {{ $usuario->nome_u }}</h1>

    {{-- Escolher escola --}}
    <form method="GET" action="{{ route('master.usuarios.roles.edit', $usuario) }}" class="mb-4">
        <label class="form-label">Escolha a escola:</label>
        <div class="input-group">
            <select name="school_id" class="form-select" onchange="this.form.submit()">
                <option value="">Selecione...</option>
                @foreach($escolas as $e)
                    <option value="{{ $e->id }}" {{ $schoolIdSelecionada == $e->id ? 'selected' : '' }}>
                        {{ $e->nome_e }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- Exibir roles apenas se escola selecionada --}}
    @if($schoolIdSelecionada)
        <form method="POST" action="{{ route('master.usuarios.roles.update', $usuario) }}">
            @csrf
            <input type="hidden" name="school_id" value="{{ $schoolIdSelecionada }}">

            <div class="card card-body shadow-sm mb-4">
                <h5>🧩 Papéis disponíveis para esta escola</h5>

                @foreach($roles as $role)
                    <div class="form-check">
                        <input class="form-check-input"
                               type="checkbox"
                               name="roles[]"
                               value="{{ $role->id }}"
                               id="role_{{ $role->id }}"
                               {{ in_array($role->id, $rolesSelecionadas) ? 'checked' : '' }}>
                        <label class="form-check-label" for="role_{{ $role->id }}">
                            {{ ucfirst($role->role_name) }}
                        </label>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-success">💾 Salvar Alterações</button>
                <a href="{{ route('master.usuarios.index') }}" class="btn btn-secondary ms-2">Voltar</a>
            </div>
        </form>
    @else
        <div class="alert alert-info">
            👈 Selecione uma escola acima para ver e editar as roles do usuário.
        </div>
    @endif

</div>
@endsection
