{{-- Lista de Escolas --}}
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
