<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoLineaInvestigacion extends Model
{
    protected $table = 'catalogo_lineas_investigacion';

    protected $fillable = ['nombre', 'area', 'activo', 'orden'];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'orden' => 'integer',
        ];
    }
}
