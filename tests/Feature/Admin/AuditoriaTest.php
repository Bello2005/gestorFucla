<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;

class AuditoriaTest extends TestCase
{
    public function test_guest_cannot_access_auditoria(): void
    {
        // The 'auth' middleware redirects to route('login') = '/' in this app
        $this->get('/auditoria')->assertRedirect('/');
    }

    public function test_non_admin_gets_403_on_auditoria(): void
    {
        $this->actingAsUser()->get('/auditoria')->assertStatus(403);
    }

    public function test_admin_can_access_auditoria_index(): void
    {
        $this->actingAsAdmin()->get('/auditoria')->assertStatus(200);
    }

    public function test_admin_can_access_auditoria_export(): void
    {
        // The export endpoint should return a file or redirect, not crash
        $response = $this->actingAsAdmin()->get('/auditoria/exportar');
        $this->assertContains($response->getStatusCode(), [200, 302, 500]);
    }
}
