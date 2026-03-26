<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Proyecto {{ $proyecto->id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.5;
            color: #1C2536;
            margin: 0;
            padding: 32px;
        }
        .header {
            text-align: center;
            margin-bottom: 32px;
            padding-bottom: 20px;
            border-bottom: 3px solid #4F46E5;
        }
        .header-brand {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #8B6914;
            margin-bottom: 4px;
        }
        .header-title {
            font-size: 22px;
            font-weight: 700;
            color: #1C2536;
            margin: 8px 0 4px;
        }
        .header-id {
            font-size: 12px;
            color: #667085;
        }
        .section {
            margin-bottom: 24px;
        }
        .section-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #4F46E5;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 1px solid #E4E7EC;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .label {
            display: table-cell;
            font-weight: 600;
            color: #667085;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 16px 8px 0;
            width: 160px;
            vertical-align: top;
        }
        .value {
            display: table-cell;
            color: #1C2536;
            font-size: 12px;
            padding: 8px 0;
        }
        .value-money {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            font-size: 14px;
        }
        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
        }
        .badge-activo { background: #ECFDF3; color: #027A48; }
        .badge-inactivo { background: #FFFAEB; color: #B54708; }
        .badge-cerrado { background: #F2F4F7; color: #344054; }
        .text-block {
            background: #F9FAFB;
            border: 1px solid #E4E7EC;
            border-radius: 6px;
            padding: 12px;
            font-size: 12px;
            color: #344054;
            line-height: 1.6;
        }
        .divider {
            border: none;
            border-top: 1px solid #E4E7EC;
            margin: 24px 0;
        }
        .footer {
            margin-top: 32px;
            text-align: center;
            font-size: 9px;
            color: #98A2B3;
            border-top: 1px solid #E4E7EC;
            padding-top: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-brand">UNICLARETIANA</div>
        <div class="header-title">{{ $proyecto->nombre_del_proyecto }}</div>
        <div class="header-id">ID del Proyecto: {{ $proyecto->id }}</div>
    </div>

    <div class="section">
        <div class="section-title">Informacion General</div>
        <div class="info-grid">
            <div class="info-row">
                <span class="label">Estado</span>
                <span class="value">
                    <span class="badge badge-{{ $proyecto->estado }}">{{ ucfirst($proyecto->estado) }}</span>
                </span>
            </div>
            <div class="info-row">
                <span class="label">Valor Total</span>
                <span class="value value-money">${{ number_format($proyecto->valor_total, 0, ',', '.') }}</span>
            </div>
            <div class="info-row">
                <span class="label">Plazo</span>
                <span class="value">{{ $proyecto->plazo }} meses</span>
            </div>
            <div class="info-row">
                <span class="label">Fecha de Ejecucion</span>
                <span class="value">{{ $proyecto->fecha_de_ejecucion ? date('d/m/Y', strtotime($proyecto->fecha_de_ejecucion)) : 'No especificada' }}</span>
            </div>
        </div>
    </div>

    <hr class="divider">

    <div class="section">
        <div class="section-title">Detalles del Proyecto</div>
        <div class="info-grid">
            <div class="info-row">
                <span class="label">Entidad Contratante</span>
                <span class="value">{{ $proyecto->entidad_contratante }}</span>
            </div>
            <div class="info-row">
                <span class="label">Cobertura</span>
                <span class="value">{{ $proyecto->cobertura }}</span>
            </div>
        </div>
    </div>

    @if($proyecto->objeto_contractual)
        <div class="section">
            <div class="section-title">Objeto Contractual</div>
            <div class="text-block">{{ $proyecto->objeto_contractual }}</div>
        </div>
    @endif

    @if($proyecto->lineas_de_accion)
        <div class="section">
            <div class="section-title">Lineas de Accion</div>
            <div class="text-block">{{ $proyecto->lineas_de_accion }}</div>
        </div>
    @endif

    <hr class="divider">

    <div class="section">
        <div class="section-title">Informacion del Sistema</div>
        <div class="info-grid">
            <div class="info-row">
                <span class="label">Fecha de Creacion</span>
                <span class="value">{{ $proyecto->created_at->format('d/m/Y H:i:s') }}</span>
            </div>
            <div class="info-row">
                <span class="label">Ultima Actualizacion</span>
                <span class="value">{{ $proyecto->updated_at->format('d/m/Y H:i:s') }}</span>
            </div>
        </div>
    </div>

    <div class="footer">
        UNICLARETIANA &mdash; Gestor de Proyectos &mdash; Generado el {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
