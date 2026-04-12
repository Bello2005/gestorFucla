@extends('layouts.main')

@section('title', 'Catálogos')

@section('breadcrumbs')
    <span class="breadcrumb-current">Catálogos</span>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Catálogos</h1>
        <p class="page-subtitle">Administración de programas, tipos de proyecto y líneas de investigación</p>
    </div>
</div>

{{-- Flash messages --}}
@if(session('success'))
    <div class="ds-alert ds-alert--success" style="margin-bottom:var(--space-4);" role="alert">
        <i class="fas fa-circle-check" aria-hidden="true"></i>
        {{ session('success') }}
    </div>
@endif

{{-- ── Tabs ──────────────────────────────────────────────────────────── --}}
<div class="uc-tabs" role="tablist" aria-label="Catálogos">
    <button type="button" class="uc-tabs__btn uc-tabs__btn--active"
            data-tab="prog" role="tab" aria-selected="true">
        <i class="fas fa-building-columns" aria-hidden="true"></i>
        Programas
        <span class="chip-count" style="margin-left:4px;">({{ $programas->count() }})</span>
    </button>
    <button type="button" class="uc-tabs__btn"
            data-tab="tip" role="tab" aria-selected="false">
        <i class="fas fa-tags" aria-hidden="true"></i>
        Tipos de proyecto
        <span class="chip-count" style="margin-left:4px;">({{ $tipos->count() }})</span>
    </button>
    <button type="button" class="uc-tabs__btn"
            data-tab="lin" role="tab" aria-selected="false">
        <i class="fas fa-flask" aria-hidden="true"></i>
        Líneas de investigación
        <span class="chip-count" style="margin-left:4px;">({{ $lineas->count() }})</span>
    </button>
</div>

{{-- ══════════════════════════════════════════════════════════════════════
     Tab: Programas
     ══════════════════════════════════════════════════════════════════════ --}}
<div id="panel-prog" class="tab-panel">

    <div class="ds-card" style="padding:0;overflow:hidden;">
        <div class="ds-card-header" style="padding:var(--space-4) var(--space-5);">
            <h3 class="ds-card-title">Programas / Departamentos</h3>
        </div>

        <div class="table-responsive">
            <table class="ds-table" role="table">
                <thead>
                    <tr>
                        <th scope="col">Nombre</th>
                        <th scope="col">Facultad</th>
                        <th scope="col" style="width:90px;">Estado</th>
                        <th scope="col" style="width:1%;" aria-label="Acciones"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($programas as $p)
                    <tr>
                        <td>
                            <form action="{{ route('catalogos.programas.update', $p) }}"
                                  method="POST" id="prog-form-{{ $p->id }}"
                                  style="display:flex;gap:var(--space-2);align-items:center;">
                                @csrf @method('PUT')
                                <input type="hidden" name="activo" value="{{ $p->activo ? 1 : 0 }}">
                                <input type="hidden" name="facultad" value="{{ $p->facultad }}" id="prog-fac-hidden-{{ $p->id }}">
                                <input type="text" name="nombre"
                                       value="{{ $p->nombre }}"
                                       class="ds-input ds-input--inline"
                                       required
                                       aria-label="Nombre del programa">
                                <button type="submit"
                                        class="action-btn action-btn--edit"
                                        title="Guardar nombre"
                                        aria-label="Guardar {{ $p->nombre }}">
                                    <i class="fas fa-check" aria-hidden="true"></i>
                                </button>
                            </form>
                        </td>
                        <td>
                            <form action="{{ route('catalogos.programas.update', $p) }}"
                                  method="POST"
                                  style="display:flex;gap:var(--space-2);align-items:center;">
                                @csrf @method('PUT')
                                <input type="hidden" name="nombre" value="{{ $p->nombre }}">
                                <input type="hidden" name="activo" value="{{ $p->activo ? 1 : 0 }}">
                                <input type="text" name="facultad"
                                       value="{{ $p->facultad }}"
                                       class="ds-input ds-input--inline"
                                       placeholder="Facultad (opcional)"
                                       aria-label="Facultad del programa">
                                <button type="submit"
                                        class="action-btn action-btn--edit"
                                        title="Guardar facultad"
                                        style="opacity:0.5;"
                                        aria-label="Guardar facultad de {{ $p->nombre }}">
                                    <i class="fas fa-check" aria-hidden="true"></i>
                                </button>
                            </form>
                        </td>
                        <td>
                            <form action="{{ route('catalogos.programas.update', $p) }}" method="POST">
                                @csrf @method('PUT')
                                <input type="hidden" name="nombre" value="{{ $p->nombre }}">
                                <input type="hidden" name="facultad" value="{{ $p->facultad }}">
                                <input type="hidden" name="activo" value="0">
                                <label class="catalogo-toggle" title="{{ $p->activo ? 'Activo' : 'Inactivo' }}">
                                    <input type="checkbox" name="activo" value="1"
                                           {{ $p->activo ? 'checked' : '' }}
                                           onchange="this.form.submit()"
                                           aria-label="Activo/Inactivo">
                                    <span class="catalogo-toggle__label">{{ $p->activo ? 'Activo' : 'Inactivo' }}</span>
                                </label>
                            </form>
                        </td>
                        <td class="cell-actions">
                            <form action="{{ route('catalogos.programas.destroy', $p) }}"
                                  method="POST"
                                  onsubmit="return confirm('¿Eliminar el programa «{{ addslashes($p->nombre) }}»?');"
                                  style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="action-btn action-btn--delete"
                                        title="Eliminar"
                                        aria-label="Eliminar {{ $p->nombre }}">
                                    <i class="fas fa-trash-alt" aria-hidden="true"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">
                            <div class="empty-state" style="padding:32px;">
                                <p class="empty-state-text">Sin programas registrados</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Add row --}}
        <div style="padding:var(--space-4) var(--space-5);border-top:1px solid var(--neutral-100);">
            <form action="{{ route('catalogos.programas.store') }}" method="POST"
                  style="display:flex;gap:var(--space-3);flex-wrap:wrap;align-items:flex-end;">
                @csrf
                <div style="flex:2;min-width:160px;">
                    <label class="ds-label" for="prog_nombre" style="font-size:var(--text-xs);">Nombre</label>
                    <input type="text" name="nombre" id="prog_nombre" class="ds-input"
                           required placeholder="Nombre del programa…">
                </div>
                <div style="flex:1;min-width:120px;">
                    <label class="ds-label" for="prog_facultad" style="font-size:var(--text-xs);">Facultad</label>
                    <input type="text" name="facultad" id="prog_facultad" class="ds-input"
                           placeholder="Opcional">
                </div>
                <div>
                    <button type="submit" class="ds-btn ds-btn--primary">
                        <i class="fas fa-plus" aria-hidden="true"></i> Agregar
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════════════
     Tab: Tipos de Proyecto
     ══════════════════════════════════════════════════════════════════════ --}}
<div id="panel-tip" class="tab-panel tab-panel--hidden">

    <div class="ds-card" style="padding:0;overflow:hidden;">
        <div class="ds-card-header" style="padding:var(--space-4) var(--space-5);">
            <h3 class="ds-card-title">Tipos de proyecto</h3>
        </div>

        <div class="table-responsive">
            <table class="ds-table" role="table">
                <thead>
                    <tr>
                        <th scope="col">Nombre</th>
                        <th scope="col" style="width:90px;">Estado</th>
                        <th scope="col" style="width:1%;" aria-label="Acciones"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tipos as $t)
                    <tr>
                        <td>
                            <form action="{{ route('catalogos.tipos.update', $t) }}"
                                  method="POST" id="tipo-form-{{ $t->id }}"
                                  style="display:flex;gap:var(--space-2);align-items:center;">
                                @csrf @method('PUT')
                                <input type="hidden" name="activo" value="{{ $t->activo ? 1 : 0 }}">
                                <input type="text" name="nombre"
                                       value="{{ $t->nombre }}"
                                       class="ds-input ds-input--inline"
                                       required
                                       aria-label="Nombre del tipo">
                                <button type="submit"
                                        class="action-btn action-btn--edit"
                                        title="Guardar"
                                        aria-label="Guardar {{ $t->nombre }}">
                                    <i class="fas fa-check" aria-hidden="true"></i>
                                </button>
                            </form>
                        </td>
                        <td>
                            <form action="{{ route('catalogos.tipos.update', $t) }}" method="POST">
                                @csrf @method('PUT')
                                <input type="hidden" name="nombre" value="{{ $t->nombre }}">
                                <input type="hidden" name="activo" value="0">
                                <label class="catalogo-toggle" title="{{ $t->activo ? 'Activo' : 'Inactivo' }}">
                                    <input type="checkbox" name="activo" value="1"
                                           {{ $t->activo ? 'checked' : '' }}
                                           onchange="this.form.submit()"
                                           aria-label="Activo/Inactivo">
                                    <span class="catalogo-toggle__label">{{ $t->activo ? 'Activo' : 'Inactivo' }}</span>
                                </label>
                            </form>
                        </td>
                        <td class="cell-actions">
                            <form action="{{ route('catalogos.tipos.destroy', $t) }}"
                                  method="POST"
                                  onsubmit="return confirm('¿Eliminar «{{ addslashes($t->nombre) }}»?');"
                                  style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="action-btn action-btn--delete"
                                        title="Eliminar"
                                        aria-label="Eliminar {{ $t->nombre }}">
                                    <i class="fas fa-trash-alt" aria-hidden="true"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3">
                            <div class="empty-state" style="padding:32px;">
                                <p class="empty-state-text">Sin tipos registrados</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Add row --}}
        <div style="padding:var(--space-4) var(--space-5);border-top:1px solid var(--neutral-100);">
            <form action="{{ route('catalogos.tipos.store') }}" method="POST"
                  style="display:flex;gap:var(--space-3);flex-wrap:wrap;align-items:flex-end;">
                @csrf
                <div style="flex:1;min-width:200px;">
                    <label class="ds-label" for="tipo_nombre" style="font-size:var(--text-xs);">Nombre del tipo</label>
                    <input type="text" name="nombre" id="tipo_nombre" class="ds-input"
                           required placeholder="Ej. Investigación aplicada…">
                </div>
                <div>
                    <button type="submit" class="ds-btn ds-btn--primary">
                        <i class="fas fa-plus" aria-hidden="true"></i> Agregar
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════════════
     Tab: Líneas de Investigación
     ══════════════════════════════════════════════════════════════════════ --}}
<div id="panel-lin" class="tab-panel tab-panel--hidden">

    <div class="ds-card" style="padding:0;overflow:hidden;">
        <div class="ds-card-header" style="padding:var(--space-4) var(--space-5);">
            <h3 class="ds-card-title">Líneas de investigación</h3>
        </div>

        <div class="table-responsive">
            <table class="ds-table" role="table">
                <thead>
                    <tr>
                        <th scope="col">Nombre</th>
                        <th scope="col">Área</th>
                        <th scope="col" style="width:90px;">Estado</th>
                        <th scope="col" style="width:1%;" aria-label="Acciones"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lineas as $l)
                    <tr>
                        <td>
                            <form action="{{ route('catalogos.lineas.update', $l) }}"
                                  method="POST" id="linea-form-{{ $l->id }}"
                                  style="display:flex;gap:var(--space-2);align-items:center;">
                                @csrf @method('PUT')
                                <input type="hidden" name="activo" value="{{ $l->activo ? 1 : 0 }}">
                                <input type="text" name="nombre"
                                       value="{{ $l->nombre }}"
                                       class="ds-input ds-input--inline"
                                       required
                                       aria-label="Nombre de la línea">
                                <button type="submit"
                                        class="action-btn action-btn--edit"
                                        title="Guardar"
                                        aria-label="Guardar {{ $l->nombre }}">
                                    <i class="fas fa-check" aria-hidden="true"></i>
                                </button>
                            </form>
                        </td>
                        <td>
                            <form action="{{ route('catalogos.lineas.update', $l) }}"
                                  method="POST" style="display:flex;gap:var(--space-2);align-items:center;">
                                @csrf @method('PUT')
                                <input type="hidden" name="nombre" value="{{ $l->nombre }}">
                                <input type="hidden" name="activo" value="{{ $l->activo ? 1 : 0 }}">
                                <input type="text" name="area"
                                       value="{{ $l->area }}"
                                       class="ds-input ds-input--inline"
                                       placeholder="Área (opcional)"
                                       aria-label="Área de la línea">
                                <button type="submit"
                                        class="action-btn action-btn--edit"
                                        title="Guardar área"
                                        style="opacity:0.5;"
                                        aria-label="Guardar área de {{ $l->nombre }}">
                                    <i class="fas fa-check" aria-hidden="true"></i>
                                </button>
                            </form>
                        </td>
                        <td>
                            <form action="{{ route('catalogos.lineas.update', $l) }}" method="POST">
                                @csrf @method('PUT')
                                <input type="hidden" name="nombre" value="{{ $l->nombre }}">
                                <input type="hidden" name="area" value="{{ $l->area }}">
                                <input type="hidden" name="activo" value="0">
                                <label class="catalogo-toggle" title="{{ $l->activo ? 'Activo' : 'Inactivo' }}">
                                    <input type="checkbox" name="activo" value="1"
                                           {{ $l->activo ? 'checked' : '' }}
                                           onchange="this.form.submit()"
                                           aria-label="Activo/Inactivo">
                                    <span class="catalogo-toggle__label">{{ $l->activo ? 'Activo' : 'Inactivo' }}</span>
                                </label>
                            </form>
                        </td>
                        <td class="cell-actions">
                            <form action="{{ route('catalogos.lineas.destroy', $l) }}"
                                  method="POST"
                                  onsubmit="return confirm('¿Eliminar «{{ addslashes($l->nombre) }}»?');"
                                  style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="action-btn action-btn--delete"
                                        title="Eliminar"
                                        aria-label="Eliminar {{ $l->nombre }}">
                                    <i class="fas fa-trash-alt" aria-hidden="true"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">
                            <div class="empty-state" style="padding:32px;">
                                <p class="empty-state-text">Sin líneas registradas</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Add row --}}
        <div style="padding:var(--space-4) var(--space-5);border-top:1px solid var(--neutral-100);">
            <form action="{{ route('catalogos.lineas.store') }}" method="POST"
                  style="display:flex;gap:var(--space-3);flex-wrap:wrap;align-items:flex-end;">
                @csrf
                <div style="flex:2;min-width:160px;">
                    <label class="ds-label" for="linea_nombre" style="font-size:var(--text-xs);">Nombre</label>
                    <input type="text" name="nombre" id="linea_nombre" class="ds-input"
                           required placeholder="Nombre de la línea…">
                </div>
                <div style="flex:1;min-width:120px;">
                    <label class="ds-label" for="linea_area" style="font-size:var(--text-xs);">Área</label>
                    <input type="text" name="area" id="linea_area" class="ds-input"
                           placeholder="Opcional">
                </div>
                <div>
                    <button type="submit" class="ds-btn ds-btn--primary">
                        <i class="fas fa-plus" aria-hidden="true"></i> Agregar
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
(function () {
    var tabs   = document.querySelectorAll('.uc-tabs .uc-tabs__btn');
    var panels = {
        prog: document.getElementById('panel-prog'),
        tip:  document.getElementById('panel-tip'),
        lin:  document.getElementById('panel-lin'),
    };

    tabs.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.getAttribute('data-tab');

            tabs.forEach(function (b) {
                b.classList.remove('uc-tabs__btn--active');
                b.setAttribute('aria-selected', 'false');
            });
            btn.classList.add('uc-tabs__btn--active');
            btn.setAttribute('aria-selected', 'true');

            Object.values(panels).forEach(function (p) {
                p.classList.add('tab-panel--hidden');
            });
            if (panels[id]) panels[id].classList.remove('tab-panel--hidden');
        });
    });

    // Focus the save button on inline-input Enter keypress
    document.querySelectorAll('.ds-input--inline').forEach(function (input) {
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                var form = input.closest('form');
                if (form) form.submit();
            }
            if (e.key === 'Escape') {
                input.value = input.defaultValue;
                input.blur();
            }
        });
    });
}());
</script>
@endpush
