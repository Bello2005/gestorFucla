<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar Sesion - UNICLARETIANA</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css'])
</head>
<body class="auth-page">
    <div class="auth-container">
        <!-- Left Panel: Branding -->
        <div class="auth-brand">
            <div class="auth-brand-content">
                <div class="auth-brand-logo--img">
                    <img src="{{ asset('images/brand/logo-mark.svg') }}" alt="UNICLARETIANA" />
                </div>
                <h1 class="auth-brand-title">UNICLARETIANA</h1>
                <div class="auth-brand-accent"></div>
                <blockquote class="auth-brand-quote">
                    "Formando líderes para la transformación social"
                </blockquote>
                <p class="auth-brand-subtitle">
                    Sistema de Gestión de Proyectos de Extensión y Proyección Social
                </p>
                <ul class="auth-features">
                    <li>
                        <span class="feature-icon"><i class="fas fa-shield-halved"></i></span>
                        Gestión segura de proyectos
                    </li>
                    <li>
                        <span class="feature-icon"><i class="fas fa-chart-line"></i></span>
                        Estadísticas en tiempo real
                    </li>
                    <li>
                        <span class="feature-icon"><i class="fas fa-file-export"></i></span>
                        Exportación a PDF, Excel y Word
                    </li>
                    <li>
                        <span class="feature-icon"><i class="fas fa-users"></i></span>
                        Control de acceso por roles
                    </li>
                </ul>
            </div>
        </div>

        <!-- Right Panel: Form -->
        <div class="auth-form-panel">
            <div class="auth-form-header">
                <h2 class="auth-form-title">Bienvenido</h2>
                <p class="auth-form-desc">Ingresa tus credenciales para acceder al sistema</p>
            </div>

            @if(session('success'))
                <div class="auth-alert auth-alert--success">
                    <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="auth-alert auth-alert--error">
                    <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" class="auth-form">
                @csrf
                <div class="form-group">
                    <label for="email">Correo electronico</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="auth-input" id="email" name="email"
                               value="{{ old('email') }}"
                               placeholder="usuario@uniclaretiana.edu.co"
                               required autofocus autocomplete="username">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Contrasena</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" class="auth-input" id="password" name="password"
                               placeholder="Ingresa tu contrasena"
                               required autocomplete="current-password"
                               style="padding-right: 44px;">
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        Recordarme
                    </label>
                    <a href="{{ route('password.request') }}" class="forgot-link">Olvidaste tu contrasena?</a>
                </div>

                <button type="submit" class="auth-submit">
                    Iniciar Sesion
                </button>
            </form>

            <div class="auth-footer">
                No tienes una cuenta? <a href="{{ route('access-requests.create') }}">Solicitar acceso</a>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const input = document.getElementById('password');
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    </script>
</body>
</html>
