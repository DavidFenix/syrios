@extends('layouts.app')

@section('content')
<div class="container">
    <h1>🔗 Associação Escola Mãe ↔ Escola Filha</h1>

    {{-- mensagens --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulário --}}
    <form method="post" action="{{ route('master.escolas.associar') }}" class="row g-3 mb-4">
        @csrf
        <div class="col-md-5">
            <label>Escola Mãe (Secretaria)</label>
            <select name="mae_id" class="form-control" required>
                <option value="">-- selecione --</option>
                @foreach($maes as $m)
                    <option value="{{ $m->id }}">{{ $m->nome_e }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-5">
            <label>Escola Filha</label>
            <select name="filha_id" class="form-control" required>
                <option value="">-- selecione --</option>
                @foreach(App\Models\Escola::whereNull('secretaria_id')->get() as $f)
                    <option value="{{ $f->id }}">{{ $f->nome_e }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Associar</button>
        </div>
    </form>

    {{-- Listagem --}}
    <h3>📋 Lista de Escolas Mães e suas Filhas</h3>
    @foreach($maes as $m)
        <h5>{{ $m->nome_e }}</h5>
        <ul>
            @forelse($m->filhas as $f)
                <li>{{ $f->nome_e }}</li>
            @empty
                <li><i>Nenhuma filha associada</i></li>
            @endforelse
        </ul>
    @endforeach
</div>
@endsection
