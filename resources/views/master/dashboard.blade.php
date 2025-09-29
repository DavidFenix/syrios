@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Dashboard Master</h1>

    {{-- Escolas --}}
    <div class="card my-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between">
            <h2>Escolas</h2>
            <a href="{{ route('master.escolas.index') }}" class="btn btn-light btn-sm">Gerenciar</a>
        </div>
        <div class="card-body">
            @include('master.escolas._list', ['escolas' => $escolas, 'filtro' => $filtro])
        </div>
    </div>

    {{-- Usuários --}}
    <div class="card my-4">
        <div class="card-header bg-success text-white">
            <h2>Usuários</h2>
        </div>
        <div class="card-body">
            @include('master.usuarios._list', ['usuarios' => $usuarios])
        </div>
    </div>

    {{-- Roles --}}
    <div class="card my-4">
        <div class="card-header bg-warning text-dark">
            <h2>Roles</h2>
        </div>
        <div class="card-body">
            @include('master.roles._list', ['roles' => $roles])
        </div>
    </div>

    {{-- Sessão de Associações --}}
    <div class="card my-4">
        <div class="card-header bg-info text-dark d-flex justify-content-between">
            <h2>Associações</h2>
        </div>
        <div class="card-body">
            <a href="{{ route('master.escolas.associacoes') }}" class="btn btn-primary">Associar Escola Mãe ↔ Filha</a>
            <a href="{{ route('master.escolas.associacoes2') }}" class="btn btn-secondary">Ver Escolas Filhas</a>
        </div>
    </div>
</div>
@endsection











{{--
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Dashboard Master</h1>

    {{-- Sessão de Escolas -}}
    <div class="card my-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between">
            <h2>Escolas</h2>
            <a href="{{ route('master.escolas.index') }}" class="btn btn-light btn-sm">Gerenciar</a>
        </div>
        <div class="card-body">
            @include('master.escolas._list', ['escolas' => $escolas])
        </div>
    </div>

    {{-- Sessão de Usuários -}}
    <div class="card my-4">
        <div class="card-header bg-success text-white d-flex justify-content-between">
            <h2>Usuários</h2>
            <a href="{{ route('master.usuarios.index') }}" class="btn btn-light btn-sm">Gerenciar</a>
        </div>
        <div class="card-body">
            @include('master.usuarios._list', ['usuarios' => $usuarios])
        </div>
    </div>

    {{-- Sessão de Roles -}}
    <div class="card my-4">
        <div class="card-header bg-warning text-dark d-flex justify-content-between">
            <h2>Roles</h2>
            <a href="{{ route('master.roles.index') }}" class="btn btn-light btn-sm">Gerenciar</a>
        </div>
        <div class="card-body">
            @include('master.roles._list', ['roles' => $roles])
        </div>
    </div>

    {{-- Sessão de Associações -}}
    <div class="card my-4">
        <div class="card-header bg-info text-dark d-flex justify-content-between">
            <h2>Associações</h2>
        </div>
        <div class="card-body">
            <a href="{{ route('master.escolas.associacoes') }}" class="btn btn-primary">Associar Escola Mãe ↔ Filha</a>
            <a href="{{ route('master.escolas.associacoes2') }}" class="btn btn-secondary">Ver Escolas Filhas</a>
        </div>
    </div>
</div>
@endsection
--}}