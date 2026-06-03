<?php

namespace Tests\Feature\Proyectos;

use App\Models\Proyecto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Cobertura de validación completa para StoreProyectoRequest y UpdateProyectoRequest.
 * Cada regla de negocio tiene al menos un happy path y un sad path.
 */
class ProyectosValidacionTest extends TestCase
{
    private function validData(array $overrides = []): array
    {
        return array_merge([
            'nombre_del_proyecto' => 'Proyecto Válido',
            'objeto_contractual'  => 'Prestar servicios',
            'entidad_contratante' => 'Entidad ABC',
            'fecha_de_ejecucion'  => '2025-06-01',
            'plazo'               => 12,
            'valor_total'         => 50_000_000,
        ], $overrides);
    }

    private function requiredFiles(): array
    {
        return [
            'archivo_proyecto' => UploadedFile::fake()->create('proyecto.pdf', 100, 'application/pdf'),
            'archivo_contrato' => UploadedFile::fake()->create('contrato.pdf', 100, 'application/pdf'),
        ];
    }

    private function updateData(Proyecto $proyecto, array $overrides = []): array
    {
        return array_merge([
            '_method'            => 'PUT',
            'is_edit'            => '1',
            'proyecto_id'        => (string) $proyecto->id,
            'nombre_del_proyecto' => 'Proyecto Actualizado',
        ], $overrides);
    }

    // ── STORE: campos opcionales ───────────────────────────────────────────

    public function test_store_accepts_all_optional_fields(): void
    {
        Storage::fake('public');

        $response = $this->actingAsUser()->post('/proyectos', array_merge(
            $this->validData([
                'objeto_contractual'  => 'Contrato de servicios especializados',
                'lineas_de_accion'    => 'Línea A, Línea B',
                'cobertura'           => 'Nacional',
                'entidad_contratante' => 'UNICLARETIANA',
                'fecha_de_ejecucion'  => '2025-01-15',
                'plazo'               => 24,
                'valor_total'         => 120_000_000,
            ]),
            $this->requiredFiles()
        ));

        $response->assertRedirect(route('proyectos.index'));
        $this->assertDatabaseHas('proyectos', [
            'nombre_del_proyecto' => 'Proyecto Válido',
            'cobertura'           => 'Nacional',
        ]);
    }

    public function test_store_nombre_max_255_chars_is_rejected(): void
    {
        $response = $this->actingAsUser()->post('/proyectos', $this->validData([
            'nombre_del_proyecto' => str_repeat('a', 256),
        ]));

        $response->assertSessionHasErrors('nombre_del_proyecto');
    }

    // ── STORE: validaciones de fecha ──────────────────────────────────────

    public function test_store_rejects_invalid_fecha_de_ejecucion(): void
    {
        $response = $this->actingAsUser()->post('/proyectos', $this->validData([
            'fecha_de_ejecucion' => 'not-a-date',
        ]));

        $response->assertSessionHasErrors('fecha_de_ejecucion');
    }

    public function test_store_accepts_null_fecha_de_ejecucion(): void
    {
        Storage::fake('public');
        $response = $this->actingAsUser()->post('/proyectos', array_merge(
            $this->validData(['fecha_de_ejecucion' => null]),
            $this->requiredFiles()
        ));
        $response->assertRedirect(route('proyectos.index'));
    }

    // ── STORE: validaciones numéricas ─────────────────────────────────────

    public function test_store_rejects_negative_plazo(): void
    {
        $response = $this->actingAsUser()->post('/proyectos', $this->validData([
            'plazo' => -1,
        ]));

        $response->assertSessionHasErrors('plazo');
    }

    public function test_store_rejects_negative_valor_total(): void
    {
        $response = $this->actingAsUser()->post('/proyectos', $this->validData([
            'valor_total' => -100,
        ]));

        $response->assertSessionHasErrors('valor_total');
    }

    public function test_store_rejects_non_numeric_plazo(): void
    {
        $response = $this->actingAsUser()->post('/proyectos', $this->validData([
            'plazo' => 'doce meses',
        ]));

        $response->assertSessionHasErrors('plazo');
    }

    public function test_store_rejects_non_numeric_valor_total(): void
    {
        $response = $this->actingAsUser()->post('/proyectos', $this->validData([
            'valor_total' => 'cien millones',
        ]));

        $response->assertSessionHasErrors('valor_total');
    }

    public function test_store_accepts_zero_plazo(): void
    {
        Storage::fake('public');
        $response = $this->actingAsUser()->post('/proyectos', array_merge(
            $this->validData(['plazo' => 0]),
            $this->requiredFiles()
        ));
        $response->assertRedirect(route('proyectos.index'));
    }

    public function test_store_accepts_zero_valor_total(): void
    {
        Storage::fake('public');
        $response = $this->actingAsUser()->post('/proyectos', array_merge(
            $this->validData(['valor_total' => 0]),
            $this->requiredFiles()
        ));
        $response->assertRedirect(route('proyectos.index'));
    }

    // ── STORE: archivos ───────────────────────────────────────────────────

    public function test_store_rejects_oversized_archivo_proyecto(): void
    {
        Storage::fake('public');

        // 20481 KB > 20480 KB (20 MB limit)
        $oversized = UploadedFile::fake()->create('big.pdf', 20481, 'application/pdf');

        $response = $this->actingAsUser()->post('/proyectos', array_merge(
            $this->validData(),
            ['archivo_proyecto' => $oversized]
        ));

        $response->assertSessionHasErrors('archivo_proyecto');
    }

    public function test_store_allows_multiple_evidencias(): void
    {
        Storage::fake('public');

        $files = [
            UploadedFile::fake()->create('ev1.pdf', 100, 'application/pdf'),
            UploadedFile::fake()->create('ev2.jpg', 50, 'image/jpeg'),
            UploadedFile::fake()->create('ev3.png', 30, 'image/png'),
        ];

        $response = $this->actingAsUser()->post('/proyectos', array_merge(
            $this->validData(),
            $this->requiredFiles(),
            ['evidencias' => $files]
        ));

        $response->assertRedirect(route('proyectos.index'));
        $proyecto = Proyecto::latest()->first();
        $this->assertCount(3, $proyecto->cargar_evidencias);
    }

    // ── UPDATE: estado ────────────────────────────────────────────────────

    public function test_update_estado_to_activo(): void
    {
        $proyecto = Proyecto::create($this->validData(['estado' => 'inactivo']));

        $this->actingAsUser()->put("/proyectos/{$proyecto->id}", $this->updateData($proyecto, [
            'estado' => 'activo',
        ]));

        $this->assertDatabaseHas('proyectos', ['id' => $proyecto->id, 'estado' => 'activo']);
    }

    public function test_update_estado_to_inactivo(): void
    {
        $proyecto = Proyecto::create($this->validData(['estado' => 'activo']));

        $this->actingAsUser()->put("/proyectos/{$proyecto->id}", $this->updateData($proyecto, [
            'estado' => 'inactivo',
        ]));

        $this->assertDatabaseHas('proyectos', ['id' => $proyecto->id, 'estado' => 'inactivo']);
    }

    public function test_update_estado_to_cerrado(): void
    {
        $proyecto = Proyecto::create($this->validData(['estado' => 'activo']));

        $this->actingAsUser()->put("/proyectos/{$proyecto->id}", $this->updateData($proyecto, [
            'estado' => 'cerrado',
        ]));

        $this->assertDatabaseHas('proyectos', ['id' => $proyecto->id, 'estado' => 'cerrado']);
    }

    public function test_update_rejects_invalid_estado(): void
    {
        $proyecto  = Proyecto::create($this->validData());
        $response  = $this->actingAsUser()->put("/proyectos/{$proyecto->id}", $this->updateData($proyecto, [
            'estado' => 'pendiente',
        ]));

        $response->assertSessionHasErrors('estado');
    }

    // ── UPDATE: validaciones ──────────────────────────────────────────────

    public function test_update_rejects_negative_plazo(): void
    {
        $proyecto = Proyecto::create($this->validData());
        $response = $this->actingAsUser()->put("/proyectos/{$proyecto->id}", $this->updateData($proyecto, [
            'plazo' => -5,
        ]));

        $response->assertSessionHasErrors('plazo');
    }

    public function test_update_rejects_invalid_fecha(): void
    {
        $proyecto = Proyecto::create($this->validData());
        $response = $this->actingAsUser()->put("/proyectos/{$proyecto->id}", $this->updateData($proyecto, [
            'fecha_de_ejecucion' => 'invalid-date',
        ]));

        $response->assertSessionHasErrors('fecha_de_ejecucion');
    }

    public function test_update_preserves_existing_files_when_none_uploaded(): void
    {
        Storage::fake('public');
        $proyecto = Proyecto::create($this->validData([
            'cargar_archivo_proyecto'    => 'proyectos/1/doc.pdf',
            'cargar_contrato_o_convenio' => 'proyectos/1/contrato.pdf',
        ]));

        $this->actingAsUser()->put("/proyectos/{$proyecto->id}", $this->updateData($proyecto));

        $fresh = $proyecto->fresh();
        $this->assertSame('proyectos/1/doc.pdf', $fresh->cargar_archivo_proyecto);
        $this->assertSame('proyectos/1/contrato.pdf', $fresh->cargar_contrato_o_convenio);
    }
}
