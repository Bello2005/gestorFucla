<?php

namespace Tests\Feature\Convocatorias;

use App\Models\Convocatoria;
use Tests\TestCase;

class ConvocatoriasCrudTest extends TestCase
{
    private function validData(array $overrides = []): array
    {
        return array_merge([
            'nombre'  => 'Convocatoria MinCiencias 2026',
            'entidad' => 'MinCiencias',
        ], $overrides);
    }

    // =========================================================
    //  Access control — GET /convocatorias (index)
    // =========================================================

    public function test_guest_cannot_list_convocatorias(): void
    {
        $this->getJson('/convocatorias')->assertStatus(302);
    }

    public function test_user_without_proyectos_permission_cannot_list(): void
    {
        $this->actingAsUser()->getJson('/convocatorias')->assertStatus(403);
    }

    public function test_user_with_proyectos_view_can_list(): void
    {
        $this->actingAsUserWith(['proyectos'], false)
            ->getJson('/convocatorias')
            ->assertOk()
            ->assertJsonIsArray();
    }

    public function test_admin_can_list_convocatorias(): void
    {
        $this->actingAsAdmin()->getJson('/convocatorias')->assertOk();
    }

    // =========================================================
    //  index devuelve los registros correctos
    // =========================================================

    public function test_index_returns_all_convocatorias(): void
    {
        $user = $this->createUserWithPermissions(['proyectos']);
        Convocatoria::create(['nombre' => 'Conv A', 'entidad' => 'Ent A', 'created_by' => $user->id]);
        Convocatoria::create(['nombre' => 'Conv B', 'entidad' => 'Ent B', 'created_by' => $user->id]);

        $response = $this->actingAs($user)->getJson('/convocatorias');

        $response->assertOk();
        $this->assertCount(2, $response->json());
    }

    public function test_index_includes_created_by_relation(): void
    {
        $user = $this->createUserWithPermissions(['proyectos']);
        Convocatoria::create(['nombre' => 'Conv', 'entidad' => 'Ent', 'created_by' => $user->id]);

        $response = $this->actingAs($user)->getJson('/convocatorias');

        $first = $response->json(0);
        $this->assertArrayHasKey('creado_por', $first);
    }

    // =========================================================
    //  store — POST /convocatorias
    // =========================================================

    public function test_guest_cannot_create_convocatoria(): void
    {
        $this->postJson('/convocatorias', $this->validData())->assertStatus(302);
    }

    public function test_user_without_edit_permission_cannot_create(): void
    {
        $this->actingAsUserWith(['proyectos'], false)
            ->postJson('/convocatorias', $this->validData())
            ->assertStatus(403);
    }

    public function test_user_with_proyectos_edit_can_create(): void
    {
        $response = $this->actingAsUserWith(['proyectos'])
            ->postJson('/convocatorias', $this->validData());

        $response->assertStatus(201)
            ->assertJsonFragment(['nombre' => 'Convocatoria MinCiencias 2026']);
    }

    public function test_admin_can_create_convocatoria(): void
    {
        $this->actingAsAdmin()
            ->postJson('/convocatorias', $this->validData())
            ->assertStatus(201);
    }

    public function test_store_saves_all_fields(): void
    {
        $this->actingAsAdmin()->postJson('/convocatorias', [
            'nombre'           => 'Conv Completa',
            'entidad'          => 'Entidad Test',
            'descripcion'      => 'Desc de prueba',
            'fecha_inicio'     => '2026-01-01',
            'fecha_cierre'     => '2026-03-31',
            'fecha_resultados' => '2026-04-30',
            'responsable'      => 'Juan Pérez',
            'enlace'           => 'https://minciencias.gov.co',
            'correo_contacto'  => 'conv@minciencias.gov.co',
            'observacion'      => 'Observación de prueba',
        ]);

        $this->assertDatabaseHas('convocatorias', [
            'nombre'          => 'Conv Completa',
            'entidad'         => 'Entidad Test',
            'responsable'     => 'Juan Pérez',
            'correo_contacto' => 'conv@minciencias.gov.co',
        ]);
    }

    public function test_store_saves_created_by_as_authenticated_user(): void
    {
        $user = $this->createUserWithPermissions(['proyectos']);

        $this->actingAs($user)->postJson('/convocatorias', $this->validData());

        $conv = Convocatoria::first();
        $this->assertSame($user->id, $conv->created_by);
    }

    // =========================================================
    //  Validación en store
    // =========================================================

    public function test_nombre_is_required(): void
    {
        $this->actingAsAdmin()
            ->postJson('/convocatorias', ['entidad' => 'Ent'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['nombre']);
    }

    public function test_entidad_is_required(): void
    {
        $this->actingAsAdmin()
            ->postJson('/convocatorias', ['nombre' => 'Conv'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['entidad']);
    }

    public function test_enlace_must_be_valid_url(): void
    {
        $this->actingAsAdmin()
            ->postJson('/convocatorias', $this->validData(['enlace' => 'not-a-url']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['enlace']);
    }

    public function test_correo_contacto_must_be_valid_email(): void
    {
        $this->actingAsAdmin()
            ->postJson('/convocatorias', $this->validData(['correo_contacto' => 'noesunmail']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['correo_contacto']);
    }

    public function test_fecha_cierre_must_be_after_or_equal_to_fecha_inicio(): void
    {
        $this->actingAsAdmin()
            ->postJson('/convocatorias', $this->validData([
                'fecha_inicio' => '2026-06-01',
                'fecha_cierre' => '2026-05-01', // anterior al inicio
            ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['fecha_cierre']);
    }

    public function test_fecha_cierre_equal_to_fecha_inicio_is_valid(): void
    {
        $this->actingAsAdmin()
            ->postJson('/convocatorias', $this->validData([
                'fecha_inicio' => '2026-06-01',
                'fecha_cierre' => '2026-06-01',
            ]))
            ->assertStatus(201);
    }

    public function test_optional_fields_can_be_null(): void
    {
        $this->actingAsAdmin()
            ->postJson('/convocatorias', ['nombre' => 'Mínima', 'entidad' => 'Ent'])
            ->assertStatus(201);
    }

    // =========================================================
    //  show — GET /convocatorias/{id}
    // =========================================================

    public function test_user_with_view_can_show_convocatoria(): void
    {
        $conv = Convocatoria::create(['nombre' => 'Conv', 'entidad' => 'Ent']);

        $this->actingAsUserWith(['proyectos'], false)
            ->getJson("/convocatorias/{$conv->id}")
            ->assertOk()
            ->assertJsonFragment(['nombre' => 'Conv']);
    }

    public function test_user_without_permission_cannot_show(): void
    {
        $conv = Convocatoria::create(['nombre' => 'Conv', 'entidad' => 'Ent']);

        $this->actingAsUser()->getJson("/convocatorias/{$conv->id}")->assertStatus(403);
    }

    public function test_show_returns_404_for_nonexistent(): void
    {
        $this->actingAsAdmin()->getJson('/convocatorias/99999')->assertStatus(404);
    }

    // =========================================================
    //  update — PUT /convocatorias/{id}
    // =========================================================

    public function test_user_with_edit_permission_can_update(): void
    {
        $conv = Convocatoria::create(['nombre' => 'Original', 'entidad' => 'Ent']);

        $this->actingAsUserWith(['proyectos'])
            ->putJson("/convocatorias/{$conv->id}", ['nombre' => 'Actualizada', 'entidad' => 'Ent'])
            ->assertOk()
            ->assertJsonFragment(['nombre' => 'Actualizada']);

        $this->assertDatabaseHas('convocatorias', ['nombre' => 'Actualizada']);
    }

    public function test_user_with_only_view_cannot_update(): void
    {
        $conv = Convocatoria::create(['nombre' => 'Conv', 'entidad' => 'Ent']);

        $this->actingAsUserWith(['proyectos'], false)
            ->putJson("/convocatorias/{$conv->id}", ['nombre' => 'Hack', 'entidad' => 'Ent'])
            ->assertStatus(403);
    }

    public function test_update_validates_fields(): void
    {
        $conv = Convocatoria::create(['nombre' => 'Conv', 'entidad' => 'Ent']);

        $this->actingAsAdmin()
            ->putJson("/convocatorias/{$conv->id}", ['enlace' => 'bad-url', 'nombre' => 'N', 'entidad' => 'E'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['enlace']);
    }

    // =========================================================
    //  destroy — DELETE /convocatorias/{id}
    // =========================================================

    public function test_user_with_edit_permission_can_delete(): void
    {
        $conv = Convocatoria::create(['nombre' => 'Conv', 'entidad' => 'Ent']);

        $this->actingAsUserWith(['proyectos'])
            ->deleteJson("/convocatorias/{$conv->id}")
            ->assertOk()
            ->assertJsonFragment(['message' => 'Convocatoria eliminada exitosamente.']);

        $this->assertDatabaseMissing('convocatorias', ['id' => $conv->id]);
    }

    public function test_user_with_only_view_cannot_delete(): void
    {
        $conv = Convocatoria::create(['nombre' => 'Conv', 'entidad' => 'Ent']);

        $this->actingAsUserWith(['proyectos'], false)
            ->deleteJson("/convocatorias/{$conv->id}")
            ->assertStatus(403);
    }

    public function test_guest_cannot_delete(): void
    {
        $conv = Convocatoria::create(['nombre' => 'Conv', 'entidad' => 'Ent']);
        $this->deleteJson("/convocatorias/{$conv->id}")->assertStatus(302);
    }

    public function test_delete_returns_404_for_nonexistent(): void
    {
        $this->actingAsAdmin()->deleteJson('/convocatorias/99999')->assertStatus(404);
    }
}
