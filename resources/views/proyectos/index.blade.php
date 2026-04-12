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
    <div class="stat-card stat-card--warning">
        <div class="stat-card-icon"><i class="fas fa-dollar-sign"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Valor Total</span>
            <span class="stat-card-value">{{ formatCOP($proyectos->sum('valor_total')) }}</span>
        </div>
    </div>
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
            <a href="{{ route('proyectos.create') }}" class="ds-btn ds-btn--primary">
                <i class="fas fa-plus"></i> Nuevo Proyecto
            </a>
        </div>
        <div class="d-flex gap-2 flex-wrap align-items-center mt-2">
            <span class="small text-muted">Certificado:</span>
            <a href="{{ request()->fullUrlWithQuery(['cert' => null]) }}" class="ds-btn ds-btn--sm {{ !request('cert') ? 'ds-btn--primary' : 'ds-btn--ghost' }}">Todos</a>
            <a href="{{ request()->fullUrlWithQuery(['cert' => 'con']) }}" class="ds-btn ds-btn--sm {{ request('cert')==='con' ? 'ds-btn--primary' : 'ds-btn--ghost' }}">Con certificado</a>
            <a href="{{ request()->fullUrlWithQuery(['cert' => 'sin']) }}" class="ds-btn ds-btn--sm {{ request('cert')==='sin' ? 'ds-btn--primary' : 'ds-btn--ghost' }}">Sin certificado</a>
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
                    <th class="text-end">Valor Total</th>
                    <th>Estado</th>
                    @if(auth()->check() && auth()->user()->roles->pluck('id')->intersect([1,2])->isNotEmpty())
                        <th class="text-center">Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($proyectos as $proyecto)
                <tr>
                    <td>
                        <div class="project-name-cell">
                            <div class="project-icon">
                                <i class="fas fa-folder"></i>
                            </div>
                            <div>
                                <span class="project-name">{{ $proyecto->nombre_del_proyecto }}</span>
                                <span class="project-id">#{{ $proyecto->id }}</span>
                            </div>
                        </div>
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
                    <td class="text-end">
                        <span class="value-cell">${{ number_format($proyecto->valor_total ?? 0, 0, ',', '.') }}</span>
                    </td>
                    <td>
                        <div class="d-inline-flex align-items-center gap-1">
                            <x-estado-badge :estado="$proyecto->estado" />
                            @if($proyecto->certificado_cumplimiento)
                                <span class="text-success" title="Certificado de cumplimiento cargado" aria-label="Certificado de cumplimiento cargado"><i class="fas fa-shield-halved"></i></span>
                            @endif
                        </div>
                    </td>
                    @if(auth()->check() && auth()->user()->roles->pluck('id')->intersect([1,2])->isNotEmpty())
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
@endpush
