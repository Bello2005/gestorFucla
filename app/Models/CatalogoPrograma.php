<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class CatalogoPrograma extends Model
{
    use Auditable;
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
