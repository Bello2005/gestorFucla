@extends('layouts.main')

@section('title', 'Gestion de Usuarios')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="breadcrumb-link">Dashboard</a>
    <i class="fas fa-chevron-right breadcrumb-sep"></i>
    <span class="breadcrumb-current">Usuarios</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Gestion de Usuarios</h1>
        <p class="page-subtitle">Administra los usuarios y sus roles en el sistema</p>
    </div>
    <div class="page-actions">
        <button type="button" class="ds-btn ds-btn--primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fas fa-plus"></i> Nuevo Usuario
        </button>
    </div>
</div>

@if(session('success'))
    <div class="ds-alert ds-alert--success" style="margin-bottom: 16px;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="ds-alert ds-alert--danger" style="margin-bottom: 16px;">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif

<div class="ds-card">
    <div class="table-toolbar">
        <div class="table-search">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Buscar usuarios..." id="searchInput" autocomplete="off">
        </div>
    </div>

    <div class="table-responsive">
        <table id="usuariosTable" class="ds-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Fecha Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $usuario)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary-50); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 600; flex-shrink: 0;">
                                {{ strtoupper(substr($usuario->name, 0, 1)) }}
                            </div>
                            <span style="font-weight: 500; color: var(--slate-900);">{{ $usuario->name }}</span>
                        </div>
                    </td>
                    <td><span style="font-size: var(--text-sm); color: var(--slate-600);">{{ $usuario->email }}</span></td>
                    <td>
                        @foreach($usuario->roles as $role)
                            <span class="ds-role-badge ds-role-badge--{{ $role->slug }}">{{ $role->name }}</span>
                        @endforeach
                    </td>
                    <td>
                        <div style="font-size: var(--text-sm); color: var(--slate-700);">{{ $usuario->created_at->format('d/m/Y') }}</div>
                        @if($usuario->last_password_reset)
                            <small style="font-size: var(--text-xs); color: var(--slate-400);">
                                <i class="fas fa-key" style="margin-right: 2px;"></i>{{ \Carbon\Carbon::parse($usuario->last_password_reset)->format('d/m/Y') }}
                            </small>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button type="button" class="action-btn action-btn--edit edit-user" data-user="{{ $usuario->id }}" title="Editar usuario">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="action-btn action-btn--view reset-password" data-user="{{ $usuario->id }}" title="Restablecer contrasena">
                                <i class="fas fa-key"></i>
                            </button>
                            @if($usuario->id !== auth()->id())
                                <button type="button" class="action-btn action-btn--delete delete-user" data-user="{{ $usuario->id }}" title="Eliminar usuario">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Crear Usuario -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-header-content">
                    <div class="modal-icon-badge modal-icon-badge--navy">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div>
                        <h5 class="modal-title">Crear Nuevo Usuario</h5>
                        <p class="modal-subtitle">Completa los campos para registrar un nuevo acceso</p>
                    </div>
                </div>
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
            <form id="createUserForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="ds-label" for="name">Nombre</label>
                        <input type="text" class="ds-input" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label class="ds-label" for="email">Email</label>
                        <input type="email" class="ds-input" id="email" name="email" required autocomplete="username">
                    </div>
                    <div class="form-group">
                        <label class="ds-label" for="password">Contraseña</label>
                        <input type="password" class="ds-input" id="password" name="password" required autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label class="ds-label">Roles</label>
                        <div class="ds-check-grid">
                            @foreach($roles as $role)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role{{ $role->id }}">
                                <label class="form-check-label" for="role{{ $role->id }}">{{ $role->name }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="ds-btn ds-btn--ghost" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="ds-btn ds-btn--primary"><i class="fas fa-save"></i> Crear Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-header-content">
                    <div class="modal-icon-badge modal-icon-badge--navy">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <div>
                        <h5 class="modal-title">Editar Usuario</h5>
                        <p class="modal-subtitle">Modifica los datos del usuario seleccionado</p>
                    </div>
                </div>
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
            <form id="editUserForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editUserId" name="user_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="ds-label" for="editName">Nombre</label>
                        <input type="text" class="ds-input" id="editName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label class="ds-label" for="editEmail">Email</label>
                        <input type="email" class="ds-input" id="editEmail" name="email" required autocomplete="username">
                    </div>
                    <div class="form-group">
                        <label class="ds-label" for="editPassword">
                            Nueva Contraseña
                            <small>(dejar vacío para mantener la actual)</small>
                        </label>
                        <input type="password" class="ds-input" id="editPassword" name="password" autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label class="ds-label">Roles</label>
                        <div class="ds-check-grid" id="editRolesContainer">
                            @foreach($roles as $role)
                            <div class="form-check">
                                <input class="form-check-input edit-role" type="checkbox" name="roles[]" value="{{ $role->id }}" id="editRole{{ $role->id }}">
                                <label class="form-check-label" for="editRole{{ $role->id }}">{{ $role->name }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="ds-btn ds-btn--ghost" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="ds-btn ds-btn--primary"><i class="fas fa-save"></i> Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Restablecer Contraseña -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-header-content">
                    <div class="modal-icon-badge modal-icon-badge--gold">
                        <i class="fas fa-key"></i>
                    </div>
                    <div>
                        <h5 class="modal-title">Restablecer Contraseña</h5>
                        <p class="modal-subtitle">Gestiona las credenciales del usuario</p>
                    </div>
                </div>
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
            <form id="resetPasswordForm">
                <input type="hidden" id="resetUserId" name="user_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="ds-label">Método de Restablecimiento</label>
                        <div class="modal-radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="reset_type" id="resetTypeEmail" value="email" checked>
                                <label class="form-check-label" for="resetTypeEmail">
                                    <i class="fas fa-envelope" style="font-size: 13px; opacity: 0.7;"></i>
                                    Enviar enlace por correo
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="reset_type" id="resetTypeTemporal" value="temporal">
                                <label class="form-check-label" for="resetTypeTemporal">
                                    <i class="fas fa-key" style="font-size: 13px; opacity: 0.7;"></i>
                                    Generar contraseña temporal
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="ds-label" for="resetReason">Motivo del Restablecimiento</label>
                        <textarea class="ds-textarea" id="resetReason" name="motivo" rows="2"></textarea>
                        <small style="color: var(--slate-400); font-size: var(--text-xs);">Recomendado para mantener un registro de las razones del cambio.</small>
                    </div>
                    <div class="form-group">
                        <label class="ds-label">Opciones adicionales</label>
                        <div class="modal-check-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="force_change" id="forceChange" checked>
                                <label class="form-check-label" for="forceChange">Obligar a cambiar la contraseña en el primer acceso</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="invalidate_sessions" id="invalidateSessions" checked>
                                <label class="form-check-label" for="invalidateSessions">Invalidar todas las sesiones activas</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="ds-btn ds-btn--ghost" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="ds-btn ds-btn--primary">Confirmar Restablecimiento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Contraseña Temporal -->
<div class="modal fade" id="temporalPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-header-content">
                    <div class="modal-icon-badge modal-icon-badge--gold">
                        <i class="fas fa-lock-open"></i>
                    </div>
                    <div>
                        <h5 class="modal-title">Contraseña Temporal Generada</h5>
                        <p class="modal-subtitle">Guarda esta contraseña antes de cerrar</p>
                    </div>
                </div>
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="ds-alert ds-alert--warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Importante:</strong> Esta contraseña temporal solo se mostrará una vez.
                </div>
                <p style="font-size: var(--text-sm); color: var(--slate-600);">El usuario deberá cambiarla inmediatamente al ingresar. Las sesiones activas han sido cerradas.</p>
                <div class="form-group">
                    <label class="ds-label">Contraseña Temporal</label>
                    <div style="display: flex; gap: 8px;">
                        <input type="text" class="ds-input" id="tempPasswordText" readonly>
                        <button class="ds-btn ds-btn--secondary" type="button" id="copyTempPassword">
                            <i class="fas fa-copy"></i> Copiar
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="ds-btn ds-btn--primary" data-bs-dismiss="modal">Entendido</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

    $('#searchInput').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $("#usuariosTable tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    function resetForm(formId) {
        $(formId)[0].reset();
        $(formId + ' .is-invalid').removeClass('is-invalid');
        $(formId + ' .invalid-feedback').remove();
    }

    function showErrors(form, errors) {
        Object.keys(errors).forEach(function(key) {
            var input = form.find('[name="' + key + '"]');
            input.addClass('is-invalid');
            input.after('<div class="invalid-feedback">' + errors[key][0] + '</div>');
        });
    }

    window.togglePassword = function(inputId) {
        var input = document.getElementById(inputId);
        input.type = input.type === 'password' ? 'text' : 'password';
    };

    $('#createUserForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        resetForm('#createUserForm');
        $.ajax({
            url: '/users', type: 'POST',
            data: new FormData(this), processData: false, contentType: false,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function() { $('#createUserModal').modal('hide'); location.reload(); },
            error: function(xhr) {
                if (xhr.status === 422) showErrors(form, xhr.responseJSON.errors);
                else alert('Error al crear usuario: ' + xhr.responseJSON.message);
            }
        });
    });

    $('.edit-user').on('click', function(e) {
        e.preventDefault();
        var userId = $(this).data('user');
        resetForm('#editUserForm');
        $('.edit-role').prop('checked', false);
        $.get('/users/' + userId, function(data) {
            $('#editUserId').val(data.id);
            $('#editName').val(data.name);
            $('#editEmail').val(data.email);
            data.roles.forEach(function(role) { $('#editRole' + role.id).prop('checked', true); });
            $('#editUserModal').modal('show');
        }).fail(function() { alert('Error al cargar los datos del usuario'); });
    });

    $('#editUserForm').on('submit', function(e) {
        e.preventDefault();
        var userId = $('#editUserId').val();
        var form = $(this);
        var formData = new FormData(this);
        formData.append('_method', 'PUT');
        $.ajax({
            url: '/users/' + userId, type: 'POST',
            data: formData, processData: false, contentType: false,
            success: function() { $('#editUserModal').modal('hide'); location.reload(); },
            error: function(xhr) {
                if (xhr.status === 422) showErrors(form, xhr.responseJSON.errors);
                else alert('Error al actualizar usuario: ' + xhr.responseJSON.message);
            }
        });
    });

    $('.delete-user').click(function() {
        var userId = $(this).data('user');
        if (confirm('¿Esta seguro de que desea eliminar este usuario?')) {
            $.ajax({
                url: '/users/' + userId, type: 'POST', data: { _method: 'DELETE' },
                success: function() { location.reload(); },
                error: function(xhr) { alert('Error al eliminar usuario: ' + xhr.responseJSON.message); }
            });
        }
    });

    $('.reset-password').click(function() {
        var userId = $(this).data('user');
        $('#resetUserId').val(userId);
        $('#resetPasswordForm')[0].reset();
        $('#resetPasswordModal').modal('show');
    });

    $('#resetPasswordForm').on('submit', function(e) {
        e.preventDefault();
        var resetType = $('input[name="reset_type"]:checked').val();
        var motivo = $('#resetReason').val();
        if (!resetType) { alert('Debe seleccionar un metodo'); return; }
        if (resetType === 'temporal' && !motivo.trim()) { alert('El motivo es obligatorio para contrasenas temporales'); return; }
        $.ajax({
            url: '/users/' + $('#resetUserId').val() + '/reset-password',
            type: 'POST', dataType: 'json',
            data: {
                reset_type: resetType, motivo: motivo || '',
                force_change: $('#forceChange').prop('checked') ? 1 : 0,
                invalidate_sessions: $('#invalidateSessions').prop('checked') ? 1 : 0
            },
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            success: function(response) {
                $('#resetPasswordModal').modal('hide');
                if (response.message.includes('temporal')) {
                    var tempPass = response.message.split('temporal: ')[1].split('.')[0];
                    $('#tempPasswordText').val(tempPass);
                    $('#temporalPasswordModal').modal('show');
                } else {
                    alert('Se ha enviado un enlace de restablecimiento al correo del usuario.');
                    location.reload();
                }
            },
            error: function(xhr) { alert('Error: ' + xhr.responseJSON.message); }
        });
    });

    $('input[name="reset_type"]').change(function() {
        var isTemporal = $(this).val() === 'temporal';
        $('#resetReason').prop('required', isTemporal);
    });

    $('#copyTempPassword').click(function() {
        navigator.clipboard.writeText($('#tempPasswordText').val()).then(function() {
            var $btn = $('#copyTempPassword');
            $btn.html('<i class="fas fa-check"></i> Copiado!');
            setTimeout(function() { $btn.html('<i class="fas fa-copy"></i> Copiar'); }, 2000);
        });
    });

    $('#temporalPasswordModal').on('hidden.bs.modal', function() { location.reload(); });
});
</script>
@endpush
