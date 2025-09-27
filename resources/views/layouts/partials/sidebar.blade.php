<!-- Sidebar -->
<div id="sidebar">
    <div class="sidebar-header">
        <div class="logo-container">
            <i class="fas fa-university text-primary fa-2x"></i>
            <div class="logo-text">
                <span class="logo-title">UNICLARETIANA</span>
                <small class="logo-subtitle">Gestor de Proyectos</small>
            </div>
        </div>
        <!-- Botón solo en móvil -->
        <button class="sidebar-toggle d-md-none" id="sidebarToggle" title="Plegar/Desplegar menú">
            <i class="fas fa-chevron-left"></i>
        </button>
    </div>

    <div class="nav-section">
        <h6 class="nav-section-title">Menú</h6>
        <ul class="nav-items">
            <li class="nav-item">
                <a href="{{ route('proyectos.index') }}" class="nav-link {{ request()->routeIs('proyectos.*') ? 'active' : '' }}">
                    <i class="fas fa-folder"></i>
                    <span class="nav-text">Proyectos</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('estadistica') }}" class="nav-link {{ request()->routeIs('estadistica') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i>
                    <span class="nav-text">Estadística</span>
                </a>
            </li>
            @if(auth()->check() && auth()->user()->hasRole('admin'))
                <li class="nav-item">
                    <a href="{{ route('audit.index') }}" class="nav-link {{ request()->routeIs('audit.*') ? 'active' : '' }}">
                        <i class="fas fa-user-secret"></i>
                        <span class="nav-text">Auditoría</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span class="nav-text">Usuarios</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('access-requests.index') }}" class="nav-link {{ request()->routeIs('access-requests.*') ? 'active' : '' }}">
                        <i class="fas fa-user-clock"></i>
                        <span class="nav-text">Solicitudes de Acceso</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>

    <!-- User Section -->
    <div class="nav-section mt-auto">
        <div class="px-4 py-3 border-top">
            @guest
                <div class="d-flex flex-column gap-2">
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="nav-link">
                            <i class="fas fa-sign-in-alt"></i>
                            <span class="nav-text">{{ __('Login') }}</span>
                        </a>
                    @endif

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="nav-link">
                            <i class="fas fa-user-plus"></i>
                            <span class="nav-text">{{ __('Register') }}</span>
                        </a>
                    @endif
                </div>
            @else
                <a href="#" class="d-flex align-items-center gap-3 px-2 rounded transition user-profile-hover text-decoration-none"
                   data-bs-toggle="modal" data-bs-target="#editProfileModal"
                   style="cursor:pointer;">
                    <div class="flex-shrink-0">
                        <div class="avatar-circle">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </div>
                    <div class="flex-grow-1 min-width-0">
                        <h6 class="mb-0 text-truncate">{{ Auth::user()->name }}</h6>
                        <small class="text-muted text-truncate">{{ Auth::user()->email }}</small>
                    </div>
                </a>
@push('styles')
    <style>
        .user-profile-hover:hover {
            background: #f0f4ff;
            box-shadow: 0 2px 8px rgba(67,97,238,0.08);
            transition: background 0.2s, box-shadow 0.2s;
        }
    </style>
@endpush
                <div class="mt-2">
                    <a class="nav-link text-danger" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="nav-text">{{ __('Logout') }}</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            @endguest
        </div>
    </div>
</div>

<!-- Sidebar Overlay for Mobile -->
<div class="sidebar-overlay"></div>