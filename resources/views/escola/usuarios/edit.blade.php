@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Usuário</h1>

    @php
        $auth = auth()->user();
        $schoolId = session('current_school_id');
        $roles = $usuario->roles->pluck('role_name')->toArray();

        $isNativo = $usuario->school_id == $schoolId;
        $isSelf = $usuario->id === $auth->id;
        $temRoleEscolaAuth = $auth->roles()
            ->wherePivot('school_id', $schoolId)
            ->where('role_name', 'escola')
            ->exists();
        $temRoleEscolaAlvo = $usuario->roles()
            ->wherePivot('school_id', $schoolId)
            ->where('role_name', 'escola')
            ->exists();

        $isVinculado = $usuario->roles()->wherePivot('school_id', $schoolId)->exists() && !$isNativo;
        $bloqueadoPorHierarquia = in_array('master', $roles) || in_array('secretaria', $roles);
        $somenteLeitura = (!$isNativo && !$isSelf) || $bloqueadoPorHierarquia || ($temRoleEscolaAuth && $temRoleEscolaAlvo);
    @endphp

    {{-- 🔹 Cabeçalho informativo --}}
    <div class="alert {{ $somenteLeitura ? 'alert-secondary' : 'alert-info' }}">
        <strong>🧾 Tipo de vínculo:</strong>
        @if($isSelf)
            <span>Você está editando sua própria conta.</span>
        @elseif($isNativo)
            <span>Usuário criado por esta escola.</span>
        @elseif($isVinculado)
            <span>Usuário apenas vinculado à sua escola.</span>
        @else
            <span>Usuário externo — não pertence nem está vinculado à sua escola.</span>
        @endif
    </div>

    {{-- 🚫 Bloqueio total se não tiver permissão --}}
    @if(!$isNativo && !$isSelf && !$isVinculado)
        <div class="alert alert-danger">
            🚫 Você não tem permissão para editar este usuário.
        </div>
        <a href="{{ route('escola.usuarios.index') }}" class="btn btn-secondary">Voltar</a>
        @php return; @endphp
    @endif

    <form method="POST" action="{{ route('escola.usuarios.update', $usuario) }}">
        @csrf
        @method('PUT')

        {{-- Nome --}}
        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="nome_u" class="form-control"
                   value="{{ old('nome_u', $usuario->nome_u) }}"
                   {{ $somenteLeitura ? 'readonly' : '' }}>
        </div>

        {{-- CPF --}}
        <div class="mb-3">
            <label class="form-label">CPF</label>
            <input type="text" class="form-control"
                   value="{{ $usuario->cpf }}" readonly>
        </div>

        {{-- Senha --}}
        <div class="mb-3">
            <label class="form-label">Senha (preencha se desejar alterar)</label>
            <input type="password" name="senha" class="form-control"
                   {{ $somenteLeitura ? 'readonly' : '' }}>
        </div>

        {{-- Status --}}
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" {{ $somenteLeitura ? 'disabled' : '' }}>
                <option value="1" {{ $usuario->status ? 'selected' : '' }}>Ativo</option>
                <option value="0" {{ !$usuario->status ? 'selected' : '' }}>Inativo</option>
            </select>
        </div>

        {{-- Roles agrupadas por escola --}}
        <div class="mb-4">
            <label class="form-label">Papéis (roles) por escola</label>
            @forelse($usuario->roles->groupBy('pivot.school_id') as $sid => $rolesGrupo)
                @php
                    $escola = \App\Models\Escola::find($sid);
                @endphp
                <div class="border rounded p-2 mb-2 bg-light">
                    <strong>{{ $escola->nome_e ?? 'Escola desconhecida' }}</strong>
                    <div class="mt-2">
                        @foreach($rolesGrupo as $r)
                            @php
                                $color = match($r->role_name) {
                                    'master' => 'danger',
                                    'secretaria' => 'primary',
                                    'escola' => 'info',
                                    'professor' => 'success',
                                    'aluno' => 'secondary',
                                    default => 'light'
                                };
                            @endphp
                            <span class="badge bg-{{ $color }}">{{ ucfirst($r->role_name) }}</span>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-muted">Nenhum papel atribuído.</p>
            @endforelse

            {{-- Botão para gerenciar roles --}}
            @if(Route::has('escola.usuarios.roles.edit'))
                <a href="{{ route('escola.usuarios.roles.edit', $usuario->id) }}"
                   class="btn btn-outline-primary btn-sm mt-2">
                    ⚙️ Gerenciar roles
                </a>
            @endif
        </div>

        {{-- Botões --}}
        @if(!$somenteLeitura)
            <button type="submit" class="btn btn-success">💾 Salvar alterações</button>
        @endif
        <a href="{{ route('escola.usuarios.index') }}" class="btn btn-secondary">Voltar</a>
    </form>
</div>
@endsection




{{--
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
--}}