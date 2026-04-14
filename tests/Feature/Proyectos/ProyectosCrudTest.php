<?php

namespace Tests\Feature\Proyectos;

use App\Models\Proyecto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProyectosCrudTest extends TestCase
{
    private function validData(array $overrides = []): array
    {
        return array_merge([
            'nombre_del_proyecto' => 'Proyecto de Prueba',
            'objeto_contractual'  => 'Objeto de prueba',
            'entidad_contratante' => 'Entidad ABC',
            'fecha_de_ejecucion'  => '2025-01-15',
            'plazo'               => 12,
            'valor_total'         => 50000000,
        ], $overrides);
    }

    // =========================================================
    //  Access control
    // =========================================================

    public function test_guest_cannot_access_proyectos(): void
    {
        // route('login') in this app = '/', so auth middleware redirects there
        $this->get('/proyectos')->assertRedirect('/');
    }

    public function test_authenticated_user_can_access_index(): void
    {
        $this->actingAsUser()->get('/proyectos')->assertStatus(200);
    }

    public function test_authenticated_user_can_access_create(): void
    {
        $this->actingAsUser()->get('/proyectos/create')->assertStatus(200);
    }

    // =========================================================
    //  store
    // =========================================================

    public function test_user_can_create_proyecto(): void
    {
        $response = $this->actingAsUser()->post('/proyectos', $this->validData());

        $response->assertRedirect(route('proyectos.index'));
        $this->assertDatabaseHas('proyectos', ['nombre_del_proyecto' => 'Proyecto de Prueba']);
    }

    public function test_store_requires_nombre(): void
    {
        $data = $this->validData(['nombre_del_proyecto' => '']);

        $response = $this->actingAsUser()->post('/proyectos', $data);

        $response->assertSessionHasErrors('nombre_del_proyecto');
    }

    public function test_store_with_file_upload(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('proyecto.pdf', 100, 'application/pdf');

        $response = $this->actingAsUser()->post('/proyectos', array_merge(
            $this->validData(),
            ['archivo_proyecto' => $file]
        ));

        $response->assertRedirect(route('proyectos.index'));
        $this->assertDatabaseHas('proyectos', ['nombre_del_proyecto' => 'Proyecto de Prueba']);
    }

    // =========================================================
    //  show
    // =========================================================

    public function test_user_can_view_proyecto(): void
    {
        $proyecto = Proyecto::create($this->validData());

        $this->actingAsUser()->get("/proyectos/{$proyecto->id}")->assertStatus(200);
    }

    // =========================================================
    //  edit
    // =========================================================

    public function test_user_can_access_edit_form(): void
    {
        $proyecto = Proyecto::create($this->validData());

        $this->actingAsUser()->get("/proyectos/{$proyecto->id}/edit")->assertStatus(200);
    }

    // =========================================================
    //  update
    // =========================================================

    public function test_user_can_update_proyecto(): void
    {
        $proyecto = Proyecto::create($this->validData());

        $response = $this->actingAsUser()->put("/proyectos/{$proyecto->id}", array_merge(
            $this->validData(['nombre_del_proyecto' => 'Nombre Actualizado']),
            [
                '_method'    => 'PUT',
                'is_edit'    => '1',
                'proyecto_id' => (string) $proyecto->id,
            ]
        ));

        $response->assertRedirect(route('proyectos.show', $proyecto));
        $this->assertDatabaseHas('proyectos', ['nombre_del_proyecto' => 'Nombre Actualizado']);
    }

    public function test_update_fails_without_security_fields(): void
    {
        $proyecto = Proyecto::create($this->validData());

        // Missing is_edit — the middleware should reject
        $response = $this->actingAsUser()->put("/proyectos/{$proyecto->id}",
            $this->validData(['nombre_del_proyecto' => 'Should Not Update'])
        );

        // Either redirected back with error or 422
        $this->assertContains($response->getStatusCode(), [302, 422]);
        $this->assertDatabaseMissing('proyectos', ['nombre_del_proyecto' => 'Should Not Update']);
    }

    // =========================================================
    //  destroy
    // =========================================================

    public function test_user_can_delete_proyecto(): void
    {
        Storage::fake('public');
        $proyecto = Proyecto::create($this->validData());

        $response = $this->actingAsUser()->delete("/proyectos/{$proyecto->id}");

        $response->assertRedirect(route('proyectos.index'));
        $this->assertDatabaseMissing('proyectos', ['id' => $proyecto->id]);
    }

    // =========================================================
    //  404 for non-existent proyecto
    // =========================================================

    public function test_show_returns_404_for_missing_proyecto(): void
    {
        $this->actingAsUser()->get('/proyectos/99999')->assertStatus(404);
    }
}
