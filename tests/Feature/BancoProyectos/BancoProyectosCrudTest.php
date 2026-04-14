<?php

namespace Tests\Feature\BancoProyectos;

use App\Models\BancoProyecto;
use App\Models\BancoProyectoAnexo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BancoProyectosCrudTest extends TestCase
{
    private function validData(array $overrides = []): array
    {
        return array_merge([
            'titulo'          => 'Banco Proyecto de Prueba',
            'fecha_registro'  => '2025-03-01',
            'duracion_meses'  => 6,
        ], $overrides);
    }

    private function createBancoProyecto(array $attrs = []): BancoProyecto
    {
        $user = $this->createUser(['email' => 'bp-owner@test.com']);
        return BancoProyecto::create(array_merge([
            'titulo'      => 'Test BP',
            'estado'      => 'borrador',
            'created_by'  => $user->id,
        ], $attrs));
    }

    // =========================================================
    //  Access control
    // =========================================================

    public function test_guest_cannot_access_banco_index(): void
    {
        // route('login') in this app = '/', so auth middleware redirects there
        $this->get('/banco-proyectos')->assertRedirect('/');
    }

    public function test_authenticated_user_can_access_index(): void
    {
        $this->actingAsUser()->get('/banco-proyectos')->assertStatus(200);
    }

    public function test_authenticated_user_can_access_create(): void
    {
        $this->actingAsUser()->get('/banco-proyectos/create')->assertStatus(200);
    }

    // =========================================================
    //  store
    // =========================================================

    public function test_user_can_create_banco_proyecto(): void
    {
        $response = $this->actingAsUser()->post('/banco-proyectos', $this->validData());

        $response->assertRedirect();
        $this->assertDatabaseHas('banco_proyectos', ['titulo' => 'Banco Proyecto de Prueba']);
    }

    public function test_store_requires_titulo(): void
    {
        $response = $this->actingAsUser()->post('/banco-proyectos', []);
        $response->assertSessionHasErrors('titulo');
    }

    public function test_store_auto_generates_codigo(): void
    {
        $this->actingAsUser()->post('/banco-proyectos', $this->validData());
        $bp = BancoProyecto::where('titulo', 'Banco Proyecto de Prueba')->first();

        $this->assertNotNull($bp->codigo);
        $this->assertStringStartsWith('BP-', $bp->codigo);
    }

    public function test_store_sets_estado_borrador(): void
    {
        $this->actingAsUser()->post('/banco-proyectos', $this->validData());
        $bp = BancoProyecto::where('titulo', 'Banco Proyecto de Prueba')->first();

        $this->assertEquals('borrador', $bp->estado);
    }

    // =========================================================
    //  show
    // =========================================================

    public function test_user_can_view_banco_proyecto(): void
    {
        $bp = $this->createBancoProyecto();
        $this->actingAsUser()->get("/banco-proyectos/{$bp->id}")->assertStatus(200);
    }

    // =========================================================
    //  edit
    // =========================================================

    public function test_user_can_access_edit_form(): void
    {
        $bp = $this->createBancoProyecto();
        $this->actingAsUser()->get("/banco-proyectos/{$bp->id}/edit")->assertStatus(200);
    }

    // =========================================================
    //  update
    // =========================================================

    public function test_user_can_update_banco_proyecto(): void
    {
        $bp = $this->createBancoProyecto();

        $response = $this->actingAsUser()->put("/banco-proyectos/{$bp->id}",
            $this->validData(['titulo' => 'Título Actualizado'])
        );

        $response->assertRedirect(route('banco.show', $bp));
        $this->assertDatabaseHas('banco_proyectos', ['titulo' => 'Título Actualizado']);
    }

    // =========================================================
    //  cambiarEstado (PATCH /banco-proyectos/{id}/estado)
    // =========================================================

    public function test_user_can_change_estado(): void
    {
        $bp = $this->createBancoProyecto();

        $response = $this->actingAsUser()->patch("/banco-proyectos/{$bp->id}/estado", [
            'estado' => 'en_evaluacion',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('banco_proyectos', ['id' => $bp->id, 'estado' => 'en_evaluacion']);
    }

    public function test_estado_must_be_valid(): void
    {
        $bp = $this->createBancoProyecto();

        $response = $this->actingAsUser()->patch("/banco-proyectos/{$bp->id}/estado", [
            'estado' => 'invalid_estado',
        ]);

        $response->assertSessionHasErrors('estado');
    }

    // =========================================================
    //  destroy
    // =========================================================

    public function test_user_can_delete_borrador(): void
    {
        $bp = $this->createBancoProyecto(['estado' => 'borrador']);

        $response = $this->actingAsUser()->delete("/banco-proyectos/{$bp->id}");

        $response->assertRedirect(route('banco.index'));
        $this->assertSoftDeleted('banco_proyectos', ['id' => $bp->id]);
    }

    public function test_admin_can_delete_any_estado(): void
    {
        $bp = $this->createBancoProyecto(['estado' => 'aprobado']);

        $response = $this->actingAsAdmin()->delete("/banco-proyectos/{$bp->id}");

        $response->assertRedirect(route('banco.index'));
        $this->assertSoftDeleted('banco_proyectos', ['id' => $bp->id]);
    }

    // =========================================================
    //  Anexos
    // =========================================================

    public function test_user_can_upload_anexo(): void
    {
        Storage::fake('public');
        $bp = $this->createBancoProyecto();
        $file = UploadedFile::fake()->create('documento.pdf', 200, 'application/pdf');

        $response = $this->actingAsUser()->post("/banco-proyectos/{$bp->id}/anexos", [
            'archivo'    => $file,
            'tipo_anexo' => 'documento_proyecto',
            'notas'      => 'Documento adjunto de prueba',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('banco_proyecto_anexos', ['banco_proyecto_id' => $bp->id]);
    }

    public function test_user_can_delete_anexo(): void
    {
        Storage::fake('public');
        $user = $this->createUser(['email' => 'anx@test.com']);
        $bp   = BancoProyecto::create(['titulo' => 'AX BP', 'estado' => 'borrador', 'created_by' => $user->id]);
        $file = UploadedFile::fake()->create('doc.pdf', 50, 'application/pdf');
        $path = $file->store("banco-proyectos/{$bp->id}/anexos", 'public');

        $anexo = BancoProyectoAnexo::create([
            'banco_proyecto_id' => $bp->id,
            'tipo_anexo'        => 'documento_proyecto',
            'nombre_original'   => 'doc.pdf',
            'ruta_archivo'      => $path,
            'tipo_archivo'      => 'application/pdf',
            'tamano_bytes'      => 50,
            'version'           => 1,
            'uploaded_by'       => $user->id,
            'uploaded_at'       => now(),
            'is_current'        => true,
        ]);

        $response = $this->actingAs($user)->delete("/banco-proyectos/{$bp->id}/anexos/{$anexo->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('banco_proyecto_anexos', ['id' => $anexo->id]);
    }

    // =========================================================
    //  historialJson
    // =========================================================

    public function test_historial_json_returns_array(): void
    {
        $bp = $this->createBancoProyecto();

        $response = $this->actingAsUser()->getJson("/banco-proyectos/{$bp->id}/historial");

        $response->assertOk()->assertJsonIsArray();
    }
}
