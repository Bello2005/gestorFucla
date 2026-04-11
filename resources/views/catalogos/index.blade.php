@extends('layouts.main')

@section('title', 'Catálogos')

@section('breadcrumbs')
    <span class="breadcrumb-current">Catálogos</span>
@endsection

@section('content')
<div class="catalogos-page">
    <h1 class="page-title">Catálogos</h1>
    <p class="page-subtitle">Programas, tipos de proyecto y líneas de investigación (solo administradores).</p>

    <div class="uc-tabs mt-4">
        <a href="#prog" class="uc-tabs__btn uc-tabs__btn--active">Programas</a>
        <a href="#tip" class="uc-tabs__btn">Tipos</a>
        <a href="#lin" class="uc-tabs__btn">Líneas</a>
    </div>

    <div id="prog" class="ds-card mb-5">
        <h2 class="uc-section-title">Programas / departamentos</h2>
        <form action="{{ route('catalogos.programas.store') }}" method="post" class="row g-2 mb-3">
            @csrf
            <div class="col-md-4"><input name="nombre" class="form-control" placeholder="Nombre" required></div>
            <div class="col-md-4"><input name="facultad" class="form-control" placeholder="Facultad"></div>
            <div class="col-md-4"><button class="ds-btn ds-btn--primary" type="submit">Agregar</button></div>
        </form>
        <table class="table table-sm">
            <thead><tr><th>Nombre</th><th>Facultad</th><th></th></tr></thead>
            <tbody>
                @foreach($programas as $p)
                    <tr>
                        <td>
                            <form action="{{ route('catalogos.programas.update', $p) }}" method="post" class="d-flex gap-1">
                                @csrf @method('PUT')
                                <input type="text" name="nombre" value="{{ $p->nombre }}" class="form-control form-control-sm uc-inline-input">
                                <input type="text" name="facultad" value="{{ $p->facultad }}" class="form-control form-control-sm uc-inline-input">
                                <button class="ds-btn ds-btn--sm ds-btn--secondary" type="submit">OK</button>
                            </form>
                        </td>
                        <td></td>
                        <td>
                            <form action="{{ route('catalogos.programas.destroy', $p) }}" method="post" onsubmit="return confirm('¿Eliminar?');">
                                @csrf @method('DELETE')
                                <button class="ds-btn ds-btn--sm ds-btn--danger" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div id="tip" class="ds-card mb-5">
        <h2 class="uc-section-title">Tipos de proyecto</h2>
        <form action="{{ route('catalogos.tipos.store') }}" method="post" class="row g-2 mb-3">
            @csrf
            <div class="col-md-8"><input name="nombre" class="form-control" placeholder="Nombre" required></div>
            <div class="col-md-4"><button class="ds-btn ds-btn--primary" type="submit">Agregar</button></div>
        </form>
        <table class="table table-sm">
            <tbody>
                @foreach($tipos as $t)
                    <tr>
                        <td>
                            <form action="{{ route('catalogos.tipos.update', $t) }}" method="post" class="d-flex gap-1">
                                @csrf @method('PUT')
                                <input type="text" name="nombre" value="{{ $t->nombre }}" class="form-control form-control-sm">
                                <button class="ds-btn ds-btn--sm ds-btn--secondary" type="submit">OK</button>
                            </form>
                        </td>
                        <td>
                            <form action="{{ route('catalogos.tipos.destroy', $t) }}" method="post" onsubmit="return confirm('¿Eliminar?');">
                                @csrf @method('DELETE')
                                <button class="ds-btn ds-btn--sm ds-btn--danger" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div id="lin" class="ds-card">
        <h2 class="uc-section-title">Líneas de investigación</h2>
        <form action="{{ route('catalogos.lineas.store') }}" method="post" class="row g-2 mb-3">
            @csrf
            <div class="col-md-4"><input name="nombre" class="form-control" placeholder="Nombre" required></div>
            <div class="col-md-4"><input name="area" class="form-control" placeholder="Área"></div>
            <div class="col-md-4"><button class="ds-btn ds-btn--primary" type="submit">Agregar</button></div>
        </form>
        <table class="table table-sm">
            <tbody>
                @foreach($lineas as $l)
                    <tr>
                        <td>
                            <form action="{{ route('catalogos.lineas.update', $l) }}" method="post" class="d-flex gap-1 flex-wrap">
                                @csrf @method('PUT')
                                <input type="text" name="nombre" value="{{ $l->nombre }}" class="form-control form-control-sm">
                                <input type="text" name="area" value="{{ $l->area }}" class="form-control form-control-sm">
                                <button class="ds-btn ds-btn--sm ds-btn--secondary" type="submit">OK</button>
                            </form>
                        </td>
                        <td>
                            <form action="{{ route('catalogos.lineas.destroy', $l) }}" method="post" onsubmit="return confirm('¿Eliminar?');">
                                @csrf @method('DELETE')
                                <button class="ds-btn ds-btn--sm ds-btn--danger" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
