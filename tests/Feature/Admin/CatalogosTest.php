<?php

namespace Tests\Feature\Admin;

use App\Models\CatalogoLineaInvestigacion;
use App\Models\CatalogoPrograma;
use App\Models\CatalogoTipoProyecto;
use Tests\TestCase;

class CatalogosTest extends TestCase
{
    // =========================================================
    //  Access control
    // =========================================================

    public function test_guest_cannot_access_catalogos(): void
    {
        // The 'auth' middleware redirects to route('login') = '/' in this app
        $this->get('/catalogos')->assertRedirect('/');
    }

    public function test_non_admin_gets_403(): void
    {
        $this->actingAsUser()->get('/catalogos')->assertStatus(403);
    }

    public function test_admin_can_access_catalogos(): void
    {
        $this->actingAsAdmin()->get('/catalogos')->assertStatus(200);
    }

    // =========================================================
    //  Programas
    // =========================================================

    public function test_admin_can_create_programa(): void
    {
        $response = $this->actingAsAdmin()->post('/catalogos/programas', [
            'nombre'   => 'Ingeniería de Software',
            'facultad' => 'Facultad de Ingeniería',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('catalogo_programas', ['nombre' => 'Ingeniería de Software']);
    }

    public function test_programa_store_requires_nombre(): void
    {
        $response = $this->actingAsAdmin()->post('/catalogos/programas', []);
        $response->assertSessionHasErrors('nombre');
    }

    public function test_admin_can_update_programa(): void
    {
        $programa = CatalogoPrograma::create(['nombre' => 'Old Name', 'orden' => 1]);

        $response = $this->actingAsAdmin()->put("/catalogos/programas/{$programa->id}", [
            'nombre' => 'New Name',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('catalogo_programas', ['nombre' => 'New Name']);
    }

    public function test_admin_can_delete_programa(): void
    {
        $programa = CatalogoPrograma::create(['nombre' => 'Delete Me', 'orden' => 99]);

        $response = $this->actingAsAdmin()->delete("/catalogos/programas/{$programa->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('catalogo_programas', ['id' => $programa->id]);
    }

    // =========================================================
    //  Tipos de proyecto
    // =========================================================

    public function test_admin_can_create_tipo(): void
    {
        $response = $this->actingAsAdmin()->post('/catalogos/tipos', [
            'nombre' => 'Investigación Aplicada',
        ]);

        $response->assertRedirect();
        // The actual table is 'catalogo_tipos_proyecto' (as defined in the model)
        $this->assertDatabaseHas('catalogo_tipos_proyecto', ['nombre' => 'Investigación Aplicada']);
    }

    public function test_admin_can_update_tipo(): void
    {
        $tipo = CatalogoTipoProyecto::create(['nombre' => 'Old Tipo', 'orden' => 1]);

        $response = $this->actingAsAdmin()->put("/catalogos/tipos/{$tipo->id}", [
            'nombre' => 'New Tipo',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('catalogo_tipos_proyecto', ['nombre' => 'New Tipo']);
    }

    public function test_admin_can_delete_tipo(): void
    {
        $tipo = CatalogoTipoProyecto::create(['nombre' => 'Bye Tipo', 'orden' => 50]);

        $response = $this->actingAsAdmin()->delete("/catalogos/tipos/{$tipo->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('catalogo_tipos_proyecto', ['id' => $tipo->id]);
    }

    // =========================================================
    //  Líneas de investigación
    // =========================================================

    public function test_admin_can_create_linea(): void
    {
        $response = $this->actingAsAdmin()->post('/catalogos/lineas', [
            'nombre' => 'Educación e Innovación',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('catalogo_lineas_investigacion', ['nombre' => 'Educación e Innovación']);
    }

    public function test_admin_can_update_linea(): void
    {
        $linea = CatalogoLineaInvestigacion::create(['nombre' => 'Old Linea', 'orden' => 1]);

        $response = $this->actingAsAdmin()->put("/catalogos/lineas/{$linea->id}", [
            'nombre' => 'New Linea',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('catalogo_lineas_investigacion', ['nombre' => 'New Linea']);
    }

    public function test_admin_can_delete_linea(): void
    {
        $linea = CatalogoLineaInvestigacion::create(['nombre' => 'Bye Linea', 'orden' => 50]);

        $response = $this->actingAsAdmin()->delete("/catalogos/lineas/{$linea->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('catalogo_lineas_investigacion', ['id' => $linea->id]);
    }
}
