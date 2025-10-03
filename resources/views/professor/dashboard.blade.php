@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">📘 Dashboard do Professor</h1>

    <div class="alert alert-info">
        Bem-vindo, {{ Auth::user()->nome_u ?? 'Professor' }}!
    </div>

    <p>
        Aqui será o painel inicial dos professores.
        Você poderá futuramente visualizar suas turmas, disciplinas, registros e notificações.
    </p>

    <ul>
        <li><strong>Turmas</strong> que você leciona</li>
        <li><strong>Disciplinas</strong> associadas</li>
        <li><strong>Ocorrências</strong> e registros</li>
    </ul>
</div>
@endsection
