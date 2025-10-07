<form method="get" class="row g-2 mb-3">
  <div class="col-auto">
    <select name="tipo" class="form-select">
      <option value="">Todas</option>
      <option value="mae"   {{ ($filtro ?? '') === 'mae' ? 'selected' : '' }}>Somente Secretarias (mães)</option>
      <option value="filha" {{ ($filtro ?? '') === 'filha' ? 'selected' : '' }}>Somente Escolas (filhas)</option>
    </select>
  </div>
  <div class="col-auto">
    <button type="submit" class="btn btn-primary">Filtrar</button>
  </div>

</form>

<table class="table table-sm table-striped align-middle">
  <thead>
    <tr>
      <th>#</th>
      <th>Nome</th>
      <th>INEP</th>
      <th>CNPJ</th>
      <th>Secretaria</th>
      <th class="text-end">Ações</th>
    </tr>
  </thead>
  <tbody>
  @forelse($escolas as $e)
    <tr>
      <td>{{ $e->id }}</td>
      <td>{{ $e->nome_e }}</td>
      <td>{{ $e->inep }}</td>
      <td>{{ $e->cnpj }}</td>
      <td>{{ optional($e->mae)->nome_e }}</td>
      <td class="text-end">
        @php
            $auth = auth()->user();
        @endphp

        {{--regra:Bloqueia edição da escola master por usuario não-super_master--}}
        {{-- Se for escola normal --}}
        @if(!$e->is_master)
            <a class="btn btn-sm btn-outline-secondary"
               href="{{ route('master.escolas.edit', $e) }}">
                Editar
            </a>
            <form action="{{ route('master.escolas.destroy', $e) }}" method="post"
                  class="d-inline"
                  onsubmit="return confirm('Excluir esta escola?');">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Excluir</button>
            </form>

        {{-- Se for a escola master --}}
        @else
           @if($auth && $auth->is_super_master)
                <a class="btn btn-sm btn-warning"
                   href="{{ route('master.escolas.edit', $e) }}"
                   title="Editar escola principal (apenas Super Master)">
                    ⚙️ Editar Master
                </a>
            @else
                <button class="btn btn-sm btn-secondary" disabled
                        title="Somente o Super Master pode editar a escola principal">
                    🔒
                </button>
            @endif
        @endif


        {{--
        @if(!$e->is_master)
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('master.escolas.edit', $e) }}">Editar</a>
            @if($e->id !== 1)
                <form action="{{ route('master.escolas.destroy', $e) }}" method="post" class="d-inline" onsubmit="return confirm('Excluir esta escola?');">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">Excluir</button>
                </form>
            @else
                <button class="btn btn-sm btn-secondary" disabled title="Você não pode excluir a escola principal">🔒</button>
            @endif
        @else
            <button class="btn btn-sm btn-secondary" disabled title="A escola principal não pode ser editada nem excluída">
                🔒
            </button>
        @endif--}}
      </td>
    </tr>
  @empty
    <tr><td colspan="6" class="text-center text-muted">Nenhum registro.</td></tr>
  @endforelse
  </tbody>
</table>




{{-- Lista de Escolas -}}
<div class="d-flex justify-content-between mb-3">
    <form method="GET" action="{{ route('master.escolas.index') }}">
        <select name="filtro" class="form-select d-inline w-auto">
            <option value="" {{ $filtro===''?'selected':'' }}>Todas</option>
            <option value="mae" {{ $filtro==='mae'?'selected':'' }}>Somente Secretarias (mães)</option>
            <option value="filha" {{ $filtro==='filha'?'selected':'' }}>Somente Escolas (filhas)</option>
        </select>
        <button type="submit" class="btn btn-primary">Filtrar</button>
    </form>
    <a href="{{ route('master.escolas.create') }}" class="btn btn-success">Nova Escola</a>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>INEP</th>
            <th>CNPJ</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach($escolas as $escola)
            <tr>
                <td>{{ $escola->id }}</td>
                <td>{{ $escola->nome_e }}</td>
                <td>{{ $escola->inep }}</td>
                <td>{{ $escola->cnpj }}</td>
                <td>
                    <a href="{{ route('master.escolas.edit', $escola->id) }}" class="btn btn-sm btn-warning">Editar</a>
                    <form action="{{ route('master.escolas.destroy', $escola->id) }}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>


{{-- Lista de Escolas -}}
<div>
    <form method="GET" action="{{ route('master.escolas.index') }}" class="mb-3">
        <select name="filtro" class="form-select d-inline w-auto">
            <option value="" {{ $filtro===''?'selected':'' }}>Todas</option>
            <option value="mae" {{ $filtro==='mae'?'selected':'' }}>Somente Secretarias (mães)</option>
            <option value="filha" {{ $filtro==='filha'?'selected':'' }}>Somente Escolas (filhas)</option>
        </select>
        <button type="submit" class="btn btn-primary">Filtrar</button>
    </form>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>INEP</th>
                <th>CNPJ</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($escolas as $escola)
                <tr>
                    <td>{{ $escola->id }}</td>
                    <td>{{ $escola->nome_e }}</td>
                    <td>{{ $escola->inep }}</td>
                    <td>{{ $escola->cnpj }}</td>
                    <td>
                        <a href="{{ route('master.escolas.edit', $escola->id) }}" class="btn btn-sm btn-warning">Editar</a>
                        <form action="{{ route('master.escolas.destroy', $escola->id) }}" method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
--}}