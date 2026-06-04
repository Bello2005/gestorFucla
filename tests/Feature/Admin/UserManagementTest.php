<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    // =========================================================
    //  Access control
    // =========================================================

    public function test_guest_cannot_access_users_index(): void
    {
        // The 'auth' middleware redirects to route('login') = '/' in this app
        $this->get('/users')->assertRedirect('/');
    }

    public function test_non_admin_gets_403_on_users_index(): void
    {
        $this->actingAsUser()->get('/users')->assertStatus(403);
    }

    public function test_admin_can_access_users_index(): void
    {
        $this->actingAsAdmin()->get('/users')->assertStatus(200);
    }

    // =========================================================
    //  show (GET /users/{id}) — returns JSON
    // =========================================================

    public function test_admin_can_show_user(): void
    {
        $target = $this->createUser(['email' => 'target@test.com']);

        $response = $this->actingAsAdmin()->getJson("/users/{$target->id}");

        $response->assertOk()->assertJsonFragment(['email' => 'target@test.com']);
    }

    // =========================================================
    //  store (POST /users)
    // =========================================================

    public function test_admin_can_create_user(): void
    {
        $response = $this->actingAsAdmin()->postJson('/users', [
            'name'     => 'New User',
            'email'    => 'new@test.com',
            'password' => 'Secret12345',
        ]);

        $response->assertOk()->assertJsonFragment(['message' => 'Usuario creado exitosamente']);
        $this->assertDatabaseHas('users', ['email' => 'new@test.com']);
    }

    public function test_store_requires_name_email_password(): void
    {
        $response = $this->actingAsAdmin()->postJson('/users', []);
        // 'roles' ya no es requerido — el rol siempre es 'user' al crear
        $response->assertStatus(422)->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_store_rejects_duplicate_email(): void
    {
        $this->createUser(['email' => 'dupe@test.com']);
        $roleId = Role::where('slug', 'user')->first()->id;

        $response = $this->actingAsAdmin()->postJson('/users', [
            'name'     => 'Dupe',
            'email'    => 'dupe@test.com',
            'password' => 'Secret12345',
            'roles'    => [$roleId],
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('email');
    }

    public function test_store_assigns_user_role_by_default(): void
    {
        $this->actingAsAdmin()->postJson('/users', [
            'name'     => 'Role Test',
            'email'    => 'roletest@test.com',
            'password' => 'Secret12345',
        ]);

        $user = User::where('email', 'roletest@test.com')->first();
        // Los usuarios nuevos siempre se crean con rol 'user'
        $this->assertTrue($user->hasRole('user'));
        $this->assertFalse($user->isAdmin());
    }

    // =========================================================
    //  update (PUT /users/{id})
    // =========================================================

    public function test_admin_can_update_user(): void
    {
        $target = $this->createUser(['email' => 'upd@test.com']);

        $response = $this->actingAsAdmin()->putJson("/users/{$target->id}", [
            'name'     => 'Updated Name',
            'email'    => 'upd@test.com',
            'is_admin' => false,
        ]);

        $response->assertOk()->assertJsonFragment(['message' => 'Usuario actualizado exitosamente']);
        $this->assertDatabaseHas('users', ['name' => 'Updated Name']);
    }

    public function test_update_rejects_email_taken_by_another_user(): void
    {
        $this->createUser(['email' => 'taken@test.com']);
        $target = $this->createUser(['email' => 'target2@test.com']);
        $roleId = Role::where('slug', 'user')->first()->id;

        $response = $this->actingAsAdmin()->putJson("/users/{$target->id}", [
            'name'  => 'X',
            'email' => 'taken@test.com',
            'roles' => [$roleId],
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('email');
    }

    // =========================================================
    //  destroy (DELETE /users/{id})
    // =========================================================

    public function test_admin_can_delete_another_user(): void
    {
        $target = $this->createUser(['email' => 'del@test.com']);

        $response = $this->actingAsAdmin()->deleteJson("/users/{$target->id}");

        $response->assertOk()->assertJsonFragment(['message' => 'Usuario eliminado exitosamente']);
        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->deleteJson("/users/{$admin->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    // =========================================================
    //  resetPassword (POST /users/{id}/reset-password)
    // =========================================================

    public function test_reset_password_via_email_sends_mail(): void
    {
        Mail::fake();
        $target = $this->createUser(['email' => 'reset@test.com']);

        $response = $this->actingAsAdmin()->postJson("/users/{$target->id}/reset-password", [
            'reset_type'          => 'email',
            'motivo'              => '',
            'force_change'        => '0',
            'invalidate_sessions' => '0',
        ]);

        $response->assertOk()->assertJsonFragment(['success' => true]);
        Mail::assertSent(\App\Mail\PasswordReset::class);
    }

    public function test_reset_password_temporal_returns_password(): void
    {
        $target = $this->createUser(['email' => 'temp@test.com']);

        $response = $this->actingAsAdmin()->postJson("/users/{$target->id}/reset-password", [
            'reset_type'          => 'temporal',
            'motivo'              => 'Prueba temporal',
            'force_change'        => '1',
            'invalidate_sessions' => '0',
        ]);

        $response->assertOk()->assertJsonFragment(['success' => true]);
        $this->assertStringContainsString('Contraseña temporal', $response->json('message'));
    }

    public function test_reset_password_validation_fails_without_motivo_for_temporal(): void
    {
        $target = $this->createUser(['email' => 'val@test.com']);

        $response = $this->actingAsAdmin()->postJson("/users/{$target->id}/reset-password", [
            'reset_type'          => 'temporal',
            'motivo'              => '',
            'force_change'        => '1',
            'invalidate_sessions' => '0',
        ]);

        $response->assertStatus(422);
    }

    // ── Validación de store ───────────────────────────────────────────────

    public function test_store_rejects_short_password(): void
    {
        $roleId = Role::where('slug', 'user')->first()->id;

        $response = $this->actingAsAdmin()->postJson('/users', [
            'name'     => 'Weak Pass',
            'email'    => 'weak@test.com',
            'password' => '123',
            'roles'    => [$roleId],
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('password');
    }

    public function test_store_rejects_invalid_email_format(): void
    {
        $roleId = Role::where('slug', 'user')->first()->id;

        $response = $this->actingAsAdmin()->postJson('/users', [
            'name'     => 'Bad Email',
            'email'    => 'not-an-email',
            'password' => 'SecurePass1!',
            'roles'    => [$roleId],
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('email');
    }

    public function test_update_admin_can_promote_user_to_admin(): void
    {
        $target = $this->createUser(['email' => 'promote@test.com']);

        $response = $this->actingAsAdmin()->putJson("/users/{$target->id}", [
            'name'     => $target->name,
            'email'    => $target->email,
            'is_admin' => true,
        ]);

        $response->assertOk();
        $this->assertTrue($target->fresh()->isAdmin());
    }

    // ── Validación de update ──────────────────────────────────────────────

    public function test_update_can_change_password(): void
    {
        $target = $this->createUser(['email' => 'pwdchange@test.com']);

        $this->actingAsAdmin()->putJson("/users/{$target->id}", [
            'name'     => $target->name,
            'email'    => $target->email,
            'password' => 'BrandNewPass1!',
            'is_admin' => false,
        ]);

        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('BrandNewPass1!', $target->fresh()->password));
    }

    // ── Reset password flags ───────────────────────────────────────────────

    public function test_reset_with_force_change_sets_temporary_flag(): void
    {
        $target = $this->createUser(['email' => 'forcechange@test.com']);

        $response = $this->actingAsAdmin()->postJson("/users/{$target->id}/reset-password", [
            'reset_type'          => 'temporal',
            'motivo'              => 'Cambio forzado por política',
            'force_change'        => '1',
            'invalidate_sessions' => '0',
        ]);

        $response->assertOk();
        $this->assertTrue((bool) $target->fresh()->is_temporary_password);
    }
}
