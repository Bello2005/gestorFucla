<?php

namespace Tests\Feature\Admin;

use App\Models\Audit;
use Tests\TestCase;

class AuditShowExportTest extends TestCase
{
    private function makeAudit(): Audit
    {
        return Audit::create([
            'table_name'  => 'proyectos',
            'operation'   => 'INSERT',
            'record_id'   => '1',
            'old_values'  => [],
            'new_values'  => ['nombre' => 'Test'],
            'changed_by'  => null,
            'user_name'   => 'System',
            'ip_address'  => '127.0.0.1',
        ]);
    }

    // ── show ──────────────────────────────────────────────────────────────

    public function test_guest_cannot_access_audit_show(): void
    {
        $audit = $this->makeAudit();

        $this->get(route('audit.show', $audit))->assertRedirect(route('login'));
    }

    public function test_non_admin_gets_403_on_show(): void
    {
        $audit = $this->makeAudit();

        $this->actingAsUser()->get(route('audit.show', $audit))->assertForbidden();
    }

    public function test_admin_can_view_audit_detail(): void
    {
        $audit    = $this->makeAudit();
        $response = $this->actingAsAdmin()->get(route('audit.show', $audit));

        $response->assertOk();
        $response->assertViewIs('audit.show');
        $response->assertViewHas('audit');
    }

    // ── export ────────────────────────────────────────────────────────────

    public function test_guest_cannot_export_audit(): void
    {
        $this->get(route('audit.export'))->assertRedirect(route('login'));
    }

    public function test_non_admin_gets_403_on_export(): void
    {
        $this->actingAsUser()->get(route('audit.export'))->assertForbidden();
    }

    public function test_admin_can_export_audit_as_csv(): void
    {
        $this->makeAudit();

        $response = $this->actingAsAdmin()->get(route('audit.export'));

        $response->assertOk();
        $this->assertStringContainsString(
            'text/csv',
            $response->headers->get('Content-Type')
        );
    }

    public function test_export_csv_contains_audit_data(): void
    {
        $this->makeAudit();

        $response = $this->actingAsAdmin()->get(route('audit.export'));
        $content  = $response->streamedContent();

        $this->assertStringContainsString('proyectos', $content);
        $this->assertStringContainsString('INSERT', $content);
    }

    // ── Filtros en index ──────────────────────────────────────────────────

    public function test_admin_can_filter_by_table_name(): void
    {
        $this->makeAudit(); // table_name = 'proyectos'
        Audit::create([
            'table_name' => 'users',
            'operation'  => 'UPDATE',
            'record_id'  => '2',
            'old_values' => [],
            'new_values' => [],
            'user_name'  => 'Admin',
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAsAdmin()->get(route('audit.index', ['table' => 'proyectos']));

        $response->assertOk();
        $response->assertViewHas('audits', fn ($paginator) => $paginator->total() === 1);
    }

    public function test_admin_can_filter_by_operation(): void
    {
        $this->makeAudit(); // operation = 'INSERT', table = 'proyectos'
        Audit::create([
            'table_name' => 'proyectos',
            'operation'  => 'DELETE',
            'record_id'  => '5',
            'old_values' => [],
            'new_values' => [],
            'user_name'  => 'Admin',
            'ip_address' => '127.0.0.1',
        ]);

        // Filter by BOTH table and operation to isolate from user-creation audit records
        $response = $this->actingAsAdmin()->get(
            route('audit.index', ['table' => 'proyectos', 'operation' => 'INSERT'])
        );

        $response->assertOk();
        $response->assertViewHas('audits', fn ($paginator) => $paginator->total() === 1);
    }

    public function test_export_respects_table_filter(): void
    {
        $this->makeAudit(); // proyectos
        Audit::create([
            'table_name' => 'users',
            'operation'  => 'INSERT',
            'record_id'  => '9',
            'old_values' => [],
            'new_values' => [],
            'user_name'  => 'Admin',
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAsAdmin()->get(route('audit.export', ['table' => 'proyectos']));
        $content  = $response->streamedContent();

        $this->assertStringContainsString('proyectos', $content);
        $this->assertStringNotContainsString('users', $content);
    }

    public function test_audit_index_paginates_results(): void
    {
        // Authenticate first so admin-creation audit records exist before we count
        $this->actingAsAdmin();

        // Create 27 'CUSTOM_OP' records with a unique operation that won't appear elsewhere
        for ($i = 1; $i <= 27; $i++) {
            Audit::create([
                'table_name' => 'test_pagination',
                'operation'  => 'CUSTOM_OP',
                'record_id'  => (string) $i,
                'old_values' => [],
                'new_values' => [],
                'user_name'  => 'Admin',
                'ip_address' => '127.0.0.1',
            ]);
        }

        // Filter to only our custom records so count is deterministic
        $response = $this->get(route('audit.index', ['operation' => 'CUSTOM_OP']));

        $response->assertOk();
        $response->assertViewHas('audits', fn ($paginator) => $paginator->total() === 27 && $paginator->count() === 25);
    }
}
