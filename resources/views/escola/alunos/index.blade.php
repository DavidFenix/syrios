@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Alunos</h1>
    <a href="{{ route('escola.alunos.create') }}" class="btn btn-primary mb-3">➕ Novo Aluno</a>

    <table class="table table-striped">
        <thead><tr><th>ID</th><th>Matrícula</th><th>Nome</th><th>Ações</th></tr></thead>
        <tbody>
        @forelse($alunos as $a)
          <tr>
            <td>{{ $a->id }}</td>
            <td>{{ $a->matricula }}</td>
            <td>{{ $a->nome_a }}</td>
            <td>
              <a href="{{ route('escola.alunos.edit',$a) }}" class="btn btn-sm btn-warning">✏️</a>
              <form action="{{ route('escola.alunos.destroy',$a) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Remover este aluno?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger">🗑</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="4" class="text-center text-muted">Nenhum aluno</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection



{{--
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Alunos</h1>
    <a href="{{ route('escola.alunos.create') }}" class="btn btn-primary mb-3">➕ Novo Aluno</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Matrícula</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($alunos as $aluno)
                <tr>
                    <td>{{ $aluno->id }}</td>
                    <td>{{ $aluno->nome_a }}</td>
                    <td>{{ $aluno->matricula }}</td>
                    <td>
                        <a href="{{ route('escola.alunos.edit', $aluno) }}" class="btn btn-sm btn-warning">Editar</a>
                        <form action="{{ route('escola.alunos.destroy', $aluno) }}" method="post" style="display:inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Excluir aluno?')">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">Nenhum aluno encontrado.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
--}}


{{--
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Alunos</h1>
    <a href="{{ route('escola.alunos.create') }}" class="btn btn-primary mb-3">Novo Aluno</a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Nome</th>
                <th>Matrícula</th>
                <th>Escola</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        @forelse($alunos as $aluno)
            <tr>
                <td>{{ $aluno->id }}</td>
                <td>{{ $aluno->nome_a }}</td>
                <td>{{ $aluno->matricula }}</td>
                <td>{{ $aluno->escola->nome_e ?? '-' }}</td>
                <td>
                    <a href="{{ route('escola.alunos.edit', $aluno) }}" class="btn btn-sm btn-warning">Editar</a>
                    <form action="{{ route('escola.alunos.destroy', $aluno) }}" method="post" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Excluir aluno?')">Excluir</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5">Nenhum aluno encontrado.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
--}}