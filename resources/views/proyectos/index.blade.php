@extends('layouts.main')

@section('title', 'Gestion de Proyectos')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="breadcrumb-link">Dashboard</a>
    <i class="fas fa-chevron-right breadcrumb-sep"></i>
    <span class="breadcrumb-current">Proyectos</span>
@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Gestion de Proyectos</h1>
        <p class="page-subtitle">Administra y gestiona todos los proyectos institucionales</p>
    </div>
    <div class="page-actions">
        <div class="status-chips">
            <button class="ds-chip active" data-estado="todos">
                <i class="fas fa-list"></i> Todos <span class="chip-count">({{ $proyectos->count() }})</span>
            </button>
            <button class="ds-chip" data-estado="activo">
                <i class="fas fa-check-circle"></i> Activos <span class="chip-count">({{ $proyectos->where('estado', 'activo')->count() }})</span>
            </button>
            <button class="ds-chip" data-estado="inactivo">
                <i class="fas fa-pause-circle"></i> Inactivos <span class="chip-count">({{ $proyectos->where('estado', 'inactivo')->count() }})</span>
            </button>
            <button class="ds-chip" data-estado="cerrado">
                <i class="fas fa-times-circle"></i> Cerrados <span class="chip-count">({{ $proyectos->where('estado', 'cerrado')->count() }})</span>
            </button>
        </div>
    </div>
</div>

<!-- KPI Stats -->
<div class="stat-cards-grid stat-cards-grid--proyectos">
    <div class="stat-card stat-card--primary">
        <div class="stat-card-icon"><i class="fas fa-folder-open"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Total Proyectos</span>
            <span class="stat-card-value">{{ $proyectos->count() }}</span>
        </div>
    </div>
    <div class="stat-card stat-card--success">
        <div class="stat-card-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Proyectos Activos</span>
            <span class="stat-card-value">{{ $proyectos->where('estado', 'activo')->count() }}</span>
        </div>
    </div>
    @if(auth()->check() && auth()->user()->canEdit('proyectos'))
    <div class="stat-card stat-card--warning">
        <div class="stat-card-icon"><i class="fas fa-dollar-sign"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Valor Total</span>
            <span class="stat-card-value">{{ formatCOP($proyectos->sum('valor_total')) }}</span>
        </div>
    </div>
    @endif
    <div class="stat-card stat-card--info">
        <div class="stat-card-icon"><i class="fas fa-building"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Entidades</span>
            <span class="stat-card-value">{{ $proyectos->unique('entidad_contratante')->count() }}</span>
        </div>
    </div>
</div>

<!-- Projects Table -->
<div class="ds-card projects-table-card uc-dt-wrap">
    <div class="table-toolbar">
        <div class="table-search">
            <i class="fas fa-search search-icon"></i>
            <input type="text" placeholder="Buscar proyecto..." id="searchProjects" autocomplete="off">
        </div>
        <div class="table-toolbar-actions">
            <div class="export-dropdown dropdown">
                <button class="ds-btn ds-btn--secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-file-export"></i> Exportar
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('proyectos.export.excel') }}">
                            <i class="fas fa-file-excel text-success-icon"></i> Excel
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('proyectos.export.pdf') }}">
                            <i class="fas fa-file-pdf text-danger-icon"></i> PDF
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('proyectos.export.word') }}">
                            <i class="fas fa-file-word text-primary-icon"></i> Word
                        </a>
                    </li>
                </ul>
            </div>
            <button type="button" class="ds-btn ds-btn--secondary" id="btnConvocatorias" title="Seguimiento a convocatorias externas (FGE-05)">
                <i class="fas fa-bullhorn"></i> Convocatorias
            </button>
            <a href="{{ route('proyectos.create') }}" class="ds-btn ds-btn--primary">
                <i class="fas fa-plus"></i> Nuevo Proyecto
            </a>
        </div>
        <div class="table-toolbar-filters">
            <span class="table-toolbar-filters__label">Certificado:</span>
            <div class="filter-pill-group">
                <a href="{{ request()->fullUrlWithQuery(['cert' => null]) }}"
                   class="filter-pill {{ !request('cert') ? 'filter-pill--active' : '' }}">
                    Todos
                </a>
                <a href="{{ request()->fullUrlWithQuery(['cert' => 'con']) }}"
                   class="filter-pill {{ request('cert')==='con' ? 'filter-pill--active' : '' }}">
                    <i class="fas fa-certificate" style="font-size:10px;"></i> Con certificado
                </a>
                <a href="{{ request()->fullUrlWithQuery(['cert' => 'sin']) }}"
                   class="filter-pill {{ request('cert')==='sin' ? 'filter-pill--active' : '' }}">
                    Sin certificado
                </a>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table id="proyectosTable" class="ds-table">
            <thead>
                <tr>
                    <th>Proyecto</th>
                    <th>Detalles del Contrato</th>
                    <th>Alcance</th>
                    <th>Entidad</th>
                    <th>Tiempo</th>
                    @if(auth()->check() && auth()->user()->canEdit('proyectos'))
                        <th class="text-end">Valor Total</th>
                    @endif
                    <th>Estado</th>
                    @if(auth()->check() && auth()->user()->canEdit('proyectos'))
                        <th class="text-center">Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($proyectos as $proyecto)
                <tr>
                    <td>
                        <a href="{{ route('proyectos.show', $proyecto->id) }}" class="project-name-cell" style="text-decoration:none;color:inherit;">
                            <div class="project-icon">
                                <i class="fas fa-folder"></i>
                            </div>
                            <div>
                                <span class="project-name">{{ $proyecto->nombre_del_proyecto }}</span>
                                <span class="project-id">#{{ $proyecto->id }}</span>
                            </div>
                        </a>
                    </td>
                    <td>
                        <div class="contract-details">{{ $proyecto->objeto_contractual }}</div>
                        <a href="#" class="ver-mas-link" data-bs-toggle="modal" data-bs-target="#verMasModal" data-objeto="{{ $proyecto->objeto_contractual }}">ver mas</a>
                        <div class="project-subtext">{{ $proyecto->lineas_de_accion }}</div>
                    </td>
                    <td>
                        <span class="cell-muted">{{ $proyecto->cobertura }}</span>
                    </td>
                    <td>
                        <span class="cell-strong">{{ $proyecto->entidad_contratante }}</span>
                    </td>
                    <td>
                        <div class="time-cell-date">{{ $proyecto->fecha_de_ejecucion ? $proyecto->fecha_de_ejecucion->format('d M, Y') : 'N/A' }}</div>
                        <div class="time-cell-duration">{{ $proyecto->plazo ? $proyecto->plazo . ' meses' : 'N/A' }}</div>
                    </td>
                    @if(auth()->check() && auth()->user()->canEdit('proyectos'))
                    <td class="text-end">
                        <span class="value-cell">${{ number_format($proyecto->valor_total ?? 0, 0, ',', '.') }}</span>
                    </td>
                    @endif
                    <td>
                        <div class="d-inline-flex align-items-center gap-1">
                            <x-estado-badge :estado="$proyecto->estado" />
                            @if($proyecto->certificado_cumplimiento)
                                <span class="text-success" title="Certificado de cumplimiento cargado" aria-label="Certificado de cumplimiento cargado"><i class="fas fa-shield-halved"></i></span>
                            @endif
                        </div>
                    </td>
                    @if(auth()->check() && auth()->user()->canEdit('proyectos'))
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('proyectos.show', $proyecto->id) }}" class="action-btn action-btn--view" title="Ver proyecto">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('proyectos.edit', $proyecto->id) }}" class="action-btn action-btn--edit" title="Editar proyecto">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('proyectos.destroy', $proyecto->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="action-btn action-btn--delete btn-delete-project" title="Eliminar proyecto" data-proyecto-id="{{ $proyecto->id }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Advanced Filters Modal -->
<div class="modal fade" id="filtrosModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filtros Avanzados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="filtrosForm">
                    <div class="filter-section">
                        <div class="filter-section-title">Estado</div>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="estados[]" value="activo" id="estadoActivo">
                                <label class="form-check-label" for="estadoActivo">Activo</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="estados[]" value="inactivo" id="estadoInactivo">
                                <label class="form-check-label" for="estadoInactivo">Inactivo</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="estados[]" value="cerrado" id="estadoCerrado">
                                <label class="form-check-label" for="estadoCerrado">Cerrado</label>
                            </div>
                        </div>
                    </div>

                    <div class="filter-section">
                        <div class="filter-section-title">Rango de Fechas</div>
                        <div class="ds-form-grid">
                            <div>
                                <label class="ds-label">Fecha Inicio</label>
                                <input type="date" class="ds-input" name="fechaInicio" id="fechaInicio">
                            </div>
                            <div>
                                <label class="ds-label">Fecha Fin</label>
                                <input type="date" class="ds-input" name="fechaFin" id="fechaFin">
                            </div>
                        </div>
                    </div>

                    <div class="filter-section">
                        <div class="filter-section-title">Rango de Monto</div>
                        <div class="ds-form-grid">
                            <div>
                                <input type="number" class="ds-input" name="montoMin" id="montoMin" placeholder="Monto minimo">
                            </div>
                            <div>
                                <input type="number" class="ds-input" name="montoMax" id="montoMax" placeholder="Monto maximo">
                            </div>
                        </div>
                    </div>

                    <div class="filter-section">
                        <div class="filter-section-title">Entidad Contratante</div>
                        <select class="ds-select" name="entidad" id="entidad">
                            <option value="">Todas las entidades</option>
                            @foreach($entidades as $entidad)
                                <option value="{{ $entidad }}">{{ $entidad }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-section">
                        <div class="d-flex align-items-center gap-2">
                            <input type="text" class="ds-input" id="presetName" placeholder="Nombre del preset (opcional)">
                            <button type="button" class="ds-btn ds-btn--secondary" id="savePreset">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                        </div>
                    </div>

                    <div class="filter-section">
                        <div class="filter-section-title">Presets Guardados</div>
                        <select class="ds-select" id="savedPresets">
                            <option value="">Seleccionar preset...</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="ds-btn ds-btn--ghost" id="limpiarFiltros">Limpiar</button>
                <button type="button" class="ds-btn ds-btn--primary" id="aplicarFiltros">Aplicar</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmLabel">Confirmar eliminacion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="deleteProjectForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body modal-body-centered">
                    <div class="confirm-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <p class="delete-confirm-text">
                        ¿Esta seguro de que desea eliminar este proyecto?
                    </p>
                    <div class="text-start">
                        <label class="ds-label" for="deleteReason">Razon del borrado</label>
                        <textarea class="ds-textarea" id="deleteReason" name="reason" rows="2" required placeholder="Indique la razon..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="ds-btn ds-btn--ghost" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="ds-btn ds-btn--danger">
                        <i class="fas fa-trash-alt"></i> Eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Ver Mas Modal -->
<div class="modal fade" id="verMasModal" tabindex="-1" aria-labelledby="verMasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verMasLabel">Detalles del Contrato</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p id="verMasTexto" class="ver-mas-text"></p>
            </div>
        </div>
    </div>
</div>
{{-- ═══════════════════════════════════════════════════════════════════
     PANEL DE CONVOCATORIAS (FGE-05)
     Se muestra como slide-panel lateral al hacer click en el botón
     ═══════════════════════════════════════════════════════════════════ --}}

<!-- Overlay del panel -->
<div id="convPanel-overlay" class="conv-overlay" onclick="cerrarConvPanel()"></div>

<!-- Panel lateral -->
<aside id="convPanel" class="conv-panel" role="dialog" aria-label="Seguimiento a Convocatorias">
    <div class="conv-panel-header">
        <div class="conv-panel-title-group">
            <div class="conv-panel-icon"><i class="fas fa-bullhorn"></i></div>
            <div>
                <h2 class="conv-panel-title">Convocatorias Externas</h2>
                <p class="conv-panel-sub">Planilla FGE-05 — Seguimiento digital</p>
            </div>
        </div>
        <div class="conv-panel-actions">
            @if(auth()->user()->canEdit('proyectos'))
            <button type="button" class="ds-btn ds-btn--primary ds-btn--sm" id="btnNuevaConvocatoria">
                <i class="fas fa-plus"></i> Nueva
            </button>
            @endif
            <button type="button" class="conv-close-btn" onclick="cerrarConvPanel()" aria-label="Cerrar">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    </div>

    <!-- Formulario inline de nueva convocatoria -->
    @if(auth()->user()->canEdit('proyectos'))
    <div id="convFormWrap" style="display:none;" class="conv-form-wrap">
        <form id="convForm" class="conv-form">
            <div class="conv-form-grid">
                <div class="conv-field conv-field--full">
                    <label class="ds-label">Nombre de la convocatoria <span class="req">*</span></label>
                    <input type="text" class="ds-input" name="nombre" required placeholder="Ej: Convocatoria MinCiencias 2026">
                </div>
                <div class="conv-field conv-field--full">
                    <label class="ds-label">Entidad u organización financiadora <span class="req">*</span></label>
                    <input type="text" class="ds-input" name="entidad" required placeholder="Ej: MinCiencias">
                </div>
                <div class="conv-field conv-field--full">
                    <label class="ds-label">Objetivo / Descripción breve</label>
                    <textarea class="ds-textarea" name="descripcion" rows="2" placeholder="Descripción del objetivo de la convocatoria..."></textarea>
                </div>
                <div class="conv-field">
                    <label class="ds-label">Fecha de inicio</label>
                    <input type="date" class="ds-input" name="fecha_inicio">
                </div>
                <div class="conv-field">
                    <label class="ds-label">Fecha de cierre</label>
                    <input type="date" class="ds-input" name="fecha_cierre">
                </div>
                <div class="conv-field">
                    <label class="ds-label">Fecha publicación resultados</label>
                    <input type="date" class="ds-input" name="fecha_resultados">
                </div>
                <div class="conv-field">
                    <label class="ds-label">Responsable formulación</label>
                    <input type="text" class="ds-input" name="responsable" placeholder="Nombre del responsable">
                </div>
                <div class="conv-field conv-field--full">
                    <label class="ds-label">Enlace de la convocatoria</label>
                    <input type="url" class="ds-input" name="enlace" placeholder="https://...">
                </div>
                <div class="conv-field conv-field--full">
                    <label class="ds-label">Correo para preguntas</label>
                    <input type="email" class="ds-input" name="correo_contacto" placeholder="convocatoria@entidad.gov.co">
                </div>
                <div class="conv-field conv-field--full">
                    <label class="ds-label">Observación</label>
                    <textarea class="ds-textarea" name="observacion" rows="2" placeholder="Notas adicionales..."></textarea>
                </div>
            </div>
            <input type="hidden" name="conv_id" id="convEditId">
            <div class="conv-form-footer">
                <span id="convFormMsg" class="conv-form-msg"></span>
                <button type="button" class="ds-btn ds-btn--ghost ds-btn--sm" id="btnCancelarConv">Cancelar</button>
                <button type="submit" class="ds-btn ds-btn--primary ds-btn--sm">
                    <i class="fas fa-save"></i> <span id="convSubmitLabel">Guardar convocatoria</span>
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- Lista de convocatorias -->
    <div class="conv-list-wrap">
        <div id="convLoadingState" class="conv-state">
            <i class="fas fa-spinner fa-spin"></i> Cargando...
        </div>
        <div id="convEmptyState" class="conv-state" style="display:none;">
            <i class="fas fa-bullhorn" style="font-size:32px;color:var(--neutral-300);display:block;margin-bottom:10px;"></i>
            No hay convocatorias registradas aún.<br>
            <small>Usa el botón <strong>+ Nueva</strong> para agregar la primera.</small>
        </div>
        <div id="convList" class="conv-list" style="display:none;"></div>
    </div>
</aside>

@push('styles')
<style>
/* ── Conv Panel ─────────────────────────────────────── */
.conv-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(8,16,36,.45); backdrop-filter: blur(4px);
    z-index: 1040;
}
.conv-overlay.open { display: block; }
.conv-panel {
    position: fixed; top: 0; right: -760px; width: min(760px, 100vw);
    height: 100vh; background: #fff; z-index: 1050;
    display: flex; flex-direction: column;
    box-shadow: -8px 0 40px rgba(0,0,0,.18);
    transition: right .3s cubic-bezier(.16,1,.3,1);
    border-left: 1px solid var(--neutral-100);
}
.conv-panel.open { right: 0; }

/* Header */
.conv-panel-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 20px 24px;
    background: linear-gradient(135deg, #0c1929 0%, #1a3456 100%);
    flex-shrink: 0;
}
.conv-panel-title-group { display: flex; align-items: center; gap: 14px; }
.conv-panel-icon {
    width: 40px; height: 40px; border-radius: 10px;
    background: rgba(198,146,42,.2); color: #f0c878;
    display: flex; align-items: center; justify-content: center; font-size: 18px;
}
.conv-panel-title { font-size: 16px; font-weight: 700; color: #fff; margin: 0; }
.conv-panel-sub { font-size: 11px; color: rgba(255,255,255,.5); margin: 2px 0 0; }
.conv-panel-actions { display: flex; align-items: center; gap: 8px; }
.conv-close-btn {
    width: 30px; height: 30px; border-radius: 8px; border: none;
    background: rgba(255,255,255,.1); color: rgba(255,255,255,.8);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; font-size: 14px; transition: background .15s;
}
.conv-close-btn:hover { background: rgba(255,255,255,.2); color: #fff; }

/* Form */
.conv-form-wrap {
    padding: 20px 24px; border-bottom: 1px solid var(--neutral-100);
    background: var(--neutral-50); flex-shrink: 0;
}
.conv-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.conv-field { display: flex; flex-direction: column; gap: 4px; }
.conv-field--full { grid-column: 1 / -1; }
.conv-field .ds-label { font-size: 12px; margin-bottom: 2px; }
.conv-field .req { color: #ef4444; }
.conv-form-footer {
    display: flex; align-items: center; justify-content: flex-end;
    gap: 8px; padding-top: 14px; margin-top: 4px;
}
.conv-form-msg { flex: 1; font-size: 12px; color: #10b981; }

/* List */
.conv-list-wrap { flex: 1; overflow-y: auto; padding: 16px 24px; }
.conv-state { text-align: center; padding: 40px 20px; color: var(--slate-400); font-size: 14px; }
.conv-list { display: flex; flex-direction: column; gap: 10px; }

/* Card */
.conv-card {
    border: 1px solid var(--neutral-200); border-radius: 12px;
    padding: 16px; background: #fff;
    transition: box-shadow .15s, border-color .15s;
}
.conv-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.08); border-color: var(--primary-200, #bfdbfe); }
.conv-card-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; margin-bottom: 8px; }
.conv-card-name { font-size: 14px; font-weight: 600; color: var(--slate-800); line-height: 1.3; }
.conv-card-entidad { font-size: 12px; color: var(--slate-500); margin-top: 2px; }
.conv-card-actions { display: flex; gap: 4px; flex-shrink: 0; }
.conv-card-btn {
    width: 28px; height: 28px; border-radius: 7px; border: none;
    background: var(--neutral-100); color: var(--slate-500);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; font-size: 12px; transition: all .15s;
}
.conv-card-btn:hover { background: var(--primary-50); color: var(--primary); }
.conv-card-btn--danger:hover { background: #fee2e2; color: #ef4444; }
.conv-card-meta { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 8px; }
.conv-chip {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 11px; padding: 3px 8px; border-radius: 20px;
    background: var(--neutral-100); color: var(--slate-600);
}
.conv-chip--cierre { background: #fef3c7; color: #92400e; }
.conv-chip--vencida { background: #fee2e2; color: #991b1b; }
.conv-chip--pronto { background: #dcfce7; color: #166534; }
.conv-card-desc { font-size: 12px; color: var(--slate-500); line-height: 1.5; }
.conv-card-links { display: flex; gap: 10px; margin-top: 8px; }
.conv-card-link { font-size: 11px; color: var(--primary); text-decoration: none; display: flex; align-items: center; gap: 3px; }
.conv-card-link:hover { text-decoration: underline; }

@media (max-width: 600px) {
    .conv-panel { width: 100vw; }
    .conv-form-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#proyectosTable').DataTable({
                dom: 't<"bottom"lp>',
                ordering: false,
                pageLength: 25,
                language: {
                    emptyTable: "No hay proyectos registrados",
                    zeroRecords: "No se encontraron resultados",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ proyectos",
                    infoEmpty: "Mostrando 0 proyectos",
                    lengthMenu: "Mostrar _MENU_ proyectos",
                    paginate: { previous: "Anterior", next: "Siguiente" }
                }
            });

            var estadoActual = 'todos';
            var busquedaActual = '';

            function getEstado(row) {
                var badge = $(row).find('[data-estado]');
                return badge.attr('data-estado') || '';
            }

            function aplicarFiltros() {
                $('#proyectosTable tbody tr').each(function() {
                    var fila = $(this);
                    var estado = getEstado(this);
                    var textoFila = fila.text().toLowerCase();
                    var coincideEstado = (estadoActual === 'todos') || (estado === estadoActual);
                    var coincideBusqueda = !busquedaActual || textoFila.includes(busquedaActual.toLowerCase());
                    if (coincideEstado && coincideBusqueda) {
                        fila.show();
                    } else {
                        fila.hide();
                    }
                });
                actualizarContadores();
            }

            function actualizarContadores() {
                var totalActivos = $('#proyectosTable tbody tr').filter(function() {
                    return getEstado(this) === 'activo';
                }).length;
                var totalInactivos = $('#proyectosTable tbody tr').filter(function() {
                    return getEstado(this) === 'inactivo';
                }).length;
                var totalCerrados = $('#proyectosTable tbody tr').filter(function() {
                    return getEstado(this) === 'cerrado';
                }).length;
                var totalFiltrados = $('#proyectosTable tbody tr:visible').length;

                $('.ds-chip[data-estado="activo"] .chip-count').text('(' + totalActivos + ')');
                $('.ds-chip[data-estado="inactivo"] .chip-count').text('(' + totalInactivos + ')');
                $('.ds-chip[data-estado="cerrado"] .chip-count').text('(' + totalCerrados + ')');
                $('.ds-chip[data-estado="todos"] .chip-count').text('(' + totalFiltrados + ')');
            }

            // Status chip click
            $('.ds-chip[data-estado]').on('click', function(e) {
                e.preventDefault();
                $('.ds-chip[data-estado]').removeClass('active');
                $(this).addClass('active');
                estadoActual = $(this).attr('data-estado');
                aplicarFiltros();
            });

            // Search input
            $('#searchProjects').on('input', function() {
                busquedaActual = $(this).val().trim();
                aplicarFiltros();
            });

            // Init
            $('.ds-chip[data-estado="todos"]').addClass('active');
            estadoActual = 'todos';
            setTimeout(aplicarFiltros, 300);

            // Delete modal
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            var deleteForm = $('#deleteProjectForm');

            $('.btn-delete-project').on('click', function() {
                var proyectoId = $(this).data('proyecto-id');
                deleteForm.attr('action', '/proyectos/' + proyectoId);
                $('#deleteReason').val('');
                deleteModal.show();
            });

            deleteForm.on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        deleteModal.hide();
                        if (typeof showToast === 'function') {
                            showToast('success', 'Proyecto eliminado', 'Proyecto eliminado correctamente');
                        }
                        setTimeout(function() {
                            window.location.reload();
                        }, 1200);
                    },
                    error: function() {
                        if (typeof showToast === 'function') {
                            showToast('danger', 'Error', 'Error al eliminar el proyecto');
                        } else {
                            alert('Error al eliminar el proyecto.');
                        }
                    }
                });
            });

            // Ver mas modal
            $('#verMasModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var objeto = button.data('objeto');
                $('#verMasTexto').text(objeto);
            });

            // Tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>

    {{-- ── CONVOCATORIAS PANEL JS ─────────────────────────────── --}}
    <script>
    const CSRF = '{{ csrf_token() }}';
    const canEdit = {{ auth()->user()->canEdit('proyectos') ? 'true' : 'false' }};

    // Abrir / cerrar panel
    function abrirConvPanel() {
        document.getElementById('convPanel').classList.add('open');
        document.getElementById('convPanel-overlay').classList.add('open');
        document.body.style.overflow = 'hidden';
        cargarConvocatorias();
    }
    function cerrarConvPanel() {
        document.getElementById('convPanel').classList.remove('open');
        document.getElementById('convPanel-overlay').classList.remove('open');
        document.body.style.overflow = '';
        ocultarFormulario();
    }

    document.getElementById('btnConvocatorias').addEventListener('click', abrirConvPanel);

    // Formulario
    const formWrap     = document.getElementById('convFormWrap');
    const form         = document.getElementById('convForm');
    const convEditId   = document.getElementById('convEditId');
    const convSubmitLbl= document.getElementById('convSubmitLabel');
    const convFormMsg  = document.getElementById('convFormMsg');

    function mostrarFormulario(data) {
        if (data) {
            // Editar
            convEditId.value           = data.id;
            form.nombre.value          = data.nombre ?? '';
            form.entidad.value         = data.entidad ?? '';
            form.descripcion.value     = data.descripcion ?? '';
            form.fecha_inicio.value    = data.fecha_inicio ?? '';
            form.fecha_cierre.value    = data.fecha_cierre ?? '';
            form.fecha_resultados.value= data.fecha_resultados ?? '';
            form.responsable.value     = data.responsable ?? '';
            form.enlace.value          = data.enlace ?? '';
            form.correo_contacto.value = data.correo_contacto ?? '';
            form.observacion.value     = data.observacion ?? '';
            convSubmitLbl.textContent  = 'Actualizar';
        } else {
            form.reset();
            convEditId.value = '';
            convSubmitLbl.textContent = 'Guardar convocatoria';
        }
        convFormMsg.textContent = '';
        formWrap.style.display = 'block';
        formWrap.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function ocultarFormulario() {
        if (formWrap) { formWrap.style.display = 'none'; form.reset(); convEditId.value = ''; }
    }

    if (canEdit) {
        document.getElementById('btnNuevaConvocatoria').addEventListener('click', () => mostrarFormulario(null));
        document.getElementById('btnCancelarConv').addEventListener('click', ocultarFormulario);
    }

    // Submit del formulario
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const id  = convEditId.value;
            const url = id ? `/convocatorias/${id}` : '/convocatorias';
            const method = id ? 'PUT' : 'POST';

            const body = {
                nombre:           form.nombre.value,
                entidad:          form.entidad.value,
                descripcion:      form.descripcion.value,
                fecha_inicio:     form.fecha_inicio.value || null,
                fecha_cierre:     form.fecha_cierre.value || null,
                fecha_resultados: form.fecha_resultados.value || null,
                responsable:      form.responsable.value,
                enlace:           form.enlace.value || null,
                correo_contacto:  form.correo_contacto.value || null,
                observacion:      form.observacion.value,
            };

            try {
                const res = await fetch(url, {
                    method,
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body: JSON.stringify(body)
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message ?? 'Error');
                convFormMsg.textContent = id ? 'Actualizado ✓' : 'Guardado ✓';
                ocultarFormulario();
                cargarConvocatorias();
            } catch(err) {
                convFormMsg.textContent = '⚠ ' + err.message;
            }
        });
    }

    // Cargar y renderizar lista
    async function cargarConvocatorias() {
        const loading = document.getElementById('convLoadingState');
        const empty   = document.getElementById('convEmptyState');
        const list    = document.getElementById('convList');

        loading.style.display = 'block';
        empty.style.display   = 'none';
        list.style.display    = 'none';

        try {
            const res  = await fetch('/convocatorias', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } });
            const data = await res.json();
            loading.style.display = 'none';

            if (!data.length) { empty.style.display = 'block'; return; }

            list.innerHTML = data.map(c => renderCard(c)).join('');
            list.style.display = 'flex';
        } catch(e) {
            loading.innerHTML = '<i class="fas fa-exclamation-circle" style="color:#ef4444"></i> Error al cargar.';
        }
    }

    function fechaRelativa(fechaStr) {
        if (!fechaStr) return null;
        const hoy    = new Date(); hoy.setHours(0,0,0,0);
        const fecha  = new Date(fechaStr + 'T00:00:00');
        const diff   = Math.ceil((fecha - hoy) / 86400000);
        if (diff < 0)   return { texto: 'Vencida',         clase: 'conv-chip--vencida' };
        if (diff <= 7)  return { texto: `Cierra en ${diff}d`, clase: 'conv-chip--pronto' };
        return { texto: fecha.toLocaleDateString('es-CO', { day:'2-digit', month:'short', year:'numeric' }), clase: 'conv-chip--cierre' };
    }

    function renderCard(c) {
        const rel = fechaRelativa(c.fecha_cierre);
        const chips = [
            c.fecha_inicio ? `<span class="conv-chip"><i class="fas fa-calendar-plus"></i> Inicio: ${new Date(c.fecha_inicio+'T00:00:00').toLocaleDateString('es-CO',{day:'2-digit',month:'short'})}</span>` : '',
            rel ? `<span class="conv-chip ${rel.clase}"><i class="fas fa-calendar-times"></i> ${rel.texto}</span>` : '',
            c.responsable ? `<span class="conv-chip"><i class="fas fa-user-tie"></i> ${c.responsable}</span>` : '',
        ].filter(Boolean).join('');

        const links = [
            c.enlace ? `<a href="${c.enlace}" target="_blank" rel="noopener" class="conv-card-link"><i class="fas fa-external-link-alt"></i> Ver convocatoria</a>` : '',
            c.correo_contacto ? `<a href="mailto:${c.correo_contacto}" class="conv-card-link"><i class="fas fa-envelope"></i> ${c.correo_contacto}</a>` : '',
        ].filter(Boolean).join('');

        const editBtn = canEdit ? `
            <button class="conv-card-btn" onclick="editarConvocatoria(${c.id})" title="Editar"><i class="fas fa-pencil"></i></button>
            <button class="conv-card-btn conv-card-btn--danger" onclick="eliminarConvocatoria(${c.id})" title="Eliminar"><i class="fas fa-trash"></i></button>
        ` : '';

        return `
        <div class="conv-card" id="conv-card-${c.id}">
            <div class="conv-card-header">
                <div>
                    <div class="conv-card-name">${c.nombre}</div>
                    <div class="conv-card-entidad"><i class="fas fa-building" style="font-size:10px;opacity:.6;"></i> ${c.entidad}</div>
                </div>
                <div class="conv-card-actions">${editBtn}</div>
            </div>
            ${chips ? `<div class="conv-card-meta">${chips}</div>` : ''}
            ${c.descripcion ? `<p class="conv-card-desc">${c.descripcion}</p>` : ''}
            ${links ? `<div class="conv-card-links">${links}</div>` : ''}
        </div>`;
    }

    async function editarConvocatoria(id) {
        const res  = await fetch(`/convocatorias/${id}`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } });
        const data = await res.json();
        mostrarFormulario(data);
    }

    async function eliminarConvocatoria(id) {
        if (!confirm('¿Eliminar esta convocatoria? Esta acción no se puede deshacer.')) return;
        const res = await fetch(`/convocatorias/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        if (res.ok) {
            document.getElementById(`conv-card-${id}`)?.remove();
            const list = document.getElementById('convList');
            if (!list.children.length) {
                list.style.display = 'none';
                document.getElementById('convEmptyState').style.display = 'block';
            }
        }
    }
    </script>
@endpush
