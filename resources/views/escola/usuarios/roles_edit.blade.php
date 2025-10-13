@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">⚙️ Gerenciar Roles de {{ $usuario->nome_u }}</h1>

    {{-- 🔹 Alerta contexto --}}
    <div class="alert alert-info">
        <strong>Escola atual:</strong> {{ $escolaAtual->nome_e ?? 'Desconhecida' }}
        <br>
        <strong>Usuário:</strong> {{ $usuario->nome_u }} (CPF: {{ $usuario->cpf }})
    </div>

    {{-- 🔸 Visão geral das roles do usuário --}}
    @if($usuario->roles->isNotEmpty())
        <div class="card mb-4 border border-primary shadow-sm">
            <div class="card-header bg-primary text-white">
                📊 Visão geral de roles do usuário
            </div>
            <div class="card-body p-2">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Escola</th>
                            <th>Roles</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuario->roles->groupBy('pivot.school_id') as $sid => $rolesGrupo)
                            @php $escola = \App\Models\Escola::find($sid); @endphp
                            <tr>
                                <td>{{ $escola->nome_e ?? '—' }}</td>
                                <td>
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
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- 🔹 Formulário de roles da escola atual --}}
    <form method="POST" action="{{ route('escola.usuarios.roles.update', $usuario->id) }}">
        @csrf
        <input type="hidden" name="school_id" value="{{ $escolaAtual->id }}">

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <strong>🧩 Papéis disponíveis nesta escola</strong>
            </div>
            <div class="card-body">
                @php
                    $auth = auth()->user();
                    $authIsSame = $auth->id === $usuario->id;
                    $rolesSelecionadas = $usuario->roles()
                        ->wherePivot('school_id', $escolaAtual->id)
                        ->pluck('role_id')
                        ->toArray();
                @endphp

                @foreach($roles as $role)
                    @php
                        $isRestrita = in_array($role->role_name, ['master', 'secretaria']);
                        $checked = in_array($role->id, $rolesSelecionadas);
                        $disabled = false;

                        // 🔒 regras de bloqueio
                        if ($isRestrita) {
                            $disabled = true;
                        }

                        // não pode remover sua própria role 'escola'
                        if ($authIsSame && $role->role_name === 'escola' && $checked) {
                            $disabled = true;
                        }
                    @endphp

                    <div class="form-check mb-2">
                        <input type="checkbox"
                               class="form-check-input"
                               name="roles[]"
                               value="{{ $role->id }}"
                               id="role_{{ $role->id }}"
                               {{ $checked ? 'checked' : '' }}
                               {{ $disabled ? 'disabled' : '' }}>
                        <label class="form-check-label" for="role_{{ $role->id }}">
                            {{ ucfirst($role->role_name) }}
                            @if($disabled)
                                <span class="text-muted small">(protegida)</span>
                            @endif
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Botões --}}
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-success">💾 Salvar alterações</button>
            <a href="{{ route('escola.usuarios.index') }}" class="btn btn-secondary ms-2">Voltar</a>
        </div>
    </form>

    <p class="text-muted mt-4 small">
        💡 Roles restritas (como <strong>master</strong> e <strong>secretaria</strong>) não podem ser alteradas a partir do painel da escola.
        <br>Somente o usuário <em>Master</em> ou a <em>Secretaria</em> superior podem gerenciá-las.
    </p>
</div>
@endsection
