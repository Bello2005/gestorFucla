<?php

namespace Tests\Feature\Proyectos;

use App\Models\Proyecto;
use Tests\TestCase;

/**
 * Verifica que el valor económico solo sea visible para:
 *  - Administradores (siempre)
 *  - El usuario que creó el proyecto (en su propio detalle)
 *
 * Un usuario externo NO debe ver el valor de proyectos ajenos.
 */
class ProyectoValorVisibilidadTest extends TestCase
{
    // ── SHOW: detalle de un proyecto ──────────────────────────────────────

    public function test_admin_ve_valor_en_show_de_cualquier_proyecto(): void
    {
        $owner  = $this->createUser(['email' => 'owner@test.com']);
        $proyecto = Proyecto::create([
            'nombre_del_proyecto' => 'Proyecto Ajeno',
            'valor_total'         => 5_000_000,
            'created_by'          => $owner->id,
        ]);

        $response = $this->actingAsAdmin()->get(route('proyectos.show', $proyecto));

        $response->assertOk();
        $response->assertSeeText('5.000.000');
    }

    public function test_creador_ve_valor_en_su_propio_proyecto(): void
    {
        $user = $this->createUserWithPermissions(['proyectos']);
        $proyecto = Proyecto::create([
            'nombre_del_proyecto' => 'Mi Proyecto',
            'valor_total'         => 3_500_000,
            'created_by'          => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('proyectos.show', $proyecto));

        $response->assertOk();
        $response->assertSeeText('3.500.000');
    }

    public function test_usuario_no_ve_valor_en_proyecto_ajeno(): void
    {
        $owner = $this->createUserWithPermissions(['proyectos'], false, ['email' => 'owner@test.com']);
        $otro  = $this->createUserWithPermissions(['proyectos'], false, ['email' => 'otro@test.com']);

        $proyecto = Proyecto::create([
            'nombre_del_proyecto' => 'Proyecto de Owner',
            'valor_total'         => 9_000_000,
            'created_by'          => $owner->id,
        ]);

        $response = $this->actingAs($otro)->get(route('proyectos.show', $proyecto));

        $response->assertOk();
        $response->assertDontSeeText('9.000.000');
    }

    public function test_usuario_sin_proyecto_no_ve_ningún_valor(): void
    {
        $owner = $this->createUserWithPermissions(['proyectos'], false, ['email' => 'owner@test.com']);
        $otro  = $this->createUserWithPermissions(['proyectos'], false, ['email' => 'otro@test.com']);

        Proyecto::create([
            'nombre_del_proyecto' => 'P1',
            'valor_total'         => 1_000_000,
            'created_by'          => $owner->id,
        ]);
        Proyecto::create([
            'nombre_del_proyecto' => 'P2',
            'valor_total'         => 2_000_000,
            'created_by'          => $owner->id,
        ]);

        // "otro" accede al show de cada proyecto que no le pertenece
        foreach (Proyecto::all() as $p) {
            $this->actingAs($otro)
                ->get(route('proyectos.show', $p))
                ->assertDontSeeText(number_format($p->valor_total, 0, ',', '.'));
        }
    }

    public function test_proyecto_sin_created_by_oculta_valor_a_usuarios_regulares(): void
    {
        $user = $this->createUserWithPermissions(['proyectos'], false);
        $proyecto = Proyecto::create([
            'nombre_del_proyecto' => 'Legado',
            'valor_total'         => 4_200_000,
            'created_by'          => null, // proyectos anteriores a la migración
        ]);

        $response = $this->actingAs($user)->get(route('proyectos.show', $proyecto));

        $response->assertOk();
        $response->assertDontSeeText('4.200.000');
    }

    // ── DASHBOARD: lista de recientes ─────────────────────────────────────

    public function test_admin_ve_valor_de_todos_en_dashboard(): void
    {
        $user = $this->createUser(['email' => 'user@test.com']);
        Proyecto::create([
            'nombre_del_proyecto' => 'P Admin',
            'valor_total'         => 7_777_777,
            'created_by'          => $user->id,
        ]);

        $response = $this->actingAsAdmin()->get(route('dashboard'));

        $response->assertOk();
        $response->assertSeeText('7.8M');
    }

    public function test_creador_ve_su_valor_en_dashboard(): void
    {
        $user = $this->createUserWithPermissions(['proyectos']);
        Proyecto::create([
            'nombre_del_proyecto' => 'Mi P',
            'valor_total'         => 2_000_000,
            'created_by'          => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSeeText('$2M COP');
    }

    public function test_usuario_no_ve_valor_de_proyecto_ajeno_en_dashboard(): void
    {
        $owner = $this->createUser(['email' => 'owner@test.com']);
        $otro  = $this->createUser(['email' => 'otro@test.com']);

        Proyecto::create([
            'nombre_del_proyecto' => 'P Ajeno',
            'valor_total'         => 8_888_888,
            'created_by'          => $owner->id,
        ]);

        $response = $this->actingAs($otro)->get(route('dashboard'));

        $response->assertOk();
        $response->assertDontSeeText('8.9M');
    }

    // ── INDEX: columna de valor total ─────────────────────────────────────

    public function test_admin_ve_columna_valor_en_index(): void
    {
        $this->actingAsAdmin()
            ->get(route('proyectos.index'))
            ->assertOk()
            ->assertSeeText('Valor Total');
    }

    public function test_usuario_regular_no_ve_columna_valor_en_index(): void
    {
        // Usuario con solo vista (sin edición) no debe ver columna financiera
        $this->actingAsUserWith(['proyectos'], false)
            ->get(route('proyectos.index'))
            ->assertOk()
            ->assertDontSeeText('Valor Total');
    }
}
