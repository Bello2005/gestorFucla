<?php

namespace App\Traits;

use App\Models\Audit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            self::audit('created', $model);
        });

        static::updated(function ($model) {
            self::audit('updated', $model);
        });

        static::deleted(function ($model) {
            self::audit('deleted', $model);
        });
    }

    protected static function audit($event, $model)
    {
        $ip = Request::ip();

        // Intentar obtener la IP real si está detrás de un proxy
        $realIp = Request::header('X-Forwarded-For');
        if ($realIp) {
            // Si hay múltiples IPs, tomar la primera
            $ips = explode(',', $realIp);
            $ip = trim($ips[0]);
        } else {
            // Intentar con X-Real-IP si X-Forwarded-For no está presente
            $realIp = Request::header('X-Real-IP');
            if ($realIp) {
                $ip = $realIp;
            }
        }

        // Mapear los eventos a las operaciones de auditoría
        $operationMap = [
            'created' => 'INSERT',
            'updated' => 'UPDATE',
            'deleted' => 'DELETE'
        ];

        $user = Auth::user();

        Audit::create([
            'table_name' => $model->getTable(),
            'operation' => $operationMap[$event] ?? strtoupper($event),
            'record_id' => (string) $model->id,
            'old_values' => $event === 'created' ? null : $model->getOriginal(),
            'new_values' => $model->getAttributes(),
            'changed_by' => $user ? $user->id : null,
            'user_name' => $user ? $user->name : null,
            'ip_address' => $ip
        ]);
    }
}
