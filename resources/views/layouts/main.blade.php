<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'UNICLARETIANA - Gestor de Proyectos')</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="{{ asset('/css/variables.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/sidebar.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/components/stats-cards.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">

    @yield('styles')
</head>
<body>
    <!-- Sidebar -->
    @include('layouts.partials.sidebar')

    <!-- Main Content -->
    <div id="main-content">
        @yield('content')
    </div>

    <!-- Modal de Cambio de Contraseña Temporal -->
    @auth
        @if(auth()->user()->is_temporary_password)
        <div class="modal fade" id="changePasswordModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="changePasswordModalLabel">Cambiar Contraseña Temporal</h5>
                    </div>
                    <div class="modal-body">
                        <form id="changePasswordForm">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Contraseña Actual</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required autocomplete="current-password">
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Nueva Contraseña</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required autocomplete="new-password">
                            </div>
                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                                <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required autocomplete="new-password">
                            </div>
                            <div class="alert alert-danger" id="passwordError" style="display: none;"></div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="changePasswordBtn">Cambiar Contraseña</button>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <!-- Modal de Editar Perfil -->
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
                                <label for="profileName" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="profileName" name="name" value="{{ Auth::user()->name }}" required autocomplete="name">
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Correo Electrónico</label>
                                <div class="email-section">
                                    <div class="current-email mb-2">
                                        <small class="text-muted d-block">Correo actual:</small>
                                        <strong>{{ Auth::user()->email }}</strong>
                                    </div>
                                    <div class="new-email">
                                        <label for="newEmail" class="form-label">Nuevo correo electrónico</label>
                                        <input type="email" class="form-control" id="newEmail" name="new_email" 
                                               placeholder="Dejar en blanco para mantener el actual"
                                               autocomplete="email">
                                        <div class="form-text">
                                            Si cambias tu correo, te enviaremos un enlace de verificación al nuevo correo.
                                            El cambio no será efectivo hasta que lo verifiques.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="currentPassword" class="form-label">Contraseña actual</label>
                                <input type="password" class="form-control" id="currentPassword" name="current_password"
                                       autocomplete="current-password">
                                <div class="form-text">Requerida solo si deseas cambiar la contraseña</div>
                            </div>

                            <div class="mb-3">
                                <label for="newPassword" class="form-label">Nueva contraseña</label>
                                <input type="password" class="form-control" id="newPassword" name="new_password"
                                       autocomplete="new-password">
                                <div class="form-text">Dejar en blanco si no deseas cambiarla</div>
                            </div>

                            <div class="mb-3">
                                <label for="newPasswordConfirmation" class="form-label">Confirmar nueva contraseña</label>
                                <input type="password" class="form-control" id="newPasswordConfirmation" 
                                       name="new_password_confirmation" autocomplete="new-password">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endauth

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/profile.js') }}"></script>

    <script>
        // Script común para el sidebar
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar')?.classList.toggle('collapsed');
            document.getElementById('main-content')?.classList.toggle('expanded');
        });

        // Cerrar sidebar en móviles al hacer clic en overlay
        document.querySelector('.sidebar-overlay')?.addEventListener('click', function() {
            document.getElementById('sidebar')?.classList.remove('show');
        });

        // Lógica para el modal de cambio de contraseña
        @auth
            @if(auth()->user()->is_temporary_password)
                // Mostrar modal al cargar la página
                document.addEventListener('DOMContentLoaded', function() {
                    const modal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
                    modal.show();
                });

                // Manejar envío del formulario
                document.getElementById('changePasswordBtn').addEventListener('click', function() {
                    const form = document.getElementById('changePasswordForm');
                    const errorDiv = document.getElementById('passwordError');
                    
                    const formData = {
                        current_password: form.current_password.value,
                        new_password: form.new_password.value,
                        new_password_confirmation: form.new_password_confirmation.value,
                        _token: '{{ csrf_token() }}'
                    };

                    fetch('{{ route("password.change.temporary") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(formData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            errorDiv.textContent = data.error || 'Error al cambiar la contraseña';
                            errorDiv.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        errorDiv.textContent = 'Error al procesar la solicitud';
                        errorDiv.style.display = 'block';
                    });
                });
            @endif
        @endauth

        // Mostrar sidebar en móviles
        document.addEventListener('DOMContentLoaded', function() {
            const mediaQuery = window.matchMedia('(max-width: 768px)');
            
            function handleMobileView(e) {
                if (e.matches) {
                    document.getElementById('sidebar')?.classList.remove('collapsed');
                    document.getElementById('main-content')?.classList.remove('expanded');
                }
            }

            mediaQuery.addListener(handleMobileView);
            handleMobileView(mediaQuery);
        });
    </script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Scripts de la página -->
    @yield('scripts')
    @stack('scripts')
</body>
</html>