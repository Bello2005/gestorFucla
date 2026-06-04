<?php

namespace Tests\Feature\Proyectos;

use App\Models\Proyecto;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProyectoFileDeleteTest extends TestCase
{
    // ── deleteEvidenciaArchivo ─────────────────────────────────────────────

    public function test_guest_cannot_delete_evidencia(): void
    {
        $proyecto = Proyecto::create(['nombre_del_proyecto' => 'Test']);

        $this->delete(route('proyectos.delete.evidencia', [$proyecto, 0]))
            ->assertRedirect(route('login'));
    }

    public function test_delete_evidencia_returns_404_when_index_missing(): void
    {
        Storage::fake('public');
        $user     = $this->createUserWithPermissions(['proyectos']);
        $proyecto = Proyecto::create(['nombre_del_proyecto' => 'Test']);

        $response = $this->actingAs($user)->deleteJson(
            route('proyectos.delete.evidencia', [$proyecto, 0])
        );

        $response->assertStatus(404);
    }

    public function test_delete_evidencia_removes_from_array(): void
    {
        Storage::fake('public');
        $user     = $this->createUserWithPermissions(['proyectos']);
        $proyecto = Proyecto::create([
            'nombre_del_proyecto' => 'Test',
            'cargar_evidencias'   => ['path/a.jpg', 'path/b.jpg'],
        ]);

        $response = $this->actingAs($user)->deleteJson(
            route('proyectos.delete.evidencia', [$proyecto, 0])
        );

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertSame(['path/b.jpg'], $proyecto->fresh()->cargar_evidencias);
    }

    // ── deleteContratoArchivo ─────────────────────────────────────────────

    public function test_guest_cannot_delete_contrato(): void
    {
        $proyecto = Proyecto::create(['nombre_del_proyecto' => 'Test']);

        $this->delete(route('proyectos.delete.contrato', $proyecto))->assertRedirect(route('login'));
    }

    public function test_delete_contrato_returns_404_when_no_file(): void
    {
        $user     = $this->createUserWithPermissions(['proyectos']);
        $proyecto = Proyecto::create(['nombre_del_proyecto' => 'Test']);

        $response = $this->actingAs($user)->deleteJson(
            route('proyectos.delete.contrato', $proyecto)
        );

        $response->assertStatus(404);
    }

    public function test_delete_contrato_clears_field(): void
    {
        Storage::fake('public');
        $user     = $this->createUserWithPermissions(['proyectos']);
        $proyecto = Proyecto::create([
            'nombre_del_proyecto'     => 'Test',
            'cargar_contrato_o_convenio' => 'path/contrato.pdf',
        ]);

        $response = $this->actingAs($user)->deleteJson(
            route('proyectos.delete.contrato', $proyecto)
        );

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertNull($proyecto->fresh()->cargar_contrato_o_convenio);
    }

    // ── deleteProyectoArchivo ─────────────────────────────────────────────

    public function test_guest_cannot_delete_proyecto_archivo(): void
    {
        $proyecto = Proyecto::create(['nombre_del_proyecto' => 'Test']);

        $this->delete(route('proyectos.delete.archivo', $proyecto))->assertRedirect(route('login'));
    }

    public function test_delete_proyecto_archivo_returns_404_when_no_file(): void
    {
        $user     = $this->createUserWithPermissions(['proyectos']);
        $proyecto = Proyecto::create(['nombre_del_proyecto' => 'Test']);

        $response = $this->actingAs($user)->deleteJson(
            route('proyectos.delete.archivo', $proyecto)
        );

        $response->assertStatus(404);
    }

    public function test_delete_proyecto_archivo_clears_field(): void
    {
        Storage::fake('public');
        $user     = $this->createUserWithPermissions(['proyectos']);
        $proyecto = Proyecto::create([
            'nombre_del_proyecto'    => 'Test',
            'cargar_archivo_proyecto' => 'path/proyecto.pdf',
        ]);

        $response = $this->actingAs($user)->deleteJson(
            route('proyectos.delete.archivo', $proyecto)
        );

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertNull($proyecto->fresh()->cargar_archivo_proyecto);
    }
}
