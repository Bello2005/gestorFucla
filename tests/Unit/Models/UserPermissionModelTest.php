<?php

namespace Tests\Unit\Models;

use App\Models\Module;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

class UserPermissionModelTest extends TestCase
{
    private function proyectosModule(): Module
    {
        return Module::where('slug', 'proyectos')->first();
    }

    // =========================================================
    //  Relations
    // =========================================================

    public function test_user_relation_is_belongs_to(): void
    {
        $user = $this->createUser();
        $perm = UserPermission::create([
            'user_id'   => $user->id,
            'module_id' => $this->proyectosModule()->id,
            'can_view'  => true,
            'can_edit'  => false,
        ]);

        $this->assertInstanceOf(BelongsTo::class, $perm->user());
        $this->assertSame($user->id, $perm->user->id);
    }

    public function test_module_relation_is_belongs_to(): void
    {
        $user   = $this->createUser();
        $module = $this->proyectosModule();
        $perm   = UserPermission::create([
            'user_id'   => $user->id,
            'module_id' => $module->id,
            'can_view'  => true,
            'can_edit'  => false,
        ]);

        $this->assertInstanceOf(BelongsTo::class, $perm->module());
        $this->assertSame($module->id, $perm->module->id);
    }

    // =========================================================
    //  Casts
    // =========================================================

    public function test_can_view_and_can_edit_are_booleans(): void
    {
        $user = $this->createUser();
        $perm = UserPermission::create([
            'user_id'   => $user->id,
            'module_id' => $this->proyectosModule()->id,
            'can_view'  => true,
            'can_edit'  => false,
        ]);

        $this->assertIsBool($perm->can_view);
        $this->assertIsBool($perm->can_edit);
    }

    // =========================================================
    //  Unique constraint (user_id, module_id)
    // =========================================================

    public function test_duplicate_user_module_pair_throws_exception(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        $user   = $this->createUser();
        $module = $this->proyectosModule();

        UserPermission::create(['user_id' => $user->id, 'module_id' => $module->id, 'can_view' => true]);
        UserPermission::create(['user_id' => $user->id, 'module_id' => $module->id, 'can_view' => false]);
    }

    // =========================================================
    //  Cascade delete with user
    // =========================================================

    public function test_permissions_deleted_when_user_is_deleted(): void
    {
        $user = $this->createUserWithPermissions(['proyectos']);
        $userId = $user->id;

        $user->delete();

        $this->assertDatabaseMissing('user_permissions', ['user_id' => $userId]);
    }

    // =========================================================
    //  grantPermissions helper respects read_only
    // =========================================================

    public function test_grant_permissions_sets_can_edit_false_for_read_only_modules(): void
    {
        $user = $this->createUser();
        $this->grantPermissions($user, ['auditoria'], true); // true but read_only

        $module = Module::where('slug', 'auditoria')->first();
        $perm   = UserPermission::where('user_id', $user->id)
                                ->where('module_id', $module->id)
                                ->first();

        $this->assertTrue($perm->can_view);
        $this->assertFalse($perm->can_edit);
    }

    public function test_grant_permissions_sets_can_edit_true_for_editable_modules(): void
    {
        $user = $this->createUser();
        $this->grantPermissions($user, ['proyectos'], true);

        $module = Module::where('slug', 'proyectos')->first();
        $perm   = UserPermission::where('user_id', $user->id)
                                ->where('module_id', $module->id)
                                ->first();

        $this->assertTrue($perm->can_view);
        $this->assertTrue($perm->can_edit);
    }
}
