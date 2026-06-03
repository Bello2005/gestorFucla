<?php

namespace Tests\Feature;

use App\Models\EmailVerification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    // ── update ────────────────────────────────────────────────────────────

    public function test_guest_cannot_update_profile(): void
    {
        $this->post(route('profile.update'), ['name' => 'Guest'])->assertRedirect(route('login'));
    }

    public function test_update_requires_name(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->postJson(route('profile.update'), ['name' => '']);

        // ProfileController wraps validation in a broad try-catch, so it returns 500 with success:false
        $response->assertJson(['success' => false]);
    }

    public function test_update_changes_name(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->postJson(route('profile.update'), ['name' => 'New Name']);

        $response->assertOk();
        $this->assertSame('New Name', $user->fresh()->name);
    }

    public function test_update_changes_password(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)->postJson(route('profile.update'), [
            'name'                     => $user->name,
            'current_password'         => 'password',
            'new_password'             => 'NewSecure1!',
            'new_password_confirmation' => 'NewSecure1!',
        ]);

        $this->assertTrue(Hash::check('NewSecure1!', $user->fresh()->password));
    }

    public function test_update_wrong_current_password_returns_error(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->postJson(route('profile.update'), [
            'name'                     => $user->name,
            'current_password'         => 'wrong',
            'new_password'             => 'NewSecure1!',
            'new_password_confirmation' => 'NewSecure1!',
        ]);

        $response->assertJson(['success' => false]);
    }

    public function test_update_email_change_sends_verification_mail(): void
    {
        Mail::fake();
        $user = $this->createUser();

        $response = $this->actingAs($user)->postJson(route('profile.update'), [
            'name'             => $user->name,
            'current_password' => 'password',
            'new_email'        => 'newemail@test.com',
        ]);

        $response->assertOk();
        $response->assertJson(['email_verification_sent' => true]);
        $this->assertDatabaseHas('email_verifications', ['new_email' => 'newemail@test.com']);
    }

    // ── verifyEmail ───────────────────────────────────────────────────────

    public function test_verify_email_with_invalid_token_redirects_with_error(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get(route('profile.verify-email', ['token' => 'bad-token']));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_verify_email_with_expired_token_redirects_with_error(): void
    {
        $user = $this->createUser();
        EmailVerification::create([
            'user_id'       => $user->id,
            'current_email' => $user->email,
            'new_email'     => 'expired@test.com',
            'token'         => 'expired-profile-token',
            'expires_at'    => now()->subHour(),
            'verified'      => false,
        ]);

        $response = $this->actingAs($user)->get(
            route('profile.verify-email', ['token' => 'expired-profile-token'])
        );

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_verify_email_with_valid_token_updates_email(): void
    {
        $user = $this->createUser();
        EmailVerification::create([
            'user_id'       => $user->id,
            'current_email' => $user->email,
            'new_email'     => 'profile-verified@test.com',
            'token'         => 'valid-profile-verify',
            'expires_at'    => now()->addHour(),
            'verified'      => false,
        ]);

        $response = $this->actingAs($user)->get(
            route('profile.verify-email', ['token' => 'valid-profile-verify'])
        );

        $response->assertOk();
        $this->assertSame('profile-verified@test.com', $user->fresh()->email);
    }
}
