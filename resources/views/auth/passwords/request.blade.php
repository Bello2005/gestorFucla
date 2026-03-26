<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contrasena - UNICLARETIANA</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="{{ asset('css/pages/auth.css') }}">
</head>
<body class="auth-page">
    <div class="auth-container" style="max-width: 480px;">
        <div class="auth-form-panel" style="padding: 48px 40px;">
            <div style="text-align: center; margin-bottom: 32px;">
                <div class="auth-brand-logo" style="margin: 0 auto 20px;">
                    <i class="fas fa-key"></i>
                </div>
                <h2 class="auth-form-title">Recuperar Contrasena</h2>
                <p class="auth-form-desc">Ingresa tu correo y te enviaremos instrucciones para recuperar tu contrasena.</p>
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

            <form method="POST" action="{{ route('password.email') }}" class="auth-form">
                @csrf
                <div class="form-group">
                    <label for="email">Correo electronico</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="auth-input" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="tu.correo@ejemplo.com">
                    </div>
                </div>

                <button type="submit" class="auth-submit">Enviar Instrucciones</button>
            </form>

            <div class="auth-footer">
                <a href="{{ route('login') }}"><i class="fas fa-arrow-left" style="margin-right: 4px;"></i> Volver al Login</a>
            </div>
        </div>
    </div>
</body>
</html>
