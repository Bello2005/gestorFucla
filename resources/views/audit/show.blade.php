@extends('layouts.main')

@section('title', 'Detalle de Auditoria')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="breadcrumb-link">Dashboard</a>
    <i class="fas fa-chevron-right breadcrumb-sep"></i>
    <a href="{{ route('audit.index') }}" class="breadcrumb-link">Auditoria</a>
    <i class="fas fa-chevron-right breadcrumb-sep"></i>
    <span class="breadcrumb-current">Registro #{{ $audit->id }}</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Detalle del Registro de Auditoria</h1>
        <p class="page-subtitle">Registro #{{ $audit->id }} - {{ $audit->created_at->format('d/m/Y H:i:s') }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('audit.index') }}" class="ds-btn ds-btn--ghost">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
    <!-- General Info -->
    <div class="ds-card">
        <div class="ds-card-header">
            <h3 class="ds-card-title"><i class="fas fa-info-circle" style="color: var(--primary); margin-right: 8px;"></i> Informacion General</h3>
        </div>
        <div class="ds-card-body">
            <div class="detail-field">
                <span class="detail-label">ID</span>
                <span class="detail-value" style="font-family: var(--font-mono);">#{{ $audit->id }}</span>
            </div>
            <div class="detail-field">
                <span class="detail-label">Tabla</span>
                <span class="detail-value">{{ ucfirst($audit->table_name) }}</span>
            </div>
            <div class="detail-field">
                <span class="detail-label">Operacion</span>
                <span class="detail-value">
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
                </span>
            </div>
            <div class="detail-field">
                <span class="detail-label">ID del Registro</span>
                <span class="detail-value" style="font-family: var(--font-mono);">{{ $audit->record_id }}</span>
            </div>
        </div>
    </div>

    <!-- Metadata -->
    <div class="ds-card">
        <div class="ds-card-header">
            <h3 class="ds-card-title"><i class="fas fa-server" style="color: var(--primary); margin-right: 8px;"></i> Metadata</h3>
        </div>
        <div class="ds-card-body">
            <div class="detail-field">
                <span class="detail-label">Usuario</span>
                <span class="detail-value">{{ $audit->user_name }}</span>
            </div>
            <div class="detail-field">
                <span class="detail-label">Direccion IP</span>
                <span class="detail-value" style="font-family: var(--font-mono);">{{ $audit->ip_address }}</span>
            </div>
            <div class="detail-field">
                <span class="detail-label">Navegador</span>
                <span class="detail-value" style="font-size: var(--text-xs); word-break: break-all;">{{ $audit->user_agent }}</span>
            </div>
            <div class="detail-field">
                <span class="detail-label">Fecha</span>
                <span class="detail-value">{{ $audit->created_at->format('d/m/Y H:i:s') }}</span>
            </div>
        </div>
    </div>
</div>

@if($audit->old_values || $audit->new_values)
    <div class="ds-card" style="margin-top: 24px;">
        <div class="ds-card-header">
            <h3 class="ds-card-title"><i class="fas fa-exchange-alt" style="color: var(--primary); margin-right: 8px;"></i> Cambios Realizados</h3>
        </div>
        <div class="table-responsive">
            <table class="ds-table">
                <thead>
                    <tr>
                        <th>Campo</th>
                        <th>Valor Anterior</th>
                        <th>Valor Nuevo</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $oldValues = is_array($audit->old_values) ? $audit->old_values : (json_decode($audit->old_values, true) ?? []);
                        $newValues = is_array($audit->new_values) ? $audit->new_values : (json_decode($audit->new_values, true) ?? []);
                        if (isset($oldValues['cargar_evidencias']) && is_string($oldValues['cargar_evidencias'])) {
                            $oldValues['cargar_evidencias'] = json_decode($oldValues['cargar_evidencias'], true) ?? [];
                        }
                        if (isset($newValues['cargar_evidencias']) && is_string($newValues['cargar_evidencias'])) {
                            $newValues['cargar_evidencias'] = json_decode($newValues['cargar_evidencias'], true) ?? [];
                        }
                        $allFields = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));
                        sort($allFields);
                    @endphp
                    @foreach($allFields as $field)
                        @php
                            $changed = isset($oldValues[$field], $newValues[$field]) && $oldValues[$field] !== $newValues[$field];
                        @endphp
                        <tr>
                            <td><span style="font-weight: 500; font-size: var(--text-sm);">{{ ucfirst(str_replace('_', ' ', $field)) }}</span></td>
                            <td style="{{ $changed ? 'background: var(--warning-50);' : '' }}">
                                <span style="font-size: var(--text-sm); color: var(--slate-600); word-break: break-all;">
                                    @if($field === 'cargar_evidencias')
                                        @if(isset($oldValues[$field]) && is_array($oldValues[$field]))
                                            {{ implode(', ', array_map('basename', $oldValues[$field])) }}
                                        @else
                                            N/A
                                        @endif
                                    @else
                                        {{ isset($oldValues[$field]) ? (is_array($oldValues[$field]) ? json_encode($oldValues[$field]) : $oldValues[$field]) : 'N/A' }}
                                    @endif
                                </span>
                            </td>
                            <td style="{{ $changed ? 'background: var(--success-50);' : '' }}">
                                <span style="font-size: var(--text-sm); color: var(--slate-600); word-break: break-all;">
                                    @if($field === 'cargar_evidencias')
                                        @if(isset($newValues[$field]) && is_array($newValues[$field]))
                                            {{ implode(', ', array_map('basename', $newValues[$field])) }}
                                        @elseif(isset($newValues[$field]) && is_string($newValues[$field]))
                                            @php $evidencias = json_decode($newValues[$field], true) ?? []; @endphp
                                            {{ is_array($evidencias) ? implode(', ', array_map('basename', $evidencias)) : 'N/A' }}
                                        @else
                                            N/A
                                        @endif
                                    @else
                                        {{ is_array($newValues[$field] ?? null) ? json_encode($newValues[$field]) : ($newValues[$field] ?? 'N/A') }}
                                    @endif
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection

@push('styles')
<style>
    .detail-field {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid var(--slate-100);
    }
    .detail-field:last-child { border-bottom: none; }
    .detail-label {
        font-size: var(--text-sm);
        font-weight: 600;
        color: var(--slate-500);
    }
    .detail-value {
        font-size: var(--text-sm);
        color: var(--slate-900);
        text-align: right;
    }

    @media (max-width: 768px) {
        div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endpush
