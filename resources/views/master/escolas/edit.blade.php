@extends('layouts.app')
@section('title','Editar instituição')

@section('content')
<h1 class="h4 mb-3">Editar Escola / Secretaria</h1>

<form method="post" class="row g-3" action="{{ route('master.escolas.update', $escola) }}">
 @csrf
 @method('PUT')

 <div class="col-md-6">
   <label class="form-label">Nome*</label>
   <input name="nome_e" class="form-control" required value="{{ old('nome_e', $escola->nome_e) }}">
 </div>
 <div class="col-md-3">
   <label class="form-label">INEP</label>
   <input name="inep" class="form-control" value="{{ old('inep', $escola->inep) }}">
 </div>
 <div class="col-md-3">
   <label class="form-label">CNPJ</label>
   <input name="cnpj" class="form-control" value="{{ old('cnpj', $escola->cnpj) }}">
 </div>
 <div class="col-md-4">
   <label class="form-label">Cidade</label>
   <input name="cidade" class="form-control" value="{{ old('cidade', $escola->cidade) }}">
 </div>
 <div class="col-md-4">
   <label class="form-label">Estado</label>
   <input name="estado" class="form-control" value="{{ old('estado', $escola->estado) }}">
 </div>
 <div class="col-md-8">
   <label class="form-label">Endereço</label>
   <input name="endereco" class="form-control" value="{{ old('endereco', $escola->endereco) }}">
 </div>
 <div class="col-md-4">
   <label class="form-label">Telefone</label>
   <input name="telefone" class="form-control" value="{{ old('telefone', $escola->telefone) }}">
 </div>

{{--nao permite definir mãe se for uma escola master(gerente maximo do sistema)--}}
 @if($escola->id != 1)
 <div class="col-md-6">
   <label class="form-label">Vincular a uma Secretaria (opcional)</label>
   <select name="secretaria_id" class="form-select">
     <option value="">— Sem secretaria (é MÃE) —</option>
     @foreach($maes as $m)
       <option value="{{ $m->id }}" {{ old('secretaria_id', $escola->secretaria_id)==$m->id?'selected':'' }}>
         {{ $m->nome_e }}
       </option>
     @endforeach
   </select>
 </div>
 @endif

 <div class="col-12">
   <button class="btn btn-primary">Salvar</button>
   <a href="{{ route('master.escolas.index') }}" class="btn btn-secondary">Voltar</a>
 </div>
</form>
@endsection
