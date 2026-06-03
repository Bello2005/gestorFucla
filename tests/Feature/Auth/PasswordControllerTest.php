<?php

namespace Tests\Feature\Auth;

use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordControllerTest extends TestCase
{
    public function test_guest_cannot_access_change_password(): void
    {
        $response = $this->post(route('password.change.temporary'), [
            'current_password'          => 'password',
            'new_password'              => 'NewPass123!',
            'new_password_confirmation' => 'NewPass123!',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_wrong_current_password_returns_422(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->postJson(route('password.change.temporary'), [
            'current_password'          => 'wrong-password',
            'new_password'              => 'NewPass123!',
            'new_password_confirmation' => 'NewPass123!',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['error' => 'La contraseña actual es incorrecta']);
    }

    public function test_password_confirmation_mismatch_fails_validation(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->postJson(route('password.change.temporary'), [
            'current_password'          => 'password',
            'new_password'              => 'NewPass123!',
            'new_password_confirmation' => 'DifferentPass!',
        ]);

        $response->assertStatus(422);
    }

    public function test_new_password_too_short_fails_validation(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->postJson(route('password.change.temporary'), [
            'current_password'          => 'password',
            'new_password'              => 'short',
            'new_password_confirmation' => 'short',
        ]);

        $response->assertStatus(422);
    }

    public function test_valid_change_returns_success(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->postJson(route('password.change.temporary'), [
            'current_password'          => 'password',
            'new_password'              => 'NewSecure1!',
            'new_password_confirmation' => 'NewSecure1!',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
    }

    public function test_valid_change_updates_password_in_database(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)->postJson(route('password.change.temporary'), [
            'current_password'          => 'password',
            'new_password'              => 'NewSecure1!',
            'new_password_confirmation' => 'NewSecure1!',
        ]);

        $this->assertTrue(Hash::check('NewSecure1!', $user->fresh()->password));
    }

    public function test_valid_change_clears_is_temporary_password_flag(): void
    {
        $user = $this->createUser(['is_temporary_password' => true]);

        $this->actingAs($user)->postJson(route('password.change.temporary'), [
            'current_password'          => 'password',
            'new_password'              => 'NewSecure1!',
            'new_password_confirmation' => 'NewSecure1!',
        ]);

        $this->assertFalse((bool) $user->fresh()->is_temporary_password);
    }
}
