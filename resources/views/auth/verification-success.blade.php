<!DOCTYPE html>
<html lang="es">
<head>
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
