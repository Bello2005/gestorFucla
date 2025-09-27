<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;
    public $resetUrl;

    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
        $this->resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $user->email
        ]);
    }

    public function build()
    {
        return $this->markdown('emails.password-reset')
                    ->subject('Recuperación de Contraseña - ' . config('app.name'))
                    ->priority(1);
    }
}