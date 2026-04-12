@extends('layouts.main')

@section('title', 'Nuevo — Banco de Proyectos')

@section('breadcrumbs')
    <a href="{{ route('banco.index') }}">Banco de Proyectos</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Nuevo proyecto</span>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Nuevo proyecto académico</h1>
        <p class="page-subtitle">Complete la información; puede guardar como borrador en cualquier momento.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('banco.index') }}" class="ds-btn ds-btn--ghost">
            <i class="fas fa-arrow-left" aria-hidden="true"></i> Volver
        </a>
    </div>
</div>

{{-- Validation errors --}}
@if($errors->any())
    <div class="ds-alert ds-alert--danger" style="margin-bottom:var(--space-4);">
        <i class="fas fa-circle-exclamation" aria-hidden="true"></i>
        <div>
            <strong>Por favor corrija los siguientes errores:</strong>
            <ul style="margin:var(--space-2) 0 0;padding-left:var(--space-4);">
                @foreach($errors->all() as $error)
                    <li style="font-size:var(--text-sm);">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<form action="{{ route('banco.store') }}" method="POST" id="bancoCreateForm" novalidate>
    @csrf
    <input type="hidden" name="estado" value="borrador">

    {{-- ── Stepper ──────────────────────────────────────────────────── --}}
    <div class="ds-card" style="padding:var(--space-6) var(--space-6) 0;">
        <div class="uc-stepper" role="list" aria-label="Pasos del formulario">
            <div class="uc-stepper__item uc-stepper__item--active" id="step-indicator-0" role="listitem">
                <div class="uc-stepper__circle" aria-current="step">1</div>
                <span class="uc-stepper__label">Identificación</span>
                <span class="uc-stepper__sublabel">Datos básicos</span>
            </div>
            <div class="uc-stepper__item" id="step-indicator-1" role="listitem">
                <div class="uc-stepper__circle">2</div>
                <span class="uc-stepper__label">Formulación</span>
                <span class="uc-stepper__sublabel">Objetivos y alcance</span>
            </div>
            <div class="uc-stepper__item" id="step-indicator-2" role="listitem">
                <div class="uc-stepper__circle">3</div>
                <span class="uc-stepper__label">Recursos</span>
                <span class="uc-stepper__sublabel">Presupuesto y plazos</span>
            </div>
            <div class="uc-stepper__item" id="step-indicator-3" role="listitem">
                <div class="uc-stepper__circle">4</div>
                <span class="uc-stepper__label">Equipo</span>
                <span class="uc-stepper__sublabel">Responsables</span>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         Step 1 — Registro e Identificación
         ══════════════════════════════════════════════════════════════ --}}
    <div class="ds-card uc-step-panel uc-step-panel--active" id="step-panel-0" style="margin-top:var(--space-4);">
        <div class="ds-card-header">
            <h2 class="ds-card-title">Registro e identificación</h2>
        </div>
        <div class="ds-card-body" style="display:flex;flex-direction:column;gap:var(--space-4);">

            <div>
                <label class="ds-label" for="titulo">
                    Título del proyecto <span style="color:var(--danger);" aria-hidden="true">*</span>
                </label>
                <input type="text" name="titulo" id="titulo" class="ds-input @error('titulo') is-invalid @enderror"
                       required value="{{ old('titulo') }}"
                       placeholder="Título completo del proyecto de investigación…">
                @error('titulo')
                    <span class="ds-form-error">{{ $message }}</span>
                @enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
                <div>
                    <label class="ds-label" for="linea_investigacion">Línea de investigación</label>
                    <select name="linea_investigacion" id="linea_investigacion" class="ds-input">
                        <option value="">— Seleccionar —</option>
                        @foreach($lineas as $l)
                            <option value="{{ $l->nombre }}" @selected(old('linea_investigacion') === $l->nombre)>
                                {{ $l->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="ds-label" for="area_facultad">Área / Facultad</label>
                    <select name="area_facultad" id="area_facultad" class="ds-input">
                        <option value="">— Seleccionar —</option>
                        @foreach($programas as $pr)
                            <option value="{{ $pr->nombre }}" @selected(old('area_facultad') === $pr->nombre)>
                                {{ $pr->nombre }}{{ $pr->facultad ? ' (' . $pr->facultad . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="ds-label" for="tipo_proyecto">Tipo de proyecto</label>
                    <select name="tipo_proyecto" id="tipo_proyecto" class="ds-input">
                        <option value="">— Seleccionar —</option>
                        @foreach($tipos as $t)
                            <option value="{{ $t->nombre }}" @selected(old('tipo_proyecto') === $t->nombre)>
                                {{ $t->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="ds-label" for="convocatoria">Convocatoria / período</label>
                    <input type="text" name="convocatoria" id="convocatoria" class="ds-input"
                           value="{{ old('convocatoria') }}" placeholder="Ej. 2025-I">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
                <div>
                    <label class="ds-label" for="fecha_registro">Fecha de registro</label>
                    <input type="date" name="fecha_registro" id="fecha_registro" class="ds-input"
                           value="{{ old('fecha_registro', now()->format('Y-m-d')) }}">
                </div>
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         Step 2 — Formulación básica
         ══════════════════════════════════════════════════════════════ --}}
    <div class="ds-card uc-step-panel" id="step-panel-1" style="margin-top:var(--space-4);">
        <div class="ds-card-header">
            <h2 class="ds-card-title">Formulación básica</h2>
        </div>
        <div class="ds-card-body" style="display:flex;flex-direction:column;gap:var(--space-5);">

            @foreach([
                ['name' => 'resumen_ejecutivo',  'label' => 'Resumen ejecutivo', 'rows' => 4, 'limit' => 300, 'hint' => 'Síntesis de máximo 300 palabras del proyecto.'],
                ['name' => 'problema_necesidad', 'label' => 'Problema / Necesidad', 'rows' => 3, 'limit' => 200, 'hint' => 'Descripción del problema o necesidad que aborda el proyecto.'],
                ['name' => 'objetivo_general',   'label' => 'Objetivo general', 'rows' => 3, 'limit' => 250, 'hint' => 'Qué se propone lograr de manera general.'],
                ['name' => 'justificacion',       'label' => 'Justificación', 'rows' => 3, 'limit' => 200, 'hint' => 'Por qué es importante realizar este proyecto.'],
                ['name' => 'alcance',             'label' => 'Alcance', 'rows' => 2, 'limit' => 0,   'hint' => 'Límites y cobertura del proyecto.'],
            ] as $field)
            <div>
                <label class="ds-label" for="{{ $field['name'] }}">
                    {{ $field['label'] }}
                </label>
                @if($field['hint'])
                    <span class="ds-form-hint" style="margin-bottom:var(--space-1);display:block;">{{ $field['hint'] }}</span>
                @endif
                <textarea
                    name="{{ $field['name'] }}"
                    id="{{ $field['name'] }}"
                    class="ds-input"
                    rows="{{ $field['rows'] }}"
                    style="resize:vertical;"
                    @if($field['limit'] > 0) data-word-limit="{{ $field['limit'] }}" @endif
                >{{ old($field['name']) }}</textarea>
                @if($field['limit'] > 0)
                    <p class="word-counter" id="wc-{{ $field['name'] }}">0 / {{ $field['limit'] }} palabras</p>
                @endif
            </div>
            @endforeach

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
                <div>
                    <label class="ds-label" for="poblacion_objetivo">Población objetivo</label>
                    <textarea name="poblacion_objetivo" id="poblacion_objetivo" class="ds-input" rows="2"
                              style="resize:vertical;">{{ old('poblacion_objetivo') }}</textarea>
                </div>
                <div>
                    <label class="ds-label" for="cobertura_geografica">Cobertura geográfica</label>
                    <input type="text" name="cobertura_geografica" id="cobertura_geografica" class="ds-input"
                           value="{{ old('cobertura_geografica') }}" placeholder="Ej. Municipal, departamental…">
                </div>
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         Step 3 — Planificación y Recursos
         ══════════════════════════════════════════════════════════════ --}}
    <div class="ds-card uc-step-panel" id="step-panel-2" style="margin-top:var(--space-4);">
        <div class="ds-card-header">
            <h2 class="ds-card-title">Planificación y recursos</h2>
        </div>
        <div class="ds-card-body" style="display:flex;flex-direction:column;gap:var(--space-4);">

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:var(--space-4);">
                <div>
                    <label class="ds-label" for="presupuesto_estimado">Presupuesto estimado (COP)</label>
                    <input type="number" step="1" min="0" name="presupuesto_estimado" id="presupuesto_estimado"
                           class="ds-input" value="{{ old('presupuesto_estimado') }}" placeholder="0">
                </div>
                <div>
                    <label class="ds-label" for="cofinanciacion">Cofinanciación (COP)</label>
                    <input type="number" step="1" min="0" name="cofinanciacion" id="cofinanciacion"
                           class="ds-input" value="{{ old('cofinanciacion') }}" placeholder="0">
                </div>
                <div>
                    <label class="ds-label" for="duracion_meses">
                        Duración (meses) <span style="color:var(--danger);" aria-hidden="true">*</span>
                    </label>
                    <input type="number" min="1" max="120" name="duracion_meses" id="duracion_meses"
                           class="ds-input @error('duracion_meses') is-invalid @enderror"
                           required value="{{ old('duracion_meses') }}" placeholder="12">
                    @error('duracion_meses')
                        <span class="ds-form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div>
                <label class="ds-label" for="fuente_financiacion">Fuente de financiación</label>
                <input type="text" name="fuente_financiacion" id="fuente_financiacion" class="ds-input"
                       value="{{ old('fuente_financiacion') }}" placeholder="Ej. Recursos propios, entidad aliada, convocatoria…">
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         Step 4 — Equipo y Responsables
         ══════════════════════════════════════════════════════════════ --}}
    <div class="ds-card uc-step-panel" id="step-panel-3" style="margin-top:var(--space-4);">
        <div class="ds-card-header">
            <h2 class="ds-card-title">Equipo y responsables</h2>
        </div>
        <div class="ds-card-body" style="display:flex;flex-direction:column;gap:var(--space-4);">

            <div>
                <label class="ds-label">Autores / Investigadores</label>
                <span class="ds-form-hint" style="display:block;margin-bottom:var(--space-2);">
                    Agrega un autor por fila.
                </span>
                <div id="autoresContainer" style="display:flex;flex-direction:column;gap:var(--space-2);">
                    @foreach(old('autores', ['']) as $autor)
                    <div class="autor-row">
                        <input type="text" name="autores[]" class="ds-input"
                               value="{{ $autor }}" placeholder="Nombre completo del autor…">
                        <button type="button" class="ds-btn ds-btn--ghost ds-btn--sm remove-autor"
                                aria-label="Eliminar autor" style="flex-shrink:0;">
                            <i class="fas fa-times" aria-hidden="true"></i>
                        </button>
                    </div>
                    @endforeach
                </div>
                <button type="button" id="addAutor"
                        class="ds-btn ds-btn--ghost ds-btn--sm"
                        style="margin-top:var(--space-2);">
                    <i class="fas fa-plus" aria-hidden="true"></i> Agregar autor
                </button>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
                <div>
                    <label class="ds-label" for="tutor_director">Tutor / Director</label>
                    <input type="text" name="tutor_director" id="tutor_director" class="ds-input"
                           value="{{ old('tutor_director') }}">
                </div>
                <div>
                    <label class="ds-label" for="programa_departamento">Programa / Departamento</label>
                    <select name="programa_departamento" id="programa_departamento" class="ds-input">
                        <option value="">— Seleccionar —</option>
                        @foreach($programas as $pr)
                            <option value="{{ $pr->nombre }}" @selected(old('programa_departamento') === $pr->nombre)>
                                {{ $pr->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="ds-label" for="entidad_aliada">Entidad aliada</label>
                    <input type="text" name="entidad_aliada" id="entidad_aliada" class="ds-input"
                           value="{{ old('entidad_aliada') }}">
                </div>
                <div>
                    <label class="ds-label" for="evaluador_asignado">Evaluador asignado</label>
                    <input type="text" name="evaluador_asignado" id="evaluador_asignado" class="ds-input"
                           value="{{ old('evaluador_asignado') }}">
                </div>
            </div>

        </div>
    </div>

    {{-- ── Navigation bar ───────────────────────────────────────────── --}}
    <div class="ds-card" style="margin-top:var(--space-4);padding:var(--space-4) var(--space-5);">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:var(--space-3);">
            <button type="button" id="prevBtn"
                    class="ds-btn ds-btn--ghost"
                    style="display:none;"
                    aria-label="Paso anterior">
                <i class="fas fa-arrow-left" aria-hidden="true"></i> Anterior
            </button>

            {{-- Draft save (always available, submits immediately) --}}
            <button type="submit" name="_draft" value="1"
                    class="ds-btn ds-btn--secondary"
                    style="margin-left:auto;">
                <i class="fas fa-floppy-disk" aria-hidden="true"></i> Guardar borrador
            </button>

            <button type="button" id="nextBtn" class="ds-btn ds-btn--primary">
                Siguiente <i class="fas fa-arrow-right" aria-hidden="true"></i>
            </button>
        </div>
    </div>

</form>

@endsection

@push('scripts')
<script>
(function () {

    var TOTAL = 4;
    var current = 0;

    var panels     = Array.from(document.querySelectorAll('.uc-step-panel'));
    var indicators = Array.from(document.querySelectorAll('[id^="step-indicator-"]'));
    var prevBtn    = document.getElementById('prevBtn');
    var nextBtn    = document.getElementById('nextBtn');

    // If validation failed (server), start on step that has errors
    @if($errors->any())
    // Try to detect which step has the error
    (function () {
        var step0Fields = ['titulo', 'linea_investigacion', 'area_facultad', 'tipo_projeto', 'convocatoria', 'fecha_registro'];
        var step1Fields = ['resumen_ejecutivo', 'problema_necesidad', 'objetivo_general', 'justificacion', 'alcance', 'poblacion_objetivo', 'cobertura_geografica'];
        var step2Fields = ['presupuesto_estimado', 'cofinanciacion', 'duracion_meses', 'fuente_financiacion'];
        var keys = Object.keys({!! json_encode($errors->keys()) !!});
        if (keys.some(function(k){ return step2Fields.includes(k); })) { current = 2; }
        else if (keys.some(function(k){ return step1Fields.includes(k); })) { current = 1; }
        else { current = 0; }
    }());
    @endif

    function goTo(n) {
        panels[current].classList.remove('uc-step-panel--active');
        indicators[current].classList.remove('uc-stepper__item--active');
        if (n > current) {
            indicators[current].classList.add('uc-stepper__item--done');
            indicators[current].querySelector('.uc-stepper__circle').innerHTML = '<i class="fas fa-check" aria-hidden="true"></i>';
        } else {
            indicators[current].classList.remove('uc-stepper__item--done');
            indicators[current].querySelector('.uc-stepper__circle').textContent = current + 1;
        }

        current = n;
        panels[current].classList.add('uc-step-panel--active');
        indicators[current].classList.add('uc-stepper__item--active');
        indicators[current].classList.remove('uc-stepper__item--done');
        indicators[current].querySelector('.uc-stepper__circle').textContent = current + 1;
        indicators[current].querySelector('.uc-stepper__circle').setAttribute('aria-current', 'step');

        prevBtn.style.display = current === 0 ? 'none' : '';
        if (current === TOTAL - 1) {
            nextBtn.innerHTML = 'Guardar <i class="fas fa-check" aria-hidden="true"></i>';
            nextBtn.type = 'submit';
        } else {
            nextBtn.innerHTML = 'Siguiente <i class="fas fa-arrow-right" aria-hidden="true"></i>';
            nextBtn.type = 'button';
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Init display
    goTo(current);

    nextBtn.addEventListener('click', function () {
        if (current < TOTAL - 1) { goTo(current + 1); }
        // On last step, type='submit' so form submits naturally
    });

    prevBtn.addEventListener('click', function () {
        if (current > 0) goTo(current - 1);
    });

    // ── Word counters ──────────────────────────────────────────────
    document.querySelectorAll('textarea[data-word-limit]').forEach(function (ta) {
        var counter = document.getElementById('wc-' + ta.id);
        if (!counter) return;
        function update() {
            var words = ta.value.trim() === '' ? 0 : ta.value.trim().split(/\s+/).length;
            var limit = parseInt(ta.dataset.wordLimit);
            counter.textContent = words + ' / ' + limit + ' palabras';
            counter.className = 'word-counter' +
                (words > limit ? ' word-counter--over' :
                 words > limit * 0.85 ? ' word-counter--warn' : '');
        }
        ta.addEventListener('input', update);
        update();
    });

    // ── Autores repeater ──────────────────────────────────────────
    function attachRemove(btn) {
        btn.addEventListener('click', function () {
            var row = btn.closest('.autor-row');
            var container = document.getElementById('autoresContainer');
            if (container.querySelectorAll('.autor-row').length > 1) {
                row.remove();
            } else {
                row.querySelector('input').value = '';
            }
        });
    }

    document.querySelectorAll('.remove-autor').forEach(attachRemove);

    document.getElementById('addAutor').addEventListener('click', function () {
        var container = document.getElementById('autoresContainer');
        var row = document.createElement('div');
        row.className = 'autor-row';
        row.innerHTML =
            '<input type="text" name="autores[]" class="ds-input" placeholder="Nombre completo del autor…">' +
            '<button type="button" class="ds-btn ds-btn--ghost ds-btn--sm remove-autor" aria-label="Eliminar autor" style="flex-shrink:0;">' +
            '<i class="fas fa-times" aria-hidden="true"></i></button>';
        container.appendChild(row);
        attachRemove(row.querySelector('.remove-autor'));
        row.querySelector('input').focus();
    });

}());
</script>
@endpush
