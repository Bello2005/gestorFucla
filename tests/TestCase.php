<?php

namespace Tests;

use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Boot the application with a fresh SQLite in-memory database.
     * This overrides the production Postgres connection from .env
     * before any config is loaded.
     */
    public function createApplication(): \Illuminate\Foundation\Application
    {
        // Force SQLite in-memory BEFORE the bootstrap/app.php loads .env vars
        $vars = [
            'APP_ENV'        => 'testing',
            'DB_CONNECTION'  => 'sqlite',
            'DB_DATABASE'    => ':memory:',
            'MAIL_MAILER'    => 'array',
            'SESSION_DRIVER' => 'array',
            'CACHE_STORE'    => 'array',
            'QUEUE_CONNECTION' => 'sync',
            'BCRYPT_ROUNDS'  => '4',
        ];

        foreach ($vars as $key => $value) {
            putenv("{$key}={$value}");
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;
        }

        $app = require __DIR__.'/../bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * Seed the roles table before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->seedRoles();
    }

    /**
     * Ensure the two base roles exist.
     * Migrations already seed them, but this acts as a safety net.
     */
    protected function seedRoles(): void
    {
        \Illuminate\Support\Facades\DB::table('roles')->upsert(
            [
                ['name' => 'Administrador', 'slug' => 'admin', 'description' => 'Administrador del sistema', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Usuario',       'slug' => 'user',  'description' => 'Usuario regular',            'created_at' => now(), 'updated_at' => now()],
            ],
            ['slug'],
            ['name', 'description', 'updated_at']
        );
    }

    /**
     * Create a plain (non-admin) user and attach the 'user' role.
     */
    protected function createUser(array $attrs = []): User
    {
        $user = User::create(array_merge([
            'name'     => 'Test User',
            'email'    => 'user@test.com',
            'password' => Hash::make('password'),
            'is_temporary_password' => false,
        ], $attrs));

        $role = Role::where('slug', 'user')->first();
        if ($role) {
            $user->roles()->attach($role->id);
        }

        return $user->fresh('roles');
    }

    /**
     * Create an admin user and attach the 'admin' role.
     */
    protected function createAdmin(array $attrs = []): User
    {
        $user = User::create(array_merge([
            'name'     => 'Admin User',
            'email'    => 'admin@test.com',
            'password' => Hash::make('password'),
            'is_temporary_password' => false,
        ], $attrs));

        $role = Role::where('slug', 'admin')->first();
        if ($role) {
            $user->roles()->attach($role->id);
        }

        return $user->fresh('roles');
    }

    /**
     * Authenticate as a new admin and return $this for chaining.
     */
    protected function actingAsAdmin(array $attrs = []): static
    {
        return $this->actingAs($this->createAdmin($attrs));
    }

    /**
     * Authenticate as a new regular user and return $this for chaining.
     */
    protected function actingAsUser(array $attrs = []): static
    {
        return $this->actingAs($this->createUser($attrs));
    }

    /**
     * Grant module permissions to a user.
     *
     * @param string[] $slugs   Module slugs to grant
     * @param bool     $canEdit Also grant edit permission (ignored for read_only modules)
     */
    protected function grantPermissions(User $user, array $slugs, bool $canEdit = true): void
    {
        $modules = Module::whereIn('slug', $slugs)->get();
        foreach ($modules as $module) {
            UserPermission::updateOrCreate(
                ['user_id' => $user->id, 'module_id' => $module->id],
                ['can_view' => true, 'can_edit' => $canEdit && !$module->read_only]
            );
        }
    }

    /**
     * Create a regular user and immediately grant module permissions.
     *
     * @param string[] $slugs
     */
    protected function createUserWithPermissions(array $slugs, bool $canEdit = true, array $attrs = []): User
    {
        $user = $this->createUser($attrs);
        $this->grantPermissions($user, $slugs, $canEdit);
        return $user->fresh(['roles', 'permissions.module']);
    }

    /**
     * Authenticate as a user who already has the given module permissions.
     * Use this instead of actingAsUser() when the route requires module.permission middleware.
     *
     * @param string[] $slugs
     */
    protected function actingAsUserWith(array $slugs, bool $canEdit = true, array $attrs = []): static
    {
        return $this->actingAs($this->createUserWithPermissions($slugs, $canEdit, $attrs));
    }
}
