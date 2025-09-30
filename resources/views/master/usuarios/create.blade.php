@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Novo Usuário</h1>

    <form method="POST" action="{{ route('master.usuarios.store') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nome*</label>
            <input type="text" name="nome_u" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">CPF*</label>
            <input type="text" name="cpf" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Senha*</label>
            <input type="password" name="senha" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Escola*</label>
            <select name="school_id" class="form-control">
                @foreach($escolas as $escola)
                    <option value="{{ $escola->id }}">{{ $escola->nome_e }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Função(Papel/Destino)</label><br>
            @foreach($roles as $role)
                <input type="checkbox" name="roles[]" value="{{ $role->id }}"> {{ $role->role_name }}<br>
            @endforeach
        </div>

        <button type="submit" class="btn btn-success">Salvar</button>
        <a href="{{ route('master.usuarios.index') }}" class="btn btn-secondary">Voltar</a>
    </form>
</div>
@endsection
