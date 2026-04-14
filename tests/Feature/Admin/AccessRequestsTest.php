<?php

namespace Tests\Feature\Admin;

use App\Models\AccessRequest;
use Tests\TestCase;

class AccessRequestsTest extends TestCase
{
    // =========================================================
    //  Public endpoints
    // =========================================================

    public function test_create_form_is_publicly_accessible(): void
    {
        $this->get('/access-requests/create')->assertStatus(200);
    }

    public function test_guest_can_submit_access_request(): void
    {
        $response = $this->post('/access-requests', [
            'name'   => 'Solicitante Test',
            'email'  => 'solicitante@ext.com',
            'phone'  => '3001234567',
            'reason' => 'Necesito acceso para gestionar proyectos',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('access_requests', ['email' => 'solicitante@ext.com']);
    }

    public function test_store_requires_name_email_reason(): void
    {
        $response = $this->post('/access-requests', []);
        $response->assertSessionHasErrors(['name', 'email', 'reason']);
    }

    public function test_store_rejects_email_already_registered_as_user(): void
    {
        $this->createUser(['email' => 'existing@test.com']);

        $response = $this->post('/access-requests', [
            'name'   => 'Dup',
            'email'  => 'existing@test.com',
            'reason' => 'Test',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_store_rejects_duplicate_pending_request(): void
    {
        AccessRequest::create([
            'name' => 'First', 'email' => 'dup@ext.com', 'reason' => 'First request',
        ]);

        $response = $this->post('/access-requests', [
            'name'   => 'Second',
            'email'  => 'dup@ext.com',
            'reason' => 'Second request',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // =========================================================
    //  Admin-only endpoints
    // =========================================================

    public function test_guest_cannot_access_admin_index(): void
    {
        // route('login') in this app = '/', so auth middleware redirects there
        $this->get('/access-requests')->assertRedirect('/');
    }

    public function test_non_admin_gets_403_on_index(): void
    {
        $this->actingAsUser()->get('/access-requests')->assertStatus(403);
    }

    public function test_admin_can_access_index(): void
    {
        $this->actingAsAdmin()->get('/access-requests')->assertStatus(200);
    }

    public function test_admin_can_approve_request(): void
    {
        $req = AccessRequest::create([
            'name' => 'Approve Me', 'email' => 'approve@ext.com', 'reason' => 'Need access',
            'status' => 'pending',
        ]);

        $response = $this->actingAsAdmin()->put("/access-requests/{$req->id}/approve");

        $response->assertOk();
        $this->assertDatabaseHas('access_requests', ['id' => $req->id, 'status' => 'approved']);
        // A new user should be created
        $this->assertDatabaseHas('users', ['email' => 'approve@ext.com']);
    }

    public function test_admin_can_reject_request(): void
    {
        $req = AccessRequest::create([
            'name' => 'Reject Me', 'email' => 'reject@ext.com', 'reason' => 'Need access',
            'status' => 'pending',
        ]);

        $response = $this->actingAsAdmin()->putJson("/access-requests/{$req->id}/reject", [
            'admin_comment' => 'No cumple los requisitos',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('access_requests', ['id' => $req->id, 'status' => 'rejected']);
    }

    public function test_admin_cannot_approve_already_processed_request(): void
    {
        $req = AccessRequest::create([
            'name' => 'Done', 'email' => 'done@ext.com', 'reason' => 'x', 'status' => 'approved',
        ]);

        $response = $this->actingAsAdmin()->put("/access-requests/{$req->id}/approve");

        $response->assertStatus(422);
    }
}
