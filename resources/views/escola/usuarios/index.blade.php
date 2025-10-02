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


{{--
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Usuários da Escola {{ $escola->nome_e }}</h1>
    <a href="{{ route('escola.usuarios.create') }}" class="btn btn-primary mb-3">Novo Usuário</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nome</th>
                <th>CPF</th>
                <th>Roles</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        @foreach($usuarios as $u)
            <tr>
                <td>{{ $u->nome_u }}</td>
                <td>{{ $u->cpf }}</td>
                <td>{{ $u->roles->pluck('role_name')->join(', ') }}</td>
                <td>
                    <a href="{{ route('escola.usuarios.edit',$u) }}" class="btn btn-sm btn-secondary">Editar</a>
                    <form action="{{ route('escola.usuarios.destroy',$u) }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir este usuário?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger">Excluir</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
--}}