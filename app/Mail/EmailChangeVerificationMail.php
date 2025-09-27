<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\EmailVerification;

class EmailChangeVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $verification;

    public function __construct(EmailVerification $verification)
    {
        $this->verification = $verification;
    }

    public function build()
    {
        $verificationUrl = url("/email/verify/{$this->verification->token}");
        
        return $this->view('emails.verify-email-change')
                    ->subject('Verificar cambio de correo electrónico')
                    ->with([
                        'name' => $this->verification->user->name,
                        'currentEmail' => $this->verification->current_email,
                        'newEmail' => $this->verification->new_email,
                        'verificationUrl' => $verificationUrl
                    ]);
    }
}