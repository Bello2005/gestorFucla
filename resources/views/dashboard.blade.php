@extends('layouts.main')

@section('title', 'Dashboard')

@section('breadcrumbs')
    <span class="breadcrumb-current">Dashboard</span>
@endsection

@section('content')

{{-- ── Page header ─────────────────────────────────────────────────── --}}
<div class="page-header">
    <div>
        <h1 class="page-title">Bienvenido, {{ $usuarioNombre }}</h1>
        <p class="page-subtitle">{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('proyectos.create') }}" class="ds-btn ds-btn--primary">
            <i class="fas fa-plus"></i> Nuevo Proyecto
        </a>
    </div>
</div>

{{-- ── KPI Metric cards ─────────────────────────────────────────────── --}}
<div class="stat-cards-grid">

    {{-- Total Proyectos (blue) --}}
    @php
        $deltaTotalRaw  = $stats['total'] - $statsAnterior['total'];
        $deltaActivoRaw = $stats['activos'] - $statsAnterior['activos'];
        $deltaValorRaw  = $stats['valor_total'] - $statsAnterior['valor_total'];
        $deltaEntRaw    = $stats['entidades'] - $statsAnterior['entidades'];
    @endphp

    <div class="stat-card stat-card--blue">
        <div class="stat-card-icon stat-card-icon--blue">
            <i class="fas fa-folder-open"></i>
        </div>
        <div class="stat-card-content">
            <span class="stat-card-label">Total Proyectos</span>
            <span class="stat-card-value">{{ $stats['total'] }}</span>
            <span class="stat-card-delta {{ $deltaTotalRaw >= 0 ? 'delta--up' : 'delta--down' }}">
                <i class="fas fa-arrow-{{ $deltaTotalRaw >= 0 ? 'up' : 'down' }}"></i>
                {{ abs($deltaTotalRaw) }} vs mes anterior
            </span>
        </div>
    </div>

    {{-- Activos (green) --}}
    <div class="stat-card stat-card--green">
        <div class="stat-card-icon stat-card-icon--green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-card-content">
            <span class="stat-card-label">Activos</span>
            <span class="stat-card-value">{{ $stats['activos'] }}</span>
            <span class="stat-card-delta {{ $deltaActivoRaw >= 0 ? 'delta--up' : 'delta--down' }}">
                <i class="fas fa-arrow-{{ $deltaActivoRaw >= 0 ? 'up' : 'down' }}"></i>
                {{ abs($deltaActivoRaw) }} vs mes anterior
            </span>
        </div>
    </div>

    {{-- Valor Total (amber) --}}
    <div class="stat-card stat-card--amber">
        <div class="stat-card-icon stat-card-icon--amber">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-card-content">
            <span class="stat-card-label">Valor Total</span>
            <span
                class="stat-card-value stat-card-value--mono"
                title="{{ formatCOPFull($stats['valor_total']) }}"
            >{{ formatCOP($stats['valor_total']) }}</span>
            @php $deltaValorLabel = formatCOP(abs($deltaValorRaw)); @endphp
            <span class="stat-card-delta {{ $deltaValorRaw >= 0 ? 'delta--up' : 'delta--down' }}">
                <i class="fas fa-arrow-{{ $deltaValorRaw >= 0 ? 'up' : 'down' }}"></i>
                {{ $deltaValorLabel }} vs mes anterior
            </span>
        </div>
    </div>

    {{-- Entidades (slate) --}}
    <div class="stat-card stat-card--slate">
        <div class="stat-card-icon stat-card-icon--slate">
            <i class="fas fa-building"></i>
        </div>
        <div class="stat-card-content">
            <span class="stat-card-label">Entidades</span>
            <span class="stat-card-value">{{ $stats['entidades'] }}</span>
            <span class="stat-card-delta {{ $deltaEntRaw >= 0 ? 'delta--up' : 'delta--down' }}">
                <i class="fas fa-arrow-{{ $deltaEntRaw >= 0 ? 'up' : 'down' }}"></i>
                {{ abs($deltaEntRaw) }} vs mes anterior
            </span>
        </div>
    </div>

</div>

{{-- ── Status summary — full-width 3-column row ────────────────────── --}}
<div class="status-row">
    @foreach($resumenEstados as $est)
    <div class="status-row-item">
        <div class="status-row-bar">
            <div
                class="status-row-fill status-row-fill--{{ $est['variant'] }}"
                style="width: {{ $stats['total'] > 0 ? round($est['count'] / $stats['total'] * 100) : 0 }}%"
            ></div>
        </div>
        <div class="status-row-info">
            <span class="status-row-label">
                <i class="fas {{ $est['icon'] }}"></i>
                {{ $est['label'] }}
            </span>
            <span class="status-row-count">{{ $est['count'] }}</span>
        </div>
    </div>
    @endforeach
</div>

{{-- ── Main grid: Recent Projects + Quick Actions ──────────────────── --}}
<div class="dashboard-grid">

    {{-- Recent Projects ──────────────────────────────────────────── --}}
    <div class="ds-card">
        <div class="ds-card-header">
            <h3 class="ds-card-title">Proyectos Recientes</h3>
            <a href="{{ route('proyectos.index') }}" class="ds-btn ds-btn--ghost ds-btn--sm">
                Ver todos <i class="fas fa-arrow-right" style="margin-left: 4px;"></i>
            </a>
        </div>
        <div class="ds-card-body" style="padding: 0;">
            @if($recientes->count() > 0)
                <div class="recent-projects-list">
                    @foreach($recientes as $proyecto)
                        <a
                            href="{{ route('proyectos.show', $proyecto->id) }}"
                            class="recent-project-item"
                            title="{{ $proyecto->nombre_del_proyecto }}"
                        >
                            <div class="recent-project-info">
                                <div class="recent-project-icon">
                                    <i class="fas fa-folder"></i>
                                </div>
                                <div class="recent-project-text">
                                    <span class="recent-project-name">
                                        {{ \Illuminate\Support\Str::limit($proyecto->nombre_del_proyecto, 55) }}
                                    </span>
                                    <span class="recent-project-entity">
                                        {{ $proyecto->entidad_contratante }}
                                    </span>
                                </div>
                            </div>
                            <div class="recent-project-meta">
                                <x-estado-badge :estado="$proyecto->estado" />
                                @if(($proyecto->valor_total ?? 0) == 0)
                                    <span class="recent-project-value recent-project-value--zero"
                                          title="Sin presupuesto registrado">Sin presupuesto</span>
                                @else
                                    <span
                                        class="recent-project-value"
                                        title="{{ formatCOPFull($proyecto->valor_total) }}"
                                    >{{ formatCOP($proyecto->valor_total) }}</span>
                                @endif
                                <span class="recent-project-time">
                                    {{ $proyecto->updated_at?->diffForHumans() ?? $proyecto->created_at?->diffForHumans() }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="empty-state" style="padding: 48px 24px;">
                    <div class="empty-state-icon">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <p class="empty-state-text">No hay proyectos registrados</p>
                    <a href="{{ route('proyectos.create') }}" class="ds-btn ds-btn--primary ds-btn--sm">
                        <i class="fas fa-plus"></i> Crear Proyecto
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Quick Actions — contextual, no duplicates ────────────────── --}}
    <div class="ds-card">
        <div class="ds-card-header">
            <h3 class="ds-card-title">Acciones Rápidas</h3>
        </div>
        <div class="ds-card-body">
            <div class="quick-actions">

                {{-- Export Excel --}}
                <a href="{{ route('proyectos.export.excel') }}" class="quick-action-item">
                    <div class="quick-action-icon quick-action-icon--success">
                        <i class="fas fa-file-excel"></i>
                    </div>
                    <span class="quick-action-label">Exportar Excel</span>
                </a>

                {{-- Statistics --}}
                <a href="{{ route('estadistica') }}" class="quick-action-item">
                    <div class="quick-action-icon quick-action-icon--info">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <span class="quick-action-label">Estadísticas</span>
                </a>

                {{-- TODO: add route 'proyectos.vencimiento' when feature is built --}}
                <a href="{{ route('proyectos.index') }}" class="quick-action-item">
                    <div class="quick-action-icon quick-action-icon--warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <span class="quick-action-label">Por vencer</span>
                </a>

                {{-- TODO: add route 'admin.usuarios' or use 'users.index' --}}
                <a href="{{ route('users.index') }}" class="quick-action-item">
                    <div class="quick-action-icon quick-action-icon--slate">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <span class="quick-action-label quick-action-label--nowrap">Gestionar usuarios</span>
                </a>

            </div>
        </div>
    </div>

</div>

@endsection

@push('styles')
<style>
    /* ── KPI cards with color semantics ──────────────────────── */
    .stat-card-icon {
        width: 44px;
        height: 44px;
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .stat-card-icon--blue   { background: #EFF6FF; color: #2563EB; }
    .stat-card-icon--green  { background: var(--success-50); color: var(--success); }
    .stat-card-icon--amber  { background: var(--warning-50); color: var(--warning); }
    .stat-card-icon--slate  { background: var(--slate-100);  color: var(--slate-600); }

    .stat-card--blue   { border-top: 3px solid #2563EB; }
    .stat-card--green  { border-top: 3px solid var(--success); }
    .stat-card--amber  { border-top: 3px solid var(--warning); }
    .stat-card--slate  { border-top: 3px solid var(--slate-400); }

    .stat-card-label {
        font-size: var(--text-xs);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-weight: var(--font-semibold);
        color: var(--slate-500);
    }
    .stat-card-value {
        font-size: var(--text-3xl);
        font-weight: var(--font-bold);
        color: var(--slate-900);
        line-height: 1.1;
        margin: 2px 0 4px;
    }
    .stat-card-value--mono {
        font-family: var(--font-mono);
        font-size: var(--text-2xl);
    }

    /* Trend delta badge */
    .stat-card-delta {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: var(--font-medium);
        padding: 2px 6px;
        border-radius: var(--radius-full);
    }
    .delta--up   { background: var(--success-50); color: var(--success); }
    .delta--down { background: var(--danger-50);  color: var(--danger); }

    /* ── Status summary row ───────────────────────────────────── */
    .status-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-top: 16px;
    }
    .status-row-item {
        background: white;
        border: 1px solid var(--slate-200);
        border-radius: var(--radius-lg);
        padding: 16px 20px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .status-row-bar {
        height: 6px;
        background: var(--slate-100);
        border-radius: var(--radius-full);
        overflow: hidden;
    }
    .status-row-fill {
        height: 100%;
        border-radius: var(--radius-full);
        transition: width 600ms cubic-bezier(0.4, 0, 0.2, 1);
    }
    .status-row-fill--success { background: var(--success); }
    .status-row-fill--warning { background: var(--warning); }
    .status-row-fill--danger  { background: var(--danger); }
    .status-row-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .status-row-label {
        font-size: var(--text-sm);
        color: var(--slate-600);
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .status-row-count {
        font-family: var(--font-mono);
        font-size: var(--text-base);
        font-weight: var(--font-bold);
        color: var(--slate-900);
    }

    /* ── Dashboard grid ────────────────────────────────────────── */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 24px;
        margin-top: 24px;
    }

    /* ── Recent Projects list ─────────────────────────────────── */
    .recent-projects-list {
        display: flex;
        flex-direction: column;
    }
    .recent-project-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 24px;
        text-decoration: none;
        color: inherit;
        border-bottom: 1px solid var(--slate-100);
        transition: background var(--transition-fast) ease;
        gap: 12px;
    }
    .recent-project-item:last-child { border-bottom: none; }
    .recent-project-item:hover { background: var(--slate-50); }

    .recent-project-info {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
        flex: 1;
    }
    .recent-project-text {
        min-width: 0;
        display: flex;
        flex-direction: column;
    }
    .recent-project-icon {
        width: 36px;
        height: 36px;
        border-radius: var(--radius-md);
        background: var(--primary-50);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }
    .recent-project-name {
        display: block;
        font-weight: var(--font-medium);
        font-size: var(--text-sm);
        color: var(--slate-900);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .recent-project-entity {
        display: block;
        font-size: var(--text-xs);
        color: var(--slate-500);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .recent-project-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 4px;
        flex-shrink: 0;
    }
    .recent-project-value {
        font-family: var(--font-mono);
        font-size: var(--text-xs);
        font-weight: var(--font-semibold);
        color: var(--slate-700);
    }
    .recent-project-value--zero {
        color: var(--slate-500);
        font-family: inherit;
        font-weight: var(--font-medium);
        max-width: 9rem;
        text-align: right;
    }
    .recent-project-time {
        font-size: 11px;
        color: var(--slate-400);
    }

    /* ── Quick Actions grid ───────────────────────────────────── */
    .quick-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    .quick-action-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        border-radius: var(--radius-md);
        border: 1px solid var(--slate-200);
        text-decoration: none;
        color: var(--slate-700);
        transition: all var(--transition-fast) ease;
    }
    .quick-action-item:hover {
        border-color: var(--slate-300);
        background: var(--slate-50);
        color: var(--slate-900);
    }
    .quick-action-icon {
        width: 36px;
        height: 36px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }
    .quick-action-icon--success { background: var(--success-50); color: var(--success); }
    .quick-action-icon--info    { background: var(--info-50);    color: var(--info); }
    .quick-action-icon--warning { background: var(--warning-50); color: var(--warning); }
    .quick-action-icon--slate   { background: var(--slate-100);  color: var(--slate-600); }
    .quick-action-label {
        font-size: var(--text-sm);
        font-weight: var(--font-medium);
    }
    .quick-action-label--nowrap {
        white-space: nowrap;
    }

    /* ── Responsive ───────────────────────────────────────────── */
    @media (max-width: 1024px) {
        .status-row { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 768px) {
        .dashboard-grid { grid-template-columns: 1fr; }
        .status-row     { grid-template-columns: 1fr; }
        .quick-actions  { grid-template-columns: 1fr; }
        .recent-project-meta { flex-direction: row; align-items: center; gap: 8px; }
    }
</style>
@endpush
