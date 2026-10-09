<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon-uniclaretiana-192.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificacion Exitosa - UNICLARETIANA</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css'])
</head>
<body class="auth-card-page">
    <div class="auth-card">
        <div class="auth-card-icon auth-card-icon--success">
            <i class="fas fa-check-circle"></i>
        </div>
        <h2 class="auth-card-title">Verificacion Exitosa</h2>
        <p class="auth-card-text">Tu contrasena ha sido actualizada correctamente. Ya puedes iniciar sesion.</p>
        <a href="{{ route('login') }}" class="auth-submit" style="display: inline-flex; text-decoration: none;">
            Iniciar Sesion
        </a>
    </div>
</body>
</html>
