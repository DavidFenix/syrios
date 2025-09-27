<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
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
</html>
