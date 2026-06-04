<?php

namespace Tests\Feature\Middleware;

use App\Models\Proyecto;
use Tests\TestCase;

class VerifyProjectEditRequestTest extends TestCase
{
    // ── missing is_edit ───────────────────────────────────────────────────

    public function test_update_without_is_edit_does_not_persist_changes(): void
    {
        $user     = $this->createUserWithPermissions(['proyectos']);
        $proyecto = Proyecto::create(['nombre_del_proyecto' => 'Original']);

        $this->actingAs($user)->put(
            route('proyectos.update', $proyecto),
            ['nombre_del_proyecto' => 'Should Not Update']
        );

        $this->assertDatabaseHas('proyectos', ['nombre_del_proyecto' => 'Original']);
    }

    public function test_update_without_is_edit_returns_redirect(): void
    {
        $user     = $this->createUserWithPermissions(['proyectos']);
        $proyecto = Proyecto::create(['nombre_del_proyecto' => 'Test']);

        $response = $this->actingAs($user)->put(
            route('proyectos.update', $proyecto),
            ['nombre_del_proyecto' => 'Updated']
        );

        $response->assertRedirect();
    }

    // ── mismatched proyecto_id ─────────────────────────────────────────────

    public function test_update_with_mismatched_proyecto_id_does_not_persist(): void
    {
        $user     = $this->createUserWithPermissions(['proyectos']);
        $proyecto = Proyecto::create(['nombre_del_proyecto' => 'Original']);

        $this->actingAs($user)->put(
            route('proyectos.update', $proyecto),
            [
                'is_edit'            => '1',
                'proyecto_id'        => '9999',
                'nombre_del_proyecto' => 'Should Not Update',
            ]
        );

        $this->assertDatabaseHas('proyectos', ['nombre_del_proyecto' => 'Original']);
    }

    // ── matching ids — passes middleware ──────────────────────────────────

    public function test_update_with_matching_ids_passes_middleware_and_updates(): void
    {
        $user     = $this->createUserWithPermissions(['proyectos']);
        $proyecto = Proyecto::create(['nombre_del_proyecto' => 'Original']);

        $this->actingAs($user)->put(
            route('proyectos.update', $proyecto),
            [
                '_method'            => 'PUT',
                'is_edit'            => '1',
                'proyecto_id'        => (string) $proyecto->id,
                'nombre_del_proyecto' => 'Updated Name',
            ]
        );

        $this->assertDatabaseHas('proyectos', ['nombre_del_proyecto' => 'Updated Name']);
    }
}
