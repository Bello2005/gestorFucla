<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Convocatoria extends Model
{
    use \App\Traits\Auditable;

    protected $fillable = [
        'nombre',
        'entidad',
        'descripcion',
        'fecha_inicio',
        'fecha_cierre',
        'fecha_resultados',
        'responsable',
        'enlace',
        'correo_contacto',
        'observacion',
        'created_by',
    ];

    protected $casts = [
        'fecha_inicio'     => 'date',
        'fecha_cierre'     => 'date',
        'fecha_resultados' => 'date',
    ];

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
