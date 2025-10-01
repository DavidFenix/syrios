@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Usuários das Escolas Filhas - {{ $secretaria->nome_e }}</h1>

    <a href="{{ route('secretaria.usuarios.create') }}" class="btn btn-primary mb-3">Novo Usuário</a>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>CPF</th>
                <th>Escola</th>
                <th>Roles</th>
                <th class="text-end">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->id }}</td>
                    <td>{{ $usuario->nome_u }}</td>
                    <td>{{ $usuario->cpf }}</td>
                    <td>{{ $usuario->escola->nome_e ?? '-' }}</td>
                    <td>
                        @foreach($usuario->roles as $role)
                            <span class="badge bg-secondary">{{ $role->role_name }}</span>
                        @endforeach
                    </td>
                    <td class="text-end">
                        <a href="{{ route('secretaria.usuarios.edit', $usuario) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                        <form action="{{ route('secretaria.usuarios.destroy', $usuario) }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir este usuário?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted">Nenhum usuário encontrado.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection




{{--
@extends('layouts.app')
@section('title','Usuários da Secretaria')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Usuários</h1>
  <a href="{{ route('secretaria.usuarios.create') }}" class="btn btn-primary">Novo usuário</a>
</div>

<table class="table table-sm table-striped align-middle">
  <thead>
    <tr>
      <th>#</th>
      <th>Nome</th>
      <th>CPF</th>
      <th>Escola</th>
      <th>Roles</th>
      <th class="text-end">Ações</th>
    </tr>
  </thead>
  <tbody>
  @forelse($usuarios as $u)
    <tr>
      <td>{{ $u->id }}</td>
      <td>{{ $u->nome_u }}</td>
      <td>{{ $u->cpf }}</td>
      <td>{{ $u->escola->nome_e }}</td>
      <td>
        @foreach($u->roles as $r)
          <span class="badge bg-info text-dark">{{ $r->role_name }}</span>
        @endforeach
      </td>
      <td class="text-end">
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('secretaria.usuarios.edit', $u) }}">Editar</a>
        <form action="{{ route('secretaria.usuarios.destroy', $u) }}" method="post" class="d-inline" onsubmit="return confirm('Excluir este usuário?');">
          @csrf @method('DELETE')
          <button class="btn btn-sm btn-outline-danger">Excluir</button>
        </form>
      </td>
    </tr>
  @empty
    <tr><td colspan="6" class="text-center text-muted">Nenhum usuário cadastrado.</td></tr>
  @endforelse
  </tbody>
</table>
@endsection
--}}