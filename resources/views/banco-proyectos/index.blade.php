@extends('layouts.main')

@section('title', 'Banco de Proyectos')

@section('breadcrumbs')
    <span class="breadcrumb-current">Banco de Proyectos</span>
@endsection

@section('content')
<div class="banco-page page-header">
    <div>
        <h1 class="page-title">Banco de Proyectos</h1>
        <p class="page-subtitle">Repositorio institucional de proyectos académicos</p>
    </div>
    <a href="{{ route('banco.create') }}" class="ds-btn ds-btn--primary">
        <i class="fas fa-plus"></i> Nuevo registro
    </a>
</div>

<div class="stat-cards-grid" style="margin-bottom: 1.5rem;">
    <div class="stat-card stat-card--primary">
        <div class="stat-card-content">
            <span class="stat-card-label">Total</span>
            <span class="stat-card-value">{{ $total }}</span>
        </div>
    </div>
    @foreach(['borrador'=>'Borradores','en_evaluacion'=>'En evaluación','aprobado'=>'Aprobados','en_ejecucion'=>'En ejecución'] as $k=>$lbl)
        <div class="stat-card">
            <div class="stat-card-content">
                <span class="stat-card-label">{{ $lbl }}</span>
                <span class="stat-card-value">{{ $counts[$k] ?? 0 }}</span>
            </div>
        </div>
    @endforeach
</div>

<div class="ds-card uc-dt-wrap">
    <form method="get" class="table-toolbar" style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--neutral-200); display:flex; gap:1rem; flex-wrap:wrap; align-items:center;">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por título o código…" class="ds-input" style="max-width:280px;">
        <select name="estado" class="form-select form-select-sm" style="max-width:220px;" onchange="this.form.submit()">
            <option value="">Todos los estados</option>
            @foreach(['borrador','en_evaluacion','aprobado','rechazado','en_ejecucion','cerrado','suspendido'] as $st)
                <option value="{{ $st }}" @selected(request('estado')===$st)>{{ str_replace('_',' ', $st) }}</option>
            @endforeach
        </select>
        <button type="submit" class="ds-btn ds-btn--secondary ds-btn--sm">Filtrar</button>
    </form>

    <div class="table-responsive">
        <table class="ds-table table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Título</th>
                    <th>Estado</th>
                    <th>Registro</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($proyectos as $p)
                    <tr>
                        <td><a href="{{ route('banco.show', $p) }}" class="banco-page uc-hero-code">{{ $p->codigo }}</a></td>
                        <td><strong>{{ \Illuminate\Support\Str::limit($p->titulo, 80) }}</strong></td>
                        <td><span class="ds-badge ds-badge--info">{{ $p->estado }}</span></td>
                        <td class="uc-text-mono">{{ $p->fecha_registro?->format('d/m/Y') }}</td>
                        <td class="text-end">
                            <a href="{{ route('banco.show', $p) }}" class="ds-btn ds-btn--ghost ds-btn--sm">Ver</a>
                            <a href="{{ route('banco.edit', $p) }}" class="ds-btn ds-btn--secondary ds-btn--sm">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-5 text-muted">No hay proyectos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
