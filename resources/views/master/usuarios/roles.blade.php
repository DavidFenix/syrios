@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">⚙️ Gerenciar Roles de {{ $usuario->nome_u }}</h1>

    {{-- Escolher escola --}}
    <form method="GET" action="{{ route('master.usuarios.roles.edit', $usuario) }}" class="mb-4">
        <label class="form-label">Escolha a escola:</label>
        <div class="input-group">
            @php
                //regra:preparando a variavel para não deixar excluir role master
                $esc_is_master = null; // inicializa antes do loop
            @endphp

            <select name="school_id" class="form-select" onchange="this.form.submit()">
                <option value="">Selecione...</option>

                @foreach($escolas as $e)
                    @if($schoolIdSelecionada == $e->id && $e->is_master)
                        @php
                            $esc_is_master = $e->is_master;
                        @endphp
                    @endif

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
                    @if($esc_is_master && $role->role_name === 'master')
                        {{--regra:não deixa desmarcar role master do usuario master da secretaria master--}}
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="roles[]"
                                   value="{{ $role->id }}"
                                   id="role_{{ $role->id }}"
                                   {{ in_array($role->id, $rolesSelecionadas) ? 'checked' : '' }}
                                   disabled>
                            <label class="form-check-label" for="role_{{ $role->id }}">
                                {{ ucfirst($role->role_name) }}
                            </label>
                        </div>
                    @else
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
                    @endif
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
