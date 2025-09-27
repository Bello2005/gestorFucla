<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editForm');
    const submitButton = document.getElementById('submitButton');
    if (!form || !submitButton) return;

    // Guarda los valores iniciales del formulario
    const initialData = {};
    Array.from(form.elements).forEach(el => {
        if (el.name) initialData[el.name] = el.value;
    });

    form.addEventListener('submit', function(e) {
        let changed = false;
        Array.from(form.elements).forEach(el => {
            if (el.name && initialData[el.name] !== el.value) changed = true;
        });
        if (!changed) {
            e.preventDefault();
            alert('No has modificado nada');
        }
    });
});
</script>
@php
    use Illuminate\Support\Str;
@endphp
@extends('layouts.main')

@section('styles')
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Project Styles -->
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">

    <style>
        .required:after {
            content: " *";
            color: red;
        }
        .nav-tabs .nav-link {
            position: relative;
        }
        .nav-tabs .nav-link.active:after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 2px;
            background: #0d6efd;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid" style="margin-left: 280px; width: calc(100% - 300px); padding: 2rem;">
    <!-- Toast de notificación -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="notificationToast" class="toast align-items-center text-white" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Encabezado -->
    <div class="row mb-4 align-items-center">
        <div class="col-12 mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('proyectos.index') }}">Proyectos</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('proyectos.show', $proyecto) }}">{{ Str::limit($proyecto->nombre_del_proyecto, 50) }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6">
            <h2 class="mb-0">
                <i class="fas fa-edit text-primary me-2"></i>
                {{ $proyecto->nombre_del_proyecto }}
            </h2>
            <p class="text-muted mt-2 mb-0">
                <span class="me-3"><i class="fas fa-hashtag me-1"></i>ID: {{ $proyecto->id }}</span>
                <span class="me-3"><i class="fas fa-calendar-plus me-1"></i>Creado: {{ $proyecto->created_at->format('d/m/Y') }}</span>
                <span><i class="fas fa-clock me-1"></i>Última actualización: {{ $proyecto->updated_at->format('d/m/Y H:i') }}</span>
            </p>
        </div>
        <div class="col-md-6 text-end">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('proyectos.show', $proyecto) }}" class="btn btn-info">
                    <i class="fas fa-eye me-2"></i>Ver Proyecto
                </a>
                <a href="{{ route('proyectos.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver al listado
                </a>
            </div>
        </div>
    </div>

    <!-- Alertas y mensajes -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <strong><i class="fas fa-exclamation-triangle me-2"></i>Por favor, corrija los siguientes errores:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Formulario principal -->
    <div class="content-card fade-in">
        <div class="card-body">
            <form id="editForm" 
                  action="{{ route('proyectos.update', ['proyecto' => $proyecto->id]) }}" 
                  method="POST" 
                  enctype="multipart/form-data"
                  onsubmit="return confirmarEnvio(event)">
                @csrf
                <!-- Campos de seguridad críticos -->
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="is_edit" value="1">
                <input type="hidden" name="proyecto_id" value="{{ $proyecto->id }}">
                <!-- Verificación adicional de seguridad -->
                <input type="hidden" name="form_action" value="update">
                <input type="hidden" name="original_url" value="{{ url()->current() }}">

                <!-- Pestañas de navegación -->
                <ul class="nav nav-tabs mb-4" id="projectTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="info-tab" data-bs-toggle="tab" href="#info" role="tab">
                            <i class="fas fa-info-circle me-2"></i>Información General
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="docs-tab" data-bs-toggle="tab" href="#docs" role="tab">
                            <i class="fas fa-file-alt me-2"></i>Documentos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="evidencias-tab" data-bs-toggle="tab" href="#evidencias" role="tab">
                            <i class="fas fa-images me-2"></i>Evidencias
                        </a>
                    </li>
                </ul>

                <!-- Contenido de las pestañas -->
                <div class="tab-content" id="projectTabsContent">
                    <!-- Pestaña de Información General -->
                    <div class="tab-pane fade show active" id="info" role="tabpanel">
                        <div class="row g-3">
                            <!-- Nombre del Proyecto -->
                            <div class="col-md-8 mb-3">
                                <label for="nombre_del_proyecto" class="form-label required">
                                    Nombre del Proyecto
                                </label>
                                <input type="text" 
                                       class="form-control form-control-lg @error('nombre_del_proyecto') is-invalid @enderror" 
                                       id="nombre_del_proyecto" 
                                       name="nombre_del_proyecto" 
                                       value="{{ old('nombre_del_proyecto', $proyecto->nombre_del_proyecto) }}" 
                                       required
                                       maxlength="255">
                                @error('nombre_del_proyecto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Estado del Proyecto -->
                            <div class="col-md-4 mb-3">
                                <label for="estado" class="form-label">Estado del Proyecto</label>
                                <select class="form-select @error('estado') is-invalid @enderror" 
                                        id="estado" 
                                        name="estado">
                                    <option value="activo" {{ old('estado', $proyecto->estado) == 'activo' ? 'selected' : '' }}>
                                        Activo
                                    </option>
                                    <option value="cerrado" {{ old('estado', $proyecto->estado) == 'cerrado' ? 'selected' : '' }}>
                                        Cerrado
                                    </option>
                                    <option value="inactivo" {{ old('estado', $proyecto->estado) == 'inactivo' ? 'selected' : '' }}>
                                        Inactivo
                                    </option>
                                </select>
                                @error('estado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Valor Total -->
                            <div class="col-md-4 mb-3">
                                <label for="valor_total" class="form-label">Valor Total ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                    <input type="number" 
                                           class="form-control @error('valor_total') is-invalid @enderror" 
                                           id="valor_total" 
                                           name="valor_total" 
                                           value="{{ old('valor_total', $proyecto->valor_total) }}"
                                           step="0.01"
                                           min="0">
                                </div>
                                @error('valor_total')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text valor-total-formatted"></div>
                            </div>

                            <!-- Plazo -->
                            <div class="col-md-4 mb-3">
                                <label for="plazo" class="form-label">Plazo (meses)</label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control @error('plazo') is-invalid @enderror" 
                                           id="plazo" 
                                           name="plazo" 
                                           value="{{ old('plazo', $proyecto->plazo) }}"
                                           step="0.01"
                                           min="0">
                                    <span class="input-group-text">meses</span>
                                </div>
                                @error('plazo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fecha de Ejecución -->
                            <div class="col-md-4 mb-3">
                                <label for="fecha_de_ejecucion" class="form-label">Fecha de Ejecución</label>
                                <input type="date" 
                                       class="form-control @error('fecha_de_ejecucion') is-invalid @enderror" 
                                       id="fecha_de_ejecucion" 
                                       name="fecha_de_ejecucion" 
                                       value="{{ old('fecha_de_ejecucion', $proyecto->fecha_de_ejecucion ? date('Y-m-d', strtotime($proyecto->fecha_de_ejecucion)) : '') }}">
                                @error('fecha_de_ejecucion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Entidad Contratante -->
                            <div class="col-md-6 mb-3">
                                <label for="entidad_contratante" class="form-label">Entidad Contratante</label>
                                <input type="text" 
                                       class="form-control @error('entidad_contratante') is-invalid @enderror" 
                                       id="entidad_contratante" 
                                       name="entidad_contratante" 
                                       value="{{ old('entidad_contratante', $proyecto->entidad_contratante) }}"
                                       maxlength="255">
                                @error('entidad_contratante')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Cobertura -->
                            <div class="col-md-6 mb-3">
                                <label for="cobertura" class="form-label">Cobertura</label>
                                <input type="text" 
                                       class="form-control @error('cobertura') is-invalid @enderror" 
                                       id="cobertura" 
                                       name="cobertura" 
                                       value="{{ old('cobertura', $proyecto->cobertura) }}"
                                       maxlength="255">
                                @error('cobertura')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Objeto Contractual -->
                            <div class="col-12 mb-3">
                                <label for="objeto_contractual" class="form-label">Objeto Contractual</label>
                                <textarea class="form-control @error('objeto_contractual') is-invalid @enderror" 
                                        id="objeto_contractual" 
                                        name="objeto_contractual" 
                                        rows="3">{{ old('objeto_contractual', $proyecto->objeto_contractual) }}</textarea>
                                @error('objeto_contractual')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Líneas de Acción -->
                            <div class="col-12 mb-3">
                                <label for="lineas_de_accion" class="form-label">Líneas de Acción</label>
                                <textarea class="form-control @error('lineas_de_accion') is-invalid @enderror" 
                                        id="lineas_de_accion" 
                                        name="lineas_de_accion" 
                                        rows="3">{{ old('lineas_de_accion', $proyecto->lineas_de_accion) }}</textarea>
                                @error('lineas_de_accion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Pestaña de Documentos -->
                    <div class="tab-pane fade" id="docs" role="tabpanel">
                        <div class="row">
                            <!-- Archivo del Proyecto -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">
                                            <i class="fas fa-file-alt text-primary me-2"></i>
                                            Archivo del Proyecto
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <input type="file" 
                                               class="form-control @error('archivo_proyecto') is-invalid @enderror" 
                                               id="archivo_proyecto" 
                                               name="archivo_proyecto"
                                               accept=".pdf,.doc,.docx,.xlsx,.xls">
                                        @error('archivo_proyecto')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            Formatos permitidos: PDF, DOC, DOCX, XLSX, XLS. Tamaño máximo: 10MB.
                                        </div>

                                        @if($proyecto->cargar_archivo_proyecto)
                                            <div class="mt-3">
                                                <h6 class="text-muted">Archivo actual:</h6>
                                                <div class="d-flex align-items-center justify-content-between bg-light p-2 rounded">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-file-alt text-primary me-2"></i>
                                                        <a href="{{ Storage::url($proyecto->cargar_archivo_proyecto) }}" 
                                                           class="text-decoration-none" 
                                                           target="_blank">
                                                            {{ basename($proyecto->cargar_archivo_proyecto) }}
                                                            <i class="fas fa-external-link-alt ms-1 small"></i>
                                                        </a>
                                                    </div>
                                                    <button type="button" 
                                                            class="btn btn-outline-danger btn-sm delete-file-btn"
                                                            data-file-type="archivo"
                                                            data-file-url="{{ route('proyectos.delete.archivo', $proyecto->id) }}">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Contrato o Convenio -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">
                                            <i class="fas fa-file-contract text-success me-2"></i>
                                            Contrato o Convenio
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <input type="file" 
                                               class="form-control @error('archivo_contrato') is-invalid @enderror" 
                                               id="archivo_contrato" 
                                               name="archivo_contrato"
                                               accept=".pdf,.doc,.docx">
                                        @error('archivo_contrato')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            Formatos permitidos: PDF, DOC, DOCX. Tamaño máximo: 10MB.
                                        </div>

                                        @if($proyecto->cargar_contrato_o_convenio)
                                            <div class="mt-3">
                                                <h6 class="text-muted">Contrato actual:</h6>
                                                <div class="d-flex align-items-center justify-content-between bg-light p-2 rounded">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-file-contract text-success me-2"></i>
                                                        <a href="{{ Storage::url($proyecto->cargar_contrato_o_convenio) }}" 
                                                           class="text-decoration-none" 
                                                           target="_blank">
                                                            {{ basename($proyecto->cargar_contrato_o_convenio) }}
                                                            <i class="fas fa-external-link-alt ms-1 small"></i>
                                                        </a>
                                                    </div>
                                                    <button type="button" 
                                                            class="btn btn-outline-danger btn-sm delete-file-btn"
                                                            data-file-type="contrato"
                                                            data-file-url="{{ route('proyectos.delete.contrato', $proyecto->id) }}">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pestaña de Evidencias -->
                    <div class="tab-pane fade" id="evidencias" role="tabpanel">
                        <div class="card">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">
                                        <i class="fas fa-images text-info me-2"></i>
                                        Evidencias del Proyecto
                                    </h5>
                                    <span class="badge bg-info">
                                        {{ $proyecto->cargar_evidencias ? count($proyecto->cargar_evidencias) : 0 }} archivos
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <input type="file" 
                                       class="form-control mb-3 @error('evidencias') is-invalid @enderror" 
                                       id="evidencias" 
                                       name="evidencias[]" 
                                       multiple
                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                @error('evidencias')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text mb-4">
                                    Formatos permitidos: PDF, DOC, DOCX, JPG, JPEG, PNG. Tamaño máximo por archivo: 10MB.
                                </div>

                                @if($proyecto->cargar_evidencias && count($proyecto->cargar_evidencias) > 0)
                                    <div class="row g-3">
                                        @foreach($proyecto->cargar_evidencias as $index => $evidencia)
                                            <div class="col-md-6 col-lg-4 evidencia-item">
                                                <div class="card h-100">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div class="d-flex align-items-center">
                                                                @php
                                                                    $extension = pathinfo($evidencia, PATHINFO_EXTENSION);
                                                                    $icon = match(strtolower($extension)) {
                                                                        'pdf' => 'fa-file-pdf text-danger',
                                                                        'doc', 'docx' => 'fa-file-word text-primary',
                                                                        'jpg', 'jpeg', 'png' => 'fa-file-image text-success',
                                                                        default => 'fa-file text-muted'
                                                                    };
                                                                @endphp
                                                                <i class="fas {{ $icon }} fa-2x me-3"></i>
                                                                <div>
                                                                    <h6 class="mb-1">
                                                                        <a href="{{ Storage::url($evidencia) }}" 
                                                                           class="text-decoration-none" 
                                                                           target="_blank">
                                                                            {{ basename($evidencia) }}
                                                                            <i class="fas fa-external-link-alt ms-1 small"></i>
                                                                        </a>
                                                                    </h6>
                                                                    <small class="text-muted">
                                                                        {{ strtoupper($extension) }}
                                                                    </small>
                                                                </div>
                                                            </div>
                                                            <button type="button" 
                                                                    class="btn btn-outline-danger btn-sm delete-evidencia-btn"
                                                                    data-proyecto-id="{{ $proyecto->id }}"
                                                                    data-index="{{ $index }}"
                                                                    data-url="{{ route('proyectos.delete.evidencia', ['proyecto' => $proyecto->id, 'index' => $index]) }}">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="alert alert-warning mt-4">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Importante:</strong> Al subir nuevas evidencias, se reemplazarán todas las anteriores.
                                    </div>
                                @else
                                    <div class="text-center text-muted py-5">
                                        <i class="fas fa-folder-open fa-3x mb-3"></i>
                                        <h5>No hay evidencias cargadas</h5>
                                        <p class="mb-0">Seleccione los archivos que desea cargar como evidencia del proyecto.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="card mt-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Última actualización: {{ $proyecto->updated_at->format('d/m/Y H:i:s') }}
                            </div>
                            <div>
                                <a href="{{ route('proyectos.index') }}" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-times me-1"></i>
                                    Cancelar
                                </a>
                                                                <button type="submit" class="btn btn-primary" id="submitButton" form="editForm">
                                    <i class="fas fa-save me-2"></i>Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>



@section('scripts')
<script src="{{ asset('js/delete-file.js') }}"></script>
<script>
        function confirmarEnvio(event) {
            const form = event.target;
            
            // Verificar que es un formulario de edición
            if (!form.querySelector('input[name="is_edit"]')) {
                console.error('Error: Falta campo is_edit');
                event.preventDefault();
                return false;
            }

            // Verificar el método PUT
            if (!form.querySelector('input[name="_method"][value="PUT"]')) {
                console.error('Error: Método HTTP incorrecto');
                event.preventDefault();
                return false;
            }

            // Verificar el ID del proyecto
            const proyectoId = form.querySelector('input[name="proyecto_id"]').value;
            const actionUrl = form.getAttribute('action');
            if (!actionUrl.includes(proyectoId)) {
                console.error('Error: ID de proyecto no coincide con la URL');
                event.preventDefault();
                return false;
            }

            return true;
        }

        document.addEventListener('DOMContentLoaded', function() {
        // Inicializar toast de notificación
        const toastElement = document.getElementById('notificationToast');
        const toast = new bootstrap.Toast(toastElement);

        // Función para mostrar notificación
        function showNotification(message, type) {
            const toastBody = toastElement.querySelector('.toast-body');
            toastBody.textContent = message;
            toastElement.classList.remove('bg-success', 'bg-danger');
            toastElement.classList.add(type === 'success' ? 'bg-success' : 'bg-danger');
            toast.show();
        }

        // Manejar eliminación de evidencias
        document.querySelectorAll('.delete-evidencia-btn').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('¿Está seguro de que desea eliminar esta evidencia?')) {
                    const url = this.dataset.url;
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                    const formData = new FormData();
                    formData.append('_method', 'DELETE');
                    formData.append('_token', csrfToken);

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.closest('.evidencia-item').remove();
                            showNotification('Evidencia eliminada exitosamente', 'success');
                            
                            // Actualizar el contador de evidencias
                            const badgeCount = document.querySelector('.badge.bg-info');
                            if (badgeCount) {
                                const currentCount = parseInt(badgeCount.textContent);
                                badgeCount.textContent = `${currentCount - 1} archivos`;
                            }
                        } else {
                            showNotification(data.message || 'Error al eliminar la evidencia', 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Error al eliminar la evidencia. Por favor, intente nuevamente.', 'danger');
                    });
                }
            });
        });

        // Manejar eliminación de contrato
        document.querySelectorAll('.delete-contrato-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (confirm('¿Está seguro de que desea eliminar este contrato?')) {
                    fetch(this.action, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.closest('.contrato-actual').remove();
                            showNotification(data.message, 'success');
                        } else {
                            showNotification(data.message, 'danger');
                        }
                    })
                    .catch(error => {
                        showNotification('Error al eliminar el contrato', 'danger');
                    });
                }
            });
        });

        // La validación y envío del formulario se maneja en edit-form.js
    });
</script>
@endsection