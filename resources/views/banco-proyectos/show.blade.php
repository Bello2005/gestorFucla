@php use Illuminate\Support\Facades\Storage; @endphp
@extends('layouts.main')

@section('title', $bancoProyecto->codigo . ' — ' . \Illuminate\Support\Str::limit($bancoProyecto->titulo, 60))

@section('breadcrumbs')
    <a href="{{ route('banco.index') }}">Banco de Proyectos</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">{{ $bancoProyecto->codigo }}</span>
@endsection

@section('content')

{{-- ── Hero header ──────────────────────────────────────────────────── --}}
<div class="page-header" style="align-items:flex-start;gap:var(--space-4);">
    <div style="flex:1;min-width:0;">
        <p class="uc-hero-code" style="margin-bottom:var(--space-1);">{{ $bancoProyecto->codigo }}</p>
        <h1 class="page-title" style="margin-bottom:var(--space-2);">{{ $bancoProyecto->titulo }}</h1>
        <div style="display:flex;flex-wrap:wrap;gap:var(--space-2);align-items:center;">
            <span class="ds-badge uc-estado--{{ $bancoProyecto->estado }}"
                  aria-label="Estado: {{ ucwords(str_replace('_', ' ', $bancoProyecto->estado)) }}">
                {{ ucwords(str_replace('_', ' ', $bancoProyecto->estado)) }}
            </span>
            @if($bancoProyecto->area_facultad)
                <span class="banco-faculty-badge">{{ $bancoProyecto->area_facultad }}</span>
            @endif
            @if($bancoProyecto->tipo_proyecto)
                <span class="banco-faculty-badge">{{ $bancoProyecto->tipo_proyecto }}</span>
            @endif
            @if($bancoProyecto->duracion_meses)
                <span class="cell-muted" style="font-size:var(--text-sm);">
                    <i class="fas fa-clock" aria-hidden="true" style="margin-right:4px;"></i>
                    {{ $bancoProyecto->duracion_meses }} meses
                </span>
            @endif
            @if($bancoProyecto->presupuesto_estimado)
                <span class="cell-muted" style="font-size:var(--text-sm);">
                    <i class="fas fa-dollar-sign" aria-hidden="true" style="margin-right:4px;"></i>
                    ${{ number_format($bancoProyecto->presupuesto_estimado, 0, ',', '.') }}
                </span>
            @endif
        </div>
    </div>
    <div class="page-actions" style="flex-shrink:0;gap:var(--space-2);">
        <form action="{{ route('banco.estado', $bancoProyecto) }}" method="POST" id="estadoForm" style="display:inline;">
            @csrf @method('PATCH')
            <select name="estado" class="ds-input" style="padding-block:6px;font-size:var(--text-sm);cursor:pointer;"
                    onchange="document.getElementById('estadoForm').submit()"
                    aria-label="Cambiar estado">
                @foreach(['borrador','en_evaluacion','aprobado','rechazado','en_ejecucion','cerrado','suspendido'] as $st)
                    <option value="{{ $st }}" @selected($bancoProyecto->estado === $st)>
                        {{ ucwords(str_replace('_', ' ', $st)) }}
                    </option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('banco.edit', $bancoProyecto) }}" class="ds-btn ds-btn--primary">
            <i class="fas fa-pencil" aria-hidden="true"></i> Editar
        </a>
    </div>
</div>

{{-- ── Tabs ──────────────────────────────────────────────────────────── --}}
<div class="uc-tabs" role="tablist" aria-label="Secciones del proyecto">
    <button type="button" class="uc-tabs__btn uc-tabs__btn--active"
            data-tab="ficha" role="tab" aria-selected="true" aria-controls="panel-ficha">
        <i class="fas fa-file-alt" aria-hidden="true"></i> Ficha técnica
    </button>
    <button type="button" class="uc-tabs__btn"
            data-tab="anexos" role="tab" aria-selected="false" aria-controls="panel-anexos">
        <i class="fas fa-paperclip" aria-hidden="true"></i> Documentos
        @php $numAnexos = $bancoProyecto->anexos->where('is_current', true)->count(); @endphp
        @if($numAnexos > 0)
            <span class="chip-count" style="margin-left:4px;">({{ $numAnexos }})</span>
        @endif
    </button>
    <button type="button" class="uc-tabs__btn"
            data-tab="cert" role="tab" aria-selected="false" aria-controls="panel-cert">
        <i class="fas fa-shield-halved" aria-hidden="true"></i> Certificado
        @if($bancoProyecto->certificado_cumplimiento)
            <i class="fas fa-circle-check" style="color:var(--success);font-size:10px;margin-left:4px;" aria-hidden="true"></i>
        @endif
    </button>
    <button type="button" class="uc-tabs__btn"
            data-tab="hist" role="tab" aria-selected="false" aria-controls="panel-hist">
        <i class="fas fa-clock-rotate-left" aria-hidden="true"></i> Historial
    </button>
</div>

{{-- ══════════════════════════════════════════════════════════════════════
     Tab 1 — Ficha Técnica
     ══════════════════════════════════════════════════════════════════════ --}}
<div id="panel-ficha" class="tab-panel" role="tabpanel" aria-labelledby="tab-ficha">

    {{-- Identificación --}}
    <div class="ds-card" style="margin-bottom:var(--space-4);">
        <div class="ds-card-header">
            <h3 class="ds-card-title">Identificación</h3>
        </div>
        <div class="ds-card-body">
            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-item-label">Código</span>
                    <span class="detail-item-value cell-mono">{{ $bancoProyecto->codigo }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-item-label">Estado</span>
                    <span class="detail-item-value">
                        <span class="ds-badge uc-estado--{{ $bancoProyecto->estado }}">
                            {{ ucwords(str_replace('_', ' ', $bancoProyecto->estado)) }}
                        </span>
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-item-label">Tipo de proyecto</span>
                    <span class="detail-item-value">{{ $bancoProyecto->tipo_proyecto ?: '—' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-item-label">Línea de investigación</span>
                    <span class="detail-item-value">{{ $bancoProyecto->linea_investigacion ?: '—' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-item-label">Área / Facultad</span>
                    <span class="detail-item-value">{{ $bancoProyecto->area_facultad ?: '—' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-item-label">Convocatoria</span>
                    <span class="detail-item-value">{{ $bancoProyecto->convocatoria ?: '—' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-item-label">Duración</span>
                    <span class="detail-item-value">
                        {{ $bancoProyecto->duracion_meses ? $bancoProyecto->duracion_meses . ' meses' : '—' }}
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-item-label">Presupuesto estimado</span>
                    <span class="detail-item-value cell-mono">
                        {{ $bancoProyecto->presupuesto_estimado
                            ? '$' . number_format($bancoProyecto->presupuesto_estimado, 0, ',', '.')
                            : '—' }}
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-item-label">Cofinanciación</span>
                    <span class="detail-item-value cell-mono">
                        {{ $bancoProyecto->cofinanciacion
                            ? '$' . number_format($bancoProyecto->cofinanciacion, 0, ',', '.')
                            : '—' }}
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-item-label">Fuente de financiación</span>
                    <span class="detail-item-value">{{ $bancoProyecto->fuente_financiacion ?: '—' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-item-label">Cobertura geográfica</span>
                    <span class="detail-item-value">{{ $bancoProyecto->cobertura_geografica ?: '—' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-item-label">Población objetivo</span>
                    <span class="detail-item-value">{{ $bancoProyecto->poblacion_objetivo ?: '—' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-item-label">Fecha de registro</span>
                    <span class="detail-item-value cell-mono">
                        {{ $bancoProyecto->fecha_registro?->format('d/m/Y') ?? '—' }}
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-item-label">Última actualización</span>
                    <span class="detail-item-value cell-mono">
                        {{ $bancoProyecto->updated_at?->format('d/m/Y H:i') ?? '—' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Formulación --}}
    <div class="ds-card" style="margin-bottom:var(--space-4);">
        <div class="ds-card-header">
            <h3 class="ds-card-title">Formulación</h3>
        </div>
        <div class="ds-card-body" style="display:flex;flex-direction:column;gap:var(--space-4);">

            @foreach([
                ['campo' => 'resumen_ejecutivo',  'label' => 'Resumen ejecutivo'],
                ['campo' => 'problema_necesidad', 'label' => 'Problema / Necesidad'],
                ['campo' => 'objetivo_general',   'label' => 'Objetivo general'],
                ['campo' => 'justificacion',       'label' => 'Justificación'],
                ['campo' => 'alcance',             'label' => 'Alcance'],
            ] as $bloque)
                <div class="detail-item" style="flex-direction:column;gap:var(--space-1);">
                    <span class="detail-item-label">{{ $bloque['label'] }}</span>
                    @if($bancoProyecto->{$bloque['campo']})
                        @php $texto = $bancoProyecto->{$bloque['campo']}; $largo = strlen($texto) > 400; @endphp
                        <div class="uc-expandable {{ $largo ? 'uc-expandable--collapsed' : '' }}">
                            <div class="uc-expandable__inner">
                                <p style="color:var(--neutral-800);font-size:var(--text-sm);line-height:var(--leading-relaxed);margin:0;">
                                    {{ $texto }}
                                </p>
                            </div>
                            @if($largo)
                                <button type="button" class="uc-expandable__toggle">Ver más</button>
                            @endif
                        </div>
                    @else
                        <span class="cell-muted">—</span>
                    @endif
                </div>
            @endforeach

        </div>
    </div>

    {{-- Equipo --}}
    <div class="ds-card">
        <div class="ds-card-header">
            <h3 class="ds-card-title">Equipo y responsables</h3>
        </div>
        <div class="ds-card-body">
            <div class="detail-grid">
                <div class="detail-item" style="flex-direction:column;gap:var(--space-1);">
                    <span class="detail-item-label">Autores / Investigadores</span>
                    @if(!empty($bancoProyecto->autores))
                        <ul style="margin:0;padding-left:var(--space-4);display:flex;flex-direction:column;gap:4px;">
                            @foreach($bancoProyecto->autores as $autor)
                                <li style="font-size:var(--text-sm);color:var(--neutral-800);">{{ $autor }}</li>
                            @endforeach
                        </ul>
                    @else
                        <span class="cell-muted">—</span>
                    @endif
                </div>
                <div class="detail-item">
                    <span class="detail-item-label">Tutor / Director</span>
                    <span class="detail-item-value">{{ $bancoProyecto->tutor_director ?: '—' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-item-label">Programa / Departamento</span>
                    <span class="detail-item-value">{{ $bancoProyecto->programa_departamento ?: '—' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-item-label">Entidad aliada</span>
                    <span class="detail-item-value">{{ $bancoProyecto->entidad_aliada ?: '—' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-item-label">Evaluador asignado</span>
                    <span class="detail-item-value">{{ $bancoProyecto->evaluador_asignado ?: '—' }}</span>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════════════
     Tab 2 — Documentos
     ══════════════════════════════════════════════════════════════════════ --}}
<div id="panel-anexos" class="tab-panel tab-panel--hidden" role="tabpanel" aria-labelledby="tab-anexos">

    <div class="ds-card" style="margin-bottom:var(--space-4);">
        <div class="ds-card-header">
            <h3 class="ds-card-title">Archivos adjuntos</h3>
        </div>
        <div class="ds-card-body">
            @php
                $currentAnexos = $bancoProyecto->anexos->where('is_current', true)->sortByDesc('uploaded_at');
                $allAnexos     = $bancoProyecto->anexos->groupBy('tipo_anexo');
            @endphp

            @if($currentAnexos->count() > 0)
                <ul class="uc-file-list">
                    @foreach($currentAnexos as $ax)
                        @php
                            $ext = strtolower(pathinfo($ax->nombre_original, PATHINFO_EXTENSION));
                            $iconClass = match(true) {
                                in_array($ext, ['pdf'])           => 'uc-file-icon--pdf',
                                in_array($ext, ['doc','docx'])    => 'uc-file-icon--doc',
                                in_array($ext, ['xls','xlsx'])    => 'uc-file-icon--xls',
                                in_array($ext, ['jpg','jpeg','png','gif','webp']) => 'uc-file-icon--img',
                                default                           => 'uc-file-icon--misc',
                            };
                            $iconFa = match($iconClass) {
                                'uc-file-icon--pdf'  => 'fa-file-pdf',
                                'uc-file-icon--doc'  => 'fa-file-word',
                                'uc-file-icon--xls'  => 'fa-file-excel',
                                'uc-file-icon--img'  => 'fa-file-image',
                                default              => 'fa-file',
                            };
                            $sizeLabel = $ax->tamano_bytes
                                ? ($ax->tamano_bytes > 1048576
                                    ? round($ax->tamano_bytes / 1048576, 1) . ' MB'
                                    : round($ax->tamano_bytes / 1024, 0) . ' KB')
                                : '';
                            $prevVersions = $bancoProyecto->anexos
                                ->where('tipo_anexo', $ax->tipo_anexo)
                                ->where('is_current', false)
                                ->sortByDesc('version');
                        @endphp
                        <li class="uc-file-item" id="file-item-{{ $ax->id }}">
                            <div class="uc-file-icon {{ $iconClass }}">
                                <i class="fas {{ $iconFa }}" aria-hidden="true"></i>
                            </div>
                            <div class="uc-file-details">
                                <span class="uc-file-name">{{ $ax->nombre_original }}</span>
                                <span class="uc-file-meta">
                                    {{ ucwords(str_replace('_', ' ', $ax->tipo_anexo)) }}
                                    @if($sizeLabel) · {{ $sizeLabel }} @endif
                                    @if($ax->uploaded_at) · {{ $ax->uploaded_at->format('d/m/Y') }} @endif
                                </span>
                            </div>
                            <span class="uc-file-version uc-file-version--new">v{{ $ax->version }}</span>
                            <div class="uc-file-actions">
                                @if($prevVersions->count() > 0)
                                    <button type="button"
                                            class="action-btn"
                                            title="Ver historial de versiones"
                                            aria-label="Versiones anteriores de {{ $ax->nombre_original }}"
                                            onclick="toggleHistory('hist-{{ $ax->id }}', this)">
                                        <i class="fas fa-history" aria-hidden="true"></i>
                                    </button>
                                @endif
                                <a href="{{ route('banco.anexos.download', [$bancoProyecto, $ax]) }}"
                                   class="action-btn action-btn--view"
                                   title="Descargar"
                                   aria-label="Descargar {{ $ax->nombre_original }}">
                                    <i class="fas fa-download" aria-hidden="true"></i>
                                </a>
                                <form action="{{ route('banco.anexos.destroy', [$bancoProyecto, $ax]) }}"
                                      method="POST"
                                      onsubmit="return confirm('¿Eliminar este archivo?');"
                                      style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="action-btn action-btn--delete"
                                            title="Eliminar"
                                            aria-label="Eliminar {{ $ax->nombre_original }}">
                                        <i class="fas fa-trash-alt" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </div>

                            {{-- Version history (hidden by default) --}}
                            @if($prevVersions->count() > 0)
                                <div id="hist-{{ $ax->id }}" class="uc-file-history" style="width:100%;margin-top:var(--space-2);">
                                    @foreach($prevVersions as $prev)
                                        @php
                                            $prevSizeLabel = $prev->tamano_bytes
                                                ? ($prev->tamano_bytes > 1048576
                                                    ? round($prev->tamano_bytes / 1048576, 1) . ' MB'
                                                    : round($prev->tamano_bytes / 1024, 0) . ' KB')
                                                : '';
                                        @endphp
                                        <div class="uc-file-history-item">
                                            <span>
                                                <span class="uc-file-version" style="margin-right:var(--space-2);">v{{ $prev->version }}</span>
                                                {{ $prev->nombre_original }}
                                                @if($prevSizeLabel) <span class="cell-muted">({{ $prevSizeLabel }})</span> @endif
                                            </span>
                                            <span style="display:flex;align-items:center;gap:var(--space-2);">
                                                <span class="cell-muted">{{ $prev->uploaded_at?->format('d/m/Y') ?? '' }}</span>
                                                <a href="{{ route('banco.anexos.download', [$bancoProyecto, $prev]) }}"
                                                   class="action-btn" style="opacity:1;"
                                                   title="Descargar versión {{ $prev->version }}">
                                                    <i class="fas fa-download" aria-hidden="true"></i>
                                                </a>
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="empty-state" style="padding:32px 24px;">
                    <div class="empty-state-icon"><i class="fas fa-paperclip" aria-hidden="true"></i></div>
                    <p class="empty-state-text">Sin documentos adjuntos</p>
                    <p class="empty-state-subtext">Sube el primer archivo usando el formulario de abajo</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Upload zone --}}
    <div class="ds-card">
        <div class="ds-card-header">
            <h3 class="ds-card-title">Subir nuevo documento</h3>
        </div>
        <div class="ds-card-body">
            <form action="{{ route('banco.anexos.store', $bancoProyecto) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  style="display:flex;flex-direction:column;gap:var(--space-4);">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
                    <div>
                        <label class="ds-label" for="tipo_anexo">Tipo de documento</label>
                        <select name="tipo_anexo" id="tipo_anexo" class="ds-input" required>
                            @foreach([
                                'documento_proyecto'   => 'Documento del proyecto',
                                'presupuesto'          => 'Presupuesto',
                                'carta_aval'           => 'Carta aval',
                                'cronograma'           => 'Cronograma',
                                'imagen_plano'         => 'Imagen / Plano',
                                'soporte_adicional'    => 'Soporte adicional',
                                'certificado_cumplimiento' => 'Certificado de cumplimiento',
                            ] as $val => $lbl)
                                <option value="{{ $val }}">{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="ds-label" for="notas_anexo">Notas <span style="font-weight:400;color:var(--neutral-400);">(opcional)</span></label>
                        <input type="text" name="notas" id="notas_anexo" class="ds-input" placeholder="Descripción breve…">
                    </div>
                </div>
                <div>
                    <label class="ds-label">Archivo</label>
                    <div class="uc-upload-zone" id="uploadZoneAnexo">
                        <input type="file" name="archivo" class="uc-upload-zone__input"
                               required id="archivoAnexo"
                               aria-label="Seleccionar archivo para subir">
                        <div class="uc-upload-zone__label">
                            <i class="fas fa-cloud-arrow-up uc-upload-zone__icon" aria-hidden="true"></i>
                            <span class="uc-upload-zone__text" id="uploadZoneText">
                                Arrastra un archivo o haz clic para seleccionar
                            </span>
                            <span class="uc-upload-zone__hint">PDF, Word, Excel, imágenes — máx. 10 MB</span>
                        </div>
                    </div>
                </div>
                <div>
                    <button type="submit" class="ds-btn ds-btn--primary">
                        <i class="fas fa-cloud-arrow-up" aria-hidden="true"></i> Subir documento
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════════════
     Tab 3 — Certificado
     ══════════════════════════════════════════════════════════════════════ --}}
<div id="panel-cert" class="tab-panel tab-panel--hidden" role="tabpanel" aria-labelledby="tab-cert">

    <div class="ds-card">
        <div class="ds-card-header">
            <h3 class="ds-card-title">Certificado de cumplimiento</h3>
        </div>
        <div class="ds-card-body">

            @if($bancoProyecto->certificado_cumplimiento)
                {{-- Certificate exists --}}
                <div style="display:flex;align-items:flex-start;gap:var(--space-4);flex-wrap:wrap;">
                    <div class="uc-file-icon uc-file-icon--pdf" style="width:48px;height:48px;font-size:20px;flex-shrink:0;">
                        <i class="fas fa-file-pdf" aria-hidden="true"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <p style="font-weight:var(--font-semibold);color:var(--neutral-900);margin:0 0 var(--space-1);">
                            {{ basename($bancoProyecto->certificado_cumplimiento) }}
                        </p>
                        <p class="cell-muted" style="margin:0 0 var(--space-1);">
                            @if($bancoProyecto->certificado_fecha)
                                Fecha: <strong>{{ $bancoProyecto->certificado_fecha->format('d/m/Y') }}</strong>
                            @endif
                        </p>
                        @if($bancoProyecto->certificado_observaciones)
                            <p style="font-size:var(--text-sm);color:var(--neutral-700);margin:var(--space-2) 0 0;">
                                {{ $bancoProyecto->certificado_observaciones }}
                            </p>
                        @endif
                    </div>
                    <div style="display:flex;gap:var(--space-2);flex-shrink:0;flex-wrap:wrap;">
                        <a href="{{ Storage::url($bancoProyecto->certificado_cumplimiento) }}"
                           target="_blank" rel="noopener"
                           class="ds-btn ds-btn--secondary ds-btn--sm">
                            <i class="fas fa-download" aria-hidden="true"></i> Descargar
                        </a>
                        <form action="{{ route('banco.certificado.destroy', $bancoProyecto) }}"
                              method="POST"
                              onsubmit="return confirm('¿Eliminar el certificado de cumplimiento?');"
                              style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="ds-btn ds-btn--ghost ds-btn--sm"
                                    style="color:var(--danger);border-color:var(--danger);">
                                <i class="fas fa-trash-alt" aria-hidden="true"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>

                <hr style="margin:var(--space-5) 0;border-color:var(--neutral-100);">
                <p style="font-size:var(--text-sm);font-weight:var(--font-medium);color:var(--neutral-600);margin-bottom:var(--space-3);">
                    Reemplazar certificado
                </p>

            @endif

            {{-- Upload form (always shown; replaces if already exists) --}}
            <form action="{{ route('banco.certificado.store', $bancoProyecto) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  style="display:flex;flex-direction:column;gap:var(--space-4);">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
                    <div>
                        <label class="ds-label" for="certificado_fecha">Fecha del certificado</label>
                        <input type="date" name="certificado_fecha" id="certificado_fecha"
                               class="ds-input"
                               value="{{ old('certificado_fecha', $bancoProyecto->certificado_fecha?->format('Y-m-d')) }}">
                    </div>
                </div>
                <div>
                    <label class="ds-label" for="certificado_obs">Observaciones <span style="font-weight:400;color:var(--neutral-400);">(opcional)</span></label>
                    <textarea name="certificado_observaciones" id="certificado_obs"
                              class="ds-input" rows="3"
                              placeholder="Notas sobre el certificado…"
                              style="resize:vertical;">{{ old('certificado_observaciones', $bancoProyecto->certificado_observaciones) }}</textarea>
                </div>
                <div>
                    <label class="ds-label">Archivo PDF</label>
                    <div class="uc-upload-zone" id="uploadZoneCert">
                        <input type="file" name="certificado" class="uc-upload-zone__input"
                               accept="application/pdf"
                               {{ $bancoProyecto->certificado_cumplimiento ? '' : 'required' }}
                               id="archivoCert"
                               aria-label="Seleccionar certificado PDF">
                        <div class="uc-upload-zone__label">
                            <i class="fas fa-cloud-arrow-up uc-upload-zone__icon" aria-hidden="true"></i>
                            <span class="uc-upload-zone__text" id="certZoneText">
                                {{ $bancoProyecto->certificado_cumplimiento
                                    ? 'Selecciona un nuevo PDF para reemplazar'
                                    : 'Arrastra el PDF o haz clic para seleccionar' }}
                            </span>
                            <span class="uc-upload-zone__hint">Solo PDF — máx. 10 MB</span>
                        </div>
                    </div>
                </div>
                <div>
                    <button type="submit" class="ds-btn ds-btn--primary">
                        <i class="fas fa-shield-halved" aria-hidden="true"></i>
                        {{ $bancoProyecto->certificado_cumplimiento ? 'Reemplazar certificado' : 'Guardar certificado' }}
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════════════
     Tab 4 — Historial
     ══════════════════════════════════════════════════════════════════════ --}}
<div id="panel-hist" class="tab-panel tab-panel--hidden" role="tabpanel" aria-labelledby="tab-hist">

    <div class="ds-card">
        <div class="ds-card-header">
            <h3 class="ds-card-title">Historial de cambios</h3>
        </div>
        <div class="ds-card-body">

            @if($historial->count() > 0)
                <div class="uc-timeline">
                    @foreach($historial as $h)
                        @php
                            $itemClass = match($h->accion) {
                                'crear', 'create'                         => 'uc-timeline__item--create',
                                'editar', 'edit', 'update'                => 'uc-timeline__item--edit',
                                'estado_cambio', 'estado', 'estado_change'=> 'uc-timeline__item--estado-change',
                                'subir_archivo', 'upload'                 => 'uc-timeline__item--upload',
                                'eliminar', 'delete'                      => 'uc-timeline__item--delete',
                                default                                   => '',
                            };
                        @endphp
                        <div class="uc-timeline__item {{ $itemClass }}">
                            <div class="uc-timeline__dot" aria-hidden="true"></div>
                            <div class="uc-timeline__meta">
                                <span>{{ $h->created_at?->format('d/m/Y H:i') ?? '—' }}</span>
                                @if($h->user_name)
                                    <span class="uc-timeline__user">
                                        <i class="fas fa-user" aria-hidden="true" style="font-size:9px;"></i>
                                        {{ $h->user_name }}
                                    </span>
                                @endif
                                @if($h->campo_modificado)
                                    <span class="ds-badge" style="background:var(--neutral-100);color:var(--neutral-600);font-size:10px;">
                                        {{ $h->campo_modificado }}
                                    </span>
                                @endif
                            </div>
                            <div class="uc-timeline__body">
                                {{ $h->descripcion ?: ucfirst(str_replace('_', ' ', $h->accion)) }}
                                @if($h->valor_anterior && $h->valor_nuevo)
                                    <span class="cell-muted" style="font-size:var(--text-xs);">
                                        <span style="text-decoration:line-through;">{{ $h->valor_anterior }}</span>
                                        → <strong>{{ $h->valor_nuevo }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state" style="padding:32px 24px;">
                    <div class="empty-state-icon"><i class="fas fa-clock-rotate-left" aria-hidden="true"></i></div>
                    <p class="empty-state-text">Sin historial registrado</p>
                    <p class="empty-state-subtext">Los cambios futuros aparecerán aquí</p>
                </div>
            @endif

        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
(function () {

    // ── Tab switching ──────────────────────────────────────────────────
    var tabs   = document.querySelectorAll('.uc-tabs__btn');
    var panels = document.querySelectorAll('.tab-panel');

    tabs.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.getAttribute('data-tab');

            tabs.forEach(function (b) {
                b.classList.remove('uc-tabs__btn--active');
                b.setAttribute('aria-selected', 'false');
            });
            btn.classList.add('uc-tabs__btn--active');
            btn.setAttribute('aria-selected', 'true');

            panels.forEach(function (p) {
                p.classList.add('tab-panel--hidden');
            });
            var target = document.getElementById('panel-' + id);
            if (target) target.classList.remove('tab-panel--hidden');
        });
    });

    // ── Expandable long-text blocks ────────────────────────────────────
    document.querySelectorAll('.uc-expandable__toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var container = btn.closest('.uc-expandable');
            var nowCollapsed = container.classList.toggle('uc-expandable--collapsed');
            btn.textContent = nowCollapsed ? 'Ver más' : 'Ver menos';
        });
    });

    // ── Version history toggle ─────────────────────────────────────────
    window.toggleHistory = function (histId, btn) {
        var el = document.getElementById(histId);
        if (!el) return;
        var open = el.classList.toggle('uc-file-history--open');
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        btn.title = open ? 'Ocultar historial' : 'Ver historial de versiones';
    };

    // ── Upload zone filename display ───────────────────────────────────
    function setupZone(inputId, textId) {
        var input = document.getElementById(inputId);
        var text  = document.getElementById(textId);
        if (!input || !text) return;
        input.addEventListener('change', function () {
            text.textContent = input.files.length > 0 ? input.files[0].name : text.dataset.default || '';
        });
        text.dataset.default = text.textContent;
    }
    setupZone('archivoAnexo', 'uploadZoneText');
    setupZone('archivoCert',  'certZoneText');

    // ── Upload zone drag-over highlight ───────────────────────────────
    document.querySelectorAll('.uc-upload-zone').forEach(function (zone) {
        zone.addEventListener('dragover',  function (e) { e.preventDefault(); zone.classList.add('uc-upload-zone--dragover'); });
        zone.addEventListener('dragleave', function ()  { zone.classList.remove('uc-upload-zone--dragover'); });
        zone.addEventListener('drop',      function ()  { zone.classList.remove('uc-upload-zone--dragover'); });
    });

}());
</script>
@endpush
