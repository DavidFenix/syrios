@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Turmas</h1>
    <a href="{{ route('escola.turmas.create') }}" class="btn btn-primary mb-3">➕ Nova Turma</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Série/Turma</th>
                <th>Turno</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($turmas as $turma)
                <tr>
                    <td>{{ $turma->id }}</td>
                    <td>{{ $turma->serie_turma }}</td>
                    <td>{{ $turma->turno }}</td>
                    <td>
                        <a href="{{ route('escola.turmas.edit', $turma) }}" class="btn btn-sm btn-warning">Editar</a>
                        <form action="{{ route('escola.turmas.destroy', $turma) }}" method="post" style="display:inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Excluir turma?')">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">Nenhuma turma cadastrada.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection



{{--

@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Turmas</h1>
    <a href="{{ route('escola.turmas.create') }}" class="btn btn-primary mb-3">Nova Turma</a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Série</th>
                <th>Turno</th>
                <th>Escola</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        @forelse($turmas as $turma)
            <tr>
                <td>{{ $turma->id }}</td>
                <td>{{ $turma->serie_turma }}</td>
                <td>{{ $turma->turno }}</td>
                <td>{{ $turma->escola->nome_e ?? '-' }}</td>
                <td>
                    <a href="{{ route('escola.turmas.edit', $turma) }}" class="btn btn-sm btn-warning">Editar</a>
                    <form action="{{ route('escola.turmas.destroy', $turma) }}" method="post" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Excluir turma?')">Excluir</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5">Nenhuma turma encontrada.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
--}}