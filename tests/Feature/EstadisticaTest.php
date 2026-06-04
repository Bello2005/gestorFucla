<?php

namespace Tests\Feature;

use App\Models\Proyecto;
use Tests\TestCase;

class EstadisticaTest extends TestCase
{
    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('estadistica'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_sees_statistics_page(): void
    {
        $this->actingAsUserWith(['estadistica'])->get(route('estadistica'))->assertOk()->assertViewIs('estadistica');
    }

    public function test_view_receives_all_required_variables(): void
    {
        $response = $this->actingAsUserWith(['estadistica'])->get(route('estadistica'));

        $response->assertViewHasAll([
            'proyectosPorEstado',
            'totalProyectos',
            'valorTotal',
            'proyectosActivos',
            'proyectosInactivos',
            'proyectosCerrados',
            'porcentajeActivos',
            'tasaExito',
        ]);
    }

    public function test_counts_reflect_database_state(): void
    {
        Proyecto::create(['nombre_del_proyecto' => 'A', 'estado' => 'activo']);
        Proyecto::create(['nombre_del_proyecto' => 'B', 'estado' => 'activo']);
        Proyecto::create(['nombre_del_proyecto' => 'C', 'estado' => 'inactivo']);
        Proyecto::create(['nombre_del_proyecto' => 'D', 'estado' => 'cerrado']);

        $response = $this->actingAsUserWith(['estadistica'])->get(route('estadistica'));

        $response->assertViewHas('totalProyectos', 4);
        $response->assertViewHas('proyectosActivos', 2);
        $response->assertViewHas('proyectosInactivos', 1);
        $response->assertViewHas('proyectosCerrados', 1);
    }

    public function test_porcentaje_activos_calculated_correctly(): void
    {
        Proyecto::create(['nombre_del_proyecto' => 'A', 'estado' => 'activo']);
        Proyecto::create(['nombre_del_proyecto' => 'B', 'estado' => 'inactivo']);

        $response = $this->actingAsUserWith(['estadistica'])->get(route('estadistica'));

        $response->assertViewHas('porcentajeActivos', 50.0);
    }

    public function test_porcentaje_activos_is_zero_when_no_projects(): void
    {
        $response = $this->actingAsUserWith(['estadistica'])->get(route('estadistica'));

        $response->assertViewHas('totalProyectos', 0);
        $response->assertViewHas('porcentajeActivos', 0);
    }

    public function test_tasa_exito_calculated_correctly(): void
    {
        Proyecto::create(['nombre_del_proyecto' => 'A', 'estado' => 'activo']);
        Proyecto::create(['nombre_del_proyecto' => 'B', 'estado' => 'activo']);
        Proyecto::create(['nombre_del_proyecto' => 'C', 'estado' => 'cerrado']);
        Proyecto::create(['nombre_del_proyecto' => 'D', 'estado' => 'cerrado']);

        $response = $this->actingAsUserWith(['estadistica'])->get(route('estadistica'));

        $response->assertViewHas('tasaExito', 50.0);
    }

    public function test_valor_total_sums_all_projects(): void
    {
        Proyecto::create(['nombre_del_proyecto' => 'P1', 'valor_total' => 1_000_000]);
        Proyecto::create(['nombre_del_proyecto' => 'P2', 'valor_total' => 2_500_000]);
        Proyecto::create(['nombre_del_proyecto' => 'P3', 'valor_total' => 500_000]);

        $response = $this->actingAsUserWith(['estadistica'])->get(route('estadistica'));

        $response->assertViewHas('valorTotal', fn ($v) => (float) $v === 4_000_000.0);
    }
}
