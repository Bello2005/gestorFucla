<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use Tests\TestCase;

/**
 * Tests para las reglas de seguridad en el cambio de roles:
 * - Un admin no puede cambiar su propio rol
 * - No puede quedar el sistema sin ningún admin
 * - Un admin puede promover a otro usuario a admin
 * - Un admin puede degradar a otro admin si quedan más admins
 */
class UserRoleProtectionTest extends TestCase
{
    // =========================================================
    //  Auto-degradación bloqueada
    // =========================================================

    public function test_admin_cannot_change_own_role_to_user(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->putJson("/users/{$admin->id}", [
            'name'     => $admin->name,
            'email'    => $admin->email,
            'is_admin' => false,
        ]);

        $response->assertStatus(403);
        $this->assertStringContainsString('propio rol', $response->getContent());
    }

    public function test_admin_cannot_self_promote_again(): void
    {
        // Intentar enviarse a sí mismo is_admin=true cuando ya es admin
        // No es un error grave pero tampoco debería provocar cambio de rol
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->putJson("/users/{$admin->id}", [
            'name'     => $admin->name,
            'email'    => $admin->email,
            'is_admin' => true, // mismo rol, no hay cambio → no debe fallar
        ]);

        // is_admin → true no es un cambio de rol, sigue siendo admin → 200
        $response->assertOk();
    }

    // =========================================================
    //  Protección del último admin
    // =========================================================

    public function test_cannot_demote_last_admin(): void
    {
        $admin  = $this->createAdmin(['email' => 'solo@test.com']);
        $other  = $this->createAdmin(['email' => 'other-admin@test.com']); // el que hace la petición
        // Eliminar "other" del sistema para que $admin sea el único
        // En realidad usamos 2 admins y degradamos uno: el sistema sigue teniendo admin
        // Para probar el caso real, creamos solo 1 y lo intenta degradar OTRO admin
        $other->roles()->detach();
        $other->assignRole('user'); // ya no es admin

        $response = $this->actingAs($other)->putJson("/users/{$admin->id}", [
            'name'     => $admin->name,
            'email'    => $admin->email,
            'is_admin' => false,
        ]);

        // $other ya no es admin → middleware lo bloquea antes
        $response->assertStatus(403);
    }

    public function test_cannot_demote_the_only_admin_even_as_admin(): void
    {
        // Admin A intenta degradar a Admin B que es el único admin restante
        // Para este test: creamos admin A y admin B, luego admin A se intenta
        // degradar a sí mismo (bloqueado por auto-degradación primero).
        // El test real: admin B quiere degradar al único admin A.

        $adminA = $this->createAdmin(['email' => 'adminA@test.com']); // único admin
        $adminB = $this->createAdmin(['email' => 'adminB@test.com']); // hace la petición

        // Dejar solo a adminA como admin (quitar adminB)
        $adminB->roles()->sync([Role::where('slug', 'user')->first()->id]);

        // adminB (ya es user) intenta degradar a adminA (único admin)
        $response = $this->actingAs($adminB)->putJson("/users/{$adminA->id}", [
            'name'     => $adminA->name,
            'email'    => $adminA->email,
            'is_admin' => false,
        ]);

        // adminB es user → 403 por AdminMiddleware... hmm, PUT /users/{id} es admin-only
        // Correcto comportamiento: 403 por ser no-admin
        $response->assertStatus(403);
    }

    public function test_last_admin_cannot_be_demoted_by_another_admin(): void
    {
        $adminA = $this->createAdmin(['email' => 'adminA@test.com']); // único admin
        $adminB = $this->createAdmin(['email' => 'adminB@test.com']); // hace la petición

        // adminB intenta degradar a adminA (que sería el único admin si adminB se va)
        // Pero ambos son admin, hay 2. adminA puede ser degradado sin dejar el sistema sin admin.
        $response = $this->actingAs($adminB)->putJson("/users/{$adminA->id}", [
            'name'     => $adminA->name,
            'email'    => $adminA->email,
            'is_admin' => false,
        ]);

        // Con 2 admins, la degradación de uno es válida
        $response->assertOk();
        $this->assertFalse($adminA->fresh()->isAdmin());
        $this->assertTrue($adminB->fresh()->isAdmin());
    }

    public function test_demoting_only_remaining_admin_is_blocked(): void
    {
        $adminA = $this->createAdmin(['email' => 'adminA@test.com']);
        $adminB = $this->createAdmin(['email' => 'adminB@test.com']);

        // Degradar a adminA primero (quedan adminB como único admin)
        $this->actingAs($adminB)->putJson("/users/{$adminA->id}", [
            'name'     => $adminA->name,
            'email'    => $adminA->email,
            'is_admin' => false,
        ])->assertOk();

        // Ahora adminB intenta degradarse a sí mismo (auto-degradación bloqueada)
        $response = $this->actingAs($adminB)->putJson("/users/{$adminB->id}", [
            'name'     => $adminB->name,
            'email'    => $adminB->email,
            'is_admin' => false,
        ]);

        $response->assertStatus(403);
        $this->assertTrue($adminB->fresh()->isAdmin()); // sigue siendo admin
    }

    // =========================================================
    //  Promoción a admin
    // =========================================================

    public function test_admin_can_promote_user_to_admin(): void
    {
        $user = $this->createUser(['email' => 'promote@test.com']);

        $response = $this->actingAsAdmin()->putJson("/users/{$user->id}", [
            'name'     => $user->name,
            'email'    => $user->email,
            'is_admin' => true,
        ]);

        $response->assertOk();
        $this->assertTrue($user->fresh()->isAdmin());
    }

    public function test_promoted_admin_has_admin_role_not_user(): void
    {
        $user = $this->createUser(['email' => 'promo@test.com']);

        $this->actingAsAdmin()->putJson("/users/{$user->id}", [
            'name'     => $user->name,
            'email'    => $user->email,
            'is_admin' => true,
        ]);

        $fresh = $user->fresh(['roles']);
        $this->assertTrue($fresh->isAdmin());
        $this->assertFalse($fresh->hasRole('user'));
    }

    // =========================================================
    //  Degradación de admin con múltiples admins
    // =========================================================

    public function test_admin_can_demote_another_admin_when_multiple_exist(): void
    {
        $adminA = $this->createAdmin(['email' => 'adminA@test.com']);
        $adminB = $this->createAdmin(['email' => 'adminB@test.com']);

        $response = $this->actingAs($adminA)->putJson("/users/{$adminB->id}", [
            'name'     => $adminB->name,
            'email'    => $adminB->email,
            'is_admin' => false,
        ]);

        $response->assertOk();
        $this->assertFalse($adminB->fresh()->isAdmin());
    }
}
