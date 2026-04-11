@php
    use Illuminate\Support\Str;
@endphp
@extends('layouts.main')

@section('title', 'Detalle del Proyecto')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="breadcrumb-link">Dashboard</a>
    <i class="fas fa-chevron-right breadcrumb-sep"></i>
    <a href="{{ route('proyectos.index') }}" class="breadcrumb-link">Proyectos</a>
    <i class="fas fa-chevron-right breadcrumb-sep"></i>
    <span class="breadcrumb-current">{{ Str::limit($proyecto->nombre_del_proyecto, 40) }}</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $proyecto->nombre_del_proyecto }}</h1>
        <p class="page-subtitle">
            <span style="margin-right: 16px;"><i class="fas fa-hashtag" style="margin-right: 4px;"></i>ID: {{ $proyecto->id }}</span>
            <span class="ds-badge ds-badge--{{ $proyecto->estado }}">
                <span class="ds-badge-dot"></span>
                {{ ucfirst($proyecto->estado) }}
            </span>
        </p>
    </div>
    <div class="page-actions">
        <div class="export-dropdown dropdown">
            <button class="ds-btn ds-btn--secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-download"></i> Exportar
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('proyectos.export.pdf', ['id' => $proyecto->id]) }}"><i class="fas fa-file-pdf" style="color: var(--danger); margin-right: 8px;"></i>PDF</a></li>
                <li><a class="dropdown-item" href="{{ route('proyectos.export.excel', ['id' => $proyecto->id]) }}"><i class="fas fa-file-excel" style="color: var(--success); margin-right: 8px;"></i>Excel</a></li>
                <li><a class="dropdown-item" href="{{ route('proyectos.export.word', ['id' => $proyecto->id]) }}"><i class="fas fa-file-word" style="color: var(--primary); margin-right: 8px;"></i>Word</a></li>
            </ul>
        </div>
        <a href="{{ route('proyectos.edit', $proyecto) }}" class="ds-btn ds-btn--primary">
            <i class="fas fa-edit"></i> Editar
        </a>
        <a href="{{ route('proyectos.index') }}" class="ds-btn ds-btn--ghost">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="stat-cards-grid">
    <div class="stat-card stat-card--warning">
        <div class="stat-card-icon"><i class="fas fa-dollar-sign"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Valor Total</span>
            <span class="stat-card-value">${{ number_format($proyecto->valor_total ?? 0, 0, ',', '.') }}</span>
        </div>
    </div>
    <div class="stat-card stat-card--success">
        <div class="stat-card-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Estado</span>
            <span class="stat-card-value">{{ ucfirst($proyecto->estado) }}</span>
        </div>
    </div>
    <div class="stat-card stat-card--info">
        <div class="stat-card-icon"><i class="fas fa-clock"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Plazo</span>
            <span class="stat-card-value">{{ $proyecto->plazo ?? 'N/A' }} meses</span>
        </div>
    </div>
    <div class="stat-card stat-card--primary">
        <div class="stat-card-icon"><i class="fas fa-file-alt"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Documentos</span>
            <span class="stat-card-value">{{ $proyecto->cargar_evidencias ? count($proyecto->cargar_evidencias) : 0 }}</span>
        </div>
    </div>
</div>

<!-- Project Details -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-top: 24px;">
    <!-- Informacion Basica -->
    <div class="ds-card">
        <div class="ds-card-header">
            <h3 class="ds-card-title"><i class="fas fa-info-circle" style="color: var(--primary); margin-right: 8px;"></i> Informacion Basica</h3>
        </div>
        <div class="ds-card-body">
            <div class="detail-field">
                <span class="detail-field-label">Nombre del Proyecto</span>
                <span class="detail-field-value">{{ $proyecto->nombre_del_proyecto }}</span>
            </div>
            <div class="detail-field">
                <span class="detail-field-label">Objeto Contractual</span>
                <span class="detail-field-value">{{ $proyecto->objeto_contractual ?: 'No especificado' }}</span>
            </div>
            <div class="detail-field">
                <span class="detail-field-label">Lineas de Accion</span>
                <span class="detail-field-value">{{ $proyecto->lineas_de_accion ?: 'No especificadas' }}</span>
            </div>
            <div class="detail-field">
                <span class="detail-field-label">Cobertura</span>
                <span class="detail-field-value">{{ $proyecto->cobertura ?: 'No especificada' }}</span>
            </div>
            <div class="detail-field">
                <span class="detail-field-label">Entidad Contratante</span>
                <span class="detail-field-value">{{ $proyecto->entidad_contratante ?: 'No especificada' }}</span>
            </div>
        </div>
    </div>

    <!-- Informacion Economica y Temporal -->
    <div class="ds-card">
        <div class="ds-card-header">
            <h3 class="ds-card-title"><i class="fas fa-chart-line" style="color: var(--primary); margin-right: 8px;"></i> Detalles Financieros</h3>
        </div>
        <div class="ds-card-body">
            <div class="detail-field">
                <span class="detail-field-label">Valor Total</span>
                <span class="detail-field-value" style="font-family: var(--font-mono); font-weight: 600; color: var(--success);">
                    ${{ number_format($proyecto->valor_total ?? 0, 0, ',', '.') }}
                </span>
            </div>
            <div class="detail-field">
                <span class="detail-field-label">Plazo</span>
                <span class="detail-field-value">
                    @if($proyecto->plazo)
                        {{ $proyecto->plazo }} meses
                    @else
                        No especificado
                    @endif
                </span>
            </div>
            <div class="detail-field">
                <span class="detail-field-label">Fecha de Ejecucion</span>
                <span class="detail-field-value">
                    @if($proyecto->fecha_de_ejecucion)
                        {{ $proyecto->fecha_de_ejecucion->format('d/m/Y') }}
                    @else
                        No especificada
                    @endif
                </span>
            </div>
            <div class="detail-field">
                <span class="detail-field-label">Fecha de Creacion</span>
                <span class="detail-field-value">{{ $proyecto->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="detail-field">
                <span class="detail-field-label">Ultima Actualizacion</span>
                <span class="detail-field-value">{{ $proyecto->updated_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Archivos y Documentos -->
<div class="ds-card" style="margin-top: 24px;">
    <div class="ds-card-header">
        <h3 class="ds-card-title"><i class="fas fa-paperclip" style="color: var(--primary); margin-right: 8px;"></i> Archivos y Documentos</h3>
    </div>
    <div class="ds-card-body">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
            <!-- Archivo del Proyecto -->
            <div class="doc-card">
                <div class="doc-card-icon" style="color: var(--primary); background: var(--primary-50);">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="doc-card-info">
                    <span class="doc-card-label">Archivo del Proyecto</span>
                    @if($proyecto->cargar_archivo_proyecto)
                        <a href="{{ Storage::url($proyecto->cargar_archivo_proyecto) }}" target="_blank" class="ds-btn ds-btn--ghost ds-btn--sm" style="margin-top: 8px;">
                            <i class="fas fa-download"></i> Descargar
                        </a>
                        <small style="color: var(--slate-500); display: block; margin-top: 4px;">{{ basename($proyecto->cargar_archivo_proyecto) }}</small>
                    @else
                        <span style="font-size: var(--text-sm); color: var(--slate-400);">No hay archivo cargado</span>
                    @endif
                </div>
            </div>

            <!-- Contrato -->
            <div class="doc-card">
                <div class="doc-card-icon" style="color: var(--success); background: var(--success-50);">
                    <i class="fas fa-file-contract"></i>
                </div>
                <div class="doc-card-info">
                    <span class="doc-card-label">Contrato o Convenio</span>
                    @if($proyecto->cargar_contrato_o_convenio)
                        <a href="{{ Storage::url($proyecto->cargar_contrato_o_convenio) }}" target="_blank" class="ds-btn ds-btn--ghost ds-btn--sm" style="margin-top: 8px;">
                            <i class="fas fa-download"></i> Descargar
                        </a>
                        <small style="color: var(--slate-500); display: block; margin-top: 4px;">{{ basename($proyecto->cargar_contrato_o_convenio) }}</small>
                    @else
                        <span style="font-size: var(--text-sm); color: var(--slate-400);">No hay contrato cargado</span>
                    @endif
                </div>
            </div>

            <!-- Evidencias -->
            <div class="doc-card">
                <div class="doc-card-icon" style="color: var(--warning); background: var(--warning-50);">
                    <i class="fas fa-images"></i>
                </div>
                <div class="doc-card-info">
                    <span class="doc-card-label">Evidencias</span>
                    @php
                        $evidencias = is_array($proyecto->cargar_evidencias) ? array_filter($proyecto->cargar_evidencias, function($item) {
                            return !is_array($item) && !empty($item);
                        }) : [];
                    @endphp
                    @if(count($evidencias) > 0)
                        <div style="margin-top: 8px;">
                            @foreach($evidencias as $index => $evidencia)
                                <a href="{{ Storage::url($evidencia) }}" target="_blank" class="ds-btn ds-btn--ghost ds-btn--sm" style="margin-bottom: 4px;">
                                    <i class="fas fa-download"></i> Evidencia {{ $index + 1 }}
                                </a>
                            @endforeach
                        </div>
                        <small style="color: var(--success); margin-top: 4px; display: block;">
                            <i class="fas fa-check-circle"></i> {{ count($evidencias) }} archivo(s)
                        </small>
                    @else
                        <span style="font-size: var(--text-sm); color: var(--slate-400);">No hay evidencias cargadas</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Certificado de cumplimiento -->
<div class="ds-card" style="margin-top: 24px;">
    <h3 class="ds-card-title"><i class="fas fa-certificate" style="color: var(--uni-gold); margin-right: 8px;"></i> Certificado de cumplimiento</h3>
    @if($proyecto->certificado_cumplimiento)
        <p class="detail-field-value mb-2">
            <a href="{{ Storage::url($proyecto->certificado_cumplimiento) }}" target="_blank" class="ds-btn ds-btn--secondary ds-btn--sm"><i class="fas fa-download"></i> Descargar PDF</a>
        </p>
        <p><span class="detail-field-label">Fecha del certificado</span> {{ optional($proyecto->certificado_fecha)->format('d/m/Y') ?? '—' }}</p>
        @if($proyecto->certificado_observaciones)
            <p><span class="detail-field-label">Observaciones</span> {{ $proyecto->certificado_observaciones }}</p>
        @endif
        <form action="{{ route('proyectos.certificado.destroy', $proyecto) }}" method="post" class="mt-3" onsubmit="return confirm('¿Eliminar certificado?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="ds-btn ds-btn--danger ds-btn--sm">Eliminar certificado</button>
        </form>
    @else
        <form action="{{ route('proyectos.certificado.store', $proyecto) }}" method="post" enctype="multipart/form-data" class="row g-2">
            @csrf
            <div class="col-12 col-md-4">
                <label class="ds-label">Archivo PDF</label>
                <input type="file" name="certificado" accept="application/pdf" class="form-control" required>
            </div>
            <div class="col-12 col-md-3">
                <label class="ds-label">Fecha</label>
                <input type="date" name="certificado_fecha" class="form-control">
            </div>
            <div class="col-12 col-md-5">
                <label class="ds-label">Observaciones</label>
                <input type="text" name="certificado_observaciones" class="form-control">
            </div>
            <div class="col-12">
                <button type="submit" class="ds-btn ds-btn--primary">Guardar certificado</button>
            </div>
        </form>
    @endif
</div>

<!-- Bottom Actions -->
<div class="ds-card" style="margin-top: 24px;">
    <div class="ds-card-body" style="display: flex; justify-content: space-between; align-items: center;">
        <a href="{{ route('proyectos.index') }}" class="ds-btn ds-btn--ghost">
            <i class="fas fa-list"></i> Ver Todos
        </a>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('proyectos.edit', $proyecto) }}" class="ds-btn ds-btn--primary">
                <i class="fas fa-edit"></i> Editar Proyecto
            </a>
            <form action="{{ route('proyectos.destroy', $proyecto) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Esta seguro de eliminar este proyecto? Esta accion no se puede deshacer.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="ds-btn ds-btn--danger">
                    <i class="fas fa-trash-alt"></i> Eliminar
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .detail-field {
        display: flex;
        flex-direction: column;
        padding: 12px 0;
        border-bottom: 1px solid var(--slate-100);
    }
    .detail-field:last-child {
        border-bottom: none;
    }
    .detail-field-label {
        font-size: var(--text-xs);
        font-weight: 600;
        color: var(--slate-500);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .detail-field-value {
        font-size: var(--text-sm);
        color: var(--slate-900);
        line-height: 1.5;
    }

    .doc-card {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        padding: 16px;
        border: 1px solid var(--slate-200);
        border-radius: var(--radius-lg);
    }
    .doc-card-icon {
        width: 44px;
        height: 44px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .doc-card-info {
        flex: 1;
        min-width: 0;
    }
    .doc-card-label {
        font-size: var(--text-sm);
        font-weight: 600;
        color: var(--slate-700);
        display: block;
    }

    @media (max-width: 768px) {
        .page-header .page-actions {
            flex-wrap: wrap;
        }
        div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
        div[style*="grid-template-columns: repeat(3"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endpush
