@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Roles</h1>
    @include('master.roles._list', ['roles' => $roles])
</div>
@endsection













{{--
@extends('layouts.app')

@section('content')
<h1>Roles</h1>

<a href="{{ route('master.roles.create') }}" class="btn btn-primary">Nova Role</a>

<table class="table mt-3">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach($roles as $role)
        <tr>
            <td>{{ $role->id }}</td>
            <td>{{ $role->role_name }}</td>
            <td>
                <a href="{{ route('master.roles.edit', $role) }}" class="btn btn-sm btn-warning">Editar</a>
                <form action="{{ route('master.roles.destroy', $role) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
--}}