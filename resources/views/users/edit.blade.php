@extends('layouts.main')

@section('title', 'Editar Usuario')

@section('breadcrumbs')
    <span>Administración</span>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('users.index') }}">Usuarios</a>
    <span class="breadcrumb-separator">/</span>
    <span>Editar</span>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="ds-card uc-max-w-form">
        <div class="ds-card-header">
            <h2 class="uc-section-title" style="margin: 0;">Editar Usuario</h2>
        </div>
        <div>
            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="ds-label">Nombre</label>
                    <input type="text" class="ds-input @error('name') is-invalid @enderror"
                        id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="ds-label">Email</label>
                    <input type="email" class="ds-input @error('email') is-invalid @enderror"
                        id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="ds-label">Contraseña (dejar en blanco para mantener la actual)</label>
                    <input type="password" class="ds-input @error('password') is-invalid @enderror"
                        id="password" name="password">
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="ds-label">Confirmar Contraseña</label>
                    <input type="password" class="ds-input"
                        id="password_confirmation" name="password_confirmation">
                </div>

                <div class="mb-4">
                    <span class="ds-label d-block mb-2">Roles</span>
                    @foreach($roles as $role)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="roles[]"
                                value="{{ $role->id }}" id="role{{ $role->id }}"
                                {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }}>
                            <label class="form-check-label" for="role{{ $role->id }}">
                                {{ $role->name }}
                            </label>
                        </div>
                    @endforeach
                    @error('roles')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between gap-2">
                    <a href="{{ route('users.index') }}" class="ds-btn ds-btn--secondary">Cancelar</a>
                    <button type="submit" class="ds-btn ds-btn--primary">Actualizar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
