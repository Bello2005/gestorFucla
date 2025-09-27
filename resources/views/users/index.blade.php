@extends('layouts.main')

@section('title', 'Gestión de Usuarios')

@push('styles')
<style>
.modal-header { 
    background-color: var(--bs-primary); 
    color: white; 
}
.modal-title { 
    color: white; 
}
.btn-close { 
    filter: brightness(0) invert(1); 
}

.role-badge {
    display: inline-block;
    padding: 0.25em 0.6em;
    font-size: 0.75em;
    font-weight: 500;
    border-radius: 0.25rem;
    color: white;
    background-color: var(--bs-primary);
    margin-right: 0.3em;
    margin-bottom: 0.3em;
}

.role-badge.admin {
    background-color: var(--bs-danger);
}

.search-row {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 1rem;
}

.search-container {
    max-width: 300px;
    width: 100%;
}

.btn-group .btn {
    padding: 0.25rem 0.5rem;
}

.btn-group .btn i {
    width: 1em;
    text-align: center;
}
</style>
@endpush


@section('content')
<div class="pt-3 pb-2 mb-3 px-4 w-100">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Gestión de Usuarios</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fas fa-plus me-2"></i>Nuevo Usuario
        </button>
    </div>
</div>
<div class="w-100 px-4">
    <div class="card shadow-sm w-100">
        <div class="card-body w-100">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="search-row mb-3">
                <div class="d-flex gap-2 align-items-center">
                    <div class="search-container flex-grow-1" style="max-width: 300px;">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Buscar usuarios...">
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive w-100">
                <table id="usuariosTable" class="table table-hover align-middle w-100">
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
                            <td>{{ $usuario->name }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td>
                                @foreach($usuario->roles as $role)
                                    <span class="role-badge {{ $role->slug }}">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                <div>{{ $usuario->created_at->format('d/m/Y') }}</div>
                                @if($usuario->last_password_reset)
                                    <small class="text-muted" title="Último restablecimiento de contraseña">
                                        <i class="fas fa-key me-1"></i>{{ \Carbon\Carbon::parse($usuario->last_password_reset)->format('d/m/Y') }}
                                    </small>
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
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Crear Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createUserForm">
                @csrf
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
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required autocomplete="new-password">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
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
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Crear Usuario
                    </button>
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
                <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i>Editar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editUserId" name="user_id">
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
                        <div class="input-group">
                            <input type="password" class="form-control" id="editPassword" name="password" autocomplete="new-password">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('editPassword')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
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
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Guardar Cambios
                    </button>
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Entendido</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    console.log('Script iniciado');

    // Verificar si jQuery está disponible
    if (typeof $ === 'undefined') {
        console.error('jQuery no está cargado');
        return;
    }

    // Configurar CSRF token para todas las solicitudes AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });

    // Búsqueda en tiempo real
    $('#searchInput').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $("#usuariosTable tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    // Función para limpiar formularios
    function resetForm(formId) {
        $(formId)[0].reset();
        $(formId + ' .is-invalid').removeClass('is-invalid');
        $(formId + ' .invalid-feedback').remove();
    }

    // Función para mostrar errores
    function showErrors(form, errors) {
        Object.keys(errors).forEach(function(key) {
            const input = form.find('[name="' + key + '"]');
            input.addClass('is-invalid');
            input.after('<div class="invalid-feedback">' + errors[key][0] + '</div>');
        });
    }

    // Toggle Password Visibility
    window.togglePassword = function(inputId) {
        const input = document.getElementById(inputId);
        const type = input.type === 'password' ? 'text' : 'password';
        input.type = type;
        const icon = $(event.currentTarget).find('i');
        icon.toggleClass('fa-eye fa-eye-slash');
    }

    // Create User
    $('#createUserForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        resetForm('#createUserForm');
        
        $.ajax({
            url: '/users',
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
                if (xhr.status === 422) {
                    showErrors(form, xhr.responseJSON.errors);
                } else {
                    alert('Error al crear usuario: ' + xhr.responseJSON.message);
                }
            }
        });
    });

    // Edit User
    $('.edit-user').on('click', function(e) {
        e.preventDefault();
        console.log('Click en editar usuario');
        const userId = $(this).data('user');
        console.log('ID del usuario:', userId);
        
        resetForm('#editUserForm');
        $('.edit-role').prop('checked', false);
        
        $.get('/users/' + userId, function(data) {
            console.log('Datos recibidos:', data);
            $('#editUserId').val(data.id);
            $('#editName').val(data.name);
            $('#editEmail').val(data.email);
            
            data.roles.forEach(function(role) {
                $('#editRole' + role.id).prop('checked', true);
            });
            
            $('#editUserModal').modal('show');
        }).fail(function(xhr, status, error) {
            console.error('Error al obtener datos del usuario:', error);
            console.error('Respuesta:', xhr.responseText);
            alert('Error al cargar los datos del usuario');
        });
    });

    // Update User
    $('#editUserForm').on('submit', function(e) {
        e.preventDefault();
        const userId = $('#editUserId').val();
        const form = $(this);
        const formData = new FormData(this);
        formData.append('_method', 'PUT'); // Agregar método PUT
        
        $.ajax({
            url: '/users/' + userId,
            type: 'POST', // Usamos POST pero con _method=PUT para soporte de FormData
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#editUserModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showErrors(form, xhr.responseJSON.errors);
                } else {
                    alert('Error al actualizar usuario: ' + xhr.responseJSON.message);
                }
            }
        });
    });

    // Delete User
    $('.delete-user').click(function() {
        const userId = $(this).data('user');
        if (confirm('¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.')) {
            $.ajax({
                url: '/users/' + userId,
                type: 'POST',
                data: {
                    _method: 'DELETE'
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
        const userId = $(this).data('user');
        $('#resetUserId').val(userId);
        $('#resetPasswordForm')[0].reset();
        $('#resetPasswordModal').modal('show');
    });

    // Validar formulario de restablecimiento
    $('#resetPasswordForm').on('submit', function(e) {
        e.preventDefault();
        console.log('Enviando formulario de restablecimiento');
        
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

        var data = {
            reset_type: resetType,
            motivo: motivo || '',  // Asegurarse de que nunca sea null
            force_change: $('#forceChange').prop('checked') ? 1 : 0,  // Enviar como 1/0
            invalidate_sessions: $('#invalidateSessions').prop('checked') ? 1 : 0  // Enviar como 1/0
        };
        
        console.log('Datos a enviar:', data);
        
        $.ajax({
            url: '/users/' + $('#resetUserId').val() + '/reset-password',
            type: 'POST',
            data: data,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            success: function(response) {
                $('#resetPasswordModal').modal('hide');
                
                if (response.message.includes('temporal')) {
                    const tempPass = response.message.split('temporal: ')[1].split('.')[0];
                    $('#tempPasswordText').val(tempPass);
                    $('#temporalPasswordModal').modal('show');
                } else {
                    alert('Se ha enviado un enlace de restablecimiento al correo del usuario.');
                    location.reload();
                }
            },
            error: function(xhr) {
                alert('Error al restablecer contraseña: ' + xhr.responseJSON.message);
            }
        });
    });

    // Cambiar validación según tipo de reset
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
@endpush