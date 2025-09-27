@extends('layouts.main')

@section('title', 'Gestión de Proyectos')

@section('styles')
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">
    
    <!-- Project Styles -->
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/proyectos.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/status-chips.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/table-states.css') }}">
    <style>
        /* Oculta el buscador de DataTables */
        #proyectosTable_filter {
            display: none !important;
        }
        /* Fuerza el layout horizontal de buscador y selector */
        .search-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: nowrap;
            width: 100%;
        }
        .search-row .search-container {
            flex: 1 1 220px;
            min-width: 180px;
        }
        .search-row #proyectosTable_length {
            flex: 0 0 auto;
            min-width: 120px;
            margin-bottom: 0 !important;
            display: flex;
            align-items: center;
            height: 38px;
        }
        /* Ajusta el select y label de DataTables para que se alineen con el input */
        #proyectosTable_length label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0;
            font-weight: 400;
        }
        #proyectosTable_length select {
            height: 32px;
            margin-bottom: 0;
            font-size: 1rem;
        }
        .search-row .btn-group {
            flex: 0 0 auto;
        }
        .search-row .btn-primary {
            flex: 0 0 auto;
            white-space: nowrap;
        }
        @media (max-width: 900px) {
            .search-row {
                flex-wrap: wrap;
            }
        }

        /* Estilos para la tabla de proyectos */
        #proyectosTable td {
            padding: 1rem;
            vertical-align: middle;
        }

        #proyectosTable .font-weight-medium {
            font-weight: 500;
            margin-bottom: 0.25rem;
            line-height: 1.4;
        }

        #proyectosTable .text-muted.small {
            font-size: 0.875rem;
            line-height: 1.2;
        }

        #proyectosTable th {
            font-weight: 600;
            background-color: #f8f9fa;
            border-bottom-width: 2px;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Barra superior -->
    <div class="status-bar">
        <div class="d-flex align-items-center">
            <h1 class="card-title">Gestión de Proyectos</h1>
            <div class="status-chips ms-4">
                <div class="status-chip activo" data-estado="activo">
                    <i class="fas fa-check-circle me-1"></i>
                    Activos <span class="count">({{ $proyectos->where('estado', 'activo')->count() }})</span>
                </div>
                <div class="status-chip inactivo" data-estado="inactivo">
                    <i class="fas fa-pause-circle me-1"></i>
                    Inactivos <span class="count">({{ $proyectos->where('estado', 'inactivo')->count() }})</span>
                </div>
                <div class="status-chip cerrado" data-estado="cerrado">
                    <i class="fas fa-times-circle me-1"></i>
                    Cerrados <span class="count">({{ $proyectos->where('estado', 'cerrado')->count() }})</span>
                </div>
                <div class="status-chip" data-estado="todos">
                    <i class="fas fa-list me-1"></i>
                    Todos <span class="count">({{ $proyectos->count() }})</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Filtros Avanzados -->
    <div class="modal fade" id="filtrosModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filtros Avanzados</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="filtrosForm">
                        <!-- Estados -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Estado</label>
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

                        <!-- Rango de Fechas -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Rango de Fechas</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="date" class="form-control" name="fechaInicio" id="fechaInicio">
                                    <label class="form-text">Fecha Inicio</label>
                                </div>
                                <div class="col-md-6">
                                    <input type="date" class="form-control" name="fechaFin" id="fechaFin">
                                    <label class="form-text">Fecha Fin</label>
                                </div>
                            </div>
                        </div>

                        <!-- Rango de Monto -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Rango de Monto</label>
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <input type="number" class="form-control" name="montoMin" id="montoMin" placeholder="Monto mínimo">
                                </div>
                                <div class="col-md-6">
                                    <input type="number" class="form-control" name="montoMax" id="montoMax" placeholder="Monto máximo">
                                </div>
                            </div>
                        </div>

                        <!-- Entidad -->
                        <div class="mb-4">
                            <label class="form-label fw-bold" for="entidad">Entidad Contratante</label>
                            <select class="form-select" name="entidad" id="entidad">
                                <option value="">Todas las entidades</option>
                                @foreach($entidades as $entidad)
                                    <option value="{{ $entidad }}">{{ $entidad }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Guardar Preset -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-2">
                                <input type="text" class="form-control" id="presetName" placeholder="Nombre del preset (opcional)">
                                <button type="button" class="btn btn-outline-primary" id="savePreset">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                            </div>
                        </div>

                        <!-- Presets Guardados -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Presets Guardados</label>
                            <select class="form-select" id="savedPresets">
                                <option value="">Seleccionar preset...</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="limpiarFiltros">Limpiar</button>
                    <button type="button" class="btn btn-primary" id="aplicarFiltros">Aplicar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card primary fade-in">
            <div class="stat-header">
                <h3 class="stat-title">Total Proyectos</h3>
                <div class="stat-icon primary">
                    <i class="fas fa-folder-open"></i>
                </div>
            </div>
            <div class="stat-value">{{ $proyectos->count() }}</div>
        </div>
        
        <div class="stat-card success fade-in">
            <div class="stat-header">
                <h3 class="stat-title">Proyectos Activos</h3>
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-value">{{ $proyectos->where('estado', 'activo')->count() }}</div>
        </div>
        
        <div class="stat-card warning fade-in">
            <div class="stat-header">
                <h3 class="stat-title">Valor Total</h3>
                <div class="stat-icon warning">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
            <div class="stat-value">${{ number_format($proyectos->sum('valor_total'), 0, ',', '.') }}</div>
        </div>
        
        <div class="stat-card info fade-in">
            <div class="stat-header">
                <h3 class="stat-title">Entidades</h3>
                <div class="stat-icon info">
                    <i class="fas fa-building"></i>
                </div>
            </div>
            <div class="stat-value">{{ $proyectos->unique('entidad_contratante')->count() }}</div>
        </div>
    </div>

    <div class="content-card fade-in">
        <div class="card-header">
            <div class="search-row">
                <div class="search-container position-relative">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search"></i></span>
                        <input type="text" placeholder="Buscar proyecto..." class="form-control border-start-0" id="searchProjects" autocomplete="off" style="border-radius: 0 0.375rem 0.375rem 0;">
                    </div>
                </div>
                <div id="proyectosTable_length"></div>
                <div class="btn-group ms-auto">
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-file-export me-2"></i>Exportar
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('proyectos.export.excel') }}">
                                <i class="fas fa-file-excel text-success me-2"></i>Excel
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('proyectos.export.pdf') }}">
                                <i class="fas fa-file-pdf text-danger me-2"></i>PDF
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('proyectos.export.word') }}">
                                <i class="fas fa-file-word text-primary me-2"></i>Word
                            </a>
                        </li>
                    </ul>
                </div>
                
                <a href="{{ route('proyectos.create') }}" class="btn btn-primary ms-2">
                    <i class="fas fa-plus me-2"></i>Nuevo Proyecto
                </a>
            </div>
        </div>
        
        <div class="table-container">
            <div class="table-responsive">
                <table id="proyectosTable" class="table table-hover align-middle">
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
                            <div class="font-weight-medium">
                                <i class="fas fa-folder fa-lg text-primary me-2"></i>
                                {{ $proyecto->nombre_del_proyecto }}
                            </div>
                            <div class="text-muted small">ID: {{ $proyecto->id }}</div>
                        </td>
                        <td>
                            <div class="font-weight-medium contract-details-truncate" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; max-height: 2.8em;">
                                {{ $proyecto->objeto_contractual }}
                            </div>
                            <a href="#" class="ver-mas-link" data-bs-toggle="modal" data-bs-target="#verMasModal" data-objeto="{{ $proyecto->objeto_contractual }}" style="font-size: 0.95em; color: #4361ee; text-decoration: underline;">…ver más</a>
                            <div class="text-muted small">{{ $proyecto->lineas_de_accion }}</div>
                        </td>
                        <td>
                            <div class="font-weight-medium">{{ $proyecto->cobertura }}</div>
                        </td>
                        <td>
                            <div class="font-weight-medium">{{ $proyecto->entidad_contratante }}</div>
                        </td>
                        <td>
                            <div class="font-weight-medium">{{ $proyecto->fecha_de_ejecucion ? $proyecto->fecha_de_ejecucion->format('d M, Y') : 'N/A' }}</div>
                            <div class="text-muted small">{{ $proyecto->plazo ? $proyecto->plazo . ' meses' : 'N/A' }}</div>
                        </td>
                        <td class="text-end">
                            <div class="value-cell">${{ number_format($proyecto->valor_total ?? 0, 0, ',', '.') }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark" data-estado="{{ $proyecto->estado }}">
                                {{ ucfirst($proyecto->estado) }}
                            </span>
                        </td>
                            @if(auth()->check() && auth()->user()->roles->pluck('id')->intersect([1,2])->isNotEmpty())
                            <td>
                                <div class="action-buttons d-flex gap-2">
                                    <a href="{{ route('proyectos.show', $proyecto->id) }}" class="btn-action" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Ver proyecto" title="Ver proyecto" style="color: #4361ee; background: #e3f0ff; border-radius: 8px; padding: 6px 10px;">
                                        <i class="fas fa-eye fa-lg"></i>
                                    </a>
                                    <a href="{{ route('proyectos.edit', $proyecto->id) }}" class="btn-action" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Editar proyecto" title="Editar proyecto" style="color: #4cc9f0; background: #e0f7fa; border-radius: 8px; padding: 6px 10px;">
                                        <i class="fas fa-edit fa-lg"></i>
                                    </a>
                                    <form action="{{ route('proyectos.destroy', $proyecto->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn-action btn-delete-project" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Eliminar proyecto" data-proyecto-id="{{ $proyecto->id }}" title="Eliminar proyecto" style="color: #e63946; background: #ffe3e3; border-radius: 8px; padding: 6px 10px;">
                                            <i class="fas fa-trash-alt fa-lg"></i>
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
    </div>
</div>

<!-- Modal de confirmación de borrado -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="deleteProjectForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>¿Está seguro de que desea eliminar este proyecto?</p>
                    <div class="mb-3">
                        <label for="deleteReason" class="form-label">Razón del borrado</label>
                        <textarea class="form-control" id="deleteReason" name="reason" rows="2" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ver Más Detalles -->
<div class="modal fade" id="verMasModal" tabindex="-1" aria-labelledby="verMasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verMasLabel">Detalles del Contrato</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p id="verMasTexto" style="white-space: pre-line;"></p>
            </div>
        </div>
    </div>
</div>

<!-- Toast de deshacer -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="undoToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Proyecto eliminado</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Cerrar"></button>
        </div>
        <div class="toast-body">
            El proyecto ha sido eliminado. <button type="button" class="btn btn-link btn-sm" id="undoDeleteBtn">Deshacer</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- jQuery primero -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables y Bootstrap -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Script de filtros final limpio -->
    <script>
        $(document).ready(function() {
            // Inicializar DataTable
            var table = $('#proyectosTable').DataTable({
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
                },
                dom: 't<"bottom"lp>',
                order: [[0, 'asc']],
                pageLength: 10
            });

            var estadoActual = 'todos';
            var busquedaActual = '';

            function getEstado(row) {
                var badge = $(row).find('span[data-estado]');
                if (badge.length === 0) badge = $(row).find('.badge[data-estado]');
                if (badge.length === 0) badge = $(row).find('[data-estado]');
                return badge.attr('data-estado') || badge.data('estado') || '';
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
                // Contadores absolutos por estado
                var totalActivos = $('#proyectosTable tbody tr').filter(function() {
                    return getEstado(this) === 'activo';
                }).length;
                var totalInactivos = $('#proyectosTable tbody tr').filter(function() {
                    return getEstado(this) === 'inactivo';
                }).length;
                var totalCerrados = $('#proyectosTable tbody tr').filter(function() {
                    return getEstado(this) === 'cerrado';
                }).length;
                var totalTodos = $('#proyectosTable tbody tr').length;

                // Contador filtrado solo para el chip 'Todos'
                var totalFiltrados = $('#proyectosTable tbody tr:visible').length;

                $('.status-chip[data-estado="activo"] .count').text('(' + totalActivos + ')');
                $('.status-chip[data-estado="inactivo"] .count').text('(' + totalInactivos + ')');
                $('.status-chip[data-estado="cerrado"] .count').text('(' + totalCerrados + ')');
                $('.status-chip[data-estado="todos"] .count').text('(' + totalFiltrados + ')');
            }

            $('.status-chip').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $('.status-chip').removeClass('active');
                $(this).addClass('active');
                estadoActual = $(this).attr('data-estado');
                aplicarFiltros();
            });

            $('#searchProjects').on('input', function() {
                busquedaActual = $(this).val().trim();
                aplicarFiltros();
            });

            $('.status-chip[data-estado="todos"]').addClass('active');
            estadoActual = 'todos';
            setTimeout(aplicarFiltros, 500);

            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
    <!-- Modal de eliminación y ver más -->
    <script>
        $(document).ready(function() {
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            var undoToast = new bootstrap.Toast(document.getElementById('undoToast'));
            var proyectoIdToDelete = null;
            var deleteForm = $('#deleteProjectForm');

            $('.btn-delete-project').on('click', function() {
                proyectoIdToDelete = $(this).data('proyecto-id');
                deleteForm.attr('action', '/proyectos/' + proyectoIdToDelete);
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
                        undoToast.show();
                        setTimeout(function() {
                            window.location.reload();
                        }, 1200);
                    },
                    error: function() {
                        alert('Error al eliminar el proyecto.');
                    }
                });
            });

            $('#undoDeleteBtn').on('click', function() {
                undoToast.hide();
                // Aquí puedes implementar la lógica de deshacer si es necesario
            });

            $('#verMasModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var objeto = button.data('objeto');
                $('#verMasTexto').text(objeto);
            });
        });
    </script>
@endpush