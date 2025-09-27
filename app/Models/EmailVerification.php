<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

class EmailVerification extends Model
{
    protected $fillable = [
        'user_id',
        'current_email',
        'new_email',
        'token',
        'signed_token',
        'verified',
        'verified_at',
        'expires_at'
    ];

    protected $casts = [
        'verified' => 'boolean',
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    public function generateSignedToken()
    {
        $this->signed_token = URL::temporarySignedRoute(
            'verify.email.change',
            $this->expires_at,
            ['token' => $this->token]
        );
        $this->save();
        
        return $this->signed_token;
    }

    public static function createVerification($userId, $currentEmail, $newEmail)
    {
        // Eliminar verificaciones anteriores pendientes para este usuario
        self::where('user_id', $userId)
            ->where('verified', false)
            ->delete();

        // Crear nueva verificación
        return self::create([
            'user_id' => $userId,
            'current_email' => $currentEmail,
            'new_email' => $newEmail,
            'token' => Str::random(64),
            'signed_token' => null,
            'expires_at' => Carbon::now()->addHours(24),
        ]);
    }
}
