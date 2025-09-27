<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Proyecto {{ $proyecto->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .project-title {
            font-size: 24px;
            color: #2563eb;
            margin-bottom: 10px;
        }
        .project-id {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 18px;
            color: #1e40af;
            margin-bottom: 10px;
        }
        .info-row {
            margin-bottom: 10px;
        }
        .label {
            font-weight: bold;
            color: #4b5563;
        }
        .value {
            color: #1f2937;
        }
        hr {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="project-title">{{ $proyecto->nombre_del_proyecto }}</h1>
        <div class="project-id">ID del Proyecto: {{ $proyecto->id }}</div>
    </div>

    <div class="section">
        <h2 class="section-title">Información General</h2>
        <div class="info-row">
            <span class="label">Estado:</span>
            <span class="value">{{ ucfirst($proyecto->estado) }}</span>
        </div>
        <div class="info-row">
            <span class="label">Valor Total:</span>
            <span class="value">${{ number_format($proyecto->valor_total, 0, ',', '.') }}</span>
        </div>
        <div class="info-row">
            <span class="label">Plazo:</span>
            <span class="value">{{ $proyecto->plazo }} meses</span>
        </div>
        <div class="info-row">
            <span class="label">Fecha de Ejecución:</span>
            <span class="value">{{ $proyecto->fecha_de_ejecucion ? date('d/m/Y', strtotime($proyecto->fecha_de_ejecucion)) : 'No especificada' }}</span>
        </div>
    </div>

    <hr>

    <div class="section">
        <h2 class="section-title">Detalles del Proyecto</h2>
        <div class="info-row">
            <span class="label">Entidad Contratante:</span>
            <span class="value">{{ $proyecto->entidad_contratante }}</span>
        </div>
        <div class="info-row">
            <span class="label">Cobertura:</span>
            <span class="value">{{ $proyecto->cobertura }}</span>
        </div>
    </div>

    <div class="section">
        <h2 class="section-title">Objeto Contractual</h2>
        <p>{{ $proyecto->objeto_contractual }}</p>
    </div>

    <div class="section">
        <h2 class="section-title">Líneas de Acción</h2>
        <p>{{ $proyecto->lineas_de_accion }}</p>
    </div>

    <hr>

    <div class="section">
        <div class="info-row">
            <span class="label">Fecha de Creación:</span>
            <span class="value">{{ $proyecto->created_at->format('d/m/Y H:i:s') }}</span>
        </div>
        <div class="info-row">
            <span class="label">Última Actualización:</span>
            <span class="value">{{ $proyecto->updated_at->format('d/m/Y H:i:s') }}</span>
        </div>
    </div>
</body>
</html>