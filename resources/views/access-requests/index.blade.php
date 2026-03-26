@extends('layouts.main')

@section('title', 'Solicitudes de Acceso')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="breadcrumb-link">Dashboard</a>
    <i class="fas fa-chevron-right breadcrumb-sep"></i>
    <span class="breadcrumb-current">Solicitudes de Acceso</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Solicitudes de Acceso</h1>
        <p class="page-subtitle">Gestiona las solicitudes de acceso al sistema</p>
    </div>
</div>

<div class="ds-card">
    <div class="table-responsive">
        <table class="ds-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Telefono</th>
                    <th>Razon</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                    <tr>
                        <td><span style="font-weight: 500; color: var(--slate-900);">{{ $request->name }}</span></td>
                        <td><span style="font-size: var(--text-sm); color: var(--slate-600);">{{ $request->email }}</span></td>
                        <td><span style="font-size: var(--text-sm); color: var(--slate-600);">{{ $request->phone ?? 'N/A' }}</span></td>
                        <td>
                            <span style="font-size: var(--text-sm); color: var(--slate-600); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; max-width: 200px;" title="{{ $request->reason }}">
                                {{ $request->reason }}
                            </span>
                        </td>
                        <td>
                            @php
                                $statusClass = match($request->status) {
                                    'approved' => 'ds-badge--activo',
                                    'rejected' => 'ds-badge--cerrado',
                                    default => 'ds-badge--inactivo'
                                };
                            @endphp
                            <span class="ds-badge {{ $statusClass }}">
                                <span class="ds-badge-dot"></span>
                                {{ ucfirst($request->status) }}
                            </span>
                        </td>
                        <td><span style="font-size: var(--text-sm);">{{ $request->created_at->format('d/m/Y H:i') }}</span></td>
                        <td>
                            @if($request->status === 'pending')
                                <div class="action-buttons">
                                    <button type="button" class="ds-btn ds-btn--success ds-btn--sm" onclick="approveRequest({{ $request->id }})">
                                        <i class="fas fa-check"></i> Aprobar
                                    </button>
                                    <button type="button" class="ds-btn ds-btn--danger ds-btn--sm" data-bs-toggle="modal" data-bs-target="#rejectModal" data-request-id="{{ $request->id }}">
                                        <i class="fas fa-times"></i> Rechazar
                                    </button>
                                </div>
                            @else
                                <span style="font-size: var(--text-xs); color: var(--slate-400);">
                                    {{ $request->reviewed_at->format('d/m/Y H:i') }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state" style="padding: 48px 24px;">
                                <div class="empty-state-icon"><i class="fas fa-inbox"></i></div>
                                <p class="empty-state-text">No hay solicitudes pendientes</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rechazar Solicitud</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="ds-label" for="admin_comment">Razon del rechazo</label>
                        <textarea class="ds-textarea" id="admin_comment" name="admin_comment" rows="3" required placeholder="Indique la razon del rechazo..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="ds-btn ds-btn--ghost" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="ds-btn ds-btn--danger"><i class="fas fa-times"></i> Rechazar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function approveRequest(id) {
    if (confirm('¿Esta seguro de que quiere aprobar esta solicitud?')) {
        fetch('/access-requests/' + id + '/approve', {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        }).then(function(response) {
            return response.json().then(function(data) {
                if (response.ok) {
                    window.location.reload();
                } else {
                    throw new Error(data.error || data.message || 'Error al aprobar');
                }
            });
        }).catch(function(error) {
            alert(error.message || 'Error al aprobar la solicitud');
        });
    }
}

document.getElementById('rejectModal').addEventListener('show.bs.modal', function(event) {
    var button = event.relatedTarget;
    var requestId = button.dataset.requestId;
    var form = this.querySelector('#rejectForm');
    form.action = '/access-requests/' + requestId + '/reject';
});
</script>
@endpush
