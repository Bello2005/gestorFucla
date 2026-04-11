<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BancoProyectoHistorial extends Model
{
    public $timestamps = false;

    public const UPDATED_AT = null;

    protected $table = 'banco_proyecto_historial';

    protected $fillable = [
        'banco_proyecto_id', 'accion', 'campo_modificado', 'valor_anterior', 'valor_nuevo',
        'descripcion', 'user_id', 'user_name', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(BancoProyecto::class, 'banco_proyecto_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
