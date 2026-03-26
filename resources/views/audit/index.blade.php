@extends('layouts.main')

@section('title', 'Registro de Auditoria')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="breadcrumb-link">Dashboard</a>
    <i class="fas fa-chevron-right breadcrumb-sep"></i>
    <span class="breadcrumb-current">Auditoria</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Registro de Auditoria</h1>
        <p class="page-subtitle">Historial de cambios y actividad del sistema</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('audit.export', request()->all()) }}" class="ds-btn ds-btn--secondary">
            <i class="fas fa-file-export"></i> Exportar
        </a>
    </div>
</div>

<!-- Filters -->
<div class="ds-card" style="margin-bottom: 24px;">
    <div class="ds-card-body">
        <form action="{{ route('audit.index') }}" method="GET">
            <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; align-items: end;">
                <div class="form-group">
                    <label class="ds-label" for="table">Tabla</label>
                    <select name="table" id="table" class="ds-select">
                        <option value="">Todas</option>
                        @foreach($tables as $table)
                            <option value="{{ $table }}" {{ request('table') == $table ? 'selected' : '' }}>{{ ucfirst($table) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="ds-label" for="operation">Operacion</label>
                    <select name="operation" id="operation" class="ds-select">
                        <option value="">Todas</option>
                        @foreach($operations as $operation)
                            <option value="{{ $operation }}" {{ request('operation') == $operation ? 'selected' : '' }}>{{ ucfirst($operation) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="ds-label" for="date_from">Fecha Desde</label>
                    <input type="date" class="ds-input" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="form-group">
                    <label class="ds-label" for="date_to">Fecha Hasta</label>
                    <input type="date" class="ds-input" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div>
                    <button type="submit" class="ds-btn ds-btn--primary" style="width: 100%;">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Audit Table -->
<div class="ds-card">
    <div class="table-responsive">
        <table class="ds-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tabla</th>
                    <th>Operacion</th>
                    <th>Usuario</th>
                    <th>Direccion IP</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($audits as $audit)
                    <tr>
                        <td><span style="font-family: var(--font-mono); font-size: var(--text-sm); color: var(--slate-500);">#{{ $audit->id }}</span></td>
                        <td><span style="font-size: var(--text-sm); font-weight: 500;">{{ ucfirst($audit->table_name) }}</span></td>
                        <td>
                            @php
                                $opClass = match($audit->operation) {
                                    'DELETE' => 'ds-badge--cerrado',
                                    'INSERT' => 'ds-badge--activo',
                                    default => 'ds-badge--inactivo'
                                };
                            @endphp
                            <span class="ds-badge {{ $opClass }}">
                                <span class="ds-badge-dot"></span>
                                {{ $audit->operation }}
                            </span>
                        </td>
                        <td><span style="font-size: var(--text-sm);">{{ $audit->user_name }}</span></td>
                        <td><span style="font-family: var(--font-mono); font-size: var(--text-sm); color: var(--slate-500);">{{ $audit->ip_address }}</span></td>
                        <td><span style="font-size: var(--text-sm);">{{ $audit->created_at->format('d/m/Y H:i:s') }}</span></td>
                        <td>
                            <a href="{{ route('audit.show', $audit) }}" class="action-btn action-btn--view" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="padding: 16px 24px; display: flex; justify-content: center;">
        {{ $audits->appends(request()->all())->links() }}
    </div>
</div>
@endsection

@push('styles')
<style>
    @media (max-width: 768px) {
        div[style*="grid-template-columns: repeat(5"] {
            grid-template-columns: 1fr 1fr !important;
        }
    }
</style>
@endpush
