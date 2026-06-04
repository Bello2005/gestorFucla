<?php

namespace Tests\Unit\Models;

use App\Models\Module;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

/**
 * Tests para los métodos del sistema de permisos añadidos a User:
 * isAdmin(), canView(), canEdit(), effectiveRoleLabel(), permissions()
 */
class UserPermissionMethodsTest extends TestCase
{
    // =========================================================
    //  permissions() relation
    // =========================================================

    public function test_permissions_relation_is_has_many(): void
    {
        $user = $this->createUser();
        $this->assertInstanceOf(HasMany::class, $user->permissions());
    }

    // =========================================================
    //  isAdmin()
    // =========================================================

    public function test_is_admin_returns_true_for_admin_user(): void
    {
        $admin = $this->createAdmin();
        $this->assertTrue($admin->isAdmin());
    }

    public function test_is_admin_returns_false_for_regular_user(): void
    {
        $user = $this->createUser();
        $this->assertFalse($user->isAdmin());
    }

    // =========================================================
    //  canView()
    // =========================================================

    public function test_admin_can_view_any_module_without_explicit_permission(): void
    {
        $admin = $this->createAdmin();
        $this->assertTrue($admin->canView('proyectos'));
        $this->assertTrue($admin->canView('banco'));
        $this->assertTrue($admin->canView('estadistica'));
        $this->assertTrue($admin->canView('auditoria'));
        $this->assertTrue($admin->canView('usuarios'));
        $this->assertTrue($admin->canView('solicitudes'));
        $this->assertTrue($admin->canView('catalogos'));
    }

    public function test_user_cannot_view_module_without_permission(): void
    {
        $user = $this->createUser();
        $this->assertFalse($user->canView('proyectos'));
        $this->assertFalse($user->canView('banco'));
        $this->assertFalse($user->canView('estadistica'));
    }

    public function test_user_can_view_module_when_granted(): void
    {
        $user = $this->createUserWithPermissions(['proyectos'], false);
        $this->assertTrue($user->canView('proyectos'));
        $this->assertFalse($user->canView('banco'));
    }

    public function test_can_view_uses_cached_permissions_relation(): void
    {
        $user = $this->createUserWithPermissions(['proyectos'], false);
        $user->load('permissions.module');

        // Con relación cargada no debe hacer queries adicionales
        $this->assertTrue($user->canView('proyectos'));
        $this->assertFalse($user->canView('banco'));
    }

    // =========================================================
    //  canEdit()
    // =========================================================

    public function test_admin_can_edit_any_module_without_explicit_permission(): void
    {
        $admin = $this->createAdmin();
        $this->assertTrue($admin->canEdit('proyectos'));
        $this->assertTrue($admin->canEdit('banco'));
    }

    public function test_user_cannot_edit_module_without_permission(): void
    {
        $user = $this->createUser();
        $this->assertFalse($user->canEdit('proyectos'));
    }

    public function test_user_with_view_only_cannot_edit(): void
    {
        $user = $this->createUserWithPermissions(['proyectos'], false); // canEdit=false
        $this->assertTrue($user->canView('proyectos'));
        $this->assertFalse($user->canEdit('proyectos'));
    }

    public function test_user_with_edit_permission_can_edit_and_view(): void
    {
        $user = $this->createUserWithPermissions(['proyectos'], true); // canEdit=true
        $this->assertTrue($user->canView('proyectos'));
        $this->assertTrue($user->canEdit('proyectos'));
    }

    public function test_edit_permission_on_read_only_module_is_never_granted(): void
    {
        // auditoria es read_only, grantPermissions no debe asignar can_edit
        $user = $this->createUserWithPermissions(['auditoria'], true);
        $this->assertTrue($user->canView('auditoria'));
        $this->assertFalse($user->canEdit('auditoria'));
    }

    public function test_permissions_on_different_modules_are_independent(): void
    {
        $user = $this->createUserWithPermissions(['proyectos'], true);
        $this->grantPermissions($user, ['banco'], false);
        $user = $user->fresh(['roles', 'permissions.module']);

        $this->assertTrue($user->canEdit('proyectos'));
        $this->assertTrue($user->canView('banco'));
        $this->assertFalse($user->canEdit('banco'));
        $this->assertFalse($user->canView('estadistica'));
    }

    // =========================================================
    //  effectiveRoleLabel()
    // =========================================================

    public function test_effective_role_label_is_admin_for_admin(): void
    {
        $admin = $this->createAdmin();
        $this->assertSame('admin', $admin->effectiveRoleLabel());
    }

    public function test_effective_role_label_is_sin_acceso_with_no_permissions(): void
    {
        $user = $this->createUser();
        $this->assertSame('sin acceso', $user->effectiveRoleLabel());
    }

    public function test_effective_role_label_is_lector_with_all_view_no_edit(): void
    {
        $user = $this->createUserWithPermissions(['proyectos', 'banco'], false);
        $this->assertSame('lector', $user->effectiveRoleLabel());
    }

    public function test_effective_role_label_is_editor_when_all_active_permissions_have_edit(): void
    {
        // Solo módulos editables con edit=true → activos todos tienen can_edit=true
        $user = $this->createUserWithPermissions(['proyectos', 'banco'], true);
        $this->assertSame('editor', $user->effectiveRoleLabel());
    }

    public function test_effective_role_label_is_personalizado_with_mixed_permissions(): void
    {
        $user = $this->createUserWithPermissions(['proyectos'], true);  // edit
        $this->grantPermissions($user, ['banco'], false);               // view only
        $user = $user->fresh(['roles', 'permissions.module']);

        $this->assertSame('personalizado', $user->effectiveRoleLabel());
    }

    public function test_effective_role_label_uses_cached_relation(): void
    {
        $user = $this->createUserWithPermissions(['proyectos'], false);
        $user->load('permissions');

        // Debe funcionar con la relación ya cargada
        $this->assertSame('lector', $user->effectiveRoleLabel());
    }

    public function test_single_view_only_permission_is_lector_not_personalizado(): void
    {
        $user = $this->createUserWithPermissions(['proyectos'], false);
        $this->assertSame('lector', $user->effectiveRoleLabel());
    }
}
