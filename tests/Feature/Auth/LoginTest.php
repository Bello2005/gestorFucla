<?php

namespace Tests\Feature\Auth;

use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_login_page_at_login_route_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_valid_credentials_redirect_to_dashboard(): void
    {
        $this->createUser(['email' => 'login@test.com']);

        $response = $this->post('/login', [
            'email'    => 'login@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_invalid_password_returns_error(): void
    {
        $this->createUser(['email' => 'bad@test.com']);

        $response = $this->post('/login', [
            'email'    => 'bad@test.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_nonexistent_email_returns_error(): void
    {
        $response = $this->post('/login', [
            'email'    => 'nobody@test.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_remember_me_sets_cookie(): void
    {
        $this->createUser(['email' => 'remember@test.com']);

        $response = $this->post('/login', [
            'email'    => 'remember@test.com',
            'password' => 'password',
            'remember' => '1',
        ]);

        $response->assertRedirect('/dashboard');
        // Laravel sets a remember cookie named "remember_web_*"
        $hasCookie = collect($response->headers->getCookies())
            ->contains(fn ($c) => str_starts_with($c->getName(), 'remember_web'));

        $this->assertTrue($hasCookie);
    }

    public function test_authenticated_user_can_see_login_page(): void
    {
        // This app does not have a guest-only guard on /login; authenticated users can still visit it
        $this->actingAsUser();

        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_validation_requires_email_and_password(): void
    {
        $response = $this->post('/login', []);
        $response->assertSessionHasErrors(['email', 'password']);
    }
}
