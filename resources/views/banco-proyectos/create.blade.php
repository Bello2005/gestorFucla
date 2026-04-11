@extends('layouts.main')

@section('title', 'Nuevo — Banco de Proyectos')

@section('breadcrumbs')
    <a href="{{ route('banco.index') }}">Banco de Proyectos</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Nuevo</span>
@endsection

@section('content')
<div class="banco-page">
    <h1 class="page-title">Nuevo proyecto académico</h1>
    <p class="page-subtitle">Complete la información; puede guardar como borrador y continuar después.</p>

    <form action="{{ route('banco.store') }}" method="post" class="ds-card mt-4">
        @csrf
        <h2 class="uc-section-title mb-3">1. Identificación</h2>
        <div class="row g-3">
            <div class="col-12">
                <label class="ds-label">Título *</label>
                <input type="text" name="titulo" class="ds-input" required value="{{ old('titulo') }}">
            </div>
            <div class="col-md-6">
                <label class="ds-label">Línea de investigación</label>
                <select name="linea_investigacion" class="form-select">
                    <option value="">—</option>
                    @foreach($lineas as $l)
                        <option value="{{ $l->nombre }}" @selected(old('linea_investigacion')===$l->nombre)>{{ $l->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="ds-label">Área / Facultad</label>
                <select name="area_facultad" class="form-select">
                    <option value="">—</option>
                    @foreach($programas as $pr)
                        <option value="{{ $pr->nombre }}" @selected(old('area_facultad')===$pr->nombre)>{{ $pr->nombre }} @if($pr->facultad) ({{ $pr->facultad }}) @endif</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="ds-label">Tipo de proyecto</label>
                <select name="tipo_proyecto" class="form-select">
                    <option value="">—</option>
                    @foreach($tipos as $t)
                        <option value="{{ $t->nombre }}" @selected(old('tipo_proyecto')===$t->nombre)>{{ $t->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="ds-label">Convocatoria / periodo</label>
                <input type="text" name="convocatoria" class="ds-input" value="{{ old('convocatoria') }}">
            </div>
        </div>

        <hr class="ds-divider my-4">

        <h2 class="uc-section-title mb-3">2. Formulación</h2>
        <div class="row g-3">
            <div class="col-12">
                <label class="ds-label">Resumen ejecutivo</label>
                <textarea name="resumen_ejecutivo" class="ds-textarea" rows="4">{{ old('resumen_ejecutivo') }}</textarea>
            </div>
            <div class="col-12">
                <label class="ds-label">Problema / necesidad</label>
                <textarea name="problema_necesidad" class="ds-textarea" rows="3">{{ old('problema_necesidad') }}</textarea>
            </div>
            <div class="col-12">
                <label class="ds-label">Objetivo general</label>
                <textarea name="objetivo_general" class="ds-textarea" rows="3">{{ old('objetivo_general') }}</textarea>
            </div>
            <div class="col-12">
                <label class="ds-label">Justificación</label>
                <textarea name="justificacion" class="ds-textarea" rows="3">{{ old('justificacion') }}</textarea>
            </div>
            <div class="col-12">
                <label class="ds-label">Alcance</label>
                <textarea name="alcance" class="ds-textarea" rows="2">{{ old('alcance') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="ds-label">Población objetivo</label>
                <textarea name="poblacion_objetivo" class="ds-textarea" rows="2">{{ old('poblacion_objetivo') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="ds-label">Cobertura geográfica</label>
                <input type="text" name="cobertura_geografica" class="ds-input" value="{{ old('cobertura_geografica') }}">
            </div>
        </div>

        <hr class="ds-divider my-4">

        <h2 class="uc-section-title mb-3">3. Recursos</h2>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="ds-label">Presupuesto estimado</label>
                <input type="number" step="0.01" name="presupuesto_estimado" class="ds-input" value="{{ old('presupuesto_estimado') }}">
            </div>
            <div class="col-md-4">
                <label class="ds-label">Cofinanciación</label>
                <input type="number" step="0.01" name="cofinanciacion" class="ds-input" value="{{ old('cofinanciacion') }}">
            </div>
            <div class="col-md-4">
                <label class="ds-label">Duración (meses)</label>
                <input type="number" name="duracion_meses" class="ds-input" min="1" value="{{ old('duracion_meses') }}">
            </div>
            <div class="col-12">
                <label class="ds-label">Fuente de financiación</label>
                <input type="text" name="fuente_financiacion" class="ds-input" value="{{ old('fuente_financiacion') }}">
            </div>
        </div>

        <hr class="ds-divider my-4">

        <h2 class="uc-section-title mb-3">4. Equipo</h2>
        <div class="row g-3">
            <div class="col-12">
                <label class="ds-label">Autores (uno por línea)</label>
                <textarea name="autores_text" class="ds-textarea" rows="3" placeholder="Nombre completo">{{ old('autores_text') }}</textarea>
                <p class="ds-form-hint">Se guardará como lista interna.</p>
            </div>
            <div class="col-md-6">
                <label class="ds-label">Tutor / director</label>
                <input type="text" name="tutor_director" class="ds-input" value="{{ old('tutor_director') }}">
            </div>
            <div class="col-md-6">
                <label class="ds-label">Programa / departamento</label>
                <input type="text" name="programa_departamento" class="ds-input" value="{{ old('programa_departamento') }}">
            </div>
            <div class="col-md-6">
                <label class="ds-label">Entidad aliada</label>
                <input type="text" name="entidad_aliada" class="ds-input" value="{{ old('entidad_aliada') }}">
            </div>
            <div class="col-md-6">
                <label class="ds-label">Evaluador</label>
                <input type="text" name="evaluador_asignado" class="ds-input" value="{{ old('evaluador_asignado') }}">
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('banco.index') }}" class="ds-btn ds-btn--secondary">Cancelar</a>
            <button type="submit" class="ds-btn ds-btn--primary">Guardar borrador</button>
        </div>
    </form>
</div>
@endsection
