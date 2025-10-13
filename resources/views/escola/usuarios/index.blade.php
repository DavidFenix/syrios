@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Usuários da Escola</h1>

    <a href="{{ route('escola.usuarios.create') }}" class="btn btn-primary mb-3">➕ Novo Usuário</a>

    <table class="table table-striped align-middle" id="tabela-usuarios-escola">
        <thead>
            <tr>
                <th>#</th>
                <th>Nome</th>
                <th>CPF</th>
                <th>Status</th>
                <th>Roles</th>
                <th class="text-end">Ações</th>
            </tr>
        </thead>
        <tbody>
        @forelse($usuarios as $u)
            @php
                $auth = auth()->user();
                $roles = $u->roles->pluck('role_name')->toArray();
                $isAuth = $auth->id === $u->id;
                $temRoleEscolaAuth = $auth->roles()
                    ->wherePivot('school_id', session('current_school_id'))
                    ->where('role_name', 'escola')
                    ->exists();
                $temRoleEscolaAlvo = $u->roles()
                    ->wherePivot('school_id', session('current_school_id'))
                    ->where('role_name', 'escola')
                    ->exists();
                $bloqueadoPorHierarquia = in_array('master', $roles) || in_array('secretaria', $roles);
                $naoPodeExcluir = $isAuth || ($temRoleEscolaAuth && $temRoleEscolaAlvo) || $bloqueadoPorHierarquia;
            @endphp

            <tr class="{{ $isAuth ? 'table-secondary' : '' }}">
                <td>{{ $u->id }}</td>
                <td>{{ $u->nome_u }}</td>
                <td>{{ $u->cpf }}</td>
                <td>
                    @if($u->status)
                        <span class="badge bg-success">Ativo</span>
                    @else
                        <span class="badge bg-danger">Inativo</span>
                    @endif
                </td>
                <td>
                    @foreach($roles as $r)
                        @php
                            $color = match($r) {
                                'master' => 'danger',
                                'secretaria' => 'primary',
                                'escola' => 'info',
                                'professor' => 'success',
                                'aluno' => 'secondary',
                                default => 'dark'
                            };
                        @endphp
                        <span class="badge bg-{{ $color }}">{{ ucfirst($r) }}</span>
                    @endforeach
                </td>
                <td class="text-end">
                    {{-- ✏️ Editar --}}
                    @if(!$isAuth)
                        <a href="{{ route('escola.usuarios.edit',$u) }}" 
                           class="btn btn-sm btn-outline-warning"
                           title="Editar usuário">✏️</a>
                    @else
                        <button class="btn btn-sm btn-secondary" disabled title="Você não pode editar a si mesmo">🔒</button>
                    @endif

                    {{-- 🗑 Excluir --}}
                    @if($naoPodeExcluir)
                        <button class="btn btn-sm btn-secondary" disabled 
                                title="Você não pode excluir este usuário">🔒</button>
                    @else
                        <form action="{{ route('escola.usuarios.destroy', $u) }}" 
                              method="POST" class="d-inline"
                              onsubmit="return confirm('Remover este usuário ou vínculo?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Excluir usuário">🗑</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center text-muted">Nenhum usuário encontrado</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    // Aplica o DataTable com filtro nas colunas Nome(1), CPF(2), Status(3), Roles(4)
    initDataTable('#tabela-usuarios-escola', { 
        order: [[1, 'asc']],
        pageLength: 10 // inicia com 10 registros por página
    }, [1, 2, 3, 4]);
});
</script>
@endpush










{{--
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Usuários da Escola</h1>
    <a href="{{ route('escola.usuarios.create') }}" class="btn btn-primary mb-3">➕ Novo Usuário</a>

    <table class="table table-striped" id="tabela-usuarios-escola">
        <thead>
            <tr>
                <th>ID</th><th>Nome</th><th>CPF</th><th>Status</th><th>Roles</th><th>Ações</th>
            </tr>
        </thead>
        <tbody>
        @forelse($usuarios as $u)
            <tr>
                <td>{{ $u->id }}</td>
                <td>{{ $u->nome_u }}</td>
                <td>{{ $u->cpf }}</td>
                <td>{{ $u->status ? 'Ativo':'Inativo' }}</td>
                <td>{{ implode(', ', $u->roles->pluck('role_name')->toArray()) }}</td>
                <td>
                    
                    @if($u->id !== auth()->id())
                        <a href="{{ route('escola.usuarios.edit',$u) }}" class="btn btn-sm btn-warning">✏️</a>
                        <form action="{{ route('escola.usuarios.destroy', $u) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Remover este usuário?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">🗑</button>
                        </form>
                    @else
                        <button class="btn btn-sm btn-secondary" disabled title="Você não pode editar a si mesmo aqui">🔒</button>
                        <button class="btn btn-sm btn-secondary" disabled title="Você não pode excluir a si mesmo">🔒</button>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center text-muted">Nenhum usuário</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    // inicializa com o script global do public/js/datatables-init.js
    // colunas filtráveis: Nome(1), CPF(2), Escola(3), Roles(4), CNPJ(5)
    initDataTable('#tabela-usuarios-escola', { order: [[1, 'asc']] }, [1, 2, 3, 4]);
});
</script>
@endpush
--}}








{{--
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Usuários da Escola</h1>
    <a href="{{ route('escola.usuarios.create') }}" class="btn btn-primary mb-3">Novo Usuário</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nome</th>
                <th>CPF</th>
                <th>Status</th>
                <th>Roles</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        @foreach($usuarios as $u)
            <tr>
                <td>{{ $u->nome_u }}</td>
                <td>{{ $u->cpf }}</td>
                <td>{{ $u->status ? 'Ativo' : 'Inativo' }}</td>
                <td>
                    @foreach($u->roles as $r)
                        <span class="badge bg-info">{{ $r->role_name }}</span>
                    @endforeach
                </td>
                <td>
                    <a href="{{ route('escola.usuarios.edit',$u) }}" class="btn btn-sm btn-warning">Editar</a>
                    <form action="{{ route('escola.usuarios.destroy',$u) }}" method="POST" style="display:inline-block;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"
                            onclick="return confirm('Excluir este usuário?')">Excluir</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
--}}