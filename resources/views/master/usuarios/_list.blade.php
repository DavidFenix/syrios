{{-- Lista de Usuários --}}
<!--a href="{{ route('master.usuarios.create') }}" class="btn btn-success mb-3">+ Novo Usuário</a-->
<form method="get" class="row g-2 mb-3">
  <div class="col-auto">
    <select name="tipo" class="form-select">
      <option value="">Todos</option>
      <option value="mae"   {{ ($filtro ?? '') === 'mae' ? 'selected' : '' }}>Somente Secretarias (mães)</option>
      <option value="filha" {{ ($filtro ?? '') === 'filha' ? 'selected' : '' }}>Somente Escolas (filhas)</option>
    </select>
  </div>
  <div class="col-auto">
    <button type="submit" class="btn btn-primary">Filtrar</button>
  </div>
</form>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>CPF</th>
            <th>Status</th>
            <th>Escola</th>
            <th>Papel</th>
            <th class="text-end">Ações</th>
        </tr>
    </thead>
    <tbody>
        @php
            $auth = auth()->user();
        @endphp

        @foreach($usuarios as $usuario)
            <tr>
                <td>{{ $usuario->id }}</td>
                <td>{{ $usuario->nome_u }}</td>
                <td>{{ $usuario->cpf }}</td>
                <td>{{ $usuario->status ? 'Ativo' : 'Inativo' }}</td>
                <td>{{ $usuario->escola->nome_e ?? '-' }}</td>
                <td>{{ $usuario->roles->pluck('role_name')->implode(', ') }}</td>

                {{-- ✅ célula correta para ações --}}
                <td class="text-end">

                    {{-- 🚫 regra: Impede o usuário de excluir a si mesmo --}}
                    @if($auth && $auth->id === $usuario->id)
                        <a href="{{ route('master.usuarios.edit', $usuario) }}" class="btn btn-sm btn-outline-secondary">
                            Editar
                        </a>
                        <button class="btn btn-sm btn-secondary" disabled title="Você não pode excluir sua própria conta">
                            🔒
                        </button>

                    {{-- 🔒 regra: Super Master (proteções especiais) --}}
                    @elseif($usuario->is_super_master)
                        @if($auth && $auth->is_super_master && $auth->id !== $usuario->id)
                            {{-- Super Master pode gerenciar outros Super Masters (não a si mesmo) --}}
                            <a href="{{ route('master.usuarios.edit', $usuario) }}" class="btn btn-sm btn-warning" title="Editar Super Master">
                                ⚙️ Editar Master
                            </a>
                            <form action="{{ route('master.usuarios.destroy', $usuario) }}" method="post" class="d-inline"
                                  onsubmit="return confirm('Excluir o Super Master?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Excluir</button>
                            </form>
                        @elseif($auth && $auth->id === $usuario->id)
                            {{-- Ele mesmo --}}
                            <a href="{{ route('master.usuarios.edit', $usuario) }}" class="btn btn-sm btn-warning">
                                ⚙️ Editar Master
                            </a>
                            <button class="btn btn-sm btn-secondary" disabled title="Você não pode excluir sua própria conta">
                                🔒
                            </button>
                        @else
                            {{-- Qualquer outro tipo de usuário --}}
                            <button class="btn btn-sm btn-secondary" disabled title="Somente o Super Master pode editar ou excluir este usuário">
                                🔒
                            </button>
                        @endif

                    {{-- 🔒 regra: Um Master comum não pode editar ou excluir outro Master --}}
                    @elseif($usuario->roles->pluck('role_name')->contains('master') && !$auth->is_super_master)
                        <button class="btn btn-sm btn-secondary" disabled title="Apenas o Super Master pode gerenciar outros Masters">
                            🔒
                        </button>

                    {{-- ✅ regra: Usuário comum (permitido editar/excluir) --}}
                    @else
                        <a href="{{ route('master.usuarios.edit', $usuario) }}" class="btn btn-sm btn-outline-secondary">
                            Editar
                        </a>
                        <form action="{{ route('master.usuarios.destroy', $usuario) }}" method="post" class="d-inline"
                              onsubmit="return confirm('Excluir este usuário?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Excluir</button>
                        </form>
                    @endif

                </td>

            </tr>
        @endforeach
    </tbody>
</table>



{{-- Lista de Usuários -}}
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
--}}