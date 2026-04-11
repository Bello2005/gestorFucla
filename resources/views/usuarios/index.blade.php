@extends('layouts.main')

@section('title', 'Gestión de Usuarios')

@section('styles')
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- DataTables (base + buttons; estilos UC vía app.css) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <style>
        #usuariosTable_filter {
            display: none !important;
        }
        .search-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: nowrap;
            width: 100%;
        }
        .search-row .search-container {
            flex: 1 1 220px;
            min-width: 180px;
        }
        .search-row #usuariosTable_length {
            flex: 0 0 auto;
            min-width: 120px;
            margin-bottom: 0 !important;
            display: flex;
            align-items: center;
            height: 38px;
        }
        .role-badge {
            display: inline-block;
            padding: 0.25em 0.6em;
            font-size: 0.85em;
            font-weight: 500;
            border-radius: 12px;
            background-color: #e9ecef;
            margin: 0.1em;
        }
        .role-badge.admin {
            background-color: #ffd700;
            color: #000;
        }
        .role-badge.user {
            background-color: #6c757d;
            color: #fff;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Gestión de Usuarios</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fas fa-plus me-2"></i>Nuevo Usuario
        </button>
    </div>

    <div class="card uc-dt-wrap">
        <div class="card-body">
            <div class="search-row mb-3">
                <div class="search-container">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Buscar usuarios...">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="usuariosTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Roles</th>
                            <th>Fecha Registro</th>
                            <th>Último Reset</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->id }}</td>
                            <td>{{ $usuario->name }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td>
                                @foreach($usuario->roles as $role)
                                    <span class="role-badge {{ $role->slug }}">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if(is_string($usuario->created_at))
                                    {{ \Carbon\Carbon::parse($usuario->created_at)->format('d/m/Y') }}
                                @else
                                    {{ $usuario->created_at->format('d/m/Y') }}
                                @endif
                            </td>
                            <td>
                                @if($usuario->last_password_reset)
                                    <span class="text-muted" title="Último restablecimiento de contraseña">
                                        @if(is_string($usuario->last_password_reset))
                                            {{ \Carbon\Carbon::parse($usuario->last_password_reset)->format('d/m/Y H:i') }}
                                        @else
                                            {{ \Carbon\Carbon::parse($usuario->last_password_reset->created_at)->format('d/m/Y H:i') }}
                                        @endif
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary edit-user" data-user="{{ $usuario->id }}" title="Editar usuario">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-info reset-password" data-user="{{ $usuario->id }}" title="Restablecer contraseña">
                                        <i class="fas fa-key"></i>
                                    </button>
                                    @if($usuario->id !== auth()->id())
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-user" data-user="{{ $usuario->id }}" title="Eliminar usuario">
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
    </div>
</div>

<!-- Modal Crear Usuario -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createUserForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required autocomplete="username">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required autocomplete="new-password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Roles</label>
                        <div class="row g-2">
                            @foreach($roles as $role)
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role{{ $role->id }}">
                                    <label class="form-check-label" for="role{{ $role->id }}">
                                        {{ $role->name }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Usuario</button>
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
                <h5 class="modal-title">Restablecer Contraseña</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="resetPasswordForm">
                <input type="hidden" id="resetUserId" name="user_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label d-block">Método de Restablecimiento</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="reset_type" id="resetTypeEmail" value="email" checked>
                            <label class="form-check-label" for="resetTypeEmail">
                                Enviar enlace por correo
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="reset_type" id="resetTypeTemporal" value="temporal">
                            <label class="form-check-label" for="resetTypeTemporal">
                                Generar contraseña temporal
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="resetReason" class="form-label">Motivo del Restablecimiento</label>
                        <textarea class="form-control" id="resetReason" name="motivo" rows="2"></textarea>
                        <div class="form-text">Recomendado para mantener un registro de las razones del cambio.</div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="force_change" id="forceChange" checked>
                            <label class="form-check-label" for="forceChange">
                                Obligar a cambiar la contraseña en el primer acceso
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="invalidate_sessions" id="invalidateSessions" checked>
                            <label class="form-check-label" for="invalidateSessions">
                                Invalidar todas las sesiones activas
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Confirmar Restablecimiento</button>
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
                <h5 class="modal-title">Contraseña Temporal Generada</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Importante:</strong> Esta contraseña temporal solo se mostrará una vez.
                </div>
                
                <p class="mb-3">Se ha generado una contraseña temporal para el usuario. Esta contraseña:</p>
                <ul class="mb-4">
                    <li>Solo será válida para un único inicio de sesión</li>
                    <li>El usuario deberá cambiarla inmediatamente al ingresar</li>
                    <li>Las sesiones activas del usuario han sido cerradas</li>
                </ul>

                <div class="mb-4">
                    <label class="form-label">Contraseña Temporal:</label>
                    <div class="input-group">
                        <input type="text" class="form-control font-monospace" id="tempPasswordText" readonly>
                        <button class="btn btn-outline-primary" type="button" id="copyTempPassword">
                            <i class="fas fa-copy me-1"></i> Copiar
                        </button>
                    </div>
                </div>

                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Asegúrate de comunicar esta contraseña al usuario de manera segura.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Entendido</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm">
                @method('PUT')
                <input type="hidden" id="editUserId" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editName" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required autocomplete="username">
                    </div>
                    <div class="mb-3">
                        <label for="editPassword" class="form-label">Nueva Contraseña (dejar en blanco para mantener la actual)</label>
                        <input type="password" class="form-control" id="editPassword" name="password" autocomplete="new-password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Roles</label>
                        <div class="row g-2" id="editRolesContainer">
                            @foreach($roles as $role)
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input edit-role" type="checkbox" name="roles[]" value="{{ $role->id }}" id="editRole{{ $role->id }}">
                                    <label class="form-check-label" for="editRole{{ $role->id }}">
                                        {{ $role->name }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    // Inicializar DataTable
    var table = $('#usuariosTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
        },
        order: [[0, 'desc']],
        pageLength: 10,
        dom: 'rtip'
    });

    // Búsqueda personalizada
    $('#searchInput').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Crear Usuario
    $('#createUserForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("usuarios.store") }}',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#createUserModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('Error al crear usuario: ' + xhr.responseJSON.message);
            }
        });
    });

    // Editar Usuario
    $('.edit-user').click(function() {
        var userId = $(this).data('user');
        var row = $(this).closest('tr');
        
        $('#editUserId').val(userId);
        $('#editName').val(row.find('td:eq(1)').text());
        $('#editEmail').val(row.find('td:eq(2)').text());
        
        // Limpiar roles anteriores
        $('.edit-role').prop('checked', false);
        
        // Marcar roles actuales
        row.find('.role-badge').each(function() {
            var roleName = $(this).text().trim().toLowerCase();
            $('#editRolesContainer input[type="checkbox"]').each(function() {
                if ($(this).next('label').text().trim().toLowerCase() === roleName) {
                    $(this).prop('checked', true);
                }
            });
        });
        
        $('#editUserModal').modal('show');
    });

    // Actualizar Usuario
    $('#editUserForm').on('submit', function(e) {
        e.preventDefault();
        var userId = $('#editUserId').val();
        $.ajax({
            url: '/usuarios/' + userId,
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#editUserModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessage = 'Se encontraron los siguientes errores:\n';
                    Object.keys(errors).forEach(function(key) {
                        errorMessage += '\n- ' + errors[key][0];
                    });
                    alert(errorMessage);
                } else {
                    alert('Error al actualizar usuario: ' + (xhr.responseJSON.message || 'Error desconocido'));
                }
            }
        });
    });

    // Eliminar Usuario
    $('.delete-user').click(function() {
        if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
            var userId = $(this).data('user');
            $.ajax({
                url: '/usuarios/' + userId,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error al eliminar usuario: ' + xhr.responseJSON.message);
                }
            });
        }
    });

    // Restablecer Contraseña
    $('.reset-password').click(function() {
        var userId = $(this).data('user');
        $('#resetUserId').val(userId);
        $('#resetPasswordModal').modal('show');
    });

    // Validar formulario de restablecimiento
    $('#resetPasswordForm').on('submit', function(e) {
        e.preventDefault();
        
        var resetType = $('input[name="reset_type"]:checked').val();
        var motivo = $('#resetReason').val();
        
        if (!resetType) {
            alert('Debe seleccionar un método de restablecimiento');
            return;
        }
        
        if (resetType === 'temporal' && !motivo.trim()) {
            alert('El motivo es obligatorio cuando se genera una contraseña temporal');
            return;
        }
        
        var formData = new FormData(this);
        // Agregar explícitamente los campos booleanos
        formData.set('force_change', $('#forceChange').prop('checked') ? '1' : '0');
        formData.set('invalidate_sessions', $('#invalidateSessions').prop('checked') ? '1' : '0');
        
        $.ajax({
            url: '/usuarios/' + $('#resetUserId').val() + '/reset-password',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#resetPasswordModal').modal('hide');
                
                // Pequeña pausa para asegurar que el modal anterior se cierre completamente
                setTimeout(function() {
                    if (response.message && response.message.includes('temporal')) {
                        // Extraer la contraseña temporal del mensaje
                        const tempPass = response.message.match(/temporal: ([^\s.]+)/)[1];
                        
                        // Actualizar y mostrar el modal de contraseña temporal
                        $('#tempPasswordText').val(tempPass);
                        $('#temporalPasswordModal').modal('show');
                    } else {
                        alert('Se ha enviado el enlace de restablecimiento al correo del usuario.');
                        location.reload();
                    }
                }, 500);
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessage = 'Se encontraron los siguientes errores:\n';
                    Object.keys(errors).forEach(function(key) {
                        errorMessage += '\n- ' + errors[key][0];
                    });
                    alert(errorMessage);
                } else {
                    alert('Error al restablecer contraseña: ' + (xhr.responseJSON.message || xhr.responseJSON.error || 'Error desconocido'));
                }
            }
        });
    });

    // Cambiar validación según tipo de restablecimiento
    $('input[name="reset_type"]').change(function() {
        var isTemporalPassword = $(this).val() === 'temporal';
        $('#resetReason').prop('required', isTemporalPassword);
        $('#resetReason').closest('.mb-3').find('.form-text')
            .text(isTemporalPassword ? 'Obligatorio para contraseñas temporales' : 'Recomendado para mantener un registro de las razones del cambio.');
    });

    // Copiar contraseña temporal al portapapeles
    $('#copyTempPassword').click(function() {
        const tempPass = $('#tempPasswordText').val();
        navigator.clipboard.writeText(tempPass)
            .then(function() {
                // Cambiar el texto del botón temporalmente
                const $btn = $('#copyTempPassword');
                const originalText = $btn.html();
                $btn.html('<i class="fas fa-check"></i> ¡Copiado!');
                setTimeout(function() {
                    $btn.html(originalText);
                }, 2000);
            })
            .catch(function(err) {
                console.error('Error al copiar: ', err);
                alert('No se pudo copiar la contraseña. Por favor, cópiela manualmente.');
            });
    });

    // Cuando se cierra el modal de contraseña temporal
    $('#temporalPasswordModal').on('hidden.bs.modal', function () {
        location.reload();
    });
});
</script>
@endsection
