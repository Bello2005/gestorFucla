<?php

namespace App\Observers;

use App\Models\Audit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditObserver
{
    protected $excludedFields = [
        'password', 
        'remember_token', 
        'updated_at', 
        'created_at'
    ];

    /**
     * Handle the Model "created" event.
     */
    public function created($model)
    {
        $this->logChanges('INSERT', $model);
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated($model)
    {
        // Para actualizaciones, solo registrar los campos que realmente cambiaron
        $changes = $model->getDirty();
        
        // Filtrar campos excluidos
        $changes = array_diff_key($changes, array_flip($this->excludedFields));
        
        if (!empty($changes)) {
            $this->logChanges('UPDATE', $model);
        }
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted($model)
    {
        $this->logChanges('DELETE', $model);
    }

    /**
     * Handle the Model "restored" event.
     */
    public function restored($model)
    {
        $this->logChanges('RESTORE', $model);
    }

    /**
     * Handle the Model "force deleted" event.
     */
    public function forceDeleted($model)
    {
        $this->logChanges('FORCE_DELETE', $model);
    }

    /**
     * Log changes to the audit table
     */
    protected function logChanges($operation, $model)
    {
        $user = Auth::user();
        
        $oldValues = null;
        $newValues = null;

        switch ($operation) {
            case 'UPDATE':
                $oldValues = array_intersect_key($model->getOriginal(), $model->getDirty());
                $newValues = $model->getDirty();
                break;
            case 'INSERT':
                $newValues = $model->getAttributes();
                break;
            case 'DELETE':
            case 'FORCE_DELETE':
                $oldValues = $model->getAttributes();
                break;
            case 'RESTORE':
                $newValues = $model->getAttributes();
                break;
        }

        // Filtrar campos excluidos
        if ($oldValues) {
            $oldValues = array_diff_key($oldValues, array_flip($this->excludedFields));
        }
        if ($newValues) {
            $newValues = array_diff_key($newValues, array_flip($this->excludedFields));
        }

        // Registrar el cambio
        Audit::create([
            'table_name' => $model->getTable(),
            'operation' => $operation,
            'record_id' => $model->getKey(),
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'changed_by' => $user ? $user->id : null,
            'user_name' => $user ? $user->name : null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent()
        ]);
    }
}