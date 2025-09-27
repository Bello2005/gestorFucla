@component('mail::message')
# Recuperación de Contraseña

¡Hola {{ $user->name }}!

Has solicitado restablecer tu contraseña. Haz clic en el botón de abajo para crear una nueva contraseña.

@component('mail::panel')
**IMPORTANTE:** 
- Este enlace es válido solo por 1 hora
- Solo puede ser usado una vez
- Si no solicitaste este cambio, por favor ignora este correo
- Por seguridad, usa una contraseña fuerte y única
@endcomponent

@component('mail::button', ['url' => $resetUrl])
Restablecer Contraseña
@endcomponent

Si el botón no funciona, puedes copiar y pegar esta URL en tu navegador:
{{ $resetUrl }}

Si tienes problemas para acceder:
- Asegúrate de copiar la contraseña exactamente como aparece
- Verifica que no haya espacios adicionales
- Contacta al soporte técnico si persisten los problemas: {{ config('mail.from.address', 'soporte@uniclaretiana.edu.co') }}

Saludos,<br>
{{ config('app.name') }}

<small>Si no solicitaste este restablecimiento de contraseña, por favor ignora este correo o contacta al administrador del sistema.</small>
@endcomponent