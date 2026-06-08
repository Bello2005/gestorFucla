<?php

namespace Tests\Feature\BancoProyectos;

use App\Models\BancoProyecto;
use App\Models\BancoProyectoAnexo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BancoProyectoAnexoTest extends TestCase
{
    private static int $bpCounter = 0;

    private function createBancoProyecto(array $attrs = []): BancoProyecto
    {
        $email = 'bp-anexo-owner-'.(++self::$bpCounter).'@test.com';
        $user = $this->createUser(['email' => $email]);

        return BancoProyecto::create(array_merge([
            'titulo'     => 'BP Anexos',
            'estado'     => 'borrador',
            'created_by' => $user->id,
        ], $attrs));
    }

    private function createAnexo(BancoProyecto $bp, array $attrs = []): BancoProyectoAnexo
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('anexo.pdf', 100, 'application/pdf');
        $path = $file->store("banco-proyectos/{$bp->id}/anexos", 'public');

        return BancoProyectoAnexo::create(array_merge([
            'banco_proyecto_id' => $bp->id,
            'tipo_anexo'        => 'documento_proyecto',
            'nombre_original'   => 'anexo.pdf',
            'ruta_archivo'      => $path,
            'tipo_archivo'      => 'application/pdf',
            'tamano_bytes'      => 1024,
            'version'           => 1,
            'uploaded_by'       => $bp->created_by,
            'uploaded_at'       => now(),
            'is_current'        => true,
        ], $attrs));
    }

    // =========================================================
    //  download
    // =========================================================

    public function test_guest_cannot_download_anexo(): void
    {
        $bp = $this->createBancoProyecto();
        $anexo = $this->createAnexo($bp);

        $this->get("/banco-proyectos/{$bp->id}/anexos/{$anexo->id}/download")
            ->assertRedirect('/');
    }

    public function test_user_with_view_can_download_anexo(): void
    {
        $bp = $this->createBancoProyecto();
        $anexo = $this->createAnexo($bp);

        $response = $this->actingAsUserWith(['banco'], false)
            ->get("/banco-proyectos/{$bp->id}/anexos/{$anexo->id}/download");

        $response->assertOk();
        $response->assertHeader('content-disposition');
    }

    public function test_download_returns_404_when_anexo_does_not_belong_to_proyecto(): void
    {
        $bp = $this->createBancoProyecto();
        $otherBp = $this->createBancoProyecto(['titulo' => 'Otro BP']);
        $anexo = $this->createAnexo($otherBp);

        $this->actingAsUserWith(['banco'], false)
            ->get("/banco-proyectos/{$bp->id}/anexos/{$anexo->id}/download")
            ->assertNotFound();
    }

    // =========================================================
    //  restore
    // =========================================================

    public function test_user_with_edit_permission_can_restore_anexo_version(): void
    {
        $bp = $this->createBancoProyecto();
        $current = $this->createAnexo($bp, ['version' => 2, 'is_current' => true]);
        $old = $this->createAnexo($bp, ['version' => 1, 'is_current' => false]);

        $response = $this->actingAsUserWith(['banco'])
            ->post("/banco-proyectos/{$bp->id}/anexos/{$old->id}/restore");

        $response->assertRedirect();
        $this->assertTrue($old->fresh()->is_current);
        $this->assertFalse($current->fresh()->is_current);
        $this->assertDatabaseHas('banco_proyecto_historial', [
            'banco_proyecto_id' => $bp->id,
            'accion'            => 'anexo_restaurado',
        ]);
    }

    public function test_user_with_only_view_cannot_restore_anexo(): void
    {
        $bp = $this->createBancoProyecto();
        $anexo = $this->createAnexo($bp, ['is_current' => false]);

        $this->actingAsUserWith(['banco'], false)
            ->post("/banco-proyectos/{$bp->id}/anexos/{$anexo->id}/restore")
            ->assertForbidden();

        $this->assertFalse($anexo->fresh()->is_current);
    }

    public function test_restore_returns_404_when_anexo_does_not_belong_to_proyecto(): void
    {
        $bp = $this->createBancoProyecto();
        $otherBp = $this->createBancoProyecto(['titulo' => 'Otro BP']);
        $anexo = $this->createAnexo($otherBp);

        $this->actingAsUserWith(['banco'])
            ->post("/banco-proyectos/{$bp->id}/anexos/{$anexo->id}/restore")
            ->assertNotFound();
    }

    public function test_guest_cannot_restore_anexo(): void
    {
        $bp = $this->createBancoProyecto();
        $anexo = $this->createAnexo($bp, ['is_current' => false]);

        $this->post("/banco-proyectos/{$bp->id}/anexos/{$anexo->id}/restore")
            ->assertRedirect('/');
    }
}
