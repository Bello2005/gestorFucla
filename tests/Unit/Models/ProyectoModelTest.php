<?php

namespace Tests\Unit\Models;

use App\Models\Proyecto;
use Tests\TestCase;

class ProyectoModelTest extends TestCase
{
    // ── cargar_evidencias accessor ─────────────────────────────────────────

    public function test_evidencias_empty_by_default(): void
    {
        $proyecto = Proyecto::create(['nombre_del_proyecto' => 'Test']);
        $this->assertSame([], $proyecto->cargar_evidencias);
    }

    public function test_evidencias_stores_and_retrieves_array(): void
    {
        $proyecto = Proyecto::create([
            'nombre_del_proyecto' => 'Test',
            'cargar_evidencias'   => ['path/a.jpg', 'path/b.jpg'],
        ]);

        $this->assertSame(['path/a.jpg', 'path/b.jpg'], $proyecto->fresh()->cargar_evidencias);
    }

    public function test_evidencias_filters_empty_strings_on_read(): void
    {
        $proyecto = Proyecto::create([
            'nombre_del_proyecto' => 'Test',
            'cargar_evidencias'   => ['path/a.jpg', '', 'path/b.jpg', '   '],
        ]);

        $this->assertSame(['path/a.jpg', 'path/b.jpg'], $proyecto->fresh()->cargar_evidencias);
    }

    public function test_evidencias_setter_stores_empty_array_for_null(): void
    {
        $proyecto = Proyecto::create(['nombre_del_proyecto' => 'Test']);
        $proyecto->cargar_evidencias = null;
        $proyecto->save();

        $this->assertSame([], $proyecto->fresh()->cargar_evidencias);
    }

    // ── valor_total_formatted accessor ────────────────────────────────────

    public function test_valor_total_formatted_returns_formatted_string(): void
    {
        $proyecto = new Proyecto(['valor_total' => 1_234_567.89]);
        $this->assertSame('1.234.567,89', $proyecto->valor_total_formatted);
    }

    public function test_valor_total_formatted_returns_zero_when_null(): void
    {
        $proyecto = new Proyecto(['valor_total' => null]);
        $this->assertSame('0,00', $proyecto->valor_total_formatted);
    }

    public function test_valor_total_formatted_returns_zero_when_zero(): void
    {
        $proyecto = new Proyecto(['valor_total' => 0]);
        $this->assertSame('0,00', $proyecto->valor_total_formatted);
    }

    // ── fecha_ejecucion_formatted accessor ────────────────────────────────

    public function test_fecha_ejecucion_formatted_returns_formatted_date(): void
    {
        $proyecto = new Proyecto(['fecha_de_ejecucion' => '2024-06-15']);
        $this->assertSame('15/06/2024', $proyecto->fecha_ejecucion_formatted);
    }

    public function test_fecha_ejecucion_formatted_returns_empty_when_null(): void
    {
        $proyecto = new Proyecto(['fecha_de_ejecucion' => null]);
        $this->assertSame('', $proyecto->fecha_ejecucion_formatted);
    }
}
