<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'permissions'               => 'required|array',
            'permissions.*.module_id'   => 'required|integer|exists:modules,id',
            'permissions.*.can_view'    => 'required|boolean',
            'permissions.*.can_edit'    => 'required|boolean',
        ]);

        collect($validated['permissions'])->each(function ($p) use ($user) {
            if ($p['can_edit']) {
                $p['can_view'] = true;
            }

            // updateOrCreate dispara eventos Eloquent → la auditoría queda registrada
            UserPermission::updateOrCreate(
                ['user_id' => $user->id, 'module_id' => $p['module_id']],
                ['can_view' => $p['can_view'], 'can_edit'  => $p['can_edit']]
            );
        });

        return response()->json([
            'message'        => 'Permisos actualizados correctamente.',
            'effective_role' => $user->fresh()->effectiveRoleLabel(),
        ]);
    }
}
