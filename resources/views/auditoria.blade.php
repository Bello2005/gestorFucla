
@extends('layouts.main')

@section('title', 'Auditoría')

@section('content')
<div class="content-wrapper">
    <div class="row align-items-center mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Auditoría</h1>
            </div>
        </div>
    </div>
    <div class="table-container" data-aos="fade-up" data-aos-delay="200">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <!-- Aquí DataTables inyecta el selector de 'Mostrar x registros' -->
            </div>
            <form method="GET" action="" class="d-flex align-items-center">
                <label for="order" class="form-label mb-0 me-2">Ordenar por:</label>
                <select name="order" id="order" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="desc" {{ request('order', 'desc') == 'desc' ? 'selected' : '' }}>Más recientes primero</option>
                    <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>Más antiguos primero</option>
                </select>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle table-nowrap" id="auditoriaTable">
                <thead>
                    <tr>
                        <th class="text-nowrap" style="min-width: 60px;">ID</th>
                        <th class="text-nowrap" style="min-width: 120px;">Tabla</th>
                        <th class="text-nowrap" style="min-width: 110px;">Operación</th>
                        <th class="text-nowrap" style="min-width: 110px;">ID Registro</th>
                        <th class="text-nowrap" style="min-width: 120px;">Usuario</th>
                        <th class="text-nowrap" style="min-width: 180px;">Correo</th>
                        <th class="text-nowrap" style="min-width: 110px;">IP</th>
                        <th class="text-nowrap" style="min-width: 130px;">Fecha</th>
                        <th class="text-nowrap" style="min-width: 220px;">Valores antiguos</th>
                        <th class="text-nowrap" style="min-width: 220px;">Valores nuevos</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    @php
                        $usuario = null;
                        $correo = null;
                        $ip = null;
                        if ($log->operation === 'INSERT' && $log->new_values) {
                            $json = json_decode($log->new_values, true);
                            $usuario = $json['usuario'] ?? '';
                            $correo = $json['correo'] ?? '';
                            $ip = $json['ip'] ?? '';
                        } elseif ($log->operation === 'UPDATE' && $log->new_values) {
                            $json = json_decode($log->new_values, true);
                            $usuario = $json['usuario'] ?? '';
                            $correo = $json['correo'] ?? '';
                            $ip = $json['ip'] ?? '';
                        } elseif ($log->operation === 'DELETE' && $log->old_values) {
                            $json = json_decode($log->old_values, true);
                            $usuario = $json['usuario'] ?? '';
                            $correo = $json['correo'] ?? '';
                            $ip = $json['ip'] ?? '';
                        }
                    @endphp
                    <tr class="fade-in">
                        <td>{{ $log->id }}</td>
                        <td>{{ $log->table_name }}</td>
                        <td>{{ $log->operation }}</td>
                        <td>{{ $log->record_id }}</td>
                        <td>{{ $usuario }}</td>
                        <td>{{ $correo }}</td>
                        <td>{{ $ip ?? $log->ip_address }}</td>
                        <td>{{ $log->created_at }}</td>
                        <td><pre class="mb-0" style="max-width:300px;white-space:pre-wrap;word-break:break-all;font-size:0.85em;">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre></td>
                        <td><pre class="mb-0" style="max-width:300px;white-space:pre-wrap;word-break:break-all;font-size:0.85em;">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-folder-open"></i>
                                <h5 class="mt-3">No hay registros de auditoría</h5>
                                <p class="text-muted">Aún no se han realizado acciones que generen auditoría</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
