{{-- Lista de Usuários --}}
<div>
    <table class="table table-striped">
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
            @foreach($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->id }}</td>
                    <td>{{ $usuario->nome_u }}</td>
                    <td>{{ $usuario->cpf }}</td>
                    <td>{{ $usuario->escola->nome_e ?? '-' }}</td>
                    <td>
                        @foreach($usuario->roles as $role)
                            <span class="badge bg-info">{{ $role->role_name }}</span>
                        @endforeach
                    </td>
                    <td>
                        <a href="{{ route('master.usuarios.edit', $usuario->id) }}" class="btn btn-sm btn-warning">Editar</a>
                        <form action="{{ route('master.usuarios.destroy', $usuario->id) }}" method="POST" style="display:inline">
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
