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

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('master.escolas.index') }}">Syrios</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMaster">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMaster">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

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

            </ul>

            <span class="navbar-text text-light">
                Bem-vindo, {{ Auth::user()->name ?? 'Master' }}
            </span>
        </div>
    </div>
</nav>

<div class="container">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


<!--doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>@yield('title','Syrios Master')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- Bootstrap 5.3 --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="{{ route('master.escolas.index') }}">Syrios Master</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link {{ request()->is('master/escolas*') ? 'active' : '' }}" 
             href="{{ route('master.escolas.index') }}">
             Escolas
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->is('master/roles*') ? 'active' : '' }}" 
             href="{{ route('master.roles.index') }}">
             Roles
          </a>
        </li>
      </ul>
      <div class="ms-auto text-white">
        [{{ session('nome_u', 'Master') }}]
      </div>
    </div>
  </div>
</nav>


<main class="container">
  @include('partials.flash')
  @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html-->



<!--DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Syrios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark p-2">
        <a class="navbar-brand" href="#">Syrios</a>
        <span class="text-light">Bem-vindo</span>
    </nav>

    <div class="container mt-4">
        {{-- Aqui o Blade vai injetar o conteúdo da página --}}
        @yield('content')
    </div>

    <footer class="bg-light text-center p-3 mt-4">
        <small>&copy; 2025 - Syrios</small>
    </footer>
</body>
</html-->
