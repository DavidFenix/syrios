<!-- resources/views/layouts/app.blade.php -->

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syrios - Painel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="{{ dashboard_route() }}">⚡ Syrios</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMaster"
                aria-controls="navbarMaster" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMaster">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                {{-- MASTER --}}
                @if(session('current_role') === 'master')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('master/escolas*') ? 'active' : '' }}"
                           href="{{ route('master.escolas.index') }}">
                            🏫 Escolas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('master/roles*') ? 'active' : '' }}"
                           href="{{ route('master.roles.index') }}">
                            ⚙️ Roles
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('master/usuarios*') ? 'active' : '' }}"
                           href="{{ route('master.usuarios.index') }}">
                            👥 Usuários
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('master/escolas-associacoes*') ? 'active' : '' }}"
                           href="{{ route('master.escolas.associacoes') }}">
                            🔗 Associações
                        </a>
                    </li>
                @endif

                {{-- SECRETARIA --}}
                @if(session('current_role') === 'secretaria')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('secretaria/escolas*') ? 'active' : '' }}"
                           href="{{ route('secretaria.escolas.index') }}">
                            🏫 Escolas Filhas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('secretaria/usuarios*') ? 'active' : '' }}"
                           href="{{ route('secretaria.usuarios.index') }}">
                            👥 Usuários
                        </a>
                    </li>
                @endif

                {{-- ESCOLA --}}
                @if(session('current_role') === 'escola')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('escola/professores*') ? 'active' : '' }}"
                           href="{{ route('escola.professores.index') }}">
                            👨‍🏫 Professores
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('escola/alunos*') ? 'active' : '' }}"
                           href="{{ route('escola.alunos.index') }}">
                            🎓 Alunos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('escola/disciplinas*') ? 'active' : '' }}"
                           href="{{ route('escola.disciplinas.index') }}">
                            📚 Disciplinas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('escola/turmas*') ? 'active' : '' }}"
                           href="{{ route('escola.turmas.index') }}">
                            🏷️ Turmas
                        </a>
                    </li>
                @endif
            </ul>

            <ul class="navbar-nav ms-auto">
                @auth
                    {{-- Contexto atual --}}
                    @if(session('current_role') && session('current_school_id'))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-warning" href="#" role="button" data-bs-toggle="dropdown">
                                🎯 {{ ucfirst(session('current_role')) }}
                                @php
                                    $escolaAtual = \App\Models\Escola::find(session('current_school_id'));
                                @endphp
                                @if($escolaAtual)
                                    — {{ $escolaAtual->nome_e }}
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                {{-- Opção de trocar contexto --}}
                                <li>
                                    <a class="dropdown-item" href="{{ route('choose.school') }}">
                                        🔄 Trocar de contexto
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    <li class="nav-item">
                        <span class="nav-link">👤 {{ Auth::user()->nome_u ?? 'Usuário' }}</span>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-link nav-link">🚪 Sair</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                @endauth
            </ul>

        </div>
    </div>
</nav>

{{-- Espaço para compensar navbar fixa --}}
<div style="margin-top: 100px;"></div>

{{-- Debug de mensagens --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="container">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

