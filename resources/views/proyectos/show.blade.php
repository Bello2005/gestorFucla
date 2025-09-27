@php
    use Illuminate\Support\Str;
@endphp
@extends('layouts.main')

@section('title', 'Detalle del Proyecto')

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
        /* Estilos para el menú desplegable */
        .dropdown-menu {
            display: none;
            position: absolute;
            background-color: #fff;
            min-width: 200px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            z-index: 1000;
            border-radius: 0.375rem;
            border: 1px solid rgba(0, 0, 0, 0.15);
        }
        
        .dropdown-menu.show {
            display: block;
        }
        
        .dropdown-item {
            display: block;
            width: 100%;
            padding: 0.5rem 1rem;
            clear: both;
            font-weight: 400;
            color: #212529;
            text-align: inherit;
            text-decoration: none;
            white-space: nowrap;
            background-color: transparent;
            border: 0;
        }
        
        .dropdown-item:hover, .dropdown-item:focus {
            color: #1e2125;
            background-color: #f8f9fa;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid" style="margin-left: 280px; width: calc(100% - 300px); padding: 2rem;">
    <!-- Encabezado -->
    <div class="row mb-4 align-items-center">
        <div class="col-12 mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('proyectos.index') }}">Proyectos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($proyecto->nombre_del_proyecto, 50) }}</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6">
            <h2 class="mb-0 text-wrap">
                <i class="fas fa-folder-open text-primary me-2"></i>
                {{ $proyecto->nombre_del_proyecto }}
            </h2>
            <p class="text-muted mb-0 mt-2">ID: {{ $proyecto->id }}</p>
        </div>
        <div class="col-md-6 text-end">
            <div class="d-flex justify-content-end gap-2">
                <!-- Botones de exportación -->
                <div class="export-dropdown me-2">
                    <button class="btn btn-success" type="button" id="exportButton">
                        <i class="fas fa-download me-2"></i>Exportar
                        <i class="fas fa-chevron-down ms-2"></i>
                    </button>
                    <div class="dropdown-menu" id="exportMenu">
                        <a class="dropdown-item" href="{{ route('proyectos.export.pdf', ['id' => $proyecto->id]) }}" data-format="pdf">
                            <i class="fas fa-file-pdf text-danger me-2"></i>Exportar como .pdf
                        </a>
                        <a class="dropdown-item" href="{{ route('proyectos.export.excel', ['id' => $proyecto->id]) }}" data-format="excel">
                            <i class="fas fa-file-excel text-success me-2"></i>Exportar como .xlsx
                        </a>
                        <a class="dropdown-item" href="{{ route('proyectos.export.word', ['id' => $proyecto->id]) }}" data-format="word">
                            <i class="fas fa-file-word text-primary me-2"></i>Exportar como .docx
                        </a>
                    </div>
                </div>
                
                <!-- Botones de acción principales -->
                <a href="{{ route('proyectos.edit', $proyecto) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Editar Proyecto
                </a>
                <a href="{{ route('proyectos.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver al listado
                </a>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card info fade-in">
                <div class="stat-header">
                    <h3 class="stat-title">Valor Total</h3>
                    <div class="stat-icon info">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
                <div class="stat-value">${{ number_format($proyecto->valor_total, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card success fade-in">
                <div class="stat-header">
                    <h3 class="stat-title">Estado</h3>
                    <div class="stat-icon success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="stat-value">{{ ucfirst($proyecto->estado) }}</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card warning fade-in">
                <div class="stat-header">
                    <h3 class="stat-title">Plazo</h3>
                    <div class="stat-icon warning">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="stat-value">{{ $proyecto->plazo }} meses</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card primary fade-in">
                <div class="stat-header">
                    <h3 class="stat-title">Documentos</h3>
                    <div class="stat-icon primary">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
                <div class="stat-value">{{ $proyecto->cargar_evidencias ? count($proyecto->cargar_evidencias) : 0 }}</div>
            </div>
        </div>
    </div>

    <div class="content-card fade-in">
        <div class="card-body">
                    <div class="row">
                        <!-- Información Básica -->
                        <div class="col-12 mb-4">
                            <h6 class="text-success border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle"></i> Información Básica
                            </h6>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nombre del Proyecto:</label>
                            <p class="form-control-plaintext">{{ $proyecto->nombre_del_proyecto }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Objeto Contractual:</label>
                            <p class="form-control-plaintext">{{ $proyecto->objeto_contractual ?: 'No especificado' }}</p>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Líneas de Acción:</label>
                            <div class="card bg-light">
                                <div class="card-body">
                                    {{ $proyecto->lineas_de_accion ?: 'No especificadas' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Cobertura:</label>
                            <p class="form-control-plaintext">{{ $proyecto->cobertura ?: 'No especificada' }}</p>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Entidad Contratante:</label>
                            <p class="form-control-plaintext">{{ $proyecto->entidad_contratante ?: 'No especificada' }}</p>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Fecha de Ejecución:</label>
                            <p class="form-control-plaintext">
                                @if($proyecto->fecha_de_ejecucion)
                                    <span class="badge bg-info">{{ $proyecto->fecha_ejecucion_formatted }}</span>
                                @else
                                    No especificada
                                @endif
                            </p>
                        </div>

                        <!-- Información Económica -->
                        <div class="col-12 mb-4 mt-4">
                            <h6 class="text-success border-bottom pb-2 mb-3">
                                <i class="fas fa-dollar-sign"></i> Información Económica
                            </h6>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Plazo:</label>
                            <p class="form-control-plaintext">
                                @if($proyecto->plazo)
                                    <span class="badge bg-warning text-dark">{{ $proyecto->plazo }} meses</span>
                                @else
                                    No especificado
                                @endif
                            </p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Valor Total:</label>
                            <p class="form-control-plaintext">
                                @if($proyecto->valor_total)
                                    <span class="badge bg-success fs-6">${{ $proyecto->valor_total_formatted }}</span>
                                @else
                                    No especificado
                                @endif
                            </p>
                        </div>

                        <!-- Archivos y Documentos -->
                        <div class="col-12 mb-4 mt-4">
                            <h6 class="text-success border-bottom pb-2 mb-3">
                                <i class="fas fa-file-alt"></i> Archivos y Documentos
                            </h6>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Archivo del Proyecto:</label>
                            <div class="mt-2">
                                @if($proyecto->cargar_archivo_proyecto)
                                    <a href="{{ Storage::url($proyecto->cargar_archivo_proyecto) }}" 
                                       target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-download"></i> Descargar Archivo
                                    </a>
                                    <small class="d-block text-muted mt-1">
                                        {{ basename($proyecto->cargar_archivo_proyecto) }}
                                    </small>
                                @else
                                    <span class="text-muted">No hay archivo cargado</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Contrato o Convenio:</label>
                            <div class="mt-2">
                                @if($proyecto->cargar_contrato_o_convenio)
                                    <a href="{{ Storage::url($proyecto->cargar_contrato_o_convenio) }}" 
                                       target="_blank" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-download"></i> Descargar Contrato
                                    </a>
                                    <small class="d-block text-muted mt-1">
                                        {{ basename($proyecto->cargar_contrato_o_convenio) }}
                                    </small>
                                @else
                                    <span class="text-muted">No hay contrato cargado</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Evidencias:</label>
                            <div class="mt-2">
                                @php
                                    $evidencias = is_array($proyecto->cargar_evidencias) ? array_filter($proyecto->cargar_evidencias) : [];
                                    $evidencias = array_filter($evidencias, function($item) {
                                        return !is_array($item) && !empty($item);
                                    });
                                @endphp
                                
                                @if(count($evidencias) > 0)
                                    @foreach($evidencias as $index => $evidencia)
                                        <div class="mb-2">
                                            <a href="{{ Storage::url($evidencia) }}" 
                                               target="_blank" class="btn btn-outline-warning btn-sm">
                                                <i class="fas fa-download"></i> Evidencia {{ $index + 1 }}
                                            </a>
                                            <small class="d-block text-muted">
                                                {{ basename($evidencia) }}
                                            </small>
                                        </div>
                                    @endforeach
                                    <small class="text-success">
                                        <i class="fas fa-check-circle"></i> 
                                        {{ count($evidencias) }} archivo(s) de evidencia
                                    </small>
                                @else
                                    <span class="text-muted">No hay evidencias cargadas</span>
                                @endif
                            </div>
                        </div>

                        <!-- Información del Sistema -->
                        <div class="col-12 mb-4 mt-4">
                            <h6 class="text-success border-bottom pb-2 mb-3">
                                <i class="fas fa-clock"></i> Información del Sistema
                            </h6>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Fecha de Creación:</label>
                            <p class="form-control-plaintext">
                                <i class="fas fa-calendar-plus text-success"></i>
                                {{ $proyecto->created_at->format('d/m/Y H:i:s') }}
                            </p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Última Actualización:</label>
                            <p class="form-control-plaintext">
                                <i class="fas fa-calendar-check text-warning"></i>
                                {{ $proyecto->updated_at->format('d/m/Y H:i:s') }}
                            </p>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <hr>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="{{ route('proyectos.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-list"></i> Ver Todos
                                    </a>
                                </div>
                                <div>
                                    <a href="{{ route('proyectos.edit', $proyecto) }}" class="btn btn-warning me-2">
                                        <i class="fas fa-edit"></i> Editar Proyecto
                                    </a>
                                    <form action="{{ route('proyectos.destroy', $proyecto) }}" method="POST" 
                                          style="display: inline;" 
                                          onsubmit="return confirm('¿Está seguro de eliminar este proyecto? Esta acción no se puede deshacer.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card-header {
        background: linear-gradient(90deg, #28a745, #20c997) !important;
    }
    
    .form-label.fw-bold {
        color: #495057;
        font-size: 0.9rem;
    }
    
    .form-control-plaintext {
        padding-left: 0;
        border-bottom: 1px solid #e9ecef;
        margin-bottom: 0;
    }
    
    .badge {
        font-size: 0.85rem;
    }
    
    .btn-outline-primary:hover,
    .btn-outline-info:hover,
    .btn-outline-warning:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .bg-light {
        background-color: #f8f9fa !important;
    }
    
    h6.text-success {
        font-weight: 600;
    }

    .nav-tabs {
        border-bottom: 2px solid #e9ecef;
    }

    .nav-tabs .nav-link {
        border: none;
        border-bottom: 2px solid transparent;
        color: #6c757d;
        font-weight: 500;
        padding: 0.75rem 1rem;
        margin-bottom: -2px;
    }

    .nav-tabs .nav-link:hover {
        border-color: transparent;
        color: #4361ee;
    }

    .nav-tabs .nav-link.active {
        color: #4361ee;
        border-bottom: 2px solid #4361ee;
        background: transparent;
    }

    .detail-card {
        background: #fff;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        height: 100%;
    }

    .detail-title {
        color: #495057;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .detail-content {
        color: #212529;
        font-size: 1rem;
        margin-bottom: 0;
    }

    .badge {
        padding: 0.5em 1em;
        font-weight: 500;
        border-radius: 6px;
    }

    .badge.activo {
        background: #dcfce7;
        color: #16a34a;
    }

    .badge.inactivo {
        background: #fee2e2;
        color: #dc2626;
    }

    .btn {
        padding: 0.5rem 1rem;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    .stat-card {
        padding: 1.5rem;
        border-radius: 12px;
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        transition: all 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.08);
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .stat-title {
        font-size: 0.875rem;
        color: #6c757d;
        margin: 0;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 600;
        color: #212529;
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 1.25rem;
    }

    .stat-icon.info {
        background: #e3f0ff;
        color: #4361ee;
    }

    .stat-icon.success {
        background: #dcfce7;
        color: #16a34a;
    }

    .stat-icon.warning {
        background: #fff7ed;
        color: #ea580c;
    }

    .stat-icon.primary {
        background: #eff6ff;
        color: #3b82f6;
    }

    .content-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        padding: 1.5rem;
        margin: 0 1rem;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in {
        animation: fadeIn 0.5s ease-out forwards;
    }

    .fade-in:nth-child(2) { animation-delay: 0.1s; }
    .fade-in:nth-child(3) { animation-delay: 0.2s; }
    .fade-in:nth-child(4) { animation-delay: 0.3s; }
</style>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const exportButton = document.getElementById('exportButton');
    const exportMenu = document.getElementById('exportMenu');
    let isMenuOpen = false;

    // Función para mostrar/ocultar el menú
    function toggleMenu() {
        if (isMenuOpen) {
            exportMenu.style.display = 'none';
        } else {
            exportMenu.style.display = 'block';
            // Posicionar el menú debajo del botón
            const buttonRect = exportButton.getBoundingClientRect();
            exportMenu.style.top = (buttonRect.bottom + window.scrollY) + 'px';
            exportMenu.style.left = (buttonRect.left + window.scrollX) + 'px';
        }
        isMenuOpen = !isMenuOpen;
    }

    // Mostrar/ocultar menú al hacer clic en el botón
    exportButton.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleMenu();
    });

    // Cerrar menú al hacer clic fuera
    document.addEventListener('click', function() {
        if (isMenuOpen) {
            toggleMenu();
        }
    });

    // Evitar que el menú se cierre al hacer clic en él
    exportMenu.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    // Manejar los enlaces de exportación
    document.querySelectorAll('.dropdown-item').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            const format = this.getAttribute('data-format');
            
            // Mostrar indicador de carga
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
            
            // Realizar la petición
            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error('Error en la exportación');
                    return response.blob();
                })
                .then(blob => {
                    // Crear un enlace temporal y descargar el archivo
                    const downloadUrl = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = downloadUrl;
                    
                    // Establecer el nombre del archivo según el formato
                    let extension;
                    switch(format) {
                        case 'excel':
                            extension = 'xlsx';
                            break;
                        case 'word':
                            extension = 'docx';
                            break;
                        default:
                            extension = format;
                    }
                    const filename = `proyecto_{{ $proyecto->id }}.${extension}`;
                    a.download = filename;
                    
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(downloadUrl);
                    document.body.removeChild(a);
                    toggleMenu(); // Cerrar el menú después de la descarga
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al generar el archivo. Por favor, intente nuevamente.');
                })
                .finally(() => {
                    // Restaurar el texto original
                    this.innerHTML = originalText;
                });
        });
    });
});
</script>
@endsection