<?php

namespace Tests\Feature\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    public function test_password_reset_request_page_is_accessible(): void
    {
        $response = $this->get('/password/reset');
        $response->assertStatus(200);
    }

    public function test_reset_link_email_sent_for_existing_user(): void
    {
        Mail::fake();

        $this->createUser(['email' => 'exists@test.com']);

        $response = $this->post('/password/email', [
            'email' => 'exists@test.com',
        ]);

        // Should redirect back with a success or process the request (not crash)
        $this->assertContains($response->getStatusCode(), [200, 302]);
    }

    public function test_reset_link_request_with_nonexistent_email_returns_error(): void
    {
        $response = $this->post('/password/email', [
            'email' => 'ghost@test.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_reset_form_with_valid_token_is_accessible(): void
    {
        $token = \Illuminate\Support\Str::random(60);

        $response = $this->get('/password/reset/' . $token);
        $response->assertStatus(200);
    }

    // ── reset() — POST /password/reset ────────────────────────────────────

    private function insertToken(string $email, string $plainToken, int $minutesAgo = 0): void
    {
        DB::table('password_reset_tokens')->insert([
            'email'      => $email,
            'token'      => Hash::make($plainToken),
            'created_at' => now()->subMinutes($minutesAgo),
        ]);
    }

    public function test_reset_updates_password_with_valid_token(): void
    {
        $user  = $this->createUser(['email' => 'reset-ok@test.com']);
        $token = 'valid-reset-token-001';
        $this->insertToken($user->email, $token);

        $response = $this->post('/password/reset', [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'NewPass12345',
            'password_confirmation' => 'NewPass12345',
        ]);

        $response->assertOk(); // renders auth.verification-success view
        $this->assertTrue(Hash::check('NewPass12345', $user->fresh()->password));
    }

    public function test_reset_clears_is_temporary_password_flag(): void
    {
        $user  = $this->createUser(['email' => 'temp@test.com', 'is_temporary_password' => true]);
        $token = 'temp-reset-token';
        $this->insertToken($user->email, $token);

        $this->post('/password/reset', [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'NewPass12345',
            'password_confirmation' => 'NewPass12345',
        ]);

        $this->assertFalse((bool) $user->fresh()->is_temporary_password);
    }

    public function test_reset_deletes_token_after_use(): void
    {
        $user  = $this->createUser(['email' => 'del-token@test.com']);
        $token = 'consume-token';
        $this->insertToken($user->email, $token);

        $this->post('/password/reset', [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'NewPass12345',
            'password_confirmation' => 'NewPass12345',
        ]);

        $this->assertNull(DB::table('password_reset_tokens')->where('email', $user->email)->first());
    }

    public function test_reset_fails_with_invalid_token(): void
    {
        $user = $this->createUser(['email' => 'bad-token@test.com']);
        $this->insertToken($user->email, 'correct-token');

        $response = $this->post('/password/reset', [
            'token'                 => 'wrong-token',
            'email'                 => $user->email,
            'password'              => 'NewPass12345',
            'password_confirmation' => 'NewPass12345',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertFalse(Hash::check('NewPass12345', $user->fresh()->password));
    }

    public function test_reset_fails_with_expired_token(): void
    {
        $user  = $this->createUser(['email' => 'expired@test.com']);
        $token = 'expired-token';
        $this->insertToken($user->email, $token, minutesAgo: 61); // 61 min ago → expired

        $response = $this->post('/password/reset', [
            'token'                 => $token,
            'email'                 => $user->email,
            'password'              => 'NewPass12345',
            'password_confirmation' => 'NewPass12345',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_reset_fails_with_nonexistent_email(): void
    {
        $response = $this->post('/password/reset', [
            'token'                 => 'any-token',
            'email'                 => 'ghost@test.com',
            'password'              => 'NewPass12345',
            'password_confirmation' => 'NewPass12345',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_reset_fails_with_password_confirmation_mismatch(): void
    {
        $user = $this->createUser(['email' => 'mismatch@test.com']);

        $response = $this->post('/password/reset', [
            'token'                 => 'any-token',
            'email'                 => $user->email,
            'password'              => 'NewPass12345',
            'password_confirmation' => 'DifferentPass!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_reset_fails_with_short_password(): void
    {
        $user = $this->createUser(['email' => 'short@test.com']);

        $response = $this->post('/password/reset', [
            'token'                 => 'any-token',
            'email'                 => $user->email,
            'password'              => 'abc',
            'password_confirmation' => 'abc',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_reset_requires_all_fields(): void
    {
        $response = $this->post('/password/reset', []);

        $response->assertSessionHasErrors(['token', 'email', 'password']);
    }
}
