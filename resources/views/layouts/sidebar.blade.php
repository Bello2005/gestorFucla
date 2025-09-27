<![CDATA[<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="fas fa-home"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('proyectos.*') ? 'active' : '' }}" href="{{ route('proyectos.index') }}">
                    <i class="fas fa-project-diagram"></i>
                    Proyectos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('audit.*') ? 'active' : '' }}" href="{{ route('audit.index') }}">
                    <i class="fas fa-history"></i>
                    Auditoría
                </a>
            </li>
            @if(auth()->user()->hasRole('admin'))
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                    <i class="fas fa-users"></i>
                    Usuarios
                </a>
            </li>
            @endif
        </ul>
    </div>
</nav>]]>