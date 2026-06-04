<?php

namespace Tests\Feature\Admin;

use App\Models\Module;
use App\Models\User;
use App\Models\UserPermission;
use Tests\TestCase;

class PermissionControllerTest extends TestCase
{
    private function allModulesPayload(bool $canView = true, bool $canEdit = false): array
    {
        return Module::all()->map(fn($m) => [
            'module_id' => $m->id,
            'can_view'  => $canView,
            'can_edit'  => $canEdit,
        ])->toArray();
    }

    // =========================================================
    //  Acceso
    // =========================================================

    public function test_guest_cannot_update_permissions(): void
    {
        $target = $this->createUser(['email' => 'target@test.com']);
        // La app redirige al login (302) incluso para JSON
        $this->postJson("/users/{$target->id}/permissions", [])->assertStatus(302);
    }

    public function test_regular_user_cannot_update_permissions(): void
    {
        $target = $this->createUser(['email' => 'target@test.com']);
        $this->actingAsUser()->postJson("/users/{$target->id}/permissions", [
            'permissions' => $this->allModulesPayload(),
        ])->assertStatus(403);
    }

    public function test_admin_can_update_permissions(): void
    {
        $target = $this->createUser(['email' => 'target@test.com']);

        $response = $this->actingAsAdmin()->postJson("/users/{$target->id}/permissions", [
            'permissions' => $this->allModulesPayload(true, false),
        ]);

        $response->assertOk()->assertJsonFragment(['message' => 'Permisos actualizados correctamente.']);
    }

    // =========================================================
    //  Persistencia de permisos
    // =========================================================

    public function test_permissions_are_saved_in_database(): void
    {
        $target     = $this->createUser(['email' => 'target@test.com']);
        $proyectos  = Module::where('slug', 'proyectos')->first();

        $this->actingAsAdmin()->postJson("/users/{$target->id}/permissions", [
            'permissions' => [
                ['module_id' => $proyectos->id, 'can_view' => true, 'can_edit' => true],
            ],
        ]);

        $this->assertDatabaseHas('user_permissions', [
            'user_id'   => $target->id,
            'module_id' => $proyectos->id,
            'can_view'  => true,
            'can_edit'  => true,
        ]);
    }

    public function test_can_edit_true_forces_can_view_true(): void
    {
        $target    = $this->createUser(['email' => 'target@test.com']);
        $proyectos = Module::where('slug', 'proyectos')->first();

        $this->actingAsAdmin()->postJson("/users/{$target->id}/permissions", [
            'permissions' => [
                ['module_id' => $proyectos->id, 'can_view' => false, 'can_edit' => true],
            ],
        ]);

        $this->assertDatabaseHas('user_permissions', [
            'user_id'   => $target->id,
            'module_id' => $proyectos->id,
            'can_view'  => true,  // forzado
            'can_edit'  => true,
        ]);
    }

    public function test_upsert_updates_existing_permissions(): void
    {
        // Reutilizamos el mismo admin para evitar colisión de email en dos createAdmin()
        $admin     = $this->createAdmin();
        $target    = $this->createUser(['email' => 'target@test.com']);
        $proyectos = Module::where('slug', 'proyectos')->first();

        // Primera llamada: solo vista
        $this->actingAs($admin)->postJson("/users/{$target->id}/permissions", [
            'permissions' => [['module_id' => $proyectos->id, 'can_view' => true, 'can_edit' => false]],
        ]);

        // Segunda llamada: actualizar a edición
        $this->actingAs($admin)->postJson("/users/{$target->id}/permissions", [
            'permissions' => [['module_id' => $proyectos->id, 'can_view' => true, 'can_edit' => true]],
        ]);

        $this->assertSame(1, UserPermission::where('user_id', $target->id)->count());
        $this->assertDatabaseHas('user_permissions', [
            'user_id'   => $target->id,
            'module_id' => $proyectos->id,
            'can_edit'  => true,
        ]);
    }

    // =========================================================
    //  Rol efectivo en respuesta
    // =========================================================

    public function test_response_includes_effective_role(): void
    {
        $target = $this->createUser(['email' => 'target@test.com']);

        $response = $this->actingAsAdmin()->postJson("/users/{$target->id}/permissions", [
            'permissions' => $this->allModulesPayload(true, false),
        ]);

        $response->assertJsonFragment(['effective_role' => 'lector']);
    }

    public function test_effective_role_is_editor_when_only_editable_modules_with_edit(): void
    {
        $target = $this->createUser(['email' => 'target@test.com']);

        // Solo los módulos que admiten edición (proyectos, banco) con can_edit=true
        $payload = Module::where('read_only', false)->get()->map(fn($m) => [
            'module_id' => $m->id,
            'can_view'  => true,
            'can_edit'  => true,
        ])->toArray();

        $response = $this->actingAsAdmin()->postJson("/users/{$target->id}/permissions", [
            'permissions' => $payload,
        ]);

        $response->assertJsonFragment(['effective_role' => 'editor']);
    }

    public function test_effective_role_is_sin_acceso_when_all_can_view_false(): void
    {
        $target = $this->createUser(['email' => 'target@test.com']);

        // can_view=false en todos → sin acceso efectivo
        $response = $this->actingAsAdmin()->postJson("/users/{$target->id}/permissions", [
            'permissions' => $this->allModulesPayload(false, false),
        ]);

        $response->assertJsonFragment(['effective_role' => 'sin acceso']);
    }

    // =========================================================
    //  Validación
    // =========================================================

    public function test_permissions_field_is_required(): void
    {
        $target = $this->createUser(['email' => 'target@test.com']);

        $this->actingAsAdmin()->postJson("/users/{$target->id}/permissions", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['permissions']);
    }

    public function test_each_permission_must_have_valid_module_id(): void
    {
        $target = $this->createUser(['email' => 'target@test.com']);

        $this->actingAsAdmin()->postJson("/users/{$target->id}/permissions", [
            'permissions' => [['module_id' => 99999, 'can_view' => true, 'can_edit' => false]],
        ])->assertStatus(422);
    }

    public function test_can_view_must_be_boolean(): void
    {
        $target    = $this->createUser(['email' => 'target@test.com']);
        $proyectos = Module::where('slug', 'proyectos')->first();

        $this->actingAsAdmin()->postJson("/users/{$target->id}/permissions", [
            'permissions' => [['module_id' => $proyectos->id, 'can_view' => 'yes', 'can_edit' => false]],
        ])->assertStatus(422);
    }
}
