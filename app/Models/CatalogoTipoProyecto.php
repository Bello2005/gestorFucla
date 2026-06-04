<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class CatalogoTipoProyecto extends Model
{
    use Auditable;
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
