<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class BancoProyecto extends Model
{
    use SoftDeletes;

    protected $table = 'banco_proyectos';

    protected $fillable = [
        'codigo', 'titulo', 'linea_investigacion', 'area_facultad', 'tipo_proyecto', 'convocatoria',
        'fecha_registro', 'estado',
        'resumen_ejecutivo', 'problema_necesidad', 'objetivo_general', 'justificacion', 'alcance',
        'poblacion_objetivo', 'cobertura_geografica',
        'presupuesto_estimado', 'fuente_financiacion', 'cofinanciacion', 'duracion_meses',
        'autores', 'tutor_director', 'programa_departamento', 'entidad_aliada', 'evaluador_asignado',
        'certificado_cumplimiento', 'certificado_fecha', 'certificado_observaciones',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'fecha_registro' => 'date',
            'presupuesto_estimado' => 'decimal:2',
            'cofinanciacion' => 'decimal:2',
            'duracion_meses' => 'integer',
            'autores' => 'array',
            'certificado_fecha' => 'date',
        ];
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function anexos(): HasMany
    {
        return $this->hasMany(BancoProyectoAnexo::class, 'banco_proyecto_id');
    }

    public function historial(): HasMany
    {
        return $this->hasMany(BancoProyectoHistorial::class, 'banco_proyecto_id')->orderByDesc('created_at');
    }

    protected static function booted(): void
    {
        static::creating(function (BancoProyecto $model) {
            if (empty($model->codigo)) {
                $model->codigo = static::generarCodigo();
            }
        });
    }

    public static function generarCodigo(): string
    {
        $year = (int) now()->format('Y');

        return DB::transaction(function () use ($year) {
            $prefix = sprintf('BP-%d-', $year);
            $last = static::withTrashed()
                ->where('codigo', 'like', $prefix.'%')
                ->orderByDesc('codigo')
                ->lockForUpdate()
                ->value('codigo');
            $next = 1;
            if ($last) {
                $num = (int) substr($last, strlen($prefix));
                $next = $num + 1;
            }

            return $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
        });
    }
}
