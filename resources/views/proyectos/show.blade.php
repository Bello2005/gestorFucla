@php
    use Illuminate\Support\Str;
@endphp
@extends('layouts.main')

@section('title', Str::limit($proyecto->nombre_del_proyecto, 60) . ' — Proyecto')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="breadcrumb-link">Dashboard</a>
    <i class="fas fa-chevron-right breadcrumb-sep"></i>
    <a href="{{ route('proyectos.index') }}" class="breadcrumb-link">Proyectos</a>
    <i class="fas fa-chevron-right breadcrumb-sep"></i>
    <span class="breadcrumb-current">{{ Str::limit($proyecto->nombre_del_proyecto, 40) }}</span>
@endsection

@section('content')
<div class="proyecto-show">

    {{-- ── Hero ──────────────────────────────────────────────────── --}}
    <div class="proyecto-show__hero">
        <div class="proyecto-show__title-block">
            <div class="proyecto-show__meta-row">
                <span class="proyecto-show__id"># {{ $proyecto->id }}</span>
                <x-estado-badge :estado="$proyecto->estado" />
                @if($proyecto->certificado_cumplimiento)
                    <span class="proyecto-show__cert-chip">
                        <i class="fas fa-shield-halved"></i> Certificado
                    </span>
                @endif
            </div>
            <h1 class="proyecto-show__h1">{{ $proyecto->nombre_del_proyecto }}</h1>
            @if($proyecto->entidad_contratante)
                <p class="proyecto-show__entity">
                    <i class="fas fa-building"></i>
                    {{ $proyecto->entidad_contratante }}
                </p>
            @endif
        </div>
        <div class="proyecto-show__actions">
            <a href="{{ route('proyectos.index') }}" class="ds-btn ds-btn--ghost ds-btn--sm">
                <i class="fas fa-arrow-left"></i>
                <span class="btn-label-desktop">Volver</span>
            </a>
            <div class="export-dropdown dropdown">
                <button class="ds-btn ds-btn--secondary ds-btn--sm dropdown-toggle"
                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-download"></i>
                    <span class="btn-label-desktop">Exportar</span>
                    <i class="fas fa-chevron-down" style="font-size:10px;margin-left:2px;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('proyectos.export.pdf',   ['id' => $proyecto->id]) }}">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('proyectos.export.excel', ['id' => $proyecto->id]) }}">
                            <i class="fas fa-file-excel"></i> Excel
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('proyectos.export.word',  ['id' => $proyecto->id]) }}">
                            <i class="fas fa-file-word"></i> Word
                        </a>
                    </li>
                </ul>
            </div>
            <a href="{{ route('proyectos.edit', $proyecto) }}" class="ds-btn ds-btn--primary ds-btn--sm">
                <i class="fas fa-pen"></i>
                <span class="btn-label-desktop">Editar</span>
            </a>
        </div>
    </div>

    {{-- ── KPI Strip ──────────────────────────────────────────────── --}}
    <div class="stat-cards-grid stat-cards-grid--show">
        <div class="stat-card stat-card--warning stat-card--row">
            <div class="stat-card-icon"><i class="fas fa-dollar-sign"></i></div>
            <div class="stat-card-content">
                <span class="stat-card-label">Valor Total</span>
                <span class="stat-card-value stat-card-value--money">
                    ${{ number_format($proyecto->valor_total ?? 0, 0, ',', '.') }}
                </span>
            </div>
        </div>
        <div class="stat-card stat-card--success stat-card--row">
            <div class="stat-card-icon"><i class="fas fa-circle-check"></i></div>
            <div class="stat-card-content">
                <span class="stat-card-label">Estado</span>
                <span class="stat-card-value stat-card-value--estado">{{ ucfirst($proyecto->estado) }}</span>
            </div>
        </div>
        <div class="stat-card stat-card--info stat-card--row">
            <div class="stat-card-icon"><i class="fas fa-hourglass-half"></i></div>
            <div class="stat-card-content">
                <span class="stat-card-label">Plazo</span>
                <span class="stat-card-value">
                    @if($proyecto->plazo)
                        {{ number_format($proyecto->plazo, 0) }}
                        <span class="stat-card-value--unit">meses</span>
                    @else
                        <span style="color:var(--slate-400);">N/A</span>
                    @endif
                </span>
            </div>
        </div>
        <div class="stat-card stat-card--primary stat-card--row">
            <div class="stat-card-icon"><i class="fas fa-paperclip"></i></div>
            <div class="stat-card-content">
                <span class="stat-card-label">Documentos</span>
                <span class="stat-card-value">
                    {{ $proyecto->cargar_evidencias ? count($proyecto->cargar_evidencias) : 0 }}
                </span>
            </div>
        </div>
    </div>

    {{-- ── Detail Grid ────────────────────────────────────────────── --}}
    <div class="proyecto-show-grid">

        {{-- Información Básica --}}
        <div class="ds-card">
            <div class="ds-card-header">
                <h2 class="ds-card-section-title">
                    <span class="ds-card-section-icon ds-card-section-icon--gold">
                        <i class="fas fa-info-circle"></i>
                    </span>
                    Información General
                </h2>
            </div>
            <div class="ds-card-body" style="padding: 0;">
                <dl class="detail-list">
                    <div class="detail-item">
                        <dt>Nombre del Proyecto</dt>
                        <dd>{{ $proyecto->nombre_del_proyecto }}</dd>
                    </div>
                    <div class="detail-item">
                        <dt>Entidad Contratante</dt>
                        <dd>{{ $proyecto->entidad_contratante ?: '—' }}</dd>
                    </div>
                    <div class="detail-item">
                        <dt>Cobertura</dt>
                        <dd>{{ $proyecto->cobertura ?: '—' }}</dd>
                    </div>
                    <div class="detail-item">
                        <dt>Líneas de Acción</dt>
                        <dd>{{ $proyecto->lineas_de_accion ?: '—' }}</dd>
                    </div>
                    <div class="detail-item detail-item--full">
                        <dt>Objeto Contractual</dt>
                        <dd>{{ $proyecto->objeto_contractual ?: '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Detalles Financieros --}}
        <div class="ds-card">
            <div class="ds-card-header">
                <h2 class="ds-card-section-title">
                    <span class="ds-card-section-icon ds-card-section-icon--success">
                        <i class="fas fa-chart-line"></i>
                    </span>
                    Detalles Financieros
                </h2>
            </div>
            <div class="ds-card-body" style="padding: 0;">
                <dl class="detail-list">
                    <div class="detail-item">
                        <dt>Valor Total</dt>
                        <dd class="detail-value--money">
                            ${{ number_format($proyecto->valor_total ?? 0, 0, ',', '.') }}
                            <small class="detail-value--currency">COP</small>
                        </dd>
                    </div>
                    <div class="detail-item">
                        <dt>Plazo de Ejecución</dt>
                        <dd>
                            @if($proyecto->plazo)
                                {{ number_format($proyecto->plazo, 0) }} meses
                            @else
                                <span class="detail-value--empty">No especificado</span>
                            @endif
                        </dd>
                    </div>
                    <div class="detail-item">
                        <dt>Fecha de Ejecución</dt>
                        <dd>
                            @if($proyecto->fecha_de_ejecucion)
                                {{ $proyecto->fecha_de_ejecucion->format('d M Y') }}
                            @else
                                <span class="detail-value--empty">No especificada</span>
                            @endif
                        </dd>
                    </div>
                    <div class="detail-item">
                        <dt>Creado</dt>
                        <dd>{{ $proyecto->created_at->format('d M Y, H:i') }}</dd>
                    </div>
                    <div class="detail-item">
                        <dt>Última Actualización</dt>
                        <dd>{{ $proyecto->updated_at->format('d M Y, H:i') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    {{-- ── Archivos y Documentos ──────────────────────────────────── --}}
    <div class="ds-card">
        <div class="ds-card-header">
            <h2 class="ds-card-section-title">
                <span class="ds-card-section-icon ds-card-section-icon--info">
                    <i class="fas fa-folder-open"></i>
                </span>
                Archivos y Documentos
            </h2>
        </div>
        <div class="ds-card-body">
            <div class="proyecto-docs-grid">

                {{-- Archivo del Proyecto --}}
                <div class="proyecto-doc-slot">
                    <div class="proyecto-doc-slot__icon proyecto-doc-slot__icon--gold">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="proyecto-doc-slot__body">
                        <span class="proyecto-doc-slot__label">Archivo del Proyecto</span>
                        @if($proyecto->cargar_archivo_proyecto)
                            <a href="{{ Storage::url($proyecto->cargar_archivo_proyecto) }}"
                               target="_blank" class="proyecto-doc-slot__filename">
                                <i class="fas fa-link"></i>
                                {{ Str::limit(basename($proyecto->cargar_archivo_proyecto), 30) }}
                            </a>
                            <a href="{{ Storage::url($proyecto->cargar_archivo_proyecto) }}"
                               target="_blank" class="ds-btn ds-btn--ghost ds-btn--sm proyecto-doc-slot__dl">
                                <i class="fas fa-download"></i> Descargar
                            </a>
                        @else
                            <span class="proyecto-doc-slot__empty">Sin archivo</span>
                        @endif
                    </div>
                </div>

                {{-- Contrato --}}
                <div class="proyecto-doc-slot">
                    <div class="proyecto-doc-slot__icon proyecto-doc-slot__icon--success">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <div class="proyecto-doc-slot__body">
                        <span class="proyecto-doc-slot__label">Contrato o Convenio</span>
                        @if($proyecto->cargar_contrato_o_convenio)
                            <a href="{{ Storage::url($proyecto->cargar_contrato_o_convenio) }}"
                               target="_blank" class="proyecto-doc-slot__filename">
                                <i class="fas fa-link"></i>
                                {{ Str::limit(basename($proyecto->cargar_contrato_o_convenio), 30) }}
                            </a>
                            <a href="{{ Storage::url($proyecto->cargar_contrato_o_convenio) }}"
                               target="_blank" class="ds-btn ds-btn--ghost ds-btn--sm proyecto-doc-slot__dl">
                                <i class="fas fa-download"></i> Descargar
                            </a>
                        @else
                            <span class="proyecto-doc-slot__empty">Sin contrato</span>
                        @endif
                    </div>
                </div>

                {{-- Evidencias --}}
                <div class="proyecto-doc-slot">
                    <div class="proyecto-doc-slot__icon proyecto-doc-slot__icon--warning">
                        <i class="fas fa-images"></i>
                    </div>
                    <div class="proyecto-doc-slot__body">
                        <span class="proyecto-doc-slot__label">Evidencias</span>
                        @php
                            $evidencias = is_array($proyecto->cargar_evidencias)
                                ? array_filter($proyecto->cargar_evidencias, fn($e) => !is_array($e) && !empty($e))
                                : [];
                        @endphp
                        @if(count($evidencias) > 0)
                            <span class="proyecto-doc-slot__count">
                                <i class="fas fa-check-circle" style="color:var(--success);"></i>
                                {{ count($evidencias) }} archivo(s)
                            </span>
                            <div class="proyecto-doc-slot__ev-list">
                                @foreach($evidencias as $i => $ev)
                                    <a href="{{ Storage::url($ev) }}" target="_blank"
                                       class="ds-btn ds-btn--ghost ds-btn--sm" style="margin-bottom:4px;">
                                        <i class="fas fa-download"></i> Evidencia {{ $i + 1 }}
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <span class="proyecto-doc-slot__empty">Sin evidencias</span>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ── Certificado de Cumplimiento ────────────────────────────── --}}
    <div class="ds-card">
        <div class="ds-card-header">
            <h2 class="ds-card-section-title">
                <span class="ds-card-section-icon ds-card-section-icon--gold">
                    <i class="fas fa-certificate"></i>
                </span>
                Certificado de Cumplimiento
            </h2>
            @if($proyecto->certificado_cumplimiento)
                <span class="ds-badge ds-badge--activo">
                    <span class="ds-badge-dot" style="background:var(--success);"></span>
                    Certificado cargado
                </span>
            @endif
        </div>
        <div class="ds-card-body">
            @if($proyecto->certificado_cumplimiento)
                <div class="proyecto-cert">
                    <div class="proyecto-cert__info">
                        <div class="proyecto-cert__file-row">
                            <span class="proyecto-doc-slot__icon proyecto-doc-slot__icon--gold" style="width:40px;height:40px;font-size:16px;">
                                <i class="fas fa-file-pdf"></i>
                            </span>
                            <div>
                                <p style="font-size:var(--text-sm);font-weight:600;color:var(--slate-900);margin:0;">
                                    Certificado de Cumplimiento
                                </p>
                                @if($proyecto->certificado_fecha)
                                    <p style="font-size:var(--text-xs);color:var(--slate-500);margin:2px 0 0;">
                                        Fecha: {{ optional($proyecto->certificado_fecha)->format('d/m/Y') ?? '—' }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        @if($proyecto->certificado_observaciones)
                            <p class="detail-item__obs">{{ $proyecto->certificado_observaciones }}</p>
                        @endif
                    </div>
                    <div class="proyecto-cert__actions">
                        <a href="{{ Storage::url($proyecto->certificado_cumplimiento) }}"
                           target="_blank" class="ds-btn ds-btn--secondary ds-btn--sm">
                            <i class="fas fa-download"></i> Descargar PDF
                        </a>
                        @if(auth()->check() && auth()->user()->roles->pluck('id')->intersect([1,2])->isNotEmpty())
                            <form action="{{ route('proyectos.certificado.destroy', $proyecto) }}"
                                  method="post" onsubmit="return confirm('¿Eliminar certificado?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ds-btn ds-btn--ghost ds-btn--sm"
                                        style="color:var(--danger);">
                                    <i class="fas fa-trash-alt"></i> Eliminar
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @else
                @if(auth()->check() && auth()->user()->roles->pluck('id')->intersect([1,2])->isNotEmpty())
                    <form action="{{ route('proyectos.certificado.store', $proyecto) }}"
                          method="post" enctype="multipart/form-data" class="proyecto-cert-form">
                        @csrf
                        <div class="proyecto-cert-form__fields">
                            <div class="form-group">
                                <label class="ds-label ds-label--required">Archivo PDF</label>
                                <input type="file" name="certificado"
                                       accept="application/pdf" class="ds-input ds-input--file" required>
                            </div>
                            <div class="form-group">
                                <label class="ds-label">Fecha del Certificado</label>
                                <input type="date" name="certificado_fecha" class="ds-input">
                            </div>
                            <div class="form-group proyecto-cert-form__obs">
                                <label class="ds-label">Observaciones</label>
                                <input type="text" name="certificado_observaciones"
                                       class="ds-input" placeholder="Notas opcionales…">
                            </div>
                        </div>
                        <button type="submit" class="ds-btn ds-btn--primary ds-btn--sm" style="margin-top:var(--space-4);">
                            <i class="fas fa-upload"></i> Subir Certificado
                        </button>
                    </form>
                @else
                    <div class="empty-state" style="padding: var(--space-10) var(--space-8);">
                        <div class="empty-state-icon"><i class="fas fa-certificate"></i></div>
                        <p class="empty-state-text">No hay certificado cargado</p>
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- ── Bottom Action Bar ──────────────────────────────────────── --}}
    <div class="proyecto-show__footer">
        <a href="{{ route('proyectos.index') }}" class="ds-btn ds-btn--ghost">
            <i class="fas fa-list"></i> Ver todos los proyectos
        </a>
        <div class="proyecto-show__footer-right">
            <a href="{{ route('proyectos.edit', $proyecto) }}" class="ds-btn ds-btn--primary">
                <i class="fas fa-pen"></i> Editar Proyecto
            </a>
            @if(auth()->check() && auth()->user()->roles->pluck('id')->intersect([1,2])->isNotEmpty())
                <form action="{{ route('proyectos.destroy', $proyecto) }}" method="POST"
                      onsubmit="return confirm('¿Eliminar este proyecto? Esta acción no se puede deshacer.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="ds-btn ds-btn--danger">
                        <i class="fas fa-trash-alt"></i> Eliminar
                    </button>
                </form>
            @endif
        </div>
    </div>

</div>{{-- /proyecto-show --}}
@endsection
