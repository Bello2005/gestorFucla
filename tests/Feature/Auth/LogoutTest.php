<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class LogoutTest extends TestCase
{
    public function test_authenticated_user_can_logout(): void
    {
        $this->actingAsUser();

        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_unauthenticated_request_to_protected_route_redirects_to_login(): void
    {
        $response = $this->get('/dashboard');
        // route('login') points to '/' in this app
        $response->assertRedirect('/');
    }

    public function test_protected_routes_require_authentication(): void
    {
        $protectedRoutes = [
            '/proyectos',
            '/banco-proyectos',
            '/estadistica',
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            // route('login') points to '/' in this app
            $this->assertEquals(302, $response->getStatusCode(), "Route $route should redirect unauthenticated users");
        }
    }
}
