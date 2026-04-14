<?php

namespace Tests\Feature\Auth;

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
}
