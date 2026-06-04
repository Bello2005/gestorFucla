<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use \App\Traits\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_temporary_password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relación con roles (muchos a muchos)
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function passwordResetHistory()
    {
        return $this->hasMany(PasswordResetHistory::class);
    }

    public function latestPasswordReset()
    {
        return $this->hasOne(PasswordResetHistory::class)
            ->latest();
    }

    /**
     * Verifica si el usuario tiene un rol específico
     *
     * @param string|Role $role
     * @return bool
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('slug', $role);
        }
        if ($role instanceof Role) {
            return $this->roles->contains('id', $role->id);
        }
        return false;
    }

    /**
     * Asigna un rol al usuario
     *
     * @param string|Role $role
     * @return $this
     */
    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)->firstOrFail();
        }
        if (!$this->hasRole($role)) {
            $this->roles()->attach($role);
        }
        return $this;
    }

    /**
     * Verifica si el usuario tiene alguno de los roles especificados
     *
     * @param array $roleNames
     * @return bool
     */
    public function hasAnyRole($roleNames)
    {
        return $this->roles()
            ->whereIn('name', (array) $roleNames)
            ->exists();
    }

    // ── Sistema de permisos por módulo ──────────────────────────────────────

    public function permissions()
    {
        return $this->hasMany(UserPermission::class);
    }

    public function isAdmin(): bool
    {
        return $this->roles->contains('slug', 'admin');
    }

    public function canView(string $moduleSlug): bool
    {
        if ($this->isAdmin()) return true;

        // Usar colección cacheada si ya fue eager-loaded
        if ($this->relationLoaded('permissions')) {
            return $this->permissions->contains(function ($p) use ($moduleSlug) {
                return $p->can_view
                    && $p->relationLoaded('module')
                    && $p->module->slug === $moduleSlug;
            });
        }

        return $this->permissions()
            ->whereHas('module', fn($q) => $q->where('slug', $moduleSlug))
            ->where('can_view', true)
            ->exists();
    }

    public function canEdit(string $moduleSlug): bool
    {
        if ($this->isAdmin()) return true;

        if ($this->relationLoaded('permissions')) {
            return $this->permissions->contains(function ($p) use ($moduleSlug) {
                return $p->can_edit
                    && $p->relationLoaded('module')
                    && $p->module->slug === $moduleSlug;
            });
        }

        return $this->permissions()
            ->whereHas('module', fn($q) => $q->where('slug', $moduleSlug))
            ->where('can_edit', true)
            ->exists();
    }

    public function effectiveRoleLabel(): string
    {
        if ($this->isAdmin()) return 'admin';

        // Usar colección cacheada si fue eager-loaded, sino consultar
        $perms = $this->relationLoaded('permissions')
            ? $this->permissions
            : $this->permissions()->get();

        if ($perms->isEmpty()) return 'sin acceso';
        if ($perms->every(fn($p) => $p->can_edit)) return 'editor';
        if ($perms->every(fn($p) => $p->can_view && !$p->can_edit)) return 'lector';

        return 'personalizado';
    }
}