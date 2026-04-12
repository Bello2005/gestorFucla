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

    <div class="stat-card stat-card--info">
        <div class="stat-card-icon">
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
    <div class="stat-card stat-card--success">
        <div class="stat-card-icon">
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
    <div class="stat-card stat-card--warning">
        <div class="stat-card-icon">
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
        <div class="stat-card-icon">
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

{{-- All styles now live in resources/css/pages/dashboard.css --}}
