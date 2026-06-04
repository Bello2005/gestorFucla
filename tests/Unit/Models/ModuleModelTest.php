<?php

namespace Tests\Unit\Models;

use App\Models\Module;
use App\Models\UserPermission;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

class ModuleModelTest extends TestCase
{
    // =========================================================
    //  Seeded data (inserted by migration)
    // =========================================================

    public function test_seven_modules_are_seeded(): void
    {
        $this->assertSame(7, Module::count());
    }

    public function test_all_expected_slugs_exist(): void
    {
        $slugs = Module::pluck('slug')->sort()->values()->toArray();
        $expected = ['auditoria', 'banco', 'catalogos', 'estadistica', 'proyectos', 'solicitudes', 'usuarios'];
        $this->assertSame($expected, $slugs);
    }

    // =========================================================
    //  read_only flag
    // =========================================================

    public function test_proyectos_and_banco_are_not_read_only(): void
    {
        $this->assertFalse(Module::where('slug', 'proyectos')->first()->read_only);
        $this->assertFalse(Module::where('slug', 'banco')->first()->read_only);
    }

    public function test_admin_only_modules_are_read_only(): void
    {
        foreach (['auditoria', 'estadistica', 'usuarios', 'solicitudes', 'catalogos'] as $slug) {
            $this->assertTrue(
                Module::where('slug', $slug)->first()->read_only,
                "Module '$slug' should be read_only"
            );
        }
    }

    // =========================================================
    //  sort_order
    // =========================================================

    public function test_modules_have_sequential_sort_order(): void
    {
        $orders = Module::orderBy('sort_order')->pluck('sort_order')->toArray();
        $this->assertSame([1, 2, 3, 4, 5, 6, 7], $orders);
    }

    public function test_proyectos_has_sort_order_one(): void
    {
        $this->assertSame(1, Module::where('slug', 'proyectos')->first()->sort_order);
    }

    // =========================================================
    //  permissions() relation
    // =========================================================

    public function test_permissions_relation_is_has_many(): void
    {
        $module = Module::where('slug', 'proyectos')->first();
        $this->assertInstanceOf(HasMany::class, $module->permissions());
    }

    public function test_permissions_relation_returns_associated_user_permissions(): void
    {
        $user   = $this->createUserWithPermissions(['proyectos']);
        $module = Module::where('slug', 'proyectos')->first();

        $this->assertCount(1, $module->permissions);
        $this->assertSame($user->id, $module->permissions->first()->user_id);
    }

    // =========================================================
    //  read_only cast is boolean
    // =========================================================

    public function test_read_only_is_cast_to_boolean(): void
    {
        $module = Module::where('slug', 'proyectos')->first();
        $this->assertIsBool($module->read_only);
    }
}
