<!DOCTYPE html>
<html>
<head>
    <title>Verificar cambio de correo electrónico</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #fff; padding: 20px;">
        <h1 style="color: #333; text-align: center;">Verificar cambio de correo electrónico</h1>
        
        <p>¡Hola {{ $name }}!</p>
        
        <p>Has solicitado cambiar tu dirección de correo electrónico.</p>
        
        <p>Tu correo actual: <strong>{{ $currentEmail }}</strong></p>
        
        <p>Nuevo correo: <strong>{{ $newEmail }}</strong></p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $verificationUrl }}" 
               style="background: #4A90E2; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 4px; display: inline-block;">
                Verificar cambio de correo
            </a>
        </div>
        
        <p>Este enlace expirará en 24 horas.</p>
        
        <p>Si no solicitaste este cambio, puedes ignorar este mensaje.</p>
        
        <p>¡Gracias por usar nuestra aplicación!</p>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
        
        <p style="font-size: 12px; color: #666; text-align: center;">
            Si tienes problemas para hacer clic en el botón "Verificar cambio de correo", 
            copia y pega la siguiente URL en tu navegador:<br>
            <span style="color: #4A90E2;">{{ $verificationUrl }}</span>
        </p>
    </div>
</body>
</html>
