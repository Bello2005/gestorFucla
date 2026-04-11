<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contrasena - UNICLARETIANA</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css'])
</head>
<body class="auth-page">
    <div class="auth-container" style="max-width: 480px;">
        <div class="auth-form-panel" style="padding: 48px 40px;">
            <div style="text-align: center; margin-bottom: 32px;">
                <div class="auth-brand-logo" style="margin: 0 auto 20px;">
                    <i class="fas fa-lock"></i>
                </div>
                <h2 class="auth-form-title">Nueva Contrasena</h2>
                <p class="auth-form-desc">Ingresa tu nueva contrasena para restablecer el acceso a tu cuenta.</p>
            </div>

            @if($errors->any())
                <div class="auth-alert auth-alert--error">
                    <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="auth-form">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="form-group">
                    <label for="password">Nueva contrasena</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" class="auth-input" id="password" name="password" required autocomplete="new-password">
                    </div>
                    @error('password')<p class="ds-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirmar contrasena</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" class="auth-input" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                    </div>
                </div>

                <button type="submit" class="auth-submit">Guardar Nueva Contrasena</button>
            </form>

            <div class="auth-footer">
                <a href="{{ route('login') }}"><i class="fas fa-arrow-left" style="margin-right: 4px;"></i> Volver al Login</a>
            </div>
        </div>
    </div>
</body>
</html>
