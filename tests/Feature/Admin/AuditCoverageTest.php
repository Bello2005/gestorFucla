<?php

namespace Tests\Feature\Admin;

use App\Models\Audit;
use App\Models\BancoProyecto;
use App\Models\BancoProyectoAnexo;
use App\Models\CatalogoLineaInvestigacion;
use App\Models\CatalogoPrograma;
use App\Models\CatalogoTipoProyecto;
use App\Models\Module;
use App\Models\UserPermission;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Verifica que todos los modelos críticos generan entradas de auditoría
 * y que el middleware de autenticación registra los eventos correctamente.
 */
class AuditCoverageTest extends TestCase
{
    private function lastAudit(string $table): ?Audit
    {
        return Audit::where('table_name', $table)->latest('id')->first();
    }

    private function auditCount(string $table): int
    {
        return Audit::where('table_name', $table)->count();
    }

    // =========================================================
    //  BancoProyecto
    // =========================================================

    public function test_banco_proyecto_create_generates_audit(): void
    {
        $user = $this->createUserWithPermissions(['banco']);

        $this->actingAs($user)->post('/banco-proyectos', [
            'titulo'         => 'BP Auditado',
            'fecha_registro' => '2026-01-01',
            'duracion_meses' => 6,
        ]);

        $audit = $this->lastAudit('banco_proyectos');
        $this->assertNotNull($audit);
        $this->assertSame('INSERT', $audit->operation);
        $this->assertSame($user->id, $audit->changed_by);
    }

    public function test_banco_proyecto_update_generates_audit(): void
    {
        $user   = $this->createUser();
        $bp     = BancoProyecto::create(['titulo' => 'Original', 'estado' => 'borrador', 'created_by' => $user->id]);
        $before = $this->auditCount('banco_proyectos');

        $bp->update(['titulo' => 'Actualizado']);

        $this->assertGreaterThan($before, $this->auditCount('banco_proyectos'));
        $audit = $this->lastAudit('banco_proyectos');
        $this->assertSame('UPDATE', $audit->operation);
        $this->assertSame('Original', $audit->old_values['titulo'] ?? null);
    }

    public function test_banco_proyecto_delete_generates_audit(): void
    {
        $user   = $this->createUser();
        $bp     = BancoProyecto::create(['titulo' => 'A Borrar', 'estado' => 'borrador', 'created_by' => $user->id]);
        $before = $this->auditCount('banco_proyectos');

        $bp->delete();

        $this->assertGreaterThan($before, $this->auditCount('banco_proyectos'));
        $this->assertSame('DELETE', $this->lastAudit('banco_proyectos')->operation);
    }

    // =========================================================
    //  BancoProyectoAnexo
    // =========================================================

    public function test_banco_proyecto_anexo_upload_generates_audit(): void
    {
        Storage::fake('public');
        $user = $this->createUserWithPermissions(['banco']);
        $bp   = BancoProyecto::create([
            'titulo'     => 'BP Anexo',
            'estado'     => 'borrador',
            'created_by' => $user->id,
        ]);

        $before = $this->auditCount('banco_proyecto_anexos');

        $this->actingAs($user)->post("/banco-proyectos/{$bp->id}/anexos", [
            'archivo'    => UploadedFile::fake()->create('doc.pdf', 50, 'application/pdf'),
            'tipo_anexo' => 'documento_proyecto',
        ]);

        $this->assertGreaterThan($before, $this->auditCount('banco_proyecto_anexos'));
        $audit = $this->lastAudit('banco_proyecto_anexos');
        $this->assertSame('INSERT', $audit->operation);
    }

    public function test_banco_proyecto_anexo_delete_generates_audit(): void
    {
        Storage::fake('public');
        $user  = $this->createUserWithPermissions(['banco']);
        $bp    = BancoProyecto::create(['titulo' => 'BP', 'estado' => 'borrador', 'created_by' => $user->id]);
        $file  = UploadedFile::fake()->create('f.pdf', 10, 'application/pdf');
        $path  = $file->store("banco-proyectos/{$bp->id}/anexos", 'public');
        $anexo = BancoProyectoAnexo::create([
            'banco_proyecto_id' => $bp->id,
            'tipo_anexo'        => 'documento_proyecto',
            'nombre_original'   => 'f.pdf',
            'ruta_archivo'      => $path,
            'tipo_archivo'      => 'application/pdf',
            'tamano_bytes'      => 10,
            'version'           => 1,
            'uploaded_by'       => $user->id,
            'uploaded_at'       => now(),
            'is_current'        => true,
        ]);

        $before = $this->auditCount('banco_proyecto_anexos');

        $this->actingAs($user)->delete("/banco-proyectos/{$bp->id}/anexos/{$anexo->id}");

        $this->assertGreaterThan($before, $this->auditCount('banco_proyecto_anexos'));
    }

    // =========================================================
    //  Catálogos
    // =========================================================

    public function test_catalogo_programa_create_generates_audit(): void
    {
        $before = $this->auditCount('catalogo_programas');

        $this->actingAsAdmin()->postJson('/catalogos/programas', [
            'nombre'   => 'Ingeniería de Sistemas',
            'facultad' => 'Ingeniería',
        ]);

        $this->assertGreaterThan($before, $this->auditCount('catalogo_programas'));
        $audit = $this->lastAudit('catalogo_programas');
        $this->assertSame('INSERT', $audit->operation);
    }

    public function test_catalogo_programa_update_generates_audit(): void
    {
        $programa = CatalogoPrograma::create(['nombre' => 'Prog Test', 'facultad' => 'Fac']);
        $before   = $this->auditCount('catalogo_programas');

        $programa->update(['nombre' => 'Prog Actualizado']);

        $this->assertGreaterThan($before, $this->auditCount('catalogo_programas'));
        $this->assertSame('UPDATE', $this->lastAudit('catalogo_programas')->operation);
        $this->assertSame('Prog Test', $this->lastAudit('catalogo_programas')->old_values['nombre']);
    }

    public function test_catalogo_programa_delete_generates_audit(): void
    {
        $programa = CatalogoPrograma::create(['nombre' => 'A Borrar', 'facultad' => 'Fac']);
        $before   = $this->auditCount('catalogo_programas');

        $programa->delete();

        $this->assertGreaterThan($before, $this->auditCount('catalogo_programas'));
        $this->assertSame('DELETE', $this->lastAudit('catalogo_programas')->operation);
    }

    public function test_catalogo_tipo_proyecto_create_generates_audit(): void
    {
        $before = $this->auditCount('catalogo_tipos_proyecto');

        $this->actingAsAdmin()->postJson('/catalogos/tipos', ['nombre' => 'Tipo Test']);

        $this->assertGreaterThan($before, $this->auditCount('catalogo_tipos_proyecto'));
    }

    public function test_catalogo_linea_investigacion_create_generates_audit(): void
    {
        $before = $this->auditCount('catalogo_lineas_investigacion');

        $this->actingAsAdmin()->postJson('/catalogos/lineas', ['nombre' => 'Línea Test', 'area' => 'Área']);

        $this->assertGreaterThan($before, $this->auditCount('catalogo_lineas_investigacion'));
    }

    // =========================================================
    //  UserPermission — cambios de permisos auditados
    // =========================================================

    public function test_granting_permission_generates_audit(): void
    {
        $target    = $this->createUser(['email' => 'target@test.com']);
        $proyectos = Module::where('slug', 'proyectos')->first();
        $before    = $this->auditCount('user_permissions');

        $this->actingAsAdmin()->postJson("/users/{$target->id}/permissions", [
            'permissions' => [
                ['module_id' => $proyectos->id, 'can_view' => true, 'can_edit' => true],
            ],
        ]);

        $this->assertGreaterThan($before, $this->auditCount('user_permissions'));
        $audit = $this->lastAudit('user_permissions');
        $this->assertSame('INSERT', $audit->operation);
    }

    public function test_revoking_permission_generates_update_audit(): void
    {
        $admin     = $this->createAdmin();
        $target    = $this->createUser(['email' => 'target@test.com']);
        $proyectos = Module::where('slug', 'proyectos')->first();

        // Otorgar primero
        $this->actingAs($admin)->postJson("/users/{$target->id}/permissions", [
            'permissions' => [['module_id' => $proyectos->id, 'can_view' => true, 'can_edit' => true]],
        ]);

        $before = $this->auditCount('user_permissions');

        // Revocar (updateOrCreate → UPDATE en Eloquent)
        $this->actingAs($admin)->postJson("/users/{$target->id}/permissions", [
            'permissions' => [['module_id' => $proyectos->id, 'can_view' => false, 'can_edit' => false]],
        ]);

        $this->assertGreaterThan($before, $this->auditCount('user_permissions'));
        $audit = $this->lastAudit('user_permissions');
        $this->assertSame('UPDATE', $audit->operation);
    }

    // =========================================================
    //  AuditAuthentication middleware — login.submit
    // =========================================================

    public function test_successful_login_generates_authentication_audit(): void
    {
        $user   = $this->createUser(['email' => 'loginaudit@test.com']);
        $before = $this->auditCount('authentication');

        $this->post('/login', [
            'email'    => 'loginaudit@test.com',
            'password' => 'password',
        ]);

        $this->assertGreaterThan($before, $this->auditCount('authentication'));
        $audit = $this->lastAudit('authentication');
        $this->assertSame('INSERT', $audit->operation);
    }

    public function test_failed_login_generates_authentication_audit(): void
    {
        $this->createUser(['email' => 'failaudit@test.com']);
        $before = $this->auditCount('authentication');

        $this->post('/login', [
            'email'    => 'failaudit@test.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGreaterThan($before, $this->auditCount('authentication'));
        $audit = $this->lastAudit('authentication');
        // success=false en new_values
        $newValues = json_decode($audit->new_values, true);
        $this->assertFalse($newValues['success']);
    }

    public function test_get_login_form_does_not_generate_audit(): void
    {
        $before = $this->auditCount('authentication');

        $this->get('/login');

        // Visitar el formulario NO debe crear una entrada de auditoría
        $this->assertSame($before, $this->auditCount('authentication'));
    }

    public function test_logout_generates_authentication_audit(): void
    {
        $before = $this->auditCount('authentication');

        $this->actingAsUser()->post('/logout');

        $this->assertGreaterThan($before, $this->auditCount('authentication'));
        $audit = $this->lastAudit('authentication');
        $this->assertSame('UPDATE', $audit->operation);
    }

    // =========================================================
    //  Auditoría guarda changed_by correctamente
    // =========================================================

    public function test_audit_records_the_user_who_made_the_change(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)->postJson('/catalogos/programas', [
            'nombre'   => 'Programa Admin',
            'facultad' => 'Fac',
        ]);

        $audit = $this->lastAudit('catalogo_programas');
        $this->assertSame($admin->id, $audit->changed_by);
        $this->assertSame($admin->name, $audit->user_name);
    }

    public function test_audit_captures_old_values_on_update(): void
    {
        $programa = CatalogoPrograma::create(['nombre' => 'Nombre Original', 'facultad' => 'Fac']);

        $this->actingAsAdmin()->putJson("/catalogos/programas/{$programa->id}", [
            'nombre'   => 'Nombre Nuevo',
            'facultad' => 'Fac',
        ]);

        $audit = $this->lastAudit('catalogo_programas');
        $this->assertSame('UPDATE', $audit->operation);
        // old_values ya está casteado como array por el modelo Audit
        $this->assertSame('Nombre Original', $audit->old_values['nombre'] ?? null);
    }
}
