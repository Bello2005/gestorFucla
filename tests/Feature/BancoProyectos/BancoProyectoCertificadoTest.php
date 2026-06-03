<?php

namespace Tests\Feature\BancoProyectos;

use App\Models\BancoProyecto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BancoProyectoCertificadoTest extends TestCase
{
    private function makeBanco(): BancoProyecto
    {
        return BancoProyecto::create([
            'titulo'     => 'BP Test',
            'estado'     => 'aprobado',
            'created_by' => $this->createAdmin()->id,
        ]);
    }

    // ── subirCertificado ──────────────────────────────────────────────────

    public function test_subir_certificado_requires_file(): void
    {
        $bp   = $this->makeBanco();
        $user = $this->createUser();

        $response = $this->actingAs($user)->post(route('banco.certificado.store', $bp), []);

        $response->assertSessionHasErrors('certificado');
    }

    public function test_subir_certificado_rejects_non_pdf(): void
    {
        Storage::fake('public');
        $bp   = $this->makeBanco();
        $user = $this->createUser();

        $response = $this->actingAs($user)->post(
            route('banco.certificado.store', $bp),
            ['certificado' => UploadedFile::fake()->create('file.txt', 5, 'text/plain')]
        );

        $response->assertSessionHasErrors('certificado');
    }

    public function test_subir_certificado_stores_and_updates_banco_proyecto(): void
    {
        Storage::fake('public');
        $bp   = $this->makeBanco();
        $user = $this->createUser();

        $response = $this->actingAs($user)->post(
            route('banco.certificado.store', $bp),
            [
                'certificado'               => UploadedFile::fake()->create('cert.pdf', 50, 'application/pdf'),
                'certificado_fecha'         => '2024-03-10',
                'certificado_observaciones' => 'Aprobado',
            ]
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $bp->refresh();
        $this->assertNotNull($bp->certificado_cumplimiento);
        $this->assertSame('Aprobado', $bp->certificado_observaciones);
    }

    // ── eliminarCertificado ───────────────────────────────────────────────

    public function test_eliminar_certificado_clears_fields(): void
    {
        Storage::fake('public');
        $user = $this->createUser();
        $bp   = BancoProyecto::create([
            'titulo'                    => 'With Cert',
            'estado'                    => 'aprobado',
            'created_by'                => $user->id,
            'certificado_cumplimiento'  => 'banco-proyectos/1/certificado/cert.pdf',
            'certificado_fecha'         => now()->toDateString(),
            'certificado_observaciones' => 'Notes',
        ]);

        $response = $this->actingAs($user)->delete(route('banco.certificado.destroy', $bp));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $bp->refresh();
        $this->assertNull($bp->certificado_cumplimiento);
        $this->assertNull($bp->certificado_fecha);
        $this->assertNull($bp->certificado_observaciones);
    }

    // ── historialJson ─────────────────────────────────────────────────────

    public function test_historial_json_returns_json_array(): void
    {
        $user = $this->createUser();
        $bp   = BancoProyecto::create([
            'titulo'     => 'BP Historial',
            'estado'     => 'borrador',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->getJson(route('banco.historial', $bp));

        $response->assertOk();
        $this->assertIsArray($response->json());
    }

    public function test_guest_cannot_access_historial(): void
    {
        $user = $this->createUser();
        $bp   = BancoProyecto::create([
            'titulo'     => 'BP',
            'estado'     => 'borrador',
            'created_by' => $user->id,
        ]);

        $this->get(route('banco.historial', $bp))->assertRedirect(route('login'));
    }

    // ── exportExcel / exportPdf (placeholders) ────────────────────────────

    public function test_export_excel_redirects_with_info_message(): void
    {
        $response = $this->actingAsUser()->get(route('banco.export.excel'));

        $response->assertRedirect(route('banco.index'));
        $response->assertSessionHas('info');
    }

    public function test_export_pdf_redirects_with_info_message(): void
    {
        $response = $this->actingAsUser()->get(route('banco.export.pdf'));

        $response->assertRedirect(route('banco.index'));
        $response->assertSessionHas('info');
    }

    public function test_guest_cannot_export_excel(): void
    {
        $this->get(route('banco.export.excel'))->assertRedirect(route('login'));
    }

    public function test_guest_cannot_export_pdf(): void
    {
        $this->get(route('banco.export.pdf'))->assertRedirect(route('login'));
    }
}
