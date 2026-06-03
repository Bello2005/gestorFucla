<?php

namespace Tests\Feature\Proyectos;

use App\Models\Proyecto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProyectoCertificadoTest extends TestCase
{
    // ── subirCertificado ──────────────────────────────────────────────────

    public function test_guest_cannot_upload_certificado(): void
    {
        $proyecto = Proyecto::create(['nombre_del_proyecto' => 'Test']);

        $this->post(route('proyectos.certificado.store', $proyecto), [])
            ->assertRedirect(route('login'));
    }

    public function test_subir_certificado_requires_file(): void
    {
        $user     = $this->createUser();
        $proyecto = Proyecto::create(['nombre_del_proyecto' => 'Test']);

        $response = $this->actingAs($user)->post(
            route('proyectos.certificado.store', $proyecto),
            []
        );

        $response->assertSessionHasErrors('certificado');
    }

    public function test_subir_certificado_rejects_non_pdf(): void
    {
        Storage::fake('public');
        $user     = $this->createUser();
        $proyecto = Proyecto::create(['nombre_del_proyecto' => 'Test']);

        $response = $this->actingAs($user)->post(
            route('proyectos.certificado.store', $proyecto),
            ['certificado' => UploadedFile::fake()->create('doc.txt', 10, 'text/plain')]
        );

        $response->assertSessionHasErrors('certificado');
    }

    public function test_subir_certificado_stores_pdf_and_updates_proyecto(): void
    {
        Storage::fake('public');
        $user     = $this->createUser();
        $proyecto = Proyecto::create(['nombre_del_proyecto' => 'Test']);

        $response = $this->actingAs($user)->post(
            route('proyectos.certificado.store', $proyecto),
            [
                'certificado'                  => UploadedFile::fake()->create('cert.pdf', 100, 'application/pdf'),
                'certificado_fecha'            => '2024-01-15',
                'certificado_observaciones'    => 'Everything OK',
            ]
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $proyecto->refresh();
        $this->assertNotNull($proyecto->certificado_cumplimiento);
        $this->assertNotNull($proyecto->certificado_fecha);
        $this->assertSame('Everything OK', $proyecto->certificado_observaciones);
    }

    // ── eliminarCertificado ───────────────────────────────────────────────

    public function test_guest_cannot_delete_certificado(): void
    {
        $proyecto = Proyecto::create(['nombre_del_proyecto' => 'Test']);

        $this->delete(route('proyectos.certificado.destroy', $proyecto))
            ->assertRedirect(route('login'));
    }

    public function test_eliminar_certificado_clears_all_certificate_fields(): void
    {
        Storage::fake('public');
        $user     = $this->createUser();
        $proyecto = Proyecto::create([
            'nombre_del_proyecto'       => 'Test',
            'certificado_cumplimiento'  => 'proyectos/certificados/1/cert.pdf',
            'certificado_fecha'         => now()->toDateString(),
            'certificado_observaciones' => 'Some notes',
        ]);

        $response = $this->actingAs($user)->delete(
            route('proyectos.certificado.destroy', $proyecto)
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $proyecto->refresh();
        $this->assertNull($proyecto->certificado_cumplimiento);
        $this->assertNull($proyecto->certificado_fecha);
        $this->assertNull($proyecto->certificado_observaciones);
    }
}
