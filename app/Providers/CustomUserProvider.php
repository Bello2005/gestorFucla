<?php

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class CustomUserProvider extends EloquentUserProvider
{
    /**
     * Validate a user against the given credentials.
     */
    public function validateCredentials(UserContract $user, array $credentials): bool
    {
        $plain = $credentials['password'];

        // Primero intentar la validación normal con hash
        if ($this->hasher->check($plain, $user->getAuthPassword())) {
            return true;
        }

        // Si falla, verificar si la contraseña sin hashear coincide
        return $plain === $user->getAuthPassword();
    }
}