<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Acceso - UNICLARETIANA</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css'])
</head>
<body class="auth-page">
    <div class="auth-container">
        <!-- Left Panel: Branding -->
        <div class="auth-brand">
            <div class="auth-brand-content">
                <div class="auth-brand-logo">
                    <i class="fas fa-university"></i>
                </div>
                <h1 class="auth-brand-title">UNICLARETIANA</h1>
                <div class="auth-brand-accent"></div>
                <p class="auth-brand-subtitle">
                    Sistema de Gestion de Proyectos de Extension y Proyeccion Social
                </p>
                <ul class="auth-features">
                    <li>
                        <span class="feature-icon"><i class="fas fa-user-check"></i></span>
                        Solicita acceso seguro y rapido
                    </li>
                    <li>
                        <span class="feature-icon"><i class="fas fa-clipboard-check"></i></span>
                        Revision por el equipo administrador
                    </li>
                    <li>
                        <span class="feature-icon"><i class="fas fa-envelope"></i></span>
                        Notificacion por correo electronico
                    </li>
                </ul>
            </div>
        </div>

        <!-- Right Panel: Form -->
        <div class="auth-form-panel">
            <div class="auth-form-header">
                <h2 class="auth-form-title">Solicitar Acceso</h2>
                <p class="auth-form-desc">Completa el formulario para solicitar acceso al sistema</p>
            </div>

            @if($errors->any())
                <div class="auth-alert auth-alert--error">
                    <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('access-requests.store') }}" class="auth-form">
                @csrf
                <div class="form-group">
                    <label for="name">Nombre completo</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-user"></i></span>
                        <input type="text" class="auth-input" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="Tu nombre completo">
                    </div>
                    @error('name')<p class="ds-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label for="email">Correo electronico</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="auth-input" id="email" name="email" value="{{ old('email') }}" required placeholder="correo@ejemplo.com">
                    </div>
                    @error('email')<p class="ds-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label for="phone">Telefono <span style="color: var(--slate-400); font-weight: 400;">(opcional)</span></label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-phone"></i></span>
                        <input type="tel" class="auth-input" id="phone" name="phone" value="{{ old('phone') }}" placeholder="300 000 0000">
                    </div>
                    @error('phone')<p class="ds-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label for="reason">Por que necesitas acceso?</label>
                    <textarea class="auth-input" id="reason" name="reason" rows="3" required placeholder="Describe brevemente tu necesidad de acceso..." style="height: auto; min-height: 80px; padding: 12px 16px 12px 40px;">{{ old('reason') }}</textarea>
                    @error('reason')<p class="ds-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                </div>

                <button type="submit" class="auth-submit">Enviar Solicitud</button>
            </form>

            <div class="auth-footer">
                <a href="{{ route('login') }}"><i class="fas fa-arrow-left" style="margin-right: 4px;"></i> Volver al Login</a>
            </div>
        </div>
    </div>
</body>
</html>
