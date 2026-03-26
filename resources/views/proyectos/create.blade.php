@extends('layouts.main')

@section('title', 'Nuevo Proyecto')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="breadcrumb-link">Dashboard</a>
    <i class="fas fa-chevron-right breadcrumb-sep"></i>
    <a href="{{ route('proyectos.index') }}" class="breadcrumb-link">Proyectos</a>
    <i class="fas fa-chevron-right breadcrumb-sep"></i>
    <span class="breadcrumb-current">Nuevo Proyecto</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Nuevo Proyecto</h1>
        <p class="page-subtitle">Completa la informacion para registrar un nuevo proyecto</p>
    </div>
    <div class="page-actions">
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

<form action="{{ route('proyectos.store') }}" method="POST" enctype="multipart/form-data" id="createForm">
    @csrf

    <!-- Informacion General -->
    <div class="ds-card" style="margin-bottom: 24px;">
        <div class="ds-card-header">
            <h3 class="ds-card-title"><i class="fas fa-info-circle" style="color: var(--primary); margin-right: 8px;"></i> Informacion General</h3>
        </div>
        <div class="ds-card-body">
            <div class="ds-form-grid">
                <div class="form-group">
                    <label class="ds-label ds-label--required" for="nombre_del_proyecto">Nombre del Proyecto</label>
                    <input type="text" class="ds-input @error('nombre_del_proyecto') is-invalid @enderror" id="nombre_del_proyecto" name="nombre_del_proyecto" value="{{ old('nombre_del_proyecto') }}" required>
                    @error('nombre_del_proyecto')<p class="ds-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="ds-label" for="objeto_contractual">Objeto Contractual</label>
                    <input type="text" class="ds-input @error('objeto_contractual') is-invalid @enderror" id="objeto_contractual" name="objeto_contractual" value="{{ old('objeto_contractual') }}">
                    @error('objeto_contractual')<p class="ds-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                </div>
            </div>

            <div class="form-group" style="margin-top: 16px;">
                <label class="ds-label" for="lineas_de_accion">Lineas de Accion</label>
                <textarea class="ds-textarea @error('lineas_de_accion') is-invalid @enderror" id="lineas_de_accion" name="lineas_de_accion" rows="3">{{ old('lineas_de_accion') }}</textarea>
                @error('lineas_de_accion')<p class="ds-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-top: 16px;">
                <div class="form-group">
                    <label class="ds-label" for="cobertura">Cobertura</label>
                    <input type="text" class="ds-input @error('cobertura') is-invalid @enderror" id="cobertura" name="cobertura" value="{{ old('cobertura') }}">
                    @error('cobertura')<p class="ds-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="ds-label" for="entidad_contratante">Entidad Contratante</label>
                    <input type="text" class="ds-input @error('entidad_contratante') is-invalid @enderror" id="entidad_contratante" name="entidad_contratante" value="{{ old('entidad_contratante') }}">
                    @error('entidad_contratante')<p class="ds-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="ds-label" for="fecha_de_ejecucion">Fecha de Ejecucion</label>
                    <input type="date" class="ds-input @error('fecha_de_ejecucion') is-invalid @enderror" id="fecha_de_ejecucion" name="fecha_de_ejecucion" value="{{ old('fecha_de_ejecucion') }}">
                    @error('fecha_de_ejecucion')<p class="ds-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                </div>
            </div>

            <div class="ds-form-grid" style="margin-top: 16px;">
                <div class="form-group">
                    <label class="ds-label" for="plazo">Plazo (meses)</label>
                    <input type="number" step="0.01" class="ds-input @error('plazo') is-invalid @enderror" id="plazo" name="plazo" value="{{ old('plazo') }}">
                    @error('plazo')<p class="ds-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="ds-label" for="valor_total">Valor Total ($)</label>
                    <input type="number" step="0.01" class="ds-input @error('valor_total') is-invalid @enderror" id="valor_total" name="valor_total" value="{{ old('valor_total') }}" min="0">
                    @error('valor_total')<p class="ds-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Archivos -->
    <div class="ds-card" style="margin-bottom: 24px;">
        <div class="ds-card-header">
            <h3 class="ds-card-title"><i class="fas fa-file-upload" style="color: var(--primary); margin-right: 8px;"></i> Gestion de Archivos</h3>
        </div>
        <div class="ds-card-body">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
                <div class="form-group">
                    <label class="ds-label" for="archivo_proyecto">Archivo del Proyecto</label>
                    <div class="ds-file-upload">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Seleccionar archivo</span>
                        <small>PDF, DOC, DOCX, XLS, XLSX (Max: 10MB)</small>
                        <input type="file" id="archivo_proyecto" name="archivo_proyecto" accept=".pdf,.doc,.docx,.xlsx,.xls">
                    </div>
                    @error('archivo_proyecto')<p class="ds-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="ds-label" for="archivo_contrato">Contrato o Convenio</label>
                    <div class="ds-file-upload">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Seleccionar archivo</span>
                        <small>PDF, DOC, DOCX (Max: 10MB)</small>
                        <input type="file" id="archivo_contrato" name="archivo_contrato" accept=".pdf,.doc,.docx">
                    </div>
                    @error('archivo_contrato')<p class="ds-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="ds-label" for="evidencias">Evidencias</label>
                    <div class="ds-file-upload">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Seleccionar archivos</span>
                        <small>PDF, DOC, JPG, PNG (Max: 10MB c/u)</small>
                        <input type="file" id="evidencias" name="evidencias[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    </div>
                    @error('evidencias.*')<p class="ds-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="ds-card">
        <div class="ds-card-body" style="display: flex; justify-content: space-between; align-items: center;">
            <a href="{{ route('proyectos.index') }}" class="ds-btn ds-btn--ghost">
                <i class="fas fa-times"></i> Cancelar
            </a>
            <button type="submit" class="ds-btn ds-btn--primary">
                <i class="fas fa-save"></i> Guardar Proyecto
            </button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.getElementById('valor_total').addEventListener('input', function(e) {
        if (e.target.value < 0) e.target.value = 0;
    });

    document.querySelectorAll('.ds-file-upload').forEach(function(zone) {
        var input = zone.querySelector('input[type="file"]');
        var label = zone.querySelector('span');

        input.addEventListener('change', function() {
            if (this.files.length > 0) {
                var names = [];
                for (var i = 0; i < this.files.length; i++) {
                    names.push(this.files[i].name);
                }
                label.textContent = names.join(', ');
                zone.classList.add('has-file');
            }
        });
    });
</script>
@endpush
