<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'UNICLARETIANA - Gestor de Proyectos')</title>

    <!-- Fonts (Plus Jakarta / DM Sans / JetBrains en bundle Vite; FA mientras migramos a Lucide) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- App Styles (Vite: incluye Bootstrap + design system) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('styles')
    @stack('styles')
</head>
<body>
    <a href="#main-content" class="uc-skip-link">Saltar al contenido</a>
    <!-- Sidebar -->
    @include('layouts.partials.sidebar')

    <!-- Main Content -->
    <div id="main-content" role="main" tabindex="-1">
        <!-- Top Header Bar -->
        <div class="top-header">
            <div class="d-flex align-items-center gap-2">
                <button class="mobile-menu-btn" id="mobileMenuBtn" title="Menu">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="breadcrumbs">
                    @yield('breadcrumbs')
                </div>
            </div>
            <div class="header-actions">
                @yield('header-actions')
            </div>
        </div>

        <!-- Page Content -->
        <div class="page-content">
            @yield('content')
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Modal: Cambio de Contrasena Temporal -->
    @auth
        @if(auth()->user()->is_temporary_password)
        <div class="modal fade" id="changePasswordModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cambiar Contrasena Temporal</h5>
                    </div>
                    <div class="modal-body">
                        <div class="modal-confirm-icon modal-confirm-icon--warning" style="margin-bottom: 16px;">
                            <i class="fas fa-key"></i>
                        </div>
                        <p class="modal-confirm-text">Tu contrasena es temporal. Por seguridad, debes cambiarla antes de continuar.</p>
                        <form id="changePasswordForm">
                            <div class="mb-3">
                                <label for="current_password" class="ds-label">Contrasena Actual</label>
                                <input type="password" class="ds-input" id="current_password" name="current_password" required autocomplete="current-password">
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="ds-label">Nueva Contrasena</label>
                                <input type="password" class="ds-input" id="new_password" name="new_password" required autocomplete="new-password">
                            </div>
                            <div class="mb-3">
                                <label for="new_password_confirmation" class="ds-label">Confirmar Nueva Contrasena</label>
                                <input type="password" class="ds-input" id="new_password_confirmation" name="new_password_confirmation" required autocomplete="new-password">
                            </div>
                            <div class="ds-alert ds-alert--danger" id="passwordError" style="display: none;"></div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="ds-btn ds-btn--primary" id="changePasswordBtn">Cambiar Contrasena</button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Modal: Editar Perfil -->
        <div class="modal fade" id="editProfileModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Perfil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editProfileForm">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="profileName" class="ds-label">Nombre</label>
                                <input type="text" class="ds-input" id="profileName" name="name" value="{{ Auth::user()->name }}" required autocomplete="name">
                            </div>

                            <div class="mb-4">
                                <label class="ds-label">Correo Electronico</label>
                                <div style="margin-bottom: 12px;">
                                    <span class="ds-badge ds-badge--info" style="height: auto; padding: 4px 10px;">
                                        <i class="fas fa-envelope" style="font-size: 11px;"></i>
                                        {{ Auth::user()->email }}
                                    </span>
                                </div>
                                <div>
                                    <label for="newEmail" class="ds-label">Nuevo correo electronico</label>
                                    <input type="email" class="ds-input" id="newEmail" name="new_email"
                                           placeholder="Dejar en blanco para mantener el actual"
                                           autocomplete="email">
                                    <p class="ds-form-hint">
                                        Si cambias tu correo, te enviaremos un enlace de verificacion al nuevo correo.
                                    </p>
                                </div>
                            </div>

                            <hr class="ds-divider">

                            <div class="mb-3">
                                <label for="currentPassword" class="ds-label">Contrasena actual</label>
                                <input type="password" class="ds-input" id="currentPassword" name="current_password" autocomplete="current-password">
                                <p class="ds-form-hint">Requerida solo si deseas cambiar la contrasena</p>
                            </div>

                            <div class="mb-3">
                                <label for="newPassword" class="ds-label">Nueva contrasena</label>
                                <input type="password" class="ds-input" id="newPassword" name="new_password" autocomplete="new-password">
                                <p class="ds-form-hint">Dejar en blanco si no deseas cambiarla</p>
                            </div>

                            <div class="mb-3">
                                <label for="newPasswordConfirmation" class="ds-label">Confirmar nueva contrasena</label>
                                <input type="password" class="ds-input" id="newPasswordConfirmation"
                                       name="new_password_confirmation" autocomplete="new-password">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="ds-btn ds-btn--secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="ds-btn ds-btn--primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endauth

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/profile.js') }}"></script>

    <script>
        // Sidebar collapse toggle
        document.getElementById('sidebarCollapseBtn')?.addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('main-content');
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('expanded');
            // Save state
            localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
        });

        // Restore sidebar state
        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            document.getElementById('sidebar')?.classList.add('collapsed');
            document.getElementById('main-content')?.classList.add('expanded');
        }

        // Mobile menu
        document.getElementById('mobileMenuBtn')?.addEventListener('click', function() {
            document.getElementById('sidebar')?.classList.toggle('show');
            document.querySelector('.sidebar-overlay')?.classList.toggle('show');
        });

        // Close sidebar on overlay click (mobile)
        document.querySelector('.sidebar-overlay')?.addEventListener('click', function() {
            document.getElementById('sidebar')?.classList.remove('show');
            this.classList.remove('show');
        });

        // Temporary password modal
        @auth
            @if(auth()->user()->is_temporary_password)
                document.addEventListener('DOMContentLoaded', function() {
                    const modal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
                    modal.show();
                });

                document.getElementById('changePasswordBtn').addEventListener('click', function() {
                    const form = document.getElementById('changePasswordForm');
                    const errorDiv = document.getElementById('passwordError');

                    fetch('{{ route("password.change.temporary") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            current_password: form.current_password.value,
                            new_password: form.new_password.value,
                            new_password_confirmation: form.new_password_confirmation.value,
                            _token: '{{ csrf_token() }}'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            errorDiv.textContent = data.error || 'Error al cambiar la contrasena';
                            errorDiv.style.display = 'flex';
                        }
                    })
                    .catch(() => {
                        errorDiv.textContent = 'Error al procesar la solicitud';
                        errorDiv.style.display = 'flex';
                    });
                });
            @endif
        @endauth

        // Toast utility
        window.showToast = function(type, title, message) {
            const container = document.getElementById('toastContainer');
            const icons = { success: 'fa-check-circle', danger: 'fa-exclamation-circle', warning: 'fa-exclamation-triangle', info: 'fa-info-circle' };
            const toast = document.createElement('div');
            toast.className = `ds-toast ds-toast--${type}`;
            toast.innerHTML = `
                <div class="ds-toast-icon"><i class="fas ${icons[type] || icons.info}"></i></div>
                <div class="ds-toast-content">
                    <div class="ds-toast-title">${title}</div>
                    ${message ? `<div class="ds-toast-message">${message}</div>` : ''}
                </div>
                <button class="ds-toast-close" onclick="this.parentElement.classList.add('hiding'); setTimeout(() => this.parentElement.remove(), 200);">
                    <i class="fas fa-times"></i>
                </button>
                <div class="ds-toast-progress"></div>
            `;
            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('hiding');
                setTimeout(() => toast.remove(), 200);
            }, 5000);
        };
    </script>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
