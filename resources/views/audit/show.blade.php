@extends('layouts.main')

@section('title', 'Detalle de Auditoría')

@section('content')
<div id="content">
    <!-- Contenido principal -->
    <div class="content-wrapper">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Detalles del Registro de Auditoría</h2>
                    <a href="{{ route('audit.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h4>Información General</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%">ID:</th>
                                    <td>{{ $audit->id }}</td>
                                </tr>
                                <tr>
                                    <th>Tabla:</th>
                                    <td>{{ ucfirst($audit->table_name) }}</td>
                                </tr>
                                <tr>
                                    <th>Operación:</th>
                                    <td>
                                        <span class="badge {{ $audit->operation == 'DELETE' ? 'bg-danger' : ($audit->operation == 'INSERT' ? 'bg-success' : 'bg-primary') }}">
                                            {{ $audit->operation }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>ID del Registro:</th>
                                    <td>{{ $audit->record_id }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h4>Metadata</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%">Usuario:</th>
                                    <td>{{ $audit->user_name }}</td>
                                </tr>
                                <tr>
                                    <th>Dirección IP:</th>
                                    <td>{{ $audit->ip_address }}</td>
                                </tr>
                                <tr>
                                    <th>Navegador:</th>
                                    <td>{{ $audit->user_agent }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha:</th>
                                    <td>{{ $audit->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($audit->old_values || $audit->new_values)
                        <div class="row">
                            <div class="col-md-12">
                                <h4>Cambios Realizados</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
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
                                                
                                                // Asegurarse de que cargar_evidencias sea siempre un array
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
                                                <tr>
                                                    <td>{{ ucfirst(str_replace('_', ' ', $field)) }}</td>
                                                    <td class="{{ isset($oldValues[$field], $newValues[$field]) && $oldValues[$field] !== $newValues[$field] ? 'table-warning' : '' }}">
                                                        @if ($field === 'cargar_evidencias')
                                                            @if (isset($oldValues[$field]) && is_array($oldValues[$field]))
                                                                {{ implode(', ', array_map('basename', $oldValues[$field])) }}
                                                            @else
                                                                N/A
                                                            @endif
                                                        @else
                                                            {{ isset($oldValues[$field]) ? (is_array($oldValues[$field]) ? json_encode($oldValues[$field]) : $oldValues[$field]) : 'N/A' }}
                                                        @endif
                                                    </td>
                                                    <td class="{{ isset($oldValues[$field], $newValues[$field]) && $oldValues[$field] !== $newValues[$field] ? 'table-success' : '' }}">
                                                        @if ($field === 'cargar_evidencias')
                                                            @if (isset($newValues[$field]) && is_array($newValues[$field]))
                                                                {{ implode(', ', array_map('basename', $newValues[$field])) }}
                                                            @elseif (isset($newValues[$field]) && is_string($newValues[$field]))
                                                                @php
                                                                    $evidencias = json_decode($newValues[$field], true) ?? [];
                                                                @endphp
                                                                {{ is_array($evidencias) ? implode(', ', array_map('basename', $evidencias)) : 'N/A' }}
                                                            @else
                                                                N/A
                                                            @endif
                                                        @else
                                                            {{ is_array($newValues[$field]) ? json_encode($newValues[$field]) : ($newValues[$field] ?? 'N/A') }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .card-header {
        background-color: #f8f9fa;
    }
    .table-warning {
        background-color: #fff3cd !important;
    }
    .table-success {
        background-color: #d1e7dd !important;
    }
</style>
@endpush