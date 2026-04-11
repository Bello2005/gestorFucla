<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BancoProyectoAnexo extends Model
{
    public $timestamps = false;

    protected $table = 'banco_proyecto_anexos';

    protected $fillable = [
        'banco_proyecto_id', 'tipo_anexo', 'nombre_original', 'ruta_archivo', 'tipo_archivo',
        'tamano_bytes', 'version', 'notas', 'uploaded_by', 'uploaded_at', 'is_current',
    ];

    protected function casts(): array
    {
        return [
            'tamano_bytes' => 'integer',
            'version' => 'integer',
            'is_current' => 'boolean',
            'uploaded_at' => 'datetime',
        ];
    }

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(BancoProyecto::class, 'banco_proyecto_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
