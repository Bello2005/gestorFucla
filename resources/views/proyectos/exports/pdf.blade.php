<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lista de Proyectos</title>
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
        .header-subtitle {
            font-size: 11px;
            color: #667085;
        }
        .project {
            margin-bottom: 32px;
            page-break-inside: avoid;
            border: 1px solid #E4E7EC;
            border-radius: 8px;
            overflow: hidden;
        }
        .project-header {
            background: #F9FAFB;
            padding: 12px 16px;
            border-bottom: 1px solid #E4E7EC;
        }
        .project-title {
            font-size: 16px;
            font-weight: 600;
            color: #1C2536;
            margin: 0;
        }
        .project-id {
            font-size: 10px;
            color: #667085;
        }
        .project-body {
            padding: 16px;
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
            padding: 6px 12px 6px 0;
            width: 140px;
            vertical-align: top;
        }
        .value {
            display: table-cell;
            color: #1C2536;
            font-size: 12px;
            padding: 6px 0;
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
        .value-money {
            font-family: 'Courier New', monospace;
            font-weight: 700;
        }
        .footer {
            margin-top: 24px;
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
        <div class="header-title">Lista de Proyectos</div>
        <div class="header-subtitle">Generado el {{ date('d/m/Y H:i') }}</div>
    </div>

    @foreach($proyectos as $proyecto)
        <div class="project">
            <div class="project-header">
                <div class="project-title">{{ $proyecto->nombre_del_proyecto }}</div>
                <div class="project-id">ID: {{ $proyecto->id }}</div>
            </div>
            <div class="project-body">
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
                        <span class="label">Entidad</span>
                        <span class="value">{{ $proyecto->entidad_contratante }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Actualizacion</span>
                        <span class="value">{{ $proyecto->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div class="footer">
        UNICLARETIANA &mdash; Gestor de Proyectos &mdash; {{ date('Y') }}
    </div>
</body>
</html>
