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

        $now = now();
        $rows = collect($validated['permissions'])->map(function ($p) use ($user, $now) {
            // can_edit implica can_view
            if ($p['can_edit']) {
                $p['can_view'] = true;
            }

            return [
                'user_id'    => $user->id,
                'module_id'  => $p['module_id'],
                'can_view'   => $p['can_view'],
                'can_edit'   => $p['can_edit'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->toArray();

        UserPermission::upsert(
            $rows,
            ['user_id', 'module_id'],
            ['can_view', 'can_edit', 'updated_at']
        );

        return response()->json([
            'message'        => 'Permisos actualizados correctamente.',
            'effective_role' => $user->fresh()->effectiveRoleLabel(),
        ]);
    }
}
