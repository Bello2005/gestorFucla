<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Estructura de Proyectos</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 10px;
            margin: 10px;
            color: #1C2536;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 3px solid #4F46E5;
        }
        .logo {
            font-weight: 700;
            color: #8B6914;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 4px;
        }
        .title {
            font-size: 11px;
            font-weight: 700;
            color: #1C2536;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #4F46E5;
            color: white;
            padding: 8px 4px;
            text-align: center;
            font-weight: 600;
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: 1px solid #4338CA;
        }
        td {
            padding: 6px 4px;
            border: 1px solid #E4E7EC;
            font-size: 8px;
            vertical-align: top;
            color: #344054;
        }
        tr:nth-child(even) td {
            background-color: #F9FAFB;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #98A2B3;
            border-top: 1px solid #E4E7EC;
            padding-top: 8px;
        }
        .footer strong {
            color: #667085;
        }
        .no-data {
            text-align: center;
            font-style: italic;
            color: #98A2B3;
            padding: 40px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">UNICLARETIANA</div>
        <div class="title">DIRECCION DE EXTENSION<br>CONSOLIDADO DE PROYECTOS</div>
    </div>

    @if($proyectos->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Nombre del Proyecto</th>
                    <th style="width: 12%;">Objeto Contractual</th>
                    <th style="width: 12%;">Lineas de Accion</th>
                    <th style="width: 8%;">Cobertura</th>
                    <th style="width: 12%;">Entidad Contratante</th>
                    <th style="width: 8%;">Fecha de Ejecucion</th>
                    <th style="width: 6%;">Plazo</th>
                    <th style="width: 10%;">Valor Total ($)</th>
                    <th style="width: 8%;">Archivos</th>
                    <th style="width: 9%;">Evidencias</th>
                </tr>
            </thead>
            <tbody>
                @foreach($proyectos as $proyecto)
                <tr>
                    <td><strong>{{ $proyecto->nombre_del_proyecto }}</strong></td>
                    <td>{{ $proyecto->objeto_contractual }}</td>
                    <td>{{ $proyecto->lineas_de_accion }}</td>
                    <td>{{ $proyecto->cobertura }}</td>
                    <td>{{ $proyecto->entidad_contratante }}</td>
                    <td class="text-center">{{ $proyecto->fecha_ejecucion_formatted }}</td>
                    <td class="text-center">{{ $proyecto->plazo }}</td>
                    <td class="text-right" style="font-family: 'Courier New', monospace; font-weight: 600;">{{ number_format($proyecto->valor_total, 2, ',', '.') }}</td>
                    <td class="text-center">
                        @if($proyecto->cargar_archivo_proyecto) Proyecto<br> @endif
                        @if($proyecto->cargar_contrato_o_convenio) Contrato<br> @endif
                        @if(!$proyecto->cargar_archivo_proyecto && !$proyecto->cargar_contrato_o_convenio) - @endif
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
            <p><strong>Total de proyectos: {{ $proyectos->count() }}</strong> | Valor total: <strong>${{ number_format($proyectos->sum('valor_total'), 2, ',', '.') }}</strong></p>
            <p>Generado el {{ date('d/m/Y H:i:s') }}</p>
        </div>
    @else
        <div class="no-data">
            <p>No hay proyectos registrados para mostrar.</p>
        </div>
    @endif
</body>
</html>
