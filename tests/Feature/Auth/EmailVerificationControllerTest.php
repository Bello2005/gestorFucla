<?php

namespace Tests\Feature\Auth;

use App\Models\EmailVerification;
use Tests\TestCase;

class EmailVerificationControllerTest extends TestCase
{
    public function test_invalid_token_renders_error_view(): void
    {
        $response = $this->get(route('verify.email.change', ['token' => 'totally-invalid-token']));

        $response->assertOk();
        $response->assertViewIs('auth.email-verified');
        $response->assertViewHas('error');
    }

    public function test_already_used_token_renders_error_view(): void
    {
        $user = $this->createUser();
        EmailVerification::create([
            'user_id'       => $user->id,
            'current_email' => $user->email,
            'new_email'     => 'used@test.com',
            'token'         => 'already-used-token',
            'expires_at'    => now()->addHour(),
            'verified'      => true,
        ]);

        $response = $this->get(route('verify.email.change', ['token' => 'already-used-token']));

        $response->assertOk();
        $response->assertViewHas('error');
    }

    public function test_expired_token_renders_expiry_error(): void
    {
        $user = $this->createUser();
        EmailVerification::create([
            'user_id'       => $user->id,
            'current_email' => $user->email,
            'new_email'     => 'expired@test.com',
            'token'         => 'expired-token',
            'expires_at'    => now()->subHour(),
            'verified'      => false,
        ]);

        $response = $this->get(route('verify.email.change', ['token' => 'expired-token']));

        $response->assertOk();
        $response->assertViewHas('error');
    }

    public function test_valid_token_updates_user_email(): void
    {
        $user = $this->createUser();
        EmailVerification::create([
            'user_id'       => $user->id,
            'current_email' => $user->email,
            'new_email'     => 'verified-new@test.com',
            'token'         => 'valid-verify-token',
            'expires_at'    => now()->addHour(),
            'verified'      => false,
        ]);

        $response = $this->get(route('verify.email.change', ['token' => 'valid-verify-token']));

        $response->assertOk();
        $response->assertViewHas('success', true);
        $this->assertSame('verified-new@test.com', $user->fresh()->email);
    }

    public function test_valid_token_marks_verification_as_done(): void
    {
        $user = $this->createUser();
        EmailVerification::create([
            'user_id'       => $user->id,
            'current_email' => $user->email,
            'new_email'     => 'done@test.com',
            'token'         => 'mark-done-token',
            'expires_at'    => now()->addHour(),
            'verified'      => false,
        ]);

        $this->get(route('verify.email.change', ['token' => 'mark-done-token']));

        $this->assertDatabaseHas('email_verifications', [
            'token'    => 'mark-done-token',
            'verified' => true,
        ]);
    }
}
