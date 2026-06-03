<?php

namespace Tests\Feature;

use App\Models\Proyecto;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_sees_dashboard(): void
    {
        $this->actingAsUser()->get(route('dashboard'))->assertOk()->assertViewIs('dashboard');
    }

    public function test_dashboard_passes_required_variables_to_view(): void
    {
        $response = $this->actingAsUser()->get(route('dashboard'));

        $response->assertViewHasAll(['stats', 'statsAnterior', 'recientes', 'resumenEstados', 'topEntidades']);
    }

    public function test_stats_total_reflects_project_count(): void
    {
        Proyecto::create(['nombre_del_proyecto' => 'P1', 'estado' => 'activo']);
        Proyecto::create(['nombre_del_proyecto' => 'P2', 'estado' => 'inactivo']);

        $response = $this->actingAsUser()->get(route('dashboard'));

        $response->assertViewHas('stats', fn ($stats) => $stats['total'] === 2);
    }

    public function test_top_entidades_grouped_correctly(): void
    {
        Proyecto::create(['nombre_del_proyecto' => 'P1', 'entidad_contratante' => 'Entidad A']);
        Proyecto::create(['nombre_del_proyecto' => 'P2', 'entidad_contratante' => 'Entidad A']);
        Proyecto::create(['nombre_del_proyecto' => 'P3', 'entidad_contratante' => 'Entidad B']);

        $response = $this->actingAsUser()->get(route('dashboard'));

        $response->assertViewHas('topEntidades', function ($top) {
            return $top->first()['nombre'] === 'Entidad A'
                && $top->first()['total'] === 2;
        });
    }

    public function test_recientes_limited_to_five(): void
    {
        for ($i = 1; $i <= 7; $i++) {
            Proyecto::create(['nombre_del_proyecto' => "P{$i}"]);
        }

        $response = $this->actingAsUser()->get(route('dashboard'));

        $response->assertViewHas('recientes', fn ($r) => $r->count() <= 5);
    }
}
