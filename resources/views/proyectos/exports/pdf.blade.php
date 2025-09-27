<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lista de Proyectos</title>
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
        .title {
            font-size: 24px;
            color: #2563eb;
            margin-bottom: 20px;
        }
        .project {
            margin-bottom: 40px;
            page-break-inside: avoid;
        }
        .project-title {
            font-size: 20px;
            color: #1e40af;
            margin-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 5px;
        }
        .info-row {
            margin-bottom: 8px;
        }
        .label {
            font-weight: bold;
            color: #4b5563;
            display: inline-block;
            width: 150px;
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
        <h1 class="title">Lista de Proyectos</h1>
    </div>

    @foreach($proyectos as $proyecto)
        <div class="project">
            <h2 class="project-title">{{ $proyecto->nombre_del_proyecto }}</h2>
            
            <div class="info-row">
                <span class="label">ID:</span>
                <span class="value">{{ $proyecto->id }}</span>
            </div>
            
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
                <span class="label">Entidad:</span>
                <span class="value">{{ $proyecto->entidad_contratante }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">Última Actualización:</span>
                <span class="value">{{ $proyecto->updated_at->format('d/m/Y H:i:s') }}</span>
            </div>
        </div>

        @if(!$loop->last)
            <hr>
        @endif
    @endforeach
</body>
</html>