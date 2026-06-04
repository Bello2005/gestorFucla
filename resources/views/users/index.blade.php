@extends('layouts.main')

@push('styles')
<style>
/* Tabs del modal de edición — override del sidebar global */
#editUserModal .modal-dialog { max-width: 640px; }
#editUserModal .nav-tabs .nav-link {
    color: var(--slate-500);
    background: transparent;
    border: 1px solid transparent;
    border-radius: 6px 6px 0 0;
    padding: 8px 18px;
    font-size: var(--text-sm);
    font-weight: var(--font-medium);
}
#editUserModal .nav-tabs .nav-link:hover { color: var(--uni-navy); background: var(--neutral-50); }
#editUserModal .nav-tabs .nav-link.active {
    color: var(--uni-navy);
    background: #fff;
    border-color: var(--neutral-200) var(--neutral-200) #fff;
    font-weight: var(--font-semibold);
}
#editUserModal .nav-tabs .nav-link.active::before { display: none; }

/* Role selector cards */
.role-selector { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 4px; }
.role-card {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 14px; border: 2px solid var(--neutral-200);
    border-radius: 12px; cursor: pointer;
    transition: border-color .15s, background .15s;
    position: relative; background: #fff;
}
.role-card:hover { border-color: var(--primary-300, #93c5fd); background: var(--primary-50, #eff6ff); }
.role-card.selected { border-color: var(--primary, #3b82f6); background: var(--primary-50, #eff6ff); }
.role-card.selected-admin { border-color: #c6922a !important; background: rgba(198,146,42,.05) !important; }
.role-card-icon {
    width: 36px; height: 36px; border-radius: 9px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: 16px;
}
.role-card-icon--user  { background: rgba(59,130,246,.1); color: #3b82f6; }
.role-card-icon--admin { background: rgba(198,146,42,.12); color: #c6922a; }
.role-card-title { font-size: 13px; font-weight: 600; color: var(--slate-800); line-height: 1.2; }
.role-card-desc  { font-size: 11px; color: var(--slate-400); margin-top: 2px; }
.role-card-check {
    position: absolute; top: 8px; right: 8px;
    width: 18px; height: 18px; border-radius: 50%; font-size: 9px;
    display: none; align-items: center; justify-content: center; color: #fff;
}
.role-card.selected .role-card-check { display: flex; background: var(--primary, #3b82f6); }
.role-card.selected-admin .role-card-check { display: flex; background: #c6922a; }

/* Toggles en la matriz de permisos */
.perm-toggle { position: relative; display: inline-block; width: 36px; height: 20px; vertical-align: middle; }
.perm-toggle input { opacity: 0; width: 0; height: 0; position: absolute; }
.perm-track {
    position: absolute; inset: 0; background: var(--neutral-200);
    border-radius: 20px; cursor: pointer; transition: background .18s;
}
.perm-track::before {
    content: ""; position: absolute;
    width: 14px; height: 14px; border-radius: 50%;
    left: 3px; top: 3px; background: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,.18); transition: transform .18s;
}
.perm-toggle input:checked + .perm-track { background: var(--primary, #3b82f6); }
.perm-toggle--edit input:checked + .perm-track { background: #c6922a; }
.perm-toggle input:checked + .perm-track::before { transform: translateX(16px); }
</style>
@endpush

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
        <p class="page-subtitle">Administra los usuarios y sus permisos en el sistema</p>
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
                    <th>Rol</th>
                    <th>Fecha Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $usuario)
                @php
                    $label = $usuario->effectiveRoleLabel();
                    $badgeClass = str_replace(' ', '-', $label);
                @endphp
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
                        <span class="ds-role-badge ds-role-badge--{{ $badgeClass }}">{{ $label }}</span>
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
                        <p class="modal-subtitle">El usuario iniciará sin permisos. Configúralos después desde Editar.</p>
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
    <div class="modal-dialog modal-dialog-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-header-content">
                    <div class="modal-icon-badge modal-icon-badge--navy">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <div>
                        <h5 class="modal-title">Editar Usuario</h5>
                        <p class="modal-subtitle">
                            Rol actual: <span id="editEffectiveRoleBadge" class="ds-role-badge"></span>
                        </p>
                    </div>
                </div>
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>

            <ul class="nav nav-tabs px-3 pt-2" id="editUserTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-datos-btn" data-bs-toggle="tab"
                        data-bs-target="#tab-datos" type="button" role="tab">
                        <i class="fas fa-user me-1"></i> Datos
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-permisos-btn" data-bs-toggle="tab"
                        data-bs-target="#tab-permisos" type="button" role="tab">
                        <i class="fas fa-shield-halved me-1"></i> Permisos
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Tab: Datos -->
                <div class="tab-pane fade show active" id="tab-datos" role="tabpanel">
                    <form id="editUserForm">
                        @csrf
                        <input type="hidden" id="editUserId" name="user_id">
                        <input type="hidden" id="editIsAdmin" name="is_admin" value="0">
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
                                    Nueva contraseña
                                    <small>(dejar vacío para mantener la actual)</small>
                                </label>
                                <input type="password" class="ds-input" id="editPassword" name="password" autocomplete="new-password">
                            </div>
                            <div class="form-group">
                                <label class="ds-label">Tipo de acceso</label>
                                <div class="role-selector">
                                    <div class="role-card" id="roleCardUser" onclick="euSelectRole(0)">
                                        <div class="role-card-icon role-card-icon--user">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <div class="role-card-title">Usuario</div>
                                            <div class="role-card-desc">Acceso por permisos</div>
                                        </div>
                                        <div class="role-card-check"><i class="fas fa-check"></i></div>
                                    </div>
                                    <div class="role-card" id="roleCardAdmin" onclick="euSelectRole(1)">
                                        <div class="role-card-icon role-card-icon--admin">
                                            <i class="fas fa-shield-halved"></i>
                                        </div>
                                        <div>
                                            <div class="role-card-title">Administrador</div>
                                            <div class="role-card-desc">Acceso total al sistema</div>
                                        </div>
                                        <div class="role-card-check"><i class="fas fa-check"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="ds-btn ds-btn--ghost" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="ds-btn ds-btn--primary">
                                <i class="fas fa-save"></i> Guardar Datos
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tab: Permisos -->
                <div class="tab-pane fade" id="tab-permisos" role="tabpanel">
                    <div class="modal-body">
                        <p style="font-size:var(--text-sm);color:var(--slate-500);margin-bottom:12px;">
                            Configura qué módulos puede ver y editar este usuario.
                            Los <strong>administradores</strong> tienen acceso total sin importar esta configuración.
                        </p>
                        <div class="ds-alert" style="font-size:var(--text-xs);padding:8px 12px;margin-bottom:14px;background:var(--neutral-50);border:1px solid var(--neutral-200);border-radius:6px;color:var(--slate-500);">
                            <i class="fas fa-lock" style="color:var(--slate-400);margin-right:4px;"></i>
                            Los módulos con <i class="fas fa-lock" style="font-size:10px;"></i> son solo lectura — las escrituras en esos módulos son exclusivas de administradores.
                        </div>
                        <div id="permissionsMatrixBody"></div>
                    </div>
                    <div class="modal-footer">
                        <span id="permSaveMsg" style="font-size:var(--text-sm);color:var(--success);display:none;">
                            <i class="fas fa-check-circle"></i> Permisos guardados
                        </span>
                        <button type="button" class="ds-btn ds-btn--ghost" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="ds-btn ds-btn--primary" id="savePermissionsBtn">
                            <i class="fas fa-shield-halved"></i> Guardar Permisos
                        </button>
                    </div>
                </div>
            </div>
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
const appModules = @json($modules);

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

    window.euSelectRole = function(isAdmin) {
        $('#editIsAdmin').val(isAdmin);
        $('#roleCardUser').removeClass('selected selected-admin');
        $('#roleCardAdmin').removeClass('selected selected-admin');
        if (isAdmin === 1) {
            $('#roleCardAdmin').addClass('selected selected-admin');
            // Los admins tienen acceso total — la pestaña Permisos no aplica
            $('#tab-permisos-btn').addClass('disabled').attr('tabindex', '-1').attr('title', 'Los administradores tienen acceso total — los permisos individuales no aplican');
            // Si estaba activa la pestaña permisos, volver a Datos
            if ($('#tab-permisos').hasClass('show')) {
                new bootstrap.Tab(document.getElementById('tab-datos-btn')).show();
            }
        } else {
            $('#roleCardUser').addClass('selected');
            $('#tab-permisos-btn').removeClass('disabled').removeAttr('tabindex').attr('title', '');
        }
    };

    function roleBadgeClass(label) { return label.replace(/ /g, '-'); }

    function updateEffectiveBadge(label) {
        var cls = roleBadgeClass(label);
        $('#editEffectiveRoleBadge').text(label).attr('class', 'ds-role-badge ds-role-badge--' + cls);
        var userId = $('#editUserId').val();
        $('button.edit-user[data-user="' + userId + '"]')
            .closest('tr').find('.ds-role-badge')
            .text(label).attr('class', 'ds-role-badge ds-role-badge--' + cls);
    }

    function buildPermissionsMatrix(permsById) {
        var html = '<table class="ds-table"><thead><tr><th>Módulo</th>'
                 + '<th style="text-align:center;width:90px;">Ver</th>'
                 + '<th style="text-align:center;width:90px;">Editar</th>'
                 + '</tr></thead><tbody>';
        appModules.forEach(function(mod) {
            var perm = permsById[mod.id] || { can_view: false, can_edit: false };
            var labelHtml = mod.label + (mod.read_only
                ? ' <i class="fas fa-lock" style="font-size:10px;color:var(--slate-300);margin-left:3px;" title="Solo lectura"></i>'
                : '');
            var editCell = mod.read_only
                ? '<td style="text-align:center;"><span style="color:var(--neutral-300);font-size:13px;" title="No aplica"><i class="fas fa-minus"></i></span></td>'
                : '<td style="text-align:center;">'
                  + '<label class="perm-toggle perm-toggle--edit">'
                  + '<input type="checkbox" class="perm-edit"' + (perm.can_edit ? ' checked' : '') + '>'
                  + '<span class="perm-track"></span></label></td>';
            html += '<tr data-module-id="' + mod.id + '" data-read-only="' + (mod.read_only ? '1' : '0') + '">'
                 +  '<td>' + labelHtml + '</td>'
                 +  '<td style="text-align:center;">'
                 +    '<label class="perm-toggle"><input type="checkbox" class="perm-view"' + (perm.can_view ? ' checked' : '') + '>'
                 +    '<span class="perm-track"></span></label>'
                 +  '</td>'
                 +  editCell
                 +  '</tr>';
        });
        html += '</tbody></table>';
        $('#permissionsMatrixBody').html(html);
        $('#permSaveMsg').hide();
    }

    $(document).on('change', '.perm-edit', function() {
        if ($(this).prop('checked'))
            $(this).closest('tr').find('.perm-view').prop('checked', true);
    });
    $(document).on('change', '.perm-view', function() {
        if (!$(this).prop('checked'))
            $(this).closest('tr').find('.perm-edit').prop('checked', false);
    });

    // Crear usuario
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

    // Abrir modal de edición
    $('.edit-user').on('click', function(e) {
        e.preventDefault();
        var userId = $(this).data('user');
        resetForm('#editUserForm');
        $('#permissionsMatrixBody').html('');
        $('#permSaveMsg').hide();

        // Volver al tab Datos
        var datosTab = new bootstrap.Tab(document.getElementById('tab-datos-btn'));
        datosTab.show();

        $.get('/users/' + userId, function(data) {
            $('#editUserId').val(data.id);
            $('#editName').val(data.name);
            $('#editEmail').val(data.email);

            euSelectRole(data.is_admin ? 1 : 0);

            // Badge efectivo
            updateEffectiveBadge(data.effective_role);

            // Construir matriz de permisos
            buildPermissionsMatrix(data.permissions);

            $('#editUserModal').modal('show');
        }).fail(function() { alert('Error al cargar los datos del usuario'); });
    });

    // Guardar datos del usuario
    $('#editUserForm').on('submit', function(e) {
        e.preventDefault();
        var userId = $('#editUserId').val();
        var form = $(this);
        $.ajax({
            url: '/users/' + userId,
            type: 'POST',
            data: {
                _method:  'PUT',
                name:     $('#editName').val(),
                email:    $('#editEmail').val(),
                password: $('#editPassword').val(),
                is_admin: $('#editIsAdmin').val()
            },
            success: function() { $('#editUserModal').modal('hide'); location.reload(); },
            error: function(xhr) {
                if (xhr.status === 422) showErrors(form, xhr.responseJSON.errors);
                else if (xhr.status === 403) alert(xhr.responseJSON.message);
                else alert('Error al actualizar usuario: ' + xhr.responseJSON.message);
            }
        });
    });

    // Guardar permisos
    $('#savePermissionsBtn').on('click', function() {
        var userId = $('#editUserId').val();
        if (!userId) return;

        var permissions = [];
        $('#permissionsMatrixBody tr[data-module-id]').each(function() {
            var isReadOnly = $(this).data('read-only') === 1 || $(this).data('read-only') === '1';
            permissions.push({
                module_id: parseInt($(this).data('module-id')),
                can_view:  $(this).find('.perm-view').prop('checked'),
                can_edit:  isReadOnly ? false : $(this).find('.perm-edit').prop('checked')
            });
        });

        $.ajax({
            url: '/users/' + userId + '/permissions',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ permissions: permissions }),
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(resp) {
                updateEffectiveBadge(resp.effective_role);
                $('#euRoleBadge').text(resp.effective_role).attr('class', 'ds-role-badge ds-role-badge--' + roleBadgeClass(resp.effective_role));
                $('#permSaveMsg').fadeIn();
                setTimeout(function() { $('#permSaveMsg').fadeOut(); }, 2500);
            },
            error: function() { alert('Error al guardar los permisos. Intenta de nuevo.'); }
        });
    });

    // Eliminar usuario
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

    // Restablecer contraseña
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
