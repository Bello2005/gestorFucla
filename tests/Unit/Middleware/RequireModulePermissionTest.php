<?php

namespace Tests\Unit\Middleware;

use Tests\TestCase;

/**
 * Verifica que el middleware module.permission protege correctamente
 * cada módulo y nivel de acceso (view / edit).
 */
class RequireModulePermissionTest extends TestCase
{
    // =========================================================
    //  Acceso sin autenticación
    // =========================================================

    public function test_guest_redirected_from_proyectos(): void
    {
        $this->get('/proyectos')->assertRedirect();
    }

    public function test_guest_redirected_from_banco(): void
    {
        $this->get('/banco-proyectos')->assertRedirect();
    }

    public function test_guest_redirected_from_estadistica(): void
    {
        $this->get('/estadistica')->assertRedirect();
    }

    // =========================================================
    //  Usuario sin permisos → 403
    // =========================================================

    public function test_user_without_permissions_cannot_view_proyectos(): void
    {
        $this->actingAsUser()->get('/proyectos')->assertStatus(403);
    }

    public function test_user_without_permissions_cannot_view_banco(): void
    {
        $this->actingAsUser()->get('/banco-proyectos')->assertStatus(403);
    }

    public function test_user_without_permissions_cannot_view_estadistica(): void
    {
        $this->actingAsUser()->get('/estadistica')->assertStatus(403);
    }

    public function test_user_without_permissions_cannot_access_create_proyecto(): void
    {
        $this->actingAsUser()->get('/proyectos/create')->assertStatus(403);
    }

    public function test_user_without_permissions_cannot_store_proyecto(): void
    {
        $this->actingAsUser()->postJson('/proyectos', [])->assertStatus(403);
    }

    public function test_user_without_permissions_cannot_access_create_banco(): void
    {
        $this->actingAsUser()->get('/banco-proyectos/create')->assertStatus(403);
    }

    public function test_user_without_permissions_cannot_store_banco(): void
    {
        $this->actingAsUser()->postJson('/banco-proyectos', [])->assertStatus(403);
    }

    // =========================================================
    //  Usuario con permiso de vista → 200 en lectura, 403 en escritura
    // =========================================================

    public function test_user_with_proyectos_view_can_access_index(): void
    {
        $this->actingAsUserWith(['proyectos'], false)->get('/proyectos')->assertOk();
    }

    public function test_user_with_proyectos_view_cannot_access_create(): void
    {
        $this->actingAsUserWith(['proyectos'], false)->get('/proyectos/create')->assertStatus(403);
    }

    public function test_user_with_proyectos_view_cannot_store(): void
    {
        $this->actingAsUserWith(['proyectos'], false)->postJson('/proyectos', [])->assertStatus(403);
    }

    public function test_user_with_banco_view_can_access_index(): void
    {
        $this->actingAsUserWith(['banco'], false)->get('/banco-proyectos')->assertOk();
    }

    public function test_user_with_banco_view_cannot_create(): void
    {
        $this->actingAsUserWith(['banco'], false)->get('/banco-proyectos/create')->assertStatus(403);
    }

    public function test_user_with_estadistica_view_can_access(): void
    {
        $this->actingAsUserWith(['estadistica'], false)->get('/estadistica')->assertOk();
    }

    // =========================================================
    //  Usuario con permiso de edición → 200 en todo
    // =========================================================

    public function test_user_with_proyectos_edit_can_access_create(): void
    {
        $this->actingAsUserWith(['proyectos'])->get('/proyectos/create')->assertOk();
    }

    public function test_user_with_banco_edit_can_access_create(): void
    {
        $this->actingAsUserWith(['banco'])->get('/banco-proyectos/create')->assertOk();
    }

    // =========================================================
    //  Admin → bypasa todos los permisos
    // =========================================================

    public function test_admin_can_access_proyectos_without_explicit_permission(): void
    {
        $this->actingAsAdmin()->get('/proyectos')->assertOk();
    }

    public function test_admin_can_access_proyectos_create(): void
    {
        $this->actingAsAdmin()->get('/proyectos/create')->assertOk();
    }

    public function test_admin_can_access_banco(): void
    {
        $this->actingAsAdmin()->get('/banco-proyectos')->assertOk();
    }

    public function test_admin_can_access_estadistica(): void
    {
        $this->actingAsAdmin()->get('/estadistica')->assertOk();
    }

    // =========================================================
    //  Módulos de administración — permiso de vista suficiente
    // =========================================================

    public function test_user_without_auditoria_permission_gets_403(): void
    {
        $this->actingAsUser()->get('/auditoria')->assertStatus(403);
    }

    public function test_user_with_auditoria_view_can_access(): void
    {
        $this->actingAsUserWith(['auditoria'], false)->get('/auditoria')->assertOk();
    }

    public function test_user_with_usuarios_view_can_see_list(): void
    {
        $this->actingAsUserWith(['usuarios'], false)->get('/users')->assertOk();
    }

    public function test_user_without_usuarios_permission_cannot_see_list(): void
    {
        $this->actingAsUser()->get('/users')->assertStatus(403);
    }

    public function test_user_with_solicitudes_view_can_see_list(): void
    {
        $this->actingAsUserWith(['solicitudes'], false)->get('/access-requests')->assertOk();
    }

    public function test_user_with_catalogos_view_can_see_list(): void
    {
        $this->actingAsUserWith(['catalogos'], false)->get('/catalogos')->assertOk();
    }
}
