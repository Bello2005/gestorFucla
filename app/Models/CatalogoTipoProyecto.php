<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoTipoProyecto extends Model
{
    protected $table = 'catalogo_tipos_proyecto';

    protected $fillable = ['nombre', 'descripcion', 'activo', 'orden'];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'orden' => 'integer',
        ];
    }
}
