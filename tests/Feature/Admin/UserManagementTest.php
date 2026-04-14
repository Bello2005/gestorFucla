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
        $roleId = Role::where('slug', 'user')->first()->id;

        $response = $this->actingAsAdmin()->postJson('/users', [
            'name'     => 'New User',
            'email'    => 'new@test.com',
            'password' => 'Secret12345',
            'roles'    => [$roleId],
        ]);

        $response->assertOk()->assertJsonFragment(['message' => 'Usuario creado exitosamente']);
        $this->assertDatabaseHas('users', ['email' => 'new@test.com']);
    }

    public function test_store_requires_name_email_password_roles(): void
    {
        $response = $this->actingAsAdmin()->postJson('/users', []);
        $response->assertStatus(422)->assertJsonValidationErrors(['name', 'email', 'password', 'roles']);
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

    public function test_store_assigns_roles(): void
    {
        $roleId = Role::where('slug', 'admin')->first()->id;

        $this->actingAsAdmin()->postJson('/users', [
            'name'     => 'Role Test',
            'email'    => 'roletest@test.com',
            'password' => 'Secret12345',
            'roles'    => [$roleId],
        ]);

        $user = User::where('email', 'roletest@test.com')->first();
        $this->assertTrue($user->hasRole('admin'));
    }

    // =========================================================
    //  update (PUT /users/{id})
    // =========================================================

    public function test_admin_can_update_user(): void
    {
        $target = $this->createUser(['email' => 'upd@test.com']);
        $roleId = Role::where('slug', 'user')->first()->id;

        $response = $this->actingAsAdmin()->putJson("/users/{$target->id}", [
            'name'  => 'Updated Name',
            'email' => 'upd@test.com',
            'roles' => [$roleId],
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
}
