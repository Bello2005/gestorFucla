@extends('layouts.main')

@section('title', isset($proyecto) ? 'Editar Proyecto' : 'Nuevo Proyecto')

@section('styles')
    <!-- Google Fonts y estilos del index -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/proyectos.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/status-chips.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/table-states.css') }}">
    <style>
        .content-card {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .card-header {
            background: linear-gradient(90deg, #28a745, #20c997) !important;
            color: #fff;
            border-radius: 1rem 1rem 0 0;
            padding: 1.5rem 2rem;
        }
        .form-label { font-weight: 600; color: #495057; }
        .form-control:focus { border-color: #28a745; box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25); }
        .required::after { content: " *"; color: red; }
    </style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="content-card fade-in">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h3 class="mb-0">
                        <i class="fas fa-{{ isset($proyecto) ? 'edit' : 'plus' }}"></i>
                        {{ isset($proyecto) ? 'Editar Proyecto' : 'Nuevo Proyecto' }}
                    </h3>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('proyectos.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ isset($proyecto) ? route('proyectos.update', $proyecto) : route('proyectos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($proyecto))
                    @method('PUT')
                @endif
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nombre_del_proyecto" class="form-label required">Nombre del Proyecto</label>
                        <input type="text" class="form-control @error('nombre_del_proyecto') is-invalid @enderror" id="nombre_del_proyecto" name="nombre_del_proyecto" value="{{ old('nombre_del_proyecto', $proyecto->nombre_del_proyecto ?? '') }}" required>
                        @error('nombre_del_proyecto')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="objeto_contractual" class="form-label">Objeto Contractual</label>
                        <input type="text" class="form-control @error('objeto_contractual') is-invalid @enderror" id="objeto_contractual" name="objeto_contractual" value="{{ old('objeto_contractual', $proyecto->objeto_contractual ?? '') }}">
                        @error('objeto_contractual')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="lineas_de_accion" class="form-label">Líneas de Acción</label>
                        <textarea class="form-control @error('lineas_de_accion') is-invalid @enderror" id="lineas_de_accion" name="lineas_de_accion" rows="3">{{ old('lineas_de_accion', $proyecto->lineas_de_accion ?? '') }}</textarea>
                        @error('lineas_de_accion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="cobertura" class="form-label">Cobertura</label>
                        <input type="text" class="form-control @error('cobertura') is-invalid @enderror" id="cobertura" name="cobertura" value="{{ old('cobertura', $proyecto->cobertura ?? '') }}">
                        @error('cobertura')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="entidad_contratante" class="form-label">Entidad Contratante</label>
                        <input type="text" class="form-control @error('entidad_contratante') is-invalid @enderror" id="entidad_contratante" name="entidad_contratante" value="{{ old('entidad_contratante', $proyecto->entidad_contratante ?? '') }}">
                        @error('entidad_contratante')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="fecha_de_ejecucion" class="form-label">Fecha de Ejecución</label>
                        <input type="date" class="form-control @error('fecha_de_ejecucion') is-invalid @enderror" id="fecha_de_ejecucion" name="fecha_de_ejecucion" value="{{ old('fecha_de_ejecucion', isset($proyecto) && $proyecto->fecha_de_ejecucion ? $proyecto->fecha_de_ejecucion->format('Y-m-d') : '') }}">
                        @error('fecha_de_ejecucion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="plazo" class="form-label">Plazo (meses)</label>
                        <input type="number" step="0.01" class="form-control @error('plazo') is-invalid @enderror" id="plazo" name="plazo" value="{{ old('plazo', $proyecto->plazo ?? '') }}">
                        @error('plazo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="valor_total" class="form-label">Valor Total ($)</label>
                        <input type="number" step="0.01" class="form-control @error('valor_total') is-invalid @enderror" id="valor_total" name="valor_total" value="{{ old('valor_total', $proyecto->valor_total ?? '') }}">
                        @error('valor_total')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-success mb-3">
                            <i class="fas fa-file-upload"></i> Gestión de Archivos
                        </h6>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="archivo_proyecto" class="form-label">Cargar Archivo (Proyecto)</label>
                        <input type="file" class="form-control @error('archivo_proyecto') is-invalid @enderror" id="archivo_proyecto" name="archivo_proyecto" accept=".pdf,.doc,.docx,.xlsx,.xls">
                        <small class="form-text text-muted">Formatos: PDF, DOC, DOCX, XLS, XLSX (Max: 10MB)</small>
                        @if(isset($proyecto) && $proyecto->cargar_archivo_proyecto)
                            <div class="mt-2">
                                <a href="{{ Storage::url($proyecto->cargar_archivo_proyecto) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i> Ver archivo actual
                                </a>
                            </div>
                        @endif
                        @error('archivo_proyecto')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="archivo_contrato" class="form-label">Cargar Contrato o Convenio</label>
                        <input type="file" class="form-control @error('archivo_contrato') is-invalid @enderror" id="archivo_contrato" name="archivo_contrato" accept=".pdf,.doc,.docx">
                        <small class="form-text text-muted">Formatos: PDF, DOC, DOCX (Max: 10MB)</small>
                        @if(isset($proyecto) && $proyecto->cargar_contrato_o_convenio)
                            <div class="mt-2">
                                <a href="{{ Storage::url($proyecto->cargar_contrato_o_convenio) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i> Ver contrato actual
                                </a>
                            </div>
                        @endif
                        @error('archivo_contrato')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="evidencias" class="form-label">Cargar Evidencias</label>
                        <input type="file" class="form-control @error('evidencias.*') is-invalid @enderror" id="evidencias" name="evidencias[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <small class="form-text text-muted">Múltiples archivos: PDF, DOC, DOCX, JPG, PNG (Max: 10MB c/u)</small>
                        @if(isset($proyecto) && $proyecto->cargar_evidencias)
                            <div class="mt-2">
                                @foreach($proyecto->cargar_evidencias as $index => $evidencia)
                                    <a href="{{ Storage::url($evidencia) }}" target="_blank" class="btn btn-sm btn-outline-primary me-1 mb-1">
                                        <i class="fas fa-download"></i> Evidencia {{ $index + 1 }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                        @error('evidencias.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('proyectos.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> {{ isset($proyecto) ? 'Actualizar' : 'Guardar' }} Proyecto
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .required::after {
        content: " *";
        color: red;
    }
    
    .card-header {
        background: linear-gradient(90deg, #28a745, #20c997) !important;
    }
    
    .form-label {
        font-weight: 600;
        color: #495057;
    }
    
    .form-control:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
    
    .btn-outline-primary:hover {
        background-color: #007bff;
        border-color: #007bff;
    }
</style>

@endsection

@section('scripts')
<script>
    // Preview de archivos seleccionados
    document.getElementById('evidencias').addEventListener('change', function(e) {
        const files = e.target.files;
        const fileList = document.getElementById('file-list');
        
        if (files.length > 0) {
            let fileNames = [];
            for (let i = 0; i < files.length; i++) {
                fileNames.push(files[i].name);
            }
            console.log('Archivos seleccionados:', fileNames.join(', '));
        }
    });

    // Validación del formulario
    document.querySelector('form').addEventListener('submit', function(e) {
        const nombreProyecto = document.getElementById('nombre_del_proyecto').value.trim();
        
        if (nombreProyecto === '') {
            e.preventDefault();
            alert('El nombre del proyecto es obligatorio');
            document.getElementById('nombre_del_proyecto').focus();
            return false;
        }
        
        return true;
    });

    // Formateo del valor total
    document.getElementById('valor_total').addEventListener('input', function(e) {
        let value = e.target.value;
        if (value < 0) {
            e.target.value = 0;
        }
    });
</script>
@endsection