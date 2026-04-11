@php
    use Illuminate\Support\Str;
@endphp
@extends('layouts.main')

@section('title', 'Editar Proyecto')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="breadcrumb-link">Dashboard</a>
    <i class="fas fa-chevron-right breadcrumb-sep"></i>
    <a href="{{ route('proyectos.index') }}" class="breadcrumb-link">Proyectos</a>
    <i class="fas fa-chevron-right breadcrumb-sep"></i>
    <a href="{{ route('proyectos.show', $proyecto) }}" class="breadcrumb-link">{{ Str::limit($proyecto->nombre_del_proyecto, 30) }}</a>
    <i class="fas fa-chevron-right breadcrumb-sep"></i>
    <span class="breadcrumb-current">Editar</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Editar Proyecto</h1>
        <p class="page-subtitle">
            <span style="margin-right: 12px;"><i class="fas fa-hashtag" style="margin-right: 4px;"></i>ID: {{ $proyecto->id }}</span>
            <span style="color: var(--slate-500);"><i class="fas fa-clock" style="margin-right: 4px;"></i>Ultima actualizacion: {{ $proyecto->updated_at->format('d/m/Y H:i') }}</span>
        </p>
    </div>
    <div class="page-actions">
        <a href="{{ route('proyectos.show', $proyecto) }}" class="ds-btn ds-btn--secondary">
            <i class="fas fa-eye"></i> Ver Proyecto
        </a>
        <a href="{{ route('proyectos.index') }}" class="ds-btn ds-btn--ghost">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

@if($errors->any())
    <div class="ds-alert ds-alert--danger" style="margin-bottom: 24px;">
        <i class="fas fa-exclamation-triangle"></i>
        <div>
            <strong>Por favor, corrija los siguientes errores:</strong>
            <ul style="margin: 8px 0 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

@if(session('success'))
    <div class="ds-alert ds-alert--success" style="margin-bottom: 24px;">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

<form id="editForm"
      action="{{ route('proyectos.update', ['proyecto' => $proyecto->id]) }}"
      method="POST"
      enctype="multipart/form-data"
      onsubmit="return confirmarEnvio(event)">
    @csrf
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="is_edit" value="1">
    <input type="hidden" name="proyecto_id" value="{{ $proyecto->id }}">
    <input type="hidden" name="form_action" value="update">
    <input type="hidden" name="original_url" value="{{ url()->current() }}">

    <!-- Tabs Navigation -->
    <div class="ds-card" style="margin-bottom: 24px;">
        <div class="edit-tabs">
            <button type="button" class="edit-tab active" data-tab="info">
                <i class="fas fa-info-circle"></i> Informacion General
            </button>
            <button type="button" class="edit-tab" data-tab="docs">
                <i class="fas fa-file-alt"></i> Documentos
            </button>
            <button type="button" class="edit-tab" data-tab="evidencias">
                <i class="fas fa-images"></i> Evidencias
                <span class="edit-tab-badge">{{ $proyecto->cargar_evidencias ? count($proyecto->cargar_evidencias) : 0 }}</span>
            </button>
            <button type="button" class="edit-tab" data-tab="certificado">
                <i class="fas fa-certificate"></i> Certificado
            </button>
        </div>

        <!-- Tab: Info General -->
        <div class="edit-tab-content active" id="tab-info">
            <div class="ds-card-body">
                <div class="ds-form-grid">
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="ds-label ds-label--required" for="nombre_del_proyecto">Nombre del Proyecto</label>
                        <input type="text" class="ds-input @error('nombre_del_proyecto') is-invalid @enderror" id="nombre_del_proyecto" name="nombre_del_proyecto" value="{{ old('nombre_del_proyecto', $proyecto->nombre_del_proyecto) }}" required maxlength="255">
                        @error('nombre_del_proyecto')<p class="ds-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="ds-form-grid" style="margin-top: 16px;">
                    <div class="form-group">
                        <label class="ds-label" for="estado">Estado del Proyecto</label>
                        <select class="ds-select @error('estado') is-invalid @enderror" id="estado" name="estado">
                            <option value="activo" {{ old('estado', $proyecto->estado) == 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="cerrado" {{ old('estado', $proyecto->estado) == 'cerrado' ? 'selected' : '' }}>Cerrado</option>
                            <option value="inactivo" {{ old('estado', $proyecto->estado) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        @error('estado')<p class="ds-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label class="ds-label" for="valor_total">Valor Total ($)</label>
                        <input type="number" class="ds-input @error('valor_total') is-invalid @enderror" id="valor_total" name="valor_total" value="{{ old('valor_total', $proyecto->valor_total) }}" step="0.01" min="0">
                        @error('valor_total')<p class="ds-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-top: 16px;">
                    <div class="form-group">
                        <label class="ds-label" for="plazo">Plazo (meses)</label>
                        <input type="number" class="ds-input @error('plazo') is-invalid @enderror" id="plazo" name="plazo" value="{{ old('plazo', $proyecto->plazo) }}" step="0.01" min="0">
                        @error('plazo')<p class="ds-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label class="ds-label" for="fecha_de_ejecucion">Fecha de Ejecucion</label>
                        <input type="date" class="ds-input @error('fecha_de_ejecucion') is-invalid @enderror" id="fecha_de_ejecucion" name="fecha_de_ejecucion" value="{{ old('fecha_de_ejecucion', $proyecto->fecha_de_ejecucion ? date('Y-m-d', strtotime($proyecto->fecha_de_ejecucion)) : '') }}">
                        @error('fecha_de_ejecucion')<p class="ds-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label class="ds-label" for="cobertura">Cobertura</label>
                        <input type="text" class="ds-input @error('cobertura') is-invalid @enderror" id="cobertura" name="cobertura" value="{{ old('cobertura', $proyecto->cobertura) }}" maxlength="255">
                        @error('cobertura')<p class="ds-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="form-group" style="margin-top: 16px;">
                    <label class="ds-label" for="entidad_contratante">Entidad Contratante</label>
                    <input type="text" class="ds-input @error('entidad_contratante') is-invalid @enderror" id="entidad_contratante" name="entidad_contratante" value="{{ old('entidad_contratante', $proyecto->entidad_contratante) }}" maxlength="255">
                    @error('entidad_contratante')<p class="ds-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                </div>

                <div class="form-group" style="margin-top: 16px;">
                    <label class="ds-label" for="objeto_contractual">Objeto Contractual</label>
                    <textarea class="ds-textarea @error('objeto_contractual') is-invalid @enderror" id="objeto_contractual" name="objeto_contractual" rows="3">{{ old('objeto_contractual', $proyecto->objeto_contractual) }}</textarea>
                    @error('objeto_contractual')<p class="ds-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                </div>

                <div class="form-group" style="margin-top: 16px;">
                    <label class="ds-label" for="lineas_de_accion">Lineas de Accion</label>
                    <textarea class="ds-textarea @error('lineas_de_accion') is-invalid @enderror" id="lineas_de_accion" name="lineas_de_accion" rows="3">{{ old('lineas_de_accion', $proyecto->lineas_de_accion) }}</textarea>
                    @error('lineas_de_accion')<p class="ds-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Tab: Documentos -->
        <div class="edit-tab-content" id="tab-docs">
            <div class="ds-card-body">
                <div class="ds-form-grid">
                    <!-- Archivo del Proyecto -->
                    <div class="doc-upload-card">
                        <div class="doc-upload-header">
                            <i class="fas fa-file-alt" style="color: var(--primary);"></i>
                            <span>Archivo del Proyecto</span>
                        </div>
                        <div class="ds-file-upload">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Seleccionar archivo</span>
                            <small>PDF, DOC, DOCX, XLS, XLSX (Max: 10MB)</small>
                            <input type="file" id="archivo_proyecto" name="archivo_proyecto" accept=".pdf,.doc,.docx,.xlsx,.xls">
                        </div>
                        @error('archivo_proyecto')<p class="ds-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                        @if($proyecto->cargar_archivo_proyecto)
                            <div class="doc-current">
                                <div class="doc-current-info">
                                    <i class="fas fa-file-alt" style="color: var(--primary);"></i>
                                    <a href="{{ Storage::url($proyecto->cargar_archivo_proyecto) }}" target="_blank">{{ basename($proyecto->cargar_archivo_proyecto) }}</a>
                                </div>
                                <button type="button" class="action-btn action-btn--delete delete-file-btn" data-file-type="archivo" data-file-url="{{ route('proyectos.delete.archivo', $proyecto->id) }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Contrato -->
                    <div class="doc-upload-card">
                        <div class="doc-upload-header">
                            <i class="fas fa-file-contract" style="color: var(--success);"></i>
                            <span>Contrato o Convenio</span>
                        </div>
                        <div class="ds-file-upload">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Seleccionar archivo</span>
                            <small>PDF, DOC, DOCX (Max: 10MB)</small>
                            <input type="file" id="archivo_contrato" name="archivo_contrato" accept=".pdf,.doc,.docx">
                        </div>
                        @error('archivo_contrato')<p class="ds-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                        @if($proyecto->cargar_contrato_o_convenio)
                            <div class="doc-current">
                                <div class="doc-current-info">
                                    <i class="fas fa-file-contract" style="color: var(--success);"></i>
                                    <a href="{{ Storage::url($proyecto->cargar_contrato_o_convenio) }}" target="_blank">{{ basename($proyecto->cargar_contrato_o_convenio) }}</a>
                                </div>
                                <button type="button" class="action-btn action-btn--delete delete-file-btn" data-file-type="contrato" data-file-url="{{ route('proyectos.delete.contrato', $proyecto->id) }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Evidencias -->
        <div class="edit-tab-content" id="tab-evidencias">
            <div class="ds-card-body">
                <div class="form-group" style="margin-bottom: 24px;">
                    <label class="ds-label">Agregar nuevas evidencias</label>
                    <div class="ds-file-upload">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Seleccionar archivos</span>
                        <small>PDF, DOC, DOCX, JPG, JPEG, PNG (Max: 10MB c/u)</small>
                        <input type="file" id="evidencias" name="evidencias[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    </div>
                    @error('evidencias')<p class="ds-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                </div>

                @if($proyecto->cargar_evidencias && count($proyecto->cargar_evidencias) > 0)
                    <div class="ds-alert ds-alert--warning" style="margin-bottom: 16px;">
                        <i class="fas fa-exclamation-triangle"></i>
                        Al subir nuevas evidencias, se reemplazaran todas las anteriores.
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px;">
                        @foreach($proyecto->cargar_evidencias as $index => $evidencia)
                            <div class="evidencia-item" style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border: 1px solid var(--slate-200); border-radius: var(--radius-md);">
                                <div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
                                    @php
                                        $extension = pathinfo($evidencia, PATHINFO_EXTENSION);
                                        $iconData = match(strtolower($extension)) {
                                            'pdf' => ['fa-file-pdf', 'var(--danger)'],
                                            'doc', 'docx' => ['fa-file-word', 'var(--primary)'],
                                            'jpg', 'jpeg', 'png' => ['fa-file-image', 'var(--success)'],
                                            default => ['fa-file', 'var(--slate-500)']
                                        };
                                    @endphp
                                    <i class="fas {{ $iconData[0] }}" style="color: {{ $iconData[1] }}; font-size: 18px;"></i>
                                    <div style="min-width: 0;">
                                        <a href="{{ Storage::url($evidencia) }}" target="_blank" style="font-size: var(--text-sm); font-weight: 500; color: var(--slate-700); text-decoration: none; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ basename($evidencia) }}
                                        </a>
                                        <small style="color: var(--slate-400);">{{ strtoupper($extension) }}</small>
                                    </div>
                                </div>
                                <button type="button" class="action-btn action-btn--delete delete-evidencia-btn" data-proyecto-id="{{ $proyecto->id }}" data-index="{{ $index }}" data-url="{{ route('proyectos.delete.evidencia', ['proyecto' => $proyecto->id, 'index' => $index]) }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state" style="padding: 48px 24px;">
                        <div class="empty-state-icon"><i class="fas fa-folder-open"></i></div>
                        <p class="empty-state-text">No hay evidencias cargadas</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="edit-tab-content" id="tab-certificado">
            <div class="ds-card-body">
                <p class="page-subtitle mb-3">Certificado de cumplimiento (PDF). Se guarda de forma independiente al resto del formulario.</p>
                @if($proyecto->certificado_cumplimiento)
                    <p><a href="{{ Storage::url($proyecto->certificado_cumplimiento) }}" target="_blank" class="ds-btn ds-btn--secondary ds-btn--sm"><i class="fas fa-download"></i> Descargar</a></p>
                    <form action="{{ route('proyectos.certificado.destroy', $proyecto) }}" method="post" onsubmit="return confirm('¿Eliminar certificado?');" class="mt-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="ds-btn ds-btn--danger ds-btn--sm">Eliminar certificado</button>
                    </form>
                @else
                    <form action="{{ route('proyectos.certificado.store', $proyecto) }}" method="post" enctype="multipart/form-data" class="row g-2">
                        @csrf
                        <div class="col-12 col-md-4">
                            <label class="ds-label">PDF</label>
                            <input type="file" name="certificado" class="form-control" accept="application/pdf" required>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="ds-label">Fecha</label>
                            <input type="date" name="certificado_fecha" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="ds-label">Observaciones</label>
                            <input type="text" name="certificado_observaciones" class="ds-input">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="ds-btn ds-btn--primary">Subir certificado</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="ds-card" style="margin-top: 24px;">
        <div class="ds-card-body" style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: var(--text-sm); color: var(--slate-500);">
                <i class="fas fa-info-circle" style="margin-right: 4px;"></i>
                Ultima actualizacion: {{ $proyecto->updated_at->format('d/m/Y H:i:s') }}
            </span>
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('proyectos.index') }}" class="ds-btn ds-btn--ghost">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit" class="ds-btn ds-btn--primary" id="submitButton">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('styles')
<style>
    .edit-tabs {
        display: flex;
        border-bottom: 1px solid var(--slate-200);
        padding: 0 24px;
    }
    .edit-tab {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 14px 16px;
        font-size: var(--text-sm);
        font-weight: 500;
        color: var(--slate-500);
        background: none;
        border: none;
        border-bottom: 2px solid transparent;
        cursor: pointer;
        transition: all var(--transition-fast) ease;
    }
    .edit-tab:hover {
        color: var(--slate-700);
    }
    .edit-tab.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
    }
    .edit-tab-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 20px;
        height: 20px;
        border-radius: var(--radius-full);
        background: var(--slate-100);
        color: var(--slate-600);
        font-size: 11px;
        font-weight: 600;
        padding: 0 6px;
    }
    .edit-tab.active .edit-tab-badge {
        background: var(--primary-50);
        color: var(--primary);
    }
    .edit-tab-content {
        display: none;
    }
    .edit-tab-content.active {
        display: block;
    }

    .doc-upload-card {
        padding: 16px;
        border: 1px solid var(--slate-200);
        border-radius: var(--radius-lg);
    }
    .doc-upload-header {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        font-size: var(--text-sm);
        color: var(--slate-700);
        margin-bottom: 12px;
    }
    .doc-current {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 12px;
        background: var(--slate-50);
        border-radius: var(--radius-md);
        margin-top: 12px;
    }
    .doc-current-info {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
    }
    .doc-current-info a {
        font-size: var(--text-sm);
        color: var(--slate-700);
        text-decoration: none;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .doc-current-info a:hover {
        color: var(--primary);
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/delete-file.js') }}"></script>
<script>
    function confirmarEnvio(event) {
        var form = event.target;
        if (!form.querySelector('input[name="is_edit"]')) { event.preventDefault(); return false; }
        if (!form.querySelector('input[name="_method"][value="PUT"]')) { event.preventDefault(); return false; }
        var proyectoId = form.querySelector('input[name="proyecto_id"]').value;
        var actionUrl = form.getAttribute('action');
        if (!actionUrl.includes(proyectoId)) { event.preventDefault(); return false; }
        return true;
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Tab switching
        document.querySelectorAll('.edit-tab').forEach(function(tab) {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.edit-tab').forEach(function(t) { t.classList.remove('active'); });
                document.querySelectorAll('.edit-tab-content').forEach(function(c) { c.classList.remove('active'); });
                this.classList.add('active');
                document.getElementById('tab-' + this.dataset.tab).classList.add('active');
            });
        });

        // File upload labels
        document.querySelectorAll('.ds-file-upload').forEach(function(zone) {
            var input = zone.querySelector('input[type="file"]');
            var label = zone.querySelector('span');
            if (input) {
                input.addEventListener('change', function() {
                    if (this.files.length > 0) {
                        var names = [];
                        for (var i = 0; i < this.files.length; i++) names.push(this.files[i].name);
                        label.textContent = names.join(', ');
                        zone.classList.add('has-file');
                    }
                });
            }
        });

        // Delete evidencias
        document.querySelectorAll('.delete-evidencia-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                if (confirm('¿Esta seguro de que desea eliminar esta evidencia?')) {
                    var url = this.dataset.url;
                    var csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                    var btn = this;
                    var formData = new FormData();
                    formData.append('_method', 'DELETE');
                    formData.append('_token', csrfToken);

                    fetch(url, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: formData
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data.success) {
                            btn.closest('.evidencia-item').remove();
                            if (typeof showToast === 'function') showToast('Evidencia eliminada', 'success');
                        } else {
                            if (typeof showToast === 'function') showToast(data.message || 'Error', 'danger');
                        }
                    })
                    .catch(function() {
                        if (typeof showToast === 'function') showToast('Error al eliminar', 'danger');
                    });
                }
            });
        });
    });
</script>
@endpush
