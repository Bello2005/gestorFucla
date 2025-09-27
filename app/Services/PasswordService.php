<?php

namespace App\Services;

class PasswordService
{
    /**
     * Genera una contraseña temporal aleatoria segura
     *
     * @param int $length Longitud de la contraseña
     * @return string
     */
    public static function generateTemporaryPassword($length = 12)
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '!@#$%^&*()-_+=';

        // Asegurar al menos un carácter de cada tipo
        $password = [
            $uppercase[random_int(0, strlen($uppercase) - 1)],
            $lowercase[random_int(0, strlen($lowercase) - 1)],
            $numbers[random_int(0, strlen($numbers) - 1)],
            $special[random_int(0, strlen($special) - 1)]
        ];

        // Llenar el resto con caracteres aleatorios
        $allChars = $uppercase . $lowercase . $numbers . $special;
        for ($i = count($password); $i < $length; $i++) {
            $password[] = $allChars[random_int(0, strlen($allChars) - 1)];
        }

        // Mezclar los caracteres
        shuffle($password);

        return implode('', $password);
    }
}