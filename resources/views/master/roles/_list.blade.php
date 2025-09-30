{{-- Lista de Roles --}}
<!--a href="{{ route('master.roles.create') }}" class="btn btn-success mb-3">+ Nova Role</a-->

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome da Role</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach($roles as $role)
            <tr>
                <td>{{ $role->id }}</td>
                <td>{{ $role->role_name }}</td>
                <td>
                    <a href="{{ route('master.roles.edit', $role->id) }}" class="btn btn-warning btn-sm">Editar</a>
                    <form action="{{ route('master.roles.destroy', $role->id) }}" method="POST" style="display:inline-block;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Excluir esta role?')">Excluir</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>



{{-- Lista de Roles -}}
<div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Role</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $role)
                <tr>
                    <td>{{ $role->id }}</td>
                    <td>{{ $role->role_name }}</td>
                    <td>
                        <a href="{{ route('master.roles.edit', $role->id) }}" class="btn btn-sm btn-warning">Editar</a>
                        <form action="{{ route('master.roles.destroy', $role->id) }}" method="POST" style="display:inline">
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