@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">✏️ Editar Usuário</h1>
    
    <form method="POST" action="{{ route('master.usuarios.update', $usuario) }}" class="card card-body shadow-sm">
        @csrf
        @method('PUT')

        {{-- Dados básicos --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Nome*</label>
                <input type="text" name="nome_u" class="form-control" value="{{ old('nome_u', $usuario->nome_u) }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">CPF*</label>
                <input type="text" name="cpf" class="form-control" value="{{ old('cpf', $usuario->cpf) }}" required maxlength="20">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Senha (preencha apenas se quiser trocar)</label>
                <input type="password" name="senha" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label">Status*</label>
                {{--regra:não deixa desativar o usuario super master--}}
                @if($usuario->is_super_master)
                    <select disabled name="status" class="form-select">
                        <option value="1" {{ old('status', $usuario->status) == 1 ? 'selected' : '' }}>Ativo</option>
                        <option value="0" {{ old('status', $usuario->status) == 0 ? 'selected' : '' }}>Inativo</option>
                    </select>
                    {{-- 👇 campo oculto com o valor real --}}
                    <input type="hidden" name="status" value="{{ old('status', $usuario->status) }}">
                @else
                    <select name="status" class="form-select">
                        <option value="1" {{ old('status', $usuario->status) == 1 ? 'selected' : '' }}>Ativo</option>
                        <option value="0" {{ old('status', $usuario->status) == 0 ? 'selected' : '' }}>Inativo</option>
                    </select>
                @endif
                
            </div>
        </div>

        {{-- Escola principal --}}
        <div class="mb-4">
            <label class="form-label">Escola de Origem</label>
            {{---regra:não deixa trocar escola de origem do usuario super marter--}}
            @if($usuario->is_super_master)
                @php
                    // Busca a escola vinculada ao usuário
                    $escolaUsuario = $escolas->firstWhere('id', old('school_id', $usuario->school_id));
                @endphp

                {{-- Exibe o nome da escola apenas para informação --}}
                <input type="text" class="form-control" value="{{ $escolaUsuario->nome_e ?? 'Desconhecida' }}" disabled>

                {{-- Envia o school_id mesmo com o campo visual desativado --}}
                <input type="hidden" name="school_id" id="school_id" value="{{ $escolaUsuario->id ?? $usuario->school_id }}">
            @else
                <select name="school_id" id="school_id" class="form-select" required>
                    <option value="">Selecione...</option>
                    @foreach($escolas as $escola)
                        <option value="{{ $escola->id }}" {{ old('school_id', $usuario->school_id) == $escola->id ? 'selected' : '' }}>
                            {{ $escola->nome_e }}
                        </option>
                    @endforeach
                </select>
            @endif

        </div>

        {{-- Papéis agrupados por escola --}}
        <div class="mb-4">
            <h5 class="mb-3">🧩 Papéis (Roles) - Apenas informe::sem alterações aqui</h5>

            {{-- Agrupa roles por escola --}}
            @php
                $rolesPorEscola = $usuario->roles->groupBy('pivot.school_id');
            @endphp

            {{-- Escolas já vinculadas --}}
            @foreach($rolesPorEscola as $schoolId => $rolesGrupo)
                <div class="border rounded p-3 mb-3 bg-light">
                    <strong>Escola: {{ optional($escolas->firstWhere('id', $schoolId))->nome_e ?? 'Desconhecida' }}</strong>
                    <div class="mt-2">
                        @foreach($roles as $role)
                            @php
                                $checked = $rolesGrupo->pluck('id')->contains($role->id);
                            @endphp
                            <div class="form-check form-check-inline">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="roles[]"
                                       value="{{ $role->id }}"
                                       id="role_{{ $schoolId }}_{{ $role->id }}"
                                       {{ $checked ? 'checked' : '' }}
                                       disabled> {{-- 👈 desativa o checkbox --}}
                                <label class="form-check-label" for="role_{{ $schoolId }}_{{ $role->id }}">
                                    {{ ucfirst($role->role_name) }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            {{-- Caso o usuário ainda não tenha vínculos --}}
            @if($rolesPorEscola->isEmpty())
                <div class="alert alert-info">
                    Este usuário ainda não possui papéis atribuídos.  
                    Use os checkboxes abaixo para definir novos papéis na escola principal selecionada acima.
                </div>

                @foreach($roles as $role)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input"
                               type="checkbox"
                               name="roles[]"
                               value="{{ $role->id }}"
                               id="role_{{ $role->id }}">
                        <label class="form-check-label" for="role_{{ $role->id }}">
                            {{ ucfirst($role->role_name) }}
                        </label>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="d-flex justify-content-end mt-3">
            <button type="submit" class="btn btn-success me-2">💾 Salvar Alterações</button>
            <a href="{{ route('master.usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection




{{--
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
            <select disabled name="school_id" class="form-control">
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
                <input disabled type="checkbox" name="roles[]" value="{{ $role->id }}"
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
--}}