@extends('layouts.main')

@section('title', 'Gestionar Solicitudes de Acceso')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Solicitudes de Acceso</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Razón</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                            <tr>
                                <td>{{ $request->name }}</td>
                                <td>{{ $request->email }}</td>
                                <td>{{ $request->phone ?? 'N/A' }}</td>
                                <td>
                                    <span class="d-inline-block text-truncate" style="max-width: 200px;" 
                                          title="{{ $request->reason }}">
                                        {{ $request->reason }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $request->status === 'approved' ? 'success' : 
                                        ($request->status === 'rejected' ? 'danger' : 'warning')
                                    }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($request->status === 'pending')
                                        <button type="button" class="btn btn-sm btn-success me-1" 
                                                onclick="approveRequest({{ $request->id }})">
                                            Aprobar
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#rejectModal" 
                                                data-request-id="{{ $request->id }}">
                                            Rechazar
                                        </button>
                                    @else
                                        <span class="text-muted">
                                            {{ $request->reviewed_at->format('d/m/Y H:i') }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    No hay solicitudes pendientes
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Rechazo -->
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
                    <div class="mb-3">
                        <label for="admin_comment" class="form-label">Razón del rechazo</label>
                        <textarea class="form-control" id="admin_comment" name="admin_comment" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Rechazar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function approveRequest(id) {
    if (confirm('¿Estás seguro de que quieres aprobar esta solicitud?')) {
        fetch(`/access-requests/${id}/approve`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        }).then(response => {
            return response.json().then(data => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    throw new Error(data.error || data.message || 'Error al aprobar la solicitud');
                }
            });
        }).catch(error => {
            console.error('Error:', error);
            alert(error.message || 'Error al aprobar la solicitud');
        });
    }
}

document.getElementById('rejectModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const requestId = button.dataset.requestId;
    const form = this.querySelector('#rejectForm');
    form.action = `/access-requests/${requestId}/reject`;
});
</script>
@endsection