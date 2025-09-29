@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Dashboard Master</h1>

    {{-- Sessão de Escolas --}}
    <div class="card my-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h2>Escolas</h2>
            <a href="{{ route('master.escolas.create') }}" class="btn btn-light btn-sm">+ Nova Escola</a>
        </div>
        <div class="card-body">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Secretaria?</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($escolas as $e)
                        <tr>
                            <td>{{ $e->id }}</td>
                            <td>{{ $e->nome_e }}</td>
                            <td>{{ $e->secretaria_id ? 'Filha' : 'Mãe' }}</td>
                            <td>
                                <a href="{{ route('master.escolas.edit', $e->id) }}" class="btn btn-sm btn-warning">Editar</a>
                                <form method="POST" action="{{ route('master.escolas.destroy', $e->id) }}" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Excluir escola?')">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Sessão de Usuários --}}
    <div class="card my-4">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h2>Usuários</h2>
            <a href="{{ route('master.usuarios.create') }}" class="btn btn-light btn-sm">+ Novo Usuário</a>
        </div>
        <div class="card-body">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>Escola</th>
                        <th>Roles</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $u)
                        <tr>
                            <td>{{ $u->id }}</td>
                            <td>{{ $u->nome_u }}</td>
                            <td>{{ $u->cpf }}</td>
                            <td>{{ $u->escola->nome_e ?? '-' }}</td>
                            <td>{{ $u->roles->pluck('role_name')->implode(', ') }}</td>
                            <td>
                                <a href="{{ route('master.usuarios.edit', $u->id) }}" class="btn btn-sm btn-warning">Editar</a>
                                <form method="POST" action="{{ route('master.usuarios.destroy', $u->id) }}" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Excluir usuário?')">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Sessão de Roles --}}
    <div class="card my-4">
        <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
            <h2>Roles</h2>
            <a href="{{ route('master.roles.create') }}" class="btn btn-light btn-sm">+ Nova Role</a>
        </div>
        <div class="card-body">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $r)
                        <tr>
                            <td>{{ $r->id }}</td>
                            <td>{{ $r->role_name }}</td>
                            <td>
                                <a href="{{ route('master.roles.edit', $r->id) }}" class="btn btn-sm btn-warning">Editar</a>
                                <form method="POST" action="{{ route('master.roles.destroy', $r->id) }}" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Excluir role?')">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Sessão de Associações --}}
    <div class="card my-4">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h2>Associações Escola Mãe ↔ Escola Filha</h2>
        </div>
        <div class="card-body">
            <p>
                Gerencie quais escolas são secretarias (mães) e quais estão vinculadas a elas (filhas).
            </p>
            <div class="d-flex gap-2">
                <a href="{{ route('master.escolas.associacoes') }}" class="btn btn-outline-primary">
                    ➕ Nova Associação
                </a>
                <a href="{{ route('master.escolas.associacoes2') }}" class="btn btn-outline-secondary">
                    👁️ Ver Filhas por Mãe
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
