<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function build()
    {
        return $this->subject('Prueba de Correo')
                    ->view('emails.test')
                    ->with([
                        'mensaje' => 'Este es un correo de prueba para verificar la configuración del sistema.'
                    ]);
    }
}