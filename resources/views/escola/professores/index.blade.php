@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Professores da Escola e demais Vinculados</h1>
    <a href="{{ route('escola.usuarios.index') }}" class="btn btn-primary mb-3">➕ Vincular ou Criar Usuário</a>

    @if($mensagem)
      <div class="alert alert-success">
        {{ $mensagem }}
      </div>
    @endif

    <table class="table table-striped" id="tabela-professores-escola">
      <thead>
        <tr>
          <th>ID</th>
          <th>Usuário</th>
          <th>Escola de origem</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        @forelse($professores as $p)
          <tr>
            <td>{{ $p->id }}</td>
            <td>{{ $p->usuario->nome_u ?? '-' }}</td>
            <td>{{ $p->usuario->escola->nome_e ?? '-' }}</td>
            <td>

              @if($p->id !== auth()->id())
              <form action="{{ route('escola.professores.destroy', $p) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Remover este professor?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger">🗑</button>
              </form>
              @else
                <button class="btn btn-sm btn-secondary" disabled title="Você não pode excluir a si mesmo">🔒</button>
              @endif

            </td>
          </tr>
        @empty
          <tr><td colspan="4" class="text-center text-muted">Nenhum professor</td></tr>
        @endforelse
      </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    // Aplica o DataTable com filtro nas colunas Nome(1), CPF(2), Status(3), Roles(4)
    initDataTable('#tabela-professores-escola', {
        order: [[1, 'asc']],
        pageLength: 10
    }, [1, 2]);
});
</script>
@endpush






{{--
@section('content')
<div class="container">
    <h1>Professores da Escola e demais Vinculados</h1>
    <a href="{{ route('escola.usuarios.index') }}" class="btn btn-primary mb-3">➕ Vincular ou Criar Usuário</a>

    <table class="table table-striped">
        <thead><tr><th>ID</th><th>Usuário</th><th>Escola de Origem</th><th>Ações</th></tr></thead>
        <tbody>
        @forelse($professores as $p)
          <tr>
            <td>{{ $p->id }}</td>
            <td>{{ $p->usuario->nome_u ?? '-' }}</td>
            <td>{{ $p->usuario->escola->nome_e ?? '—' }}</td>
            <td>
              <form action="{{ route('escola.professores.destroy',$p) }}" method="POST" class="d-inline" 
                    onsubmit="return confirm('Remover este professor?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger">🗑</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="3" class="text-center text-muted">Nenhum professor</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
--}}


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
@endsection
--}}


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