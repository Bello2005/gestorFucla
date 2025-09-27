<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\EmailVerification;
use Illuminate\Support\Facades\Log;

class EmailChangeVerification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $verification;

    public function __construct(EmailVerification $verification)
    {
        $this->verification = $verification;
    }

    public function via(object $notifiable): array
    {
        Log::info('Enviando notificación de cambio de correo', [
            'user_id' => $notifiable->id,
            'new_email' => $this->verification->new_email
        ]);
        return ['mail'];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    public function routeNotificationForMail($notifiable)
    {
        // Forzar el envío al nuevo correo
        return $this->verification->new_email;
    }

    public function toMail(object $notifiable): MailMessage
    {
        try {
            $verificationUrl = $this->verification->generateSignedToken();
            
            Log::info('Generando mensaje de correo', [
                'verification_url' => $verificationUrl,
                'new_email' => $this->verification->new_email
            ]);

            // Forzar el envío al nuevo correo usando mailer directamente
            $mailer = app()->make(\Illuminate\Mail\Mailer::class);
            $mailer->to($this->verification->new_email);

            $message = new MailMessage;
            $message->subject('Verificar cambio de correo electrónico')
                ->greeting('¡Hola ' . $notifiable->name . '!')
                ->line('Has solicitado cambiar tu dirección de correo electrónico.')
                ->line('Tu correo actual: ' . $this->verification->current_email)
                ->line('Nuevo correo: ' . $this->verification->new_email)
                ->action('Verificar cambio de correo', $verificationUrl)
                ->line('Este enlace expirará en 24 horas.')
                ->line('Si no solicitaste este cambio, puedes ignorar este mensaje.')
                ->salutation('¡Gracias por usar nuestra aplicación!');

            // Registrar intento de envío en el log
            Log::info('Enviando correo de verificación', [
                'to' => $this->verification->new_email,
                'url' => $verificationUrl
            ]);

            return $message;
        } catch (\Exception $e) {
            Log::error('Error al generar el mensaje de correo: ' . $e->getMessage());
            throw $e;
        }
    }

    public function toArray(object $notifiable): array
    {
        return [
            'verification_id' => $this->verification->id,
            'new_email' => $this->verification->new_email,
        ];
    }
}