<?php

namespace Tests\Feature\Errors;

use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    // =========================================================
    //  404
    // =========================================================

    public function test_unknown_route_returns_404_view(): void
    {
        $response = $this->actingAsUser()->get('/ruta-que-no-existe-' . uniqid());
        $response->assertStatus(404);
        $response->assertSee('Página no encontrada');
    }

    public function test_unknown_route_as_guest_returns_404(): void
    {
        $response = $this->get('/ruta-inexistente-' . uniqid());
        $response->assertStatus(404);
    }

    // =========================================================
    //  403 — Admin-only routes accessed by non-admin
    // =========================================================

    public function test_non_admin_accessing_users_route_gets_403(): void
    {
        $response = $this->actingAsUser()->get('/users');
        $response->assertStatus(403);
    }

    public function test_403_page_contains_zona_restringida(): void
    {
        $response = $this->actingAsUser()->get('/users');
        $response->assertStatus(403);
        $response->assertSee('Zona Restringida');
    }

    public function test_non_admin_accessing_auditoria_gets_403(): void
    {
        $this->actingAsUser()->get('/auditoria')->assertStatus(403);
    }

    public function test_non_admin_accessing_catalogos_gets_403(): void
    {
        $this->actingAsUser()->get('/catalogos')->assertStatus(403);
    }

    public function test_non_admin_accessing_access_requests_gets_403(): void
    {
        $this->actingAsUser()->get('/access-requests')->assertStatus(403);
    }
}
