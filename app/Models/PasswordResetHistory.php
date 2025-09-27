<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PasswordResetHistory extends Model
{
    protected $table = 'password_reset_history';

    protected $fillable = [
        'user_id',
        'type',
        'reason',
        'token',
        'completed',
        'completed_at'
    ];

    protected $casts = [
        'completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function createReset($userId, $type, $reason = null, $token = null)
    {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'reason' => $reason,
            'token' => $token,
            'completed' => false
        ]);
    }

    public function markAsCompleted()
    {
        $this->completed = true;
        $this->completed_at = Carbon::now();
        $this->save();
    }
}