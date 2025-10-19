{{-- layouts/menu_professor.blade.php --}}
<nav class="nav flex-column">
    <a href="{{ route('professor.dashboard') }}" class="nav-link {{ request()->routeIs('professor.dashboard') ? 'active' : '' }}">
        🏠 Painel Inicial
    </a>

    <a href="{{ route('professor.ofertas.index') }}" class="nav-link {{ request()->routeIs('professor.ofertas.*') ? 'active' : '' }}">
        📚 Minhas Ofertas
    </a>

    <a href="{{ route('professor.ocorrencias.index') }}" class="nav-link {{ request()->routeIs('professor.ocorrencias.*') ? 'active' : '' }}">
        ⚠️ Ocorrências
    </a>

    <a href="{{ route('professor.relatorios.index') }}" class="nav-link {{ request()->routeIs('professor.relatorios.*') ? 'active' : '' }}">
        📊 Relatórios
    </a>

    <a href="{{ route('professor.perfil') }}" class="nav-link {{ request()->routeIs('professor.perfil') ? 'active' : '' }}">
        👤 Meu Perfil
    </a>
</nav>
