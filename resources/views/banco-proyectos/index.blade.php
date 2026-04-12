@extends('layouts.main')

@section('title', 'Banco de Proyectos')

@section('breadcrumbs')
    <span class="breadcrumb-current">Banco de Proyectos</span>
@endsection

@section('content')

{{-- ── Page header ─────────────────────────────────────────────────── --}}
<div class="page-header">
    <div>
        <h1 class="page-title">Banco de Proyectos</h1>
        <p class="page-subtitle">Repositorio institucional de proyectos académicos</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('banco.create') }}" class="ds-btn ds-btn--primary">
            <i class="fas fa-plus"></i> Nuevo registro
        </a>
    </div>
</div>

{{-- ── Stat cards ──────────────────────────────────────────────────── --}}
<div class="banco-kpi-grid">

    <div class="stat-card stat-card--primary">
        <div class="banco-stat-icon banco-stat-icon--total">
            <i class="fas fa-database" aria-hidden="true"></i>
        </div>
        <div class="stat-card-content">
            <span class="stat-card-label">Total registros</span>
            <span class="stat-card-value">{{ $total }}</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="banco-stat-icon banco-stat-icon--borrador">
            <i class="fas fa-pencil" aria-hidden="true"></i>
        </div>
        <div class="stat-card-content">
            <span class="stat-card-label">Borradores</span>
            <span class="stat-card-value">{{ $counts['borrador'] ?? 0 }}</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="banco-stat-icon banco-stat-icon--evaluacion">
            <i class="fas fa-magnifying-glass" aria-hidden="true"></i>
        </div>
        <div class="stat-card-content">
            <span class="stat-card-label">En evaluación</span>
            <span class="stat-card-value">{{ $counts['en_evaluacion'] ?? 0 }}</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="banco-stat-icon banco-stat-icon--aprobado">
            <i class="fas fa-check-circle" aria-hidden="true"></i>
        </div>
        <div class="stat-card-content">
            <span class="stat-card-label">Aprobados</span>
            <span class="stat-card-value">{{ $counts['aprobado'] ?? 0 }}</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="banco-stat-icon banco-stat-icon--ejecucion">
            <i class="fas fa-circle-play" aria-hidden="true"></i>
        </div>
        <div class="stat-card-content">
            <span class="stat-card-label">En ejecución</span>
            <span class="stat-card-value">{{ $counts['en_ejecucion'] ?? 0 }}</span>
        </div>
    </div>

</div>

{{-- ── Table card ───────────────────────────────────────────────────── --}}
<div class="ds-card" style="padding:0;overflow:hidden;">

    {{-- Toolbar --}}
    <form method="get" id="bancoFilterForm">
        <div class="table-toolbar" style="flex-wrap:wrap;gap:var(--space-3);">
            <div class="table-search">
                <i class="fas fa-search search-icon" aria-hidden="true"></i>
                <input
                    type="text"
                    id="bancoSearch"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Buscar por título o código…"
                    autocomplete="off"
                    aria-label="Buscar proyectos"
                >
                <input type="hidden" name="estado" id="estadoInput" value="{{ request('estado') }}">
            </div>

            <div class="table-toolbar-chips" style="display:flex;gap:6px;flex-wrap:wrap;flex:1;align-items:center;">
                <button type="button" class="ds-chip {{ !request('estado') ? 'active' : '' }}" data-estado="">
                    Todos <span class="chip-count">({{ $total }})</span>
                </button>
                @foreach([
                    'borrador'      => 'Borrador',
                    'en_evaluacion' => 'Evaluación',
                    'aprobado'      => 'Aprobado',
                    'rechazado'     => 'Rechazado',
                    'en_ejecucion'  => 'En ejecución',
                    'cerrado'       => 'Cerrado',
                    'suspendido'    => 'Suspendido',
                ] as $val => $label)
                    @if(($counts[$val] ?? 0) > 0 || request('estado') === $val)
                    <button
                        type="button"
                        class="ds-chip {{ request('estado') === $val ? 'active' : '' }}"
                        data-estado="{{ $val }}"
                    >{{ $label }} <span class="chip-count">({{ $counts[$val] ?? 0 }})</span></button>
                    @endif
                @endforeach
            </div>

            <div class="table-actions">
                <a href="{{ route('banco.export.excel') }}" class="ds-btn ds-btn--secondary ds-btn--sm" title="Exportar a Excel">
                    <i class="fas fa-file-excel" aria-hidden="true"></i> Exportar
                </a>
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="ds-table banco-table" role="table">
            <thead>
                <tr>
                    <th scope="col" style="width:120px;">Código</th>
                    <th scope="col">Título</th>
                    <th scope="col">Línea / Área</th>
                    <th scope="col">Estado</th>
                    <th scope="col" style="width:105px;">Registro</th>
                    <th scope="col" style="width:1%;" aria-label="Acciones"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($proyectos as $p)
                <tr>
                    <td>
                        <a href="{{ route('banco.show', $p) }}" class="uc-hero-code">{{ $p->codigo }}</a>
                    </td>
                    <td>
                        <span style="font-weight:var(--font-semibold);color:var(--neutral-900);">
                            {{ \Illuminate\Support\Str::limit($p->titulo, 75) }}
                        </span>
                        @if($p->area_facultad)
                            <br><span class="banco-faculty-badge">{{ $p->area_facultad }}</span>
                        @endif
                    </td>
                    <td>
                        <span class="cell-muted">{{ $p->linea_investigacion ?: '—' }}</span>
                    </td>
                    <td>
                        <span class="ds-badge uc-estado--{{ $p->estado }}"
                              aria-label="Estado: {{ ucwords(str_replace('_', ' ', $p->estado)) }}">
                            {{ ucwords(str_replace('_', ' ', $p->estado)) }}
                        </span>
                    </td>
                    <td class="cell-mono">{{ $p->fecha_registro?->format('d/m/Y') }}</td>
                    <td class="cell-actions">
                        <div class="row-actions">
                            <a href="{{ route('banco.show', $p) }}"
                               class="action-btn action-btn--view"
                               title="Ver ficha"
                               aria-label="Ver {{ $p->codigo }}">
                                <i class="fas fa-eye" aria-hidden="true"></i>
                            </a>
                            <a href="{{ route('banco.edit', $p) }}"
                               class="action-btn action-btn--edit"
                               title="Editar"
                               aria-label="Editar {{ $p->codigo }}">
                                <i class="fas fa-pencil" aria-hidden="true"></i>
                            </a>
                            <div class="dropdown">
                                <button class="action-btn" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false"
                                        aria-label="Más acciones para {{ $p->codigo }}">
                                    <i class="fas fa-ellipsis-vertical" aria-hidden="true"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <form action="{{ route('banco.estado', $p) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="estado" value="en_evaluacion">
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-magnifying-glass me-2" style="color:var(--info);" aria-hidden="true"></i>
                                                Enviar a evaluación
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="{{ route('banco.estado', $p) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="estado" value="aprobado">
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-check me-2" style="color:var(--success);" aria-hidden="true"></i>
                                                Aprobar
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="{{ route('banco.estado', $p) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="estado" value="rechazado">
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-times me-2" style="color:var(--danger);" aria-hidden="true"></i>
                                                Rechazar
                                            </button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('banco.destroy', $p) }}" method="POST"
                                              onsubmit="return confirm('¿Eliminar {{ addslashes($p->codigo) }}? Esta acción no se puede deshacer.');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-trash-alt me-2" aria-hidden="true"></i>
                                                Eliminar
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state" style="padding:48px 24px;">
                            <div class="empty-state-icon">
                                <i class="fas fa-database" aria-hidden="true"></i>
                            </div>
                            <p class="empty-state-text">No hay proyectos registrados</p>
                            @if(request('q') || request('estado'))
                                <p class="empty-state-subtext">Intenta con otros filtros de búsqueda</p>
                                <a href="{{ route('banco.index') }}" class="ds-btn ds-btn--ghost ds-btn--sm">
                                    Limpiar filtros
                                </a>
                            @else
                                <a href="{{ route('banco.create') }}" class="ds-btn ds-btn--primary ds-btn--sm">
                                    <i class="fas fa-plus" aria-hidden="true"></i> Crear primer registro
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($proyectos->hasPages())
    <div style="padding:var(--space-4) var(--space-5);border-top:1px solid var(--neutral-100);">
        {{ $proyectos->appends(request()->query())->links() }}
    </div>
    @endif

</div>

@endsection

@push('scripts')
<script>
(function () {
    // Chip filter → update hidden estado input → submit form
    document.querySelectorAll('.table-toolbar-chips .ds-chip').forEach(function (chip) {
        chip.addEventListener('click', function () {
            document.querySelectorAll('.table-toolbar-chips .ds-chip')
                    .forEach(function (c) { c.classList.remove('active'); });
            this.classList.add('active');
            document.getElementById('estadoInput').value = this.dataset.estado;
            document.getElementById('bancoFilterForm').submit();
        });
    });

    // Debounced live search — submits after 400ms of idle typing
    var searchTimer;
    var searchInput = document.getElementById('bancoSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function () {
                document.getElementById('bancoFilterForm').submit();
            }, 400);
        });
    }
}());
</script>
@endpush
