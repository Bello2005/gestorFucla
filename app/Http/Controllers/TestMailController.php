<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Log;

class TestMailController extends Controller
{
    public function testMail(Request $request)
    {
        try {
            $to = $request->input('email', 'deiner2005@outlook.com');
            
            Log::info('Iniciando prueba de envío de correo', [
                'mail_config' => [
                    'driver' => config('mail.default'),
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'encryption' => config('mail.mailers.smtp.encryption'),
                    'from_address' => config('mail.from.address')
                ]
            ]);
            
            Log::info('Verificando configuración SMTP', [
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'encryption' => config('mail.mailers.smtp.encryption'),
                'username' => config('mail.mailers.smtp.username')
            ]);
            
            // Intentar enviar el correo
            Mail::raw('Este es un correo de prueba enviado a las ' . now(), function($message) use ($to) {
                $message->to($to)
                        ->subject('Prueba de correo - ' . now());
            });
            
            Log::info('Correo de prueba enviado exitosamente', [
                'to' => $to,
                'timestamp' => now()->toDateTimeString()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Correo de prueba enviado exitosamente a: ' . $to,
                'timestamp' => now()->toDateTimeString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al enviar correo de prueba', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'to' => $to ?? null
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar correo: ' . $e->getMessage(),
                'details' => [
                    'error_type' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }
}