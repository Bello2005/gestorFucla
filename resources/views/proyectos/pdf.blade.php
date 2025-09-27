<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Estructura de Proyectos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 10px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #28a745;
            padding-bottom: 10px;
        }
        
        .logo {
            font-weight: bold;
            color: #28a745;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .title {
            font-size: 12px;
            font-weight: bold;
            color: #333;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        th {
            background-color: #28a745;
            color: white;
            padding: 8px 4px;
            text-align: center;
            font-weight: bold;
            font-size: 8px;
            border: 1px solid #000;
        }
        
        td {
            padding: 6px 4px;
            border: 1px solid #000;
            font-size: 8px;
            vertical-align: top;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #666;
        }
        
        .no-data {
            text-align: center;
            font-style: italic;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">UNICLARETIANA</div>
        <div class="title">DIRECCIÓN DE EXTENSIÓN<br>CONSOLIDADO DE PROYECTOS</div>
    </div>
    
    @if($proyectos->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">NOMBRE DEL PROYECTO</th>
                    <th style="width: 12%;">OBJETO CONTRACTUAL</th>
                    <th style="width: 12%;">LÍNEAS DE ACCIÓN</th>
                    <th style="width: 8%;">COBERTURA</th>
                    <th style="width: 12%;">ENTIDAD CONTRATANTE</th>
                    <th style="width: 8%;">FECHA DE EJECUCIÓN</th>
                    <th style="width: 6%;">PLAZO</th>
                    <th style="width: 10%;">VALOR TOTAL ($)</th>
                    <th style="width: 8%;">ARCHIVOS</th>
                    <th style="width: 9%;">EVIDENCIAS</th>
                </tr>
            </thead>
            <tbody>
                @foreach($proyectos as $proyecto)
                <tr>
                    <td>{{ $proyecto->nombre_del_proyecto }}</td>
                    <td>{{ $proyecto->objeto_contractual }}</td>
                    <td>{{ $proyecto->lineas_de_accion }}</td>
                    <td>{{ $proyecto->cobertura }}</td>
                    <td>{{ $proyecto->entidad_contratante }}</td>
                    <td class="text-center">{{ $proyecto->fecha_ejecucion_formatted }}</td>
                    <td class="text-center">{{ $proyecto->plazo }}</td>
                    <td class="text-right">{{ number_format($proyecto->valor_total, 2, ',', '.') }}</td>
                    <td class="text-center">
                        @if($proyecto->cargar_archivo_proyecto)
                            Proyecto<br>
                        @endif
                        @if($proyecto->cargar_contrato_o_convenio)
                            Contrato<br>
                        @endif
                        @if(!$proyecto->cargar_archivo_proyecto && !$proyecto->cargar_contrato_o_convenio)
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        @if($proyecto->cargar_evidencias)
                            {{ count($proyecto->cargar_evidencias) }} archivo(s)
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="footer">
            <p>Total de proyectos: {{ $proyectos->count() }} | Generado el {{ date('d/m/Y H:i:s') }}</p>
            <p>Valor total de todos los proyectos: ${{ number_format($proyectos->sum('valor_total'), 2, ',', '.') }}</p>
        </div>
    @else
        <div class="no-data">
            <p>No hay proyectos registrados para mostrar.</p>
        </div>
    @endif
</body>
</html>