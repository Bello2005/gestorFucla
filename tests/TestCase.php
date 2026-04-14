<?php

namespace Tests;

use App\Models\Role;
use App\Models\User;
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
}
