<!-- resources/views/layouts/app.blade.php -->

<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>@yield('title','Syrios Master')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- Bootstrap 5.3 --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="{{ route('master.escolas.index') }}">Syrios Master</a>
    <div class="ms-auto text-white">
      {{ session('nome_u', 'Master') }}
    </div>
  </div>
</nav>

<main class="container">
  @include('partials.flash')
  @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



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
