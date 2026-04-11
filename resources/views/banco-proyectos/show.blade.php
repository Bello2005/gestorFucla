@extends('layouts.main')

@section('title', $bancoProyecto->codigo)

@section('breadcrumbs')
    <a href="{{ route('banco.index') }}">Banco de Proyectos</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">{{ $bancoProyecto->codigo }}</span>
@endsection

@section('content')
<div class="banco-page">
    <div class="page-header">
        <div>
            <p class="uc-hero-code mb-1">{{ $bancoProyecto->codigo }}</p>
            <h1 class="page-title">{{ $bancoProyecto->titulo }}</h1>
            <p class="page-subtitle">Estado: <strong>{{ $bancoProyecto->estado }}</strong>
                @if($bancoProyecto->duracion_meses) · {{ $bancoProyecto->duracion_meses }} meses @endif
                @if($bancoProyecto->presupuesto_estimado) · ${{ number_format($bancoProyecto->presupuesto_estimado, 0, ',', '.') }} @endif
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('banco.edit', $bancoProyecto) }}" class="ds-btn ds-btn--primary">Editar</a>
            <form action="{{ route('banco.estado', $bancoProyecto) }}" method="post" class="d-flex gap-2 align-items-center">
                @csrf
                @method('PATCH')
                <select name="estado" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach(['borrador','en_evaluacion','aprobado','rechazado','en_ejecucion','cerrado','suspendido'] as $st)
                        <option value="{{ $st }}" @selected($bancoProyecto->estado===$st)>{{ $st }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <div class="uc-tabs" role="tablist">
        <button type="button" class="uc-tabs__btn uc-tabs__btn--active" data-tab="ficha">Ficha técnica</button>
        <button type="button" class="uc-tabs__btn" data-tab="anexos">Documentos</button>
        <button type="button" class="uc-tabs__btn" data-tab="cert">Certificado</button>
        <button type="button" class="uc-tabs__btn" data-tab="hist">Historial</button>
    </div>

    <div id="panel-ficha" class="ds-card tab-panel">
        <div class="row g-4">
            <div class="col-lg-6">
                <h3 class="uc-card-title">Formulación</h3>
                <p class="small text-muted">Resumen</p>
                <p>{{ $bancoProyecto->resumen_ejecutivo ?: '—' }}</p>
                <p class="small text-muted mt-3">Problema</p>
                <p>{{ $bancoProyecto->problema_necesidad ?: '—' }}</p>
            </div>
            <div class="col-lg-6">
                <h3 class="uc-card-title">Equipo</h3>
                <ul>
                    @forelse($bancoProyecto->autores ?? [] as $a)
                        <li>{{ $a }}</li>
                    @empty
                        <li>—</li>
                    @endforelse
                </ul>
                <p><strong>Tutor:</strong> {{ $bancoProyecto->tutor_director ?: '—' }}</p>
            </div>
        </div>
    </div>

    <div id="panel-anexos" class="ds-card tab-panel d-none">
        <h3 class="uc-card-title">Anexos</h3>
        <ul class="list-unstyled">
            @foreach($bancoProyecto->anexos->where('is_current', true) as $ax)
                <li class="mb-2 d-flex justify-content-between align-items-center">
                    <span>{{ $ax->nombre_original }} <span class="badge bg-secondary">v{{ $ax->version }}</span></span>
                    <a href="{{ route('banco.anexos.download', [$bancoProyecto, $ax]) }}" class="ds-btn ds-btn--sm ds-btn--secondary">Descargar</a>
                </li>
            @endforeach
        </ul>
        <form action="{{ route('banco.anexos.store', $bancoProyecto) }}" method="post" enctype="multipart/form-data" class="uc-upload-zone mt-3">
            @csrf
            <p class="mb-2"><strong>Subir anexo</strong></p>
            <select name="tipo_anexo" class="form-select mb-2" required>
                @foreach(['documento_proyecto','presupuesto','carta_aval','cronograma','imagen_plano','soporte_adicional','certificado_cumplimiento'] as $tp)
                    <option value="{{ $tp }}">{{ $tp }}</option>
                @endforeach
            </select>
            <input type="file" name="archivo" required class="form-control">
            <button type="submit" class="ds-btn ds-btn--primary mt-2">Subir</button>
        </form>
    </div>

    <div id="panel-cert" class="ds-card tab-panel d-none">
        <h3 class="uc-card-title">Certificado de cumplimiento</h3>
        @if($bancoProyecto->certificado_cumplimiento)
            <p>Archivo cargado. Fecha: {{ optional($bancoProyecto->certificado_fecha)->format('d/m/Y') ?? '—' }}</p>
            <form action="{{ route('banco.certificado.destroy', $bancoProyecto) }}" method="post" onsubmit="return confirm('¿Eliminar certificado?');">
                @csrf
                @method('DELETE')
                <button class="ds-btn ds-btn--danger ds-btn--sm">Eliminar</button>
            </form>
        @else
            <form action="{{ route('banco.certificado.store', $bancoProyecto) }}" method="post" enctype="multipart/form-data" class="row g-2">
                @csrf
                <div class="col-12">
                    <input type="file" name="certificado" accept="application/pdf" required class="form-control">
                </div>
                <div class="col-md-6">
                    <input type="date" name="certificado_fecha" class="form-control" placeholder="Fecha">
                </div>
                <div class="col-12">
                    <textarea name="certificado_observaciones" class="ds-textarea" placeholder="Observaciones"></textarea>
                </div>
                <div class="col-12">
                    <button class="ds-btn ds-btn--primary" type="submit">Guardar certificado</button>
                </div>
            </form>
        @endif
    </div>

    <div id="panel-hist" class="ds-card tab-panel d-none">
        <h3 class="uc-card-title">Historial</h3>
        <div class="uc-timeline">
            @foreach($historial as $h)
                <div class="uc-timeline__item uc-timeline__item--major">
                    <div class="uc-timeline__dot"></div>
                    <div class="uc-timeline__meta">{{ $h->created_at->format('d/m/Y H:i') }} — {{ $h->user_name }}</div>
                    <div class="uc-timeline__body">{{ $h->descripcion ?? $h->accion }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.uc-tabs__btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var id = btn.getAttribute('data-tab');
        document.querySelectorAll('.uc-tabs__btn').forEach(function(b) { b.classList.remove('uc-tabs__btn--active'); });
        btn.classList.add('uc-tabs__btn--active');
        document.querySelectorAll('.tab-panel').forEach(function(p) { p.classList.add('d-none'); });
        document.getElementById('panel-' + id).classList.remove('d-none');
    });
});
</script>
@endpush
@endsection
