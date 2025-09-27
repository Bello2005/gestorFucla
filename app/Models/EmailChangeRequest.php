<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Support\Str;

class EmailChangeRequest extends Model
{
    protected $fillable = [
        'user_id',
        'current_email',
        'new_email',
        'token',
        'expires_at',
        'verified_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    public static function createRequest($userId, $currentEmail, $newEmail): self
    {
        // Invalidar solicitudes anteriores pendientes
        self::where('user_id', $userId)
            ->whereNull('verified_at')
            ->delete();

        return self::create([
            'user_id' => $userId,
            'current_email' => $currentEmail,
            'new_email' => $newEmail,
            'token' => Str::random(64),
            'expires_at' => Carbon::now()->addHours(24),
        ]);
    }
}
