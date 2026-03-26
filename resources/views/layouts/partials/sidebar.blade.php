<!-- Sidebar -->
<div id="sidebar">
    <div class="sidebar-header">
        <div class="logo-container">
            <div class="logo-icon">
                <i class="fas fa-university"></i>
            </div>
            <div class="logo-text">
                <span class="logo-title">UNICLARETIANA</span>
                <small class="logo-subtitle">Gestor de Proyectos</small>
            </div>
        </div>
    </div>

    <div class="nav-section" style="flex: 1;">
        <h6 class="nav-section-title">Menu</h6>
        <ul class="nav-items">
            <li>
                <a href="{{ route('proyectos.index') }}" class="nav-link {{ request()->routeIs('proyectos.*') ? 'active' : '' }}">
                    <i class="fas fa-briefcase"></i>
                    <span class="nav-text">Proyectos</span>
                </a>
            </li>
            <li>
                <a href="{{ route('estadistica') }}" class="nav-link {{ request()->routeIs('estadistica') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span class="nav-text">Estadistica</span>
                </a>
            </li>
            @if(auth()->check() && auth()->user()->hasRole('admin'))
                <li style="margin-top: 8px;">
                    <h6 class="nav-section-title" style="padding-left: 12px; margin-bottom: 4px;">Administracion</h6>
                </li>
                <li>
                    <a href="{{ route('audit.index') }}" class="nav-link {{ request()->routeIs('audit.*') ? 'active' : '' }}">
                        <i class="fas fa-user-shield"></i>
                        <span class="nav-text">Auditoria</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="fas fa-users-cog"></i>
                        <span class="nav-text">Usuarios</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('access-requests.index') }}" class="nav-link {{ request()->routeIs('access-requests.*') ? 'active' : '' }}">
                        <i class="fas fa-inbox"></i>
                        <span class="nav-text">Solicitudes</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>

    <!-- User Section -->
    <div class="sidebar-user">
        @guest
            <a href="{{ route('login') }}" class="nav-link">
                <i class="fas fa-sign-in-alt"></i>
                <span class="nav-text">Iniciar Sesion</span>
            </a>
        @else
            <a href="#" class="user-profile-link" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                <div class="avatar-circle">{{ substr(Auth::user()->name, 0, 1) }}</div>
                <div class="user-info">
                    <h6 class="user-name">{{ Auth::user()->name }}</h6>
                    <small class="user-email">{{ Auth::user()->email }}</small>
                </div>
            </a>
            <a class="sidebar-logout" href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i>
                <span class="nav-text">Cerrar Sesion</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        @endguest
    </div>

    <!-- Collapse Toggle (desktop) -->
    <button class="sidebar-collapse-btn" id="sidebarCollapseBtn" title="Colapsar menu">
        <i class="fas fa-chevron-left"></i>
    </button>
</div>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay"></div>
