<?php

namespace Tests\Unit\Models;

use App\Models\BancoProyecto;
use Tests\TestCase;

class BancoProyectoModelTest extends TestCase
{
    private ?int $ownerId = null;

    private function makeProyecto(string $titulo = 'Test'): BancoProyecto
    {
        if ($this->ownerId === null) {
            $this->ownerId = $this->createUser()->id;
        }

        return BancoProyecto::create([
            'titulo'     => $titulo,
            'estado'     => 'borrador',
            'created_by' => $this->ownerId,
        ]);
    }

    // ── generarCodigo ─────────────────────────────────────────────────────

    public function test_genera_codigo_matches_expected_format(): void
    {
        $codigo = BancoProyecto::generarCodigo();
        $year   = now()->format('Y');

        $this->assertMatchesRegularExpression("/^BP-{$year}-\d{4}$/", $codigo);
    }

    public function test_genera_codigo_starts_at_0001_when_no_records(): void
    {
        $year = now()->format('Y');
        $this->assertSame("BP-{$year}-0001", BancoProyecto::generarCodigo());
    }

    public function test_genera_codigo_increments_after_existing_records(): void
    {
        $this->makeProyecto('P1');
        $this->makeProyecto('P2');

        $year  = now()->format('Y');
        $third = BancoProyecto::generarCodigo();

        $this->assertSame("BP-{$year}-0003", $third);
    }

    public function test_codigo_is_auto_generated_on_create(): void
    {
        $bp = $this->makeProyecto();

        $this->assertNotNull($bp->codigo);
        $this->assertMatchesRegularExpression('/^BP-\d{4}-\d{4}$/', $bp->codigo);
    }

    public function test_codigo_not_overwritten_if_already_set(): void
    {
        $user = $this->createUser();
        $bp   = BancoProyecto::create([
            'titulo'     => 'Custom Code',
            'estado'     => 'borrador',
            'created_by' => $user->id,
            'codigo'     => 'BP-CUSTOM-0001',
        ]);

        $this->assertSame('BP-CUSTOM-0001', $bp->codigo);
    }
}
