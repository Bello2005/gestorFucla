@extends('layouts.main')

@section('title', 'Registro de Auditoría')

@section('content')
<div id="content">
    <!-- Contenido principal -->
    <div class="content-wrapper">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Registro de Auditoría</h2>
                    <div>
                        <a href="{{ route('audit.export', request()->all()) }}" class="btn btn-success">
                            <i class="fas fa-file-export"></i> Exportar
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filtros -->
                    <form action="{{ route('audit.index') }}" method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label for="table" class="form-label">Tabla</label>
                                <select name="table" id="table" class="form-select">
                                    <option value="">Todas</option>
                                    @foreach($tables as $table)
                                        <option value="{{ $table }}" {{ request('table') == $table ? 'selected' : '' }}>
                                            {{ ucfirst($table) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label for="operation" class="form-label">Operación</label>
                                <select name="operation" id="operation" class="form-select">
                                    <option value="">Todas</option>
                                    @foreach($operations as $operation)
                                        <option value="{{ $operation }}" {{ request('operation') == $operation ? 'selected' : '' }}>
                                            {{ ucfirst($operation) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label for="date_from" class="form-label">Fecha Desde</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                            </div>

                            <div class="col-md-2">
                                <label for="date_to" class="form-label">Fecha Hasta</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Tabla de registros -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tabla</th>
                                    <th>Operación</th>
                                    <th>Usuario</th>
                                    <th>Dirección IP</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($audits as $audit)
                                    <tr>
                                        <td>{{ $audit->id }}</td>
                                        <td>{{ ucfirst($audit->table_name) }}</td>
                                        <td>
                                            <span class="badge {{ $audit->operation == 'DELETE' ? 'bg-danger' : ($audit->operation == 'INSERT' ? 'bg-success' : 'bg-primary') }}">
                                                {{ $audit->operation }}
                                            </span>
                                        </td>
                                        <td>{{ $audit->user_name }}</td>
                                        <td>{{ $audit->ip_address }}</td>
                                        <td>{{ $audit->created_at->format('d/m/Y H:i:s') }}</td>
                                        <td>
                                            <a href="{{ route('audit.show', $audit) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Ver Detalles
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-center">
                        {{ $audits->appends(request()->all())->links() }}
                    </div>
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
    .table th {
        background-color: #f8f9fa;
    }
</style>
@endpush