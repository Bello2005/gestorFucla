<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar cambio de correo electronico</title>
</head>
<body style="font-family: 'Helvetica Neue', Arial, sans-serif; line-height: 1.6; margin: 0; padding: 0; background-color: #F9FAFB;">
    <div style="max-width: 560px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);">
        <!-- Header -->
        <div style="background: #0C111D; padding: 32px 40px; text-align: center;">
            <div style="display: inline-block; width: 48px; height: 48px; background: linear-gradient(135deg, #C9A84C, #8B6914); border-radius: 10px; line-height: 48px; margin-bottom: 12px;">
                <span style="color: white; font-size: 20px; font-weight: 700;">U</span>
            </div>
            <h1 style="color: #ffffff; font-size: 20px; font-weight: 600; margin: 8px 0 0;">Verificar cambio de correo</h1>
        </div>

        <!-- Body -->
        <div style="padding: 32px 40px;">
            <p style="font-size: 15px; color: #344054; margin-bottom: 16px;">Hola <strong>{{ $name }}</strong>,</p>
            <p style="font-size: 14px; color: #667085; margin-bottom: 24px;">Has solicitado cambiar tu direccion de correo electronico.</p>

            <div style="background: #F9FAFB; border: 1px solid #E4E7EC; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
                <div style="margin-bottom: 8px;">
                    <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #98A2B3; font-weight: 600;">Correo actual</span><br>
                    <span style="font-size: 14px; color: #344054; font-weight: 500;">{{ $currentEmail }}</span>
                </div>
                <div>
                    <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #98A2B3; font-weight: 600;">Nuevo correo</span><br>
                    <span style="font-size: 14px; color: #4F46E5; font-weight: 600;">{{ $newEmail }}</span>
                </div>
            </div>

            <div style="text-align: center; margin: 32px 0;">
                <a href="{{ $verificationUrl }}"
                   style="display: inline-block; background: #4F46E5; color: #ffffff; padding: 12px 32px; text-decoration: none; border-radius: 8px; font-size: 14px; font-weight: 600;">
                    Verificar cambio de correo
                </a>
            </div>

            <p style="font-size: 13px; color: #98A2B3; margin-bottom: 8px;">Este enlace expirara en 24 horas.</p>
            <p style="font-size: 13px; color: #98A2B3;">Si no solicitaste este cambio, puedes ignorar este mensaje.</p>
        </div>

        <!-- Footer -->
        <div style="padding: 20px 40px; background: #F9FAFB; border-top: 1px solid #E4E7EC; text-align: center;">
            <p style="font-size: 11px; color: #98A2B3; margin: 0;">
                Si tienes problemas con el boton, copia y pega esta URL en tu navegador:<br>
                <span style="color: #4F46E5; word-break: break-all;">{{ $verificationUrl }}</span>
            </p>
        </div>
    </div>
</body>
</html>
