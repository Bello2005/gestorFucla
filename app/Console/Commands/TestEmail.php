<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía un correo de prueba a la dirección especificada';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        try {
            \Mail::to($email)->send(new \App\Mail\TestMail());
            $this->info("Correo enviado exitosamente a {$email}");
        } catch (\Exception $e) {
            $this->error("Error al enviar el correo: " . $e->getMessage());
            $this->line("Detalles completos del error:");
            $this->line($e->getTraceAsString());
        }
    }
}
