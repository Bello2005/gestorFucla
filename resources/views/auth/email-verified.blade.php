<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificacion de Correo - UNICLARETIANA</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css'])
</head>
<body class="auth-card-page">
    <div class="auth-card">
        @if(isset($error))
            <div class="auth-card-icon" style="background: var(--danger-50); color: var(--danger);">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h2 class="auth-card-title">Error de Verificacion</h2>
            <p class="auth-card-text">{{ $error }}</p>
        @else
            <div class="auth-card-icon auth-card-icon--success">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 class="auth-card-title">Correo Verificado</h2>
            <p class="auth-card-text">{{ $message ?? 'Tu direccion de correo electronico ha sido verificada y actualizada exitosamente.' }}</p>
        @endif
        <p style="font-size: 13px; color: var(--slate-400); margin-bottom: 16px;">Redirigiendo al dashboard en 3 segundos...</p>
        <a href="{{ route('dashboard') }}" class="auth-submit" style="display: inline-flex; text-decoration: none;">
            Ir al Dashboard
        </a>
    </div>

    <script>
        setTimeout(function() {
            window.location.href = "{{ route('dashboard') }}";
        }, 3000);
    </script>
</body>
</html>
