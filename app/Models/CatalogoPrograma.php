<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoPrograma extends Model
{
    protected $table = 'catalogo_programas';

    protected $fillable = ['nombre', 'facultad', 'activo', 'orden'];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'orden' => 'integer',
        ];
    }
}
