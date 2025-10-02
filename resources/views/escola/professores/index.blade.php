@extends('layouts.app')

@section('content')
Por enquanto, é o cadastro de usuario que permite cadastrar na tabela professor também se a role professor for marcada.
{{--
<div class="container">
    <h1>Professores</h1>
    <a href="{{ route('escola.professores.create') }}" class="btn btn-primary mb-3">➕ Novo Professor</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuário</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($professores as $prof)
                <tr>
                    <td>{{ $prof->id }}</td>
                    <td>{{ $prof->usuario_id }}</td>
                    <td>
                        <a href="{{ route('escola.professores.edit', $prof) }}" class="btn btn-sm btn-warning">Editar</a>
                        <form action="{{ route('escola.professores.destroy', $prof) }}" method="post" style="display:inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Excluir professor?')">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3">Nenhum professor encontrado.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
--}}
@endsection



{{--
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Professores</h1>
    <a href="{{ route('escola.professores.create') }}" class="btn btn-primary mb-3">Novo Professor</a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Usuário</th>
                <th>Escola</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        @forelse($professores as $prof)
            <tr>
                <td>{{ $prof->id }}</td>
                <td>{{ $prof->usuario->nome_u ?? '-' }}</td>
                <td>{{ $prof->escola->nome_e ?? '-' }}</td>
                <td>
                    <a href="{{ route('escola.professores.edit', $prof) }}" class="btn btn-sm btn-warning">Editar</a>
                    <form action="{{ route('escola.professores.destroy', $prof) }}" method="post" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Excluir professor?')">Excluir</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4">Nenhum professor encontrado.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
--}}