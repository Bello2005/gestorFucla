<nav id="sidebar" class="uc-sidebar" role="navigation" aria-label="Principal">
    <div class="sidebar-header">
        <a href="{{ route('dashboard') }}" class="logo-container">
            <img class="logo-mark" src="{{ asset('images/brand/logo-mark.png') }}" width="44" height="44" alt="" aria-hidden="true" />
            <div class="logo-text">
                <span class="logo-title">UNICLARETIANA</span>
                <small class="logo-subtitle">Gestión de Extensión</small>
            </div>
        </a>
    </div>

    <div class="nav-section">
        <h2 class="nav-section-title">Menú principal</h2>
        <ul class="nav-items">
            <li>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif>
                    <x-uc.icon name="layout-dashboard" />
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('proyectos.index') }}" class="nav-link {{ request()->routeIs('proyectos.*') ? 'active' : '' }}" @if(request()->routeIs('proyectos.*')) aria-current="page" @endif>
                    <x-uc.icon name="briefcase" />
                    <span class="nav-text">Proyectos Activos</span>
                </a>
            </li>
            <li>
                <a href="{{ route('banco.index') }}" class="nav-link {{ request()->routeIs('banco.*') ? 'active' : '' }}" @if(request()->routeIs('banco.*')) aria-current="page" @endif>
                    <x-uc.icon name="library-big" />
                    <span class="nav-text">Banco de Proyectos</span>
                </a>
            </li>
            <li>
                <a href="{{ route('estadistica') }}" class="nav-link {{ request()->routeIs('estadistica') ? 'active' : '' }}" @if(request()->routeIs('estadistica')) aria-current="page" @endif>
                    <x-uc.icon name="line-chart" />
                    <span class="nav-text">Estadísticas</span>
                </a>
            </li>
        </ul>

        @if(auth()->check() && auth()->user()->hasRole('admin'))
            <h2 class="nav-section-title" style="margin-top: 12px;">Administración</h2>
            <ul class="nav-items">
                <li>
                    <a href="{{ route('audit.index') }}" class="nav-link {{ request()->routeIs('audit.*') ? 'active' : '' }}">
                        <x-uc.icon name="shield" />
                        <span class="nav-text">Auditoría</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <x-uc.icon name="users" />
                        <span class="nav-text">Usuarios</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('access-requests.index') }}" class="nav-link {{ request()->routeIs('access-requests.*') ? 'active' : '' }}">
                        <x-uc.icon name="inbox" />
                        <span class="nav-text">Solicitudes</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('catalogos.index') }}" class="nav-link {{ request()->routeIs('catalogos.*') ? 'active' : '' }}">
                        <x-uc.icon name="book-marked" />
                        <span class="nav-text">Catálogos</span>
                    </a>
                </li>
            </ul>
        @endif
    </div>

    <div class="sidebar-user">
        @guest
            <a href="{{ route('login') }}" class="nav-link">
                <span class="nav-text">Iniciar sesión</span>
            </a>
        @else
            <a href="#" class="user-profile-link" data-bs-toggle="modal" data-bs-target="#editProfileModal" aria-label="Editar perfil">
                <div class="avatar-circle" aria-hidden="true">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <div class="user-info">
                    <h3 class="user-name">{{ Auth::user()->name }}</h3>
                    <small class="user-email">{{ Auth::user()->email }}</small>
                </div>
            </a>
            <a class="sidebar-logout" href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <x-uc.icon name="log-out" />
                <span class="nav-text">Cerrar sesión</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        @endguest
    </div>

    <button type="button" class="sidebar-collapse-btn" id="sidebarCollapseBtn" title="Colapsar menú" aria-label="Colapsar menú lateral">
        <i class="fas fa-chevron-left" aria-hidden="true"></i>
    </button>
</nav>

<div class="sidebar-overlay" role="presentation" aria-hidden="true"></div>
