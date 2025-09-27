<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación Exitosa</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        .success-container {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f3f4f6;
        }
        .success-card {
            background-color: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        .success-icon {
            color: #10b981;
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .success-title {
            color: #1f2937;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .success-message {
            color: #4b5563;
            margin-bottom: 1.5rem;
        }
        .back-button {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        .back-button:hover {
            background-color: #1d4ed8;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-card">
            <div class="success-icon">✓</div>
            <h1 class="success-title">¡Verificación Exitosa!</h1>
            <p class="success-message">Tu contraseña ha sido actualizada correctamente.</p>
            <a href="{{ route('login') }}" class="back-button">Volver al Inicio</a>
        </div>
    </div>
</body>
</html>