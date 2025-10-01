<!-- resources/views/layouts/app.blade.php -->

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syrios - Painel Master</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="{{ route('master.dashboard') }}">⚡ Syrios</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMaster"
                aria-controls="navbarMaster" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMaster">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                {{-- MASTER --}}
                @if(Auth::user() && Auth::user()->hasRole('master'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('master/dashboard*') ? 'active' : '' }}"
                       href="{{ route('master.dashboard') }}">
                        📊 Dashboard
                    </a>
                </li>

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
                @if(Auth::user() && Auth::user()->hasRole('secretaria'))
                  <li class="nav-item">
                    <a class="nav-link {{ request()->is('secretaria/escolas*') ? 'active' : '' }}"
                       href="{{ route('secretaria.escolas.index') }}">
                      Escolas Filhas
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link {{ request()->is('secretaria/usuarios*') ? 'active' : '' }}"
                       href="{{ route('secretaria.usuarios.index') }}">
                      Usuários
                    </a>
                  </li>
                @endif

                {{-- ESCOLA --}}
                @if(Auth::user() && Auth::user()->hasRole('escola'))
                  <li class="nav-item">
                    <a class="nav-link {{ request()->is('escola/usuarios*') ? 'active' : '' }}"
                       href="{{ route('escola.usuarios.index') }}">
                      Professores
                    </a>
                  </li>
                @endif


            </ul>

            <ul class="navbar-nav ms-auto">
                @auth
                    <li class="nav-item">
                        <span class="nav-link">Bem-vindo, {{ Auth::user()->nome_u ?? 'Visitante' }}</span>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-link nav-link">Sair</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                @endauth
            </ul>

            </span>
        </div>
    </div>
</nav>

{{-- Espaço para compensar navbar fixa --}}
<div style="margin-top: 70px;"></div>

{{-- Inicio Debug --}}
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
{{-- Fim Debug --}}

<div class="container">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

