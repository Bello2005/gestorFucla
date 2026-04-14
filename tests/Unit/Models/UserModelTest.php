<?php

namespace Tests\Unit\Models;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    // =========================================================
    //  roles() relation
    // =========================================================

    public function test_roles_relation_is_belongs_to_many(): void
    {
        $user = $this->createUser(['email' => 'relation@test.com']);
        $this->assertInstanceOf(BelongsToMany::class, $user->roles());
    }

    // =========================================================
    //  hasRole()
    // =========================================================

    public function test_has_role_returns_true_for_assigned_role(): void
    {
        $user = $this->createUser(['email' => 'hasrole1@test.com']);
        $this->assertTrue($user->hasRole('user'));
    }

    public function test_has_role_returns_false_for_unassigned_role(): void
    {
        $user = $this->createUser(['email' => 'hasrole2@test.com']);
        $this->assertFalse($user->hasRole('admin'));
    }

    public function test_has_role_returns_false_for_non_string_input(): void
    {
        $user = $this->createUser(['email' => 'hasrole3@test.com']);
        $this->assertFalse($user->hasRole(null));
        $this->assertFalse($user->hasRole(1));
    }

    public function test_admin_has_role_admin(): void
    {
        $admin = $this->createAdmin(['email' => 'adminrole@test.com']);
        $this->assertTrue($admin->hasRole('admin'));
        $this->assertFalse($admin->hasRole('user'));
    }

    // =========================================================
    //  assignRole()
    // =========================================================

    public function test_assign_role_by_slug_attaches_the_role(): void
    {
        $user = User::create([
            'name' => 'Fresh', 'email' => 'fresh@test.com', 'password' => 'secret',
        ]);

        $user->assignRole('user');
        $user->refresh()->load('roles');

        $this->assertTrue($user->hasRole('user'));
    }

    public function test_assign_role_does_not_duplicate(): void
    {
        $user = $this->createUser(['email' => 'nodup@test.com']); // already has 'user'
        $user->refresh()->load('roles');
        $user->assignRole('user'); // should be a no-op
        $user->refresh()->load('roles');

        $this->assertCount(1, $user->roles);
    }

    public function test_assign_role_accepts_role_model_instance(): void
    {
        $user = User::create([
            'name' => 'Model', 'email' => 'model@test.com', 'password' => 'secret',
        ]);
        $user->refresh()->load('roles');

        $role = Role::where('slug', 'admin')->first();
        $user->assignRole($role);
        $user->refresh()->load('roles');

        $this->assertTrue($user->hasRole('admin'));
    }

    // =========================================================
    //  hasAnyRole()
    // =========================================================

    public function test_has_any_role_returns_true_when_one_matches(): void
    {
        $user = $this->createUser(['email' => 'hanyroletrue@test.com']);
        // hasAnyRole queries the DB by roles.name.
        // Migrations seed roles with name='Usuario' (slug='user') and name='Administrador' (slug='admin').
        $roleName = \App\Models\Role::where('slug', 'user')->value('name');
        $this->assertTrue($user->hasAnyRole([$roleName]));
    }

    public function test_has_any_role_returns_false_when_none_matches(): void
    {
        $user = $this->createUser(['email' => 'hanyrolefsl@test.com']);
        $this->assertFalse($user->hasAnyRole(['SuperAdmin', 'Root']));
    }

    // =========================================================
    //  Password hashing (cast)
    // =========================================================

    public function test_password_is_hashed_on_create(): void
    {
        $user = User::create([
            'name' => 'Hash Test', 'email' => 'hash@test.com', 'password' => 'plain-secret',
        ]);

        $this->assertNotEquals('plain-secret', $user->password);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('plain-secret', $user->fresh()->password));
    }
}
