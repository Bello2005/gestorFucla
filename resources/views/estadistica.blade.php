@extends('layouts.main')

@section('title', 'Estadisticas')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}" class="breadcrumb-link">Dashboard</a>
    <i class="fas fa-chevron-right breadcrumb-sep"></i>
    <span class="breadcrumb-current">Estadisticas</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Analitica de Proyectos</h1>
        <p class="page-subtitle">Informacion en tiempo real para la toma de decisiones estrategicas</p>
    </div>
    <div class="page-actions">
        <div class="chart-type-selector">
            <label class="ds-label chart-type-label" for="chartTypeSelector">Tipo de Grafico</label>
            <select id="chartTypeSelector" class="ds-select chart-type-select">
                <option value="bar">Barras</option>
                <option value="pie">Pastel</option>
                <option value="doughnut">Dona</option>
                <option value="line">Lineas</option>
                <option value="radar">Radar</option>
                <option value="polarArea">Area Polar</option>
            </select>
        </div>
    </div>
</div>

<!-- KPI Stats -->
<div class="stat-cards-grid">
    <div class="stat-card stat-card--primary">
        <div class="stat-card-icon"><i class="fas fa-folder-open"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Total Proyectos</span>
            <span class="stat-card-value stat-counter" data-target="{{ $totalProyectos }}">{{ $totalProyectos }}</span>
            <span class="stat-card-trend stat-card-trend--up">
                <i class="fas fa-arrow-up"></i> +{{ $crecimientoProyectos }}% vs mes anterior
            </span>
        </div>
    </div>
    <div class="stat-card stat-card--success">
        <div class="stat-card-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Proyectos Activos</span>
            <span class="stat-card-value stat-counter" data-target="{{ $proyectosActivos }}">{{ $proyectosActivos }}</span>
            <span class="stat-card-trend">{{ $porcentajeActivos }}% del total</span>
        </div>
    </div>
    <div class="stat-card stat-card--warning">
        <div class="stat-card-icon"><i class="fas fa-dollar-sign"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Valor Total</span>
            <span class="stat-card-value">${{ number_format($valorTotal / 1000000, 1) }}M</span>
            <span class="stat-card-trend stat-card-trend--up">
                <i class="fas fa-arrow-up"></i> +{{ $crecimiento }}% crecimiento
            </span>
        </div>
    </div>
    <div class="stat-card stat-card--info">
        <div class="stat-card-icon"><i class="fas fa-chart-bar"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Tasa de Exito</span>
            <span class="stat-card-value stat-counter" data-target="{{ $tasaExito }}">{{ $tasaExito }}%</span>
            <span class="stat-card-trend">
                @if($tasaExito >= 90) Lider en la industria
                @elseif($tasaExito >= 70) Muy buen desempeno
                @elseif($tasaExito >= 50) Desempeno aceptable
                @else Mejorable
                @endif
            </span>
        </div>
    </div>
</div>

<!-- Main Chart -->
<div class="ds-card analytics-main-card">
    <div class="ds-card-header">
        <div>
            <h3 class="ds-card-title">Distribucion de Proyectos</h3>
            <p class="chart-subtitle">Visualizacion del estado de los proyectos en la organizacion</p>
        </div>
        <div class="live-update-label">
            <span class="live-dot"></span>
            Ultima actualizacion: ahora mismo
        </div>
    </div>
    <div class="ds-card-body">
        <div class="main-chart-wrap">
            <canvas id="mainChart"></canvas>
        </div>
    </div>
</div>

<!-- Detailed Metrics -->
<div class="analytics-detail-grid">
    <!-- Status Counts -->
    <div class="ds-card">
        <div class="ds-card-header">
            <h3 class="ds-card-title">Resumen por Estado</h3>
            <span class="section-meta">Numeros absolutos</span>
        </div>
        <div class="ds-card-body">
            @php
                $estados = [
                    ['nombre' => 'Activo', 'key' => 'activo', 'color' => 'success', 'desc' => 'En ejecucion', 'icon' => 'fa-play-circle'],
                    ['nombre' => 'Inactivo', 'key' => 'inactivo', 'color' => 'warning', 'desc' => 'En pausa', 'icon' => 'fa-pause-circle'],
                    ['nombre' => 'Cerrado', 'key' => 'cerrado', 'color' => 'danger', 'desc' => 'Completados', 'icon' => 'fa-check-circle'],
                ];
                $proyectosPorEstadoMap = $proyectosPorEstado->keyBy(function($item) {
                    return strtolower($item->estado);
                });
            @endphp

            <div class="status-metric-list">
                @foreach($estados as $estado)
                    @php
                        $total = $proyectosPorEstadoMap->has($estado['key']) ? $proyectosPorEstadoMap[$estado['key']]->total : 0;
                    @endphp
                    <div class="status-metric-card status-metric-card--{{ $estado['color'] }}">
                        <div class="status-metric-left">
                            <div class="status-metric-icon status-metric-icon--{{ $estado['color'] }}">
                                <i class="fas {{ $estado['icon'] }}"></i>
                            </div>
                            <div>
                                <span class="status-metric-title">Proyectos {{ $estado['nombre'] }}</span>
                                <span class="status-metric-desc">{{ $estado['desc'] }}</span>
                            </div>
                        </div>
                        <div class="status-metric-right">
                            <span class="status-metric-value">{{ $total }}</span>
                            <span class="status-metric-unit">proyectos</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Distribution Analysis -->
    <div class="ds-card">
        <div class="ds-card-header">
            <h3 class="ds-card-title">Analisis de Distribucion</h3>
            <span class="section-meta">Desglose porcentual</span>
        </div>
        <div class="ds-card-body">
            <div class="distribution-list">
                @foreach($estados as $estado)
                    @php
                        $total = $proyectosPorEstadoMap->has($estado['key']) ? $proyectosPorEstadoMap[$estado['key']]->total : 0;
                        $porcentaje = $totalProyectos > 0 ? round(($total / $totalProyectos) * 100, 1) : 0;
                    @endphp
                    <div>
                        <div class="distribution-row-head">
                            <span class="distribution-row-label">{{ $estado['nombre'] }}</span>
                            <span class="distribution-row-value distribution-row-value--{{ $estado['color'] }}">{{ $porcentaje }}%</span>
                        </div>
                        <div class="progress-bar-track">
                            <div class="progress-bar-fill progress-bar-fill--{{ $estado['color'] }}" style="width: {{ $porcentaje }}%"></div>
                        </div>
                        <div class="distribution-row-meta">
                            <span>{{ $total }} proyectos</span>
                            <span>de {{ $totalProyectos }} en total</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .chart-type-selector {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .chart-type-label {
        margin-bottom: 0;
    }
    .chart-type-select {
        width: auto;
        min-width: 180px;
    }
    .analytics-main-card {
        margin-top: 24px;
    }
    .chart-subtitle {
        font-size: var(--text-sm);
        color: var(--slate-500);
        margin-top: 4px;
    }
    .live-update-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: var(--text-sm);
        color: var(--slate-500);
    }
    .main-chart-wrap {
        position: relative;
        height: 380px;
    }
    .analytics-detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-top: 24px;
    }
    .section-meta {
        font-size: var(--text-sm);
        color: var(--slate-500);
    }
    .status-metric-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .status-metric-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .status-metric-right {
        text-align: right;
    }
    .distribution-list {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    .distribution-row-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    .distribution-row-label {
        font-size: var(--text-sm);
        font-weight: 500;
        color: var(--slate-700);
    }
    .distribution-row-value {
        font-family: var(--font-mono);
        font-weight: 700;
        font-size: var(--text-base);
    }
    .distribution-row-value--success { color: var(--success); }
    .distribution-row-value--warning { color: var(--warning); }
    .distribution-row-value--danger { color: var(--danger); }
    .distribution-row-meta {
        display: flex;
        justify-content: space-between;
        margin-top: 6px;
        font-size: var(--text-xs);
        color: var(--slate-500);
    }

    .stat-card-trend {
        display: block;
        font-size: var(--text-xs);
        color: var(--slate-500);
        margin-top: 4px;
    }
    .stat-card-trend--up {
        color: var(--success);
    }
    .stat-card-trend--up i {
        font-size: 10px;
    }

    .live-dot {
        width: 8px;
        height: 8px;
        background: var(--success);
        border-radius: 50%;
        display: inline-block;
        animation: livePulse 2s ease-in-out infinite;
    }
    @keyframes livePulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }

    /* Status metric cards */
    .status-metric-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px;
        border-radius: var(--radius-lg);
        transition: all var(--transition-fast) ease;
    }
    .status-metric-card--success { background: var(--success-50); }
    .status-metric-card--warning { background: var(--warning-50); }
    .status-metric-card--danger { background: var(--danger-50, #FEE2E2); }
    .status-metric-card:hover { box-shadow: var(--shadow-md); }

    .status-metric-icon {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: white;
    }
    .status-metric-icon--success { background: var(--success); }
    .status-metric-icon--warning { background: var(--warning); }
    .status-metric-icon--danger { background: var(--danger); }

    .status-metric-title {
        display: block;
        font-size: var(--text-sm);
        font-weight: 600;
        color: var(--slate-800);
    }
    .status-metric-desc {
        display: block;
        font-size: var(--text-xs);
        color: var(--slate-500);
    }
    .status-metric-value {
        display: block;
        font-family: var(--font-mono);
        font-size: 28px;
        font-weight: 700;
        color: var(--slate-900);
    }
    .status-metric-unit {
        display: block;
        font-size: var(--text-xs);
        color: var(--slate-500);
    }

    /* Progress bars */
    .progress-bar-track {
        height: 8px;
        background: var(--slate-100);
        border-radius: var(--radius-full);
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        border-radius: var(--radius-full);
        transition: width 800ms cubic-bezier(0.4, 0, 0.2, 1);
    }
    .progress-bar-fill--success { background: var(--success); }
    .progress-bar-fill--warning { background: var(--warning); }
    .progress-bar-fill--danger { background: var(--danger); }

    @media (max-width: 768px) {
        .chart-type-selector {
            flex-direction: column;
            align-items: stretch;
        }
        .analytics-detail-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <script id="chartData" type="application/json">@json($proyectosPorEstado->pluck('total'))</script>
    <script id="chartLabels" type="application/json">@json($proyectosPorEstado->pluck('estado'))</script>

    <script>
        var chartData = JSON.parse(document.getElementById('chartData').textContent);
        var chartLabels = JSON.parse(document.getElementById('chartLabels').textContent);
        var mainChart;

        function cssVar(name) {
            return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
        }

        var chartColors = {
            base: [cssVar('--success'), cssVar('--warning'), cssVar('--danger'), cssVar('--info'), cssVar('--primary')],
            soft: ['rgba(18, 183, 106, 0.24)', 'rgba(247, 144, 9, 0.24)', 'rgba(240, 68, 56, 0.24)', 'rgba(11, 165, 236, 0.24)', 'rgba(79, 70, 229, 0.24)'],
            radial: ['rgba(18, 183, 106, 0.78)', 'rgba(247, 144, 9, 0.78)', 'rgba(240, 68, 56, 0.78)', 'rgba(11, 165, 236, 0.78)', 'rgba(79, 70, 229, 0.78)']
        };

        function createChart(type) {
            var ctx = document.getElementById('mainChart').getContext('2d');
            if (mainChart) mainChart.destroy();

            var isRadial = ['pie', 'doughnut', 'polarArea'].includes(type);
            var maxValue = chartData.length ? Math.max.apply(null, chartData) : 0;
            var gradient = ctx.createLinearGradient(0, 0, 0, 380);
            gradient.addColorStop(0, 'rgba(79, 70, 229, 0.35)');
            gradient.addColorStop(1, 'rgba(79, 70, 229, 0.06)');

            var dataset = {
                label: 'Proyectos',
                data: chartData,
                borderWidth: type === 'line' ? 3 : 2,
                borderRadius: type === 'bar' ? 10 : 0,
                borderSkipped: false,
                pointRadius: type === 'line' || type === 'radar' ? 4 : 0,
                pointHoverRadius: type === 'line' || type === 'radar' ? 6 : 0,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: chartColors.base[4],
                pointBorderWidth: 2,
                tension: type === 'line' || type === 'radar' ? 0.35 : 0
            };

            if (type === 'bar') {
                dataset.backgroundColor = chartColors.soft;
                dataset.borderColor = chartColors.base;
            } else if (type === 'line') {
                dataset.backgroundColor = gradient;
                dataset.borderColor = chartColors.base[4];
                dataset.fill = true;
            } else if (type === 'radar') {
                dataset.backgroundColor = 'rgba(79, 70, 229, 0.2)';
                dataset.borderColor = chartColors.base[4];
                dataset.fill = true;
            } else if (type === 'doughnut') {
                dataset.backgroundColor = chartColors.radial;
                dataset.borderColor = '#ffffff';
                dataset.borderWidth = 2;
                dataset.spacing = 2;
                dataset.hoverOffset = 8;
            } else if (type === 'pie' || type === 'polarArea') {
                dataset.backgroundColor = chartColors.radial;
                dataset.borderColor = '#ffffff';
                dataset.borderWidth = 2;
            }

            mainChart = new Chart(ctx, {
                type: type,
                data: {
                    labels: chartLabels,
                    datasets: [dataset]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 900,
                        easing: 'easeOutCubic'
                    },
                    transitions: {
                        active: {
                            animation: {
                                duration: 220
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: isRadial,
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 18,
                                font: { size: 12, family: 'Inter' },
                                color: '#475467'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(12, 17, 29, 0.9)',
                            titleColor: 'white',
                            bodyColor: 'white',
                            cornerRadius: 12,
                            caretPadding: 8,
                            boxPadding: 6,
                            padding: 12,
                            displayColors: true,
                            callbacks: {
                                label: function(ctx) {
                                    var value = typeof ctx.parsed === 'object' ? ctx.parsed.r : ctx.parsed;
                                    return ctx.label + ': ' + value + ' proyectos';
                                }
                            }
                        }
                    },
                    scales: !isRadial ? {
                        y: {
                            beginAtZero: true,
                            suggestedMax: maxValue + 1,
                            grid: { color: 'rgba(16, 24, 40, 0.06)', drawBorder: false },
                            ticks: { font: { family: 'Inter', size: 12 }, color: '#667085' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'Inter', size: 12 }, color: '#667085' }
                        }
                    } : {},
                    elements: {
                        arc: {
                            borderWidth: type === 'doughnut' || type === 'pie' || type === 'polarArea' ? 2 : 0
                        }
                    },
                    cutout: type === 'doughnut' ? '62%' : undefined
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            createChart('bar');

            document.getElementById('chartTypeSelector').addEventListener('change', function(e) {
                createChart(e.target.value);
            });

            // Counter animation (800ms)
            document.querySelectorAll('.stat-counter').forEach(function(el, index) {
                var text = el.textContent.replace(/[^0-9.]/g, '');
                var target = parseFloat(text);
                if (isNaN(target)) return;
                var suffix = el.textContent.replace(/[0-9.,]/g, '');
                el.textContent = '0' + suffix;

                setTimeout(function() {
                    var start = performance.now();
                    function step(now) {
                        var progress = Math.min((now - start) / 800, 1);
                        var current = Math.floor(progress * target);
                        el.textContent = current.toLocaleString() + suffix;
                        if (progress < 1) requestAnimationFrame(step);
                        else el.textContent = target.toLocaleString() + suffix;
                    }
                    requestAnimationFrame(step);
                }, index * 150);
            });
        });
    </script>
@endpush
