@extends('layouts.main')

@section('title', 'Panel de Analítica - UNICLARETIANA')

@push('styles')
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        .gradient-bg {
            background: #ffffff;
        }
        
        .glass-effect {
            backdrop-filter: blur(16px);
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .stat-number {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: none;
        }
        
        .chart-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }
        
        .progress-ring {
            transform: rotate(-90deg);
        }
        
        .floating-element {
            animation: float 6s ease-in-out infinite;
        }
        
        .floating-element:nth-child(1) { background: linear-gradient(45deg, #4f46e5, #7c3aed); }
        .floating-element:nth-child(2) { background: linear-gradient(45deg, #06b6d4, #3b82f6); }
        .floating-element:nth-child(3) { background: linear-gradient(45deg, #10b981, #059669); }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .metric-trend {
            position: relative;
            overflow: hidden;
        }
        
        .metric-trend::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }
        
        .metric-trend:hover::before {
            left: 100%;
        }
        
        .custom-selector {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .pulse-dot {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: .5;
            }
        }
    </style>
</push>

@section('content')
<div class="min-h-screen gradient-bg">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gray-50">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-50/80 to-purple-50/80"></div>
        
        <!-- Floating geometric elements -->
        <div class="absolute top-20 left-10 w-20 h-20 rounded-full floating-element opacity-20"></div>
        <div class="absolute top-40 right-20 w-16 h-16 rounded-lg rotate-45 floating-element opacity-20" style="animation-delay: -2s;"></div>
        <div class="absolute bottom-40 left-1/4 w-12 h-12 rounded-full floating-element opacity-20" style="animation-delay: -4s;"></div>
        
        <div class="relative px-6 py-12 max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between mb-12">
                <div class="mb-8 lg:mb-0">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-3 h-3 bg-green-500 rounded-full pulse-dot"></div>
                        <span class="text-gray-600 text-sm font-medium uppercase tracking-wider">Panel en Vivo</span>
                    </div>
                    <h1 class="text-4xl lg:text-6xl font-bold text-gray-800 mb-4 leading-tight">
                        Analítica de Proyectos
                        <span class="block text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">
                            UNICLARETIANA
                        </span>
                    </h1>
                    <p class="text-gray-600 text-lg max-w-2xl leading-relaxed">
                        Información en tiempo real y analítica integral para la toma de decisiones estratégicas. Supervisa el desempeño de los proyectos, sigue métricas clave y visualiza patrones de éxito.
                    </p>
                </div>
                
                <!-- Chart Type Selector -->
                <div class="custom-selector rounded-2xl p-6 shadow-2xl border border-white/20">
                    <label class="block text-gray-700 text-sm font-semibold mb-3">Tipo de Gráfico</label>
                    <div class="relative">
                        <select id="chartTypeSelector" class="appearance-none w-full bg-transparent border-2 border-indigo-200 rounded-xl px-4 py-3 pr-8 text-gray-700 font-medium focus:outline-none focus:border-indigo-500 transition-colors">
                            <option value="bar">📊 Gráfico de Barras</option>
                            <option value="pie">🥧 Gráfico de Pastel</option>
                            <option value="doughnut">🍩 Gráfico de Dona</option>
                            <option value="line">📈 Gráfico de Líneas</option>
                            <option value="radar">🎯 Gráfico de Radar</option>
                            <option value="polarArea">⭕ Área Polar</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                <!-- Total Projects -->
                <div class="glass-effect rounded-2xl p-6 card-hover group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-indigo-500 p-3 rounded-xl shadow-lg group-hover:shadow-xl transition-shadow">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <div class="text-gray-600 text-xs uppercase tracking-wider font-medium">Portafolio</div>
                            <div class="text-gray-700 text-sm">Total de Proyectos</div>
                        </div>
                    </div>
                    <div class="stat-number text-4xl font-bold mb-2">{{ $totalProyectos }}</div>
                    <div class="flex items-center text-green-600 text-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        <span>+{{ $crecimientoProyectos }}% vs mes anterior</span>
                    </div>
                </div>

                <!-- Active Projects -->
                <div class="glass-effect rounded-2xl p-6 card-hover group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-green-500 p-3 rounded-xl shadow-lg group-hover:shadow-xl transition-shadow">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <div class="text-gray-600 text-xs uppercase tracking-wider font-medium">Estado</div>
                            <div class="text-gray-700 text-sm">Proyectos Activos</div>
                        </div>
                    </div>
                    <div class="stat-number text-4xl font-bold mb-2">{{ $proyectosActivos }}</div>
                    <div class="flex items-center text-green-600 text-sm">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                        <span>{{ $porcentajeActivos }}% del total</span>
                    </div>
                </div>

                <!-- Total Value -->
                <div class="glass-effect rounded-2xl p-6 card-hover group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-yellow-500 p-3 rounded-xl shadow-lg group-hover:shadow-xl transition-shadow">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <div class="text-gray-600 text-xs uppercase tracking-wider font-medium">Inversión</div>
                            <div class="text-gray-700 text-sm">Valor Total</div>
                        </div>
                    </div>
                    <div class="stat-number text-4xl font-bold mb-2">${{ number_format($valorTotal / 1000000, 1) }}M</div>
                    <div class="flex items-center text-yellow-600 text-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        <span>+{{ $crecimiento }}% crecimiento</span>
                    </div>
                </div>

                <!-- Success Rate -->
                <div class="glass-effect rounded-2xl p-6 card-hover group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-purple-500 p-3 rounded-xl shadow-lg group-hover:shadow-xl transition-shadow">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <div class="text-gray-600 text-xs uppercase tracking-wider font-medium">Desempeño</div>
                            <div class="text-gray-700 text-sm">Tasa de Éxito</div>
                        </div>
                    </div>
                    <div class="stat-number text-4xl font-bold mb-2">{{ $tasaExito }}%</div>
                    <div class="flex items-center text-purple-600 text-sm">
                        <div class="w-2 h-2 bg-purple-500 rounded-full mr-2"></div>
                        <span>
                            @if($tasaExito >= 90)
                                Líder en la industria
                            @elseif($tasaExito >= 70)
                                Muy buen desempeño
                            @elseif($tasaExito >= 50)
                                Desempeño aceptable
                            @else
                                Mejorable
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="px-6 pb-12">
        <div class="max-w-7xl mx-auto">
            <!-- Main Chart -->
            <div class="chart-container rounded-3xl shadow-2xl p-8 mb-8 border border-white/20">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Análisis de Distribución de Proyectos</h2>
                        <p class="text-gray-600">Visualización en tiempo real del estado de los proyectos en la organización</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center text-sm text-gray-600">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                            <span>Última actualización: ahora mismo</span>
                        </div>
                    </div>
                </div>
                
                <div class="relative">
                    <canvas id="mainChart" class="max-w-full h-96"></canvas>
                </div>
            </div>

            <!-- Detailed Metrics Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Project Status Counts -->
                <div class="chart-container rounded-2xl shadow-xl p-6 border border-white/20">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-800">Resumen de Estado de Proyectos</h3>
                        <div class="text-sm text-gray-500">Números absolutos</div>
                    </div>
                    
                    <div class="space-y-4">
                        @php
                            $estadosFijos = collect([
                                (object)['estado' => 'Activo', 'total' => 0, 'color' => 'green', 'icon' => 'play'],
                                (object)['estado' => 'Inactivo', 'total' => 0, 'color' => 'yellow', 'icon' => 'pause'],
                                (object)['estado' => 'Cerrado', 'total' => 0, 'color' => 'red', 'icon' => 'check-circle'],
                            ]);
                            $proyectosPorEstadoMap = $proyectosPorEstado->keyBy(function($item) {
                                return strtolower($item->estado);
                            });
                        @endphp
                        
                        @foreach($estadosFijos as $index => $estadoFijo)
                            @php
                                $key = strtolower($estadoFijo->estado);
                                $total = $proyectosPorEstadoMap->has($key) ? $proyectosPorEstadoMap[$key]->total : 0;
                                $colors = [
                                    'green' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'accent' => 'bg-green-500'],
                                    'yellow' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'accent' => 'bg-yellow-500'],
                                    'red' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'accent' => 'bg-red-500']
                                ];
                                $currentColor = $colors[$estadoFijo->color];
                            @endphp
                            
                            <div class="metric-trend {{ $currentColor['bg'] }} rounded-xl p-4 flex items-center justify-between group hover:shadow-lg transition-all">
                                <div class="flex items-center space-x-4">
                                    <div class="{{ $currentColor['accent'] }} p-3 rounded-lg shadow-sm">
                                        @if($estadoFijo->icon === 'play')
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        @elseif($estadoFijo->icon === 'pause')
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="{{ $currentColor['text'] }} font-semibold">Proyectos {{ $estadoFijo->estado }}</p>
                                        <p class="text-gray-600 text-sm">{{ $estadoFijo->estado === 'Activo' ? 'En ejecución' : ($estadoFijo->estado === 'Inactivo' ? 'En pausa' : 'Completados') }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="{{ $currentColor['text'] }} text-3xl font-bold">{{ $total }}</div>
                                    <div class="text-gray-500 text-sm">proyectos</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Project Status Percentages -->
                <div class="chart-container rounded-2xl shadow-xl p-6 border border-white/20">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-800">Análisis de Distribución</h3>
                        <div class="text-sm text-gray-500">Desglose porcentual</div>
                    </div>
                    
                    <div class="space-y-6">
                        @foreach($estadosFijos as $index => $estadoFijo)
                            @php
                                $key = strtolower($estadoFijo->estado);
                                $total = $proyectosPorEstadoMap->has($key) ? $proyectosPorEstadoMap[$key]->total : 0;
                                $porcentaje = $totalProyectos > 0 ? round(($total / $totalProyectos) * 100, 1) : 0;
                                $colors = [
                                    'green' => ['progress' => 'bg-green-500', 'text' => 'text-green-600', 'bg' => 'bg-green-50'],
                                    'yellow' => ['progress' => 'bg-yellow-500', 'text' => 'text-yellow-600', 'bg' => 'bg-yellow-50'],
                                    'red' => ['progress' => 'bg-red-500', 'text' => 'text-red-600', 'bg' => 'bg-red-50']
                                ];
                                $currentColor = $colors[$estadoFijo->color];
                            @endphp
                            
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-700">{{ $estadoFijo->estado }}</span>
                                    <span class="{{ $currentColor['text'] }} font-bold text-lg">{{ $porcentaje }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                    <div class="{{ $currentColor['progress'] }} h-3 rounded-full transition-all duration-1000 ease-out" 
                                         style="width: {{ $porcentaje }}%"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500">
                                    <span>{{ $total }} proyectos</span>
                                    <span>de {{ $totalProyectos }} en total</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <script id="chartData" type="application/json">@json($proyectosPorEstado->pluck('total'))</script>
    <script id="chartLabels" type="application/json">@json($proyectosPorEstado->pluck('estado'))</script>
    
    <script>
        // Chart.js configuration with modern styling
        Chart.register(ChartDataLabels);
        
        const chartData = JSON.parse(document.getElementById('chartData').textContent);
        const chartLabels = JSON.parse(document.getElementById('chartLabels').textContent);
        
        const colors = {
            primary: '#4f46e5',
            secondary: '#7c3aed',
            success: '#10b981',
            warning: '#f59e0b',
            danger: '#ef4444',
            info: '#3b82f6'
        };
        
        const gradientColors = [
            {
                start: 'rgba(16, 185, 129, 0.8)',
                end: 'rgba(16, 185, 129, 0.2)'
            },
            {
                start: 'rgba(245, 158, 11, 0.8)',
                end: 'rgba(245, 158, 11, 0.2)'
            },
            {
                start: 'rgba(239, 68, 68, 0.8)',
                end: 'rgba(239, 68, 68, 0.2)'
            }
        ];
        
        let mainChart;
        
        function createChart(type = 'bar') {
            const ctx = document.getElementById('mainChart').getContext('2d');
            
            if (mainChart) {
                mainChart.destroy();
            }
            
            const isRadialChart = ['pie', 'doughnut', 'polarArea'].includes(type);
            
            mainChart = new Chart(ctx, {
                type: type,
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Proyectos',
                        data: chartData,
                        backgroundColor: isRadialChart ? [
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(147, 51, 234, 0.8)'
                        ] : chartData.map((_, index) => {
                            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                            const colorSet = gradientColors[index % gradientColors.length];
                            gradient.addColorStop(0, colorSet.start);
                            gradient.addColorStop(1, colorSet.end);
                            return gradient;
                        }),
                        borderColor: isRadialChart ? [
                            'rgba(16, 185, 129, 1)',
                            'rgba(245, 158, 11, 1)',
                            'rgba(239, 68, 68, 1)',
                            'rgba(59, 130, 246, 1)',
                            'rgba(147, 51, 234, 1)'
                        ] : [
                            'rgba(16, 185, 129, 1)',
                            'rgba(245, 158, 11, 1)',
                            'rgba(239, 68, 68, 1)'
                        ],
                        borderWidth: 2,
                        borderRadius: type === 'bar' ? 8 : 0,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: isRadialChart,
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    size: 14,
                                    family: 'Inter'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: 'white',
                            bodyColor: 'white',
                            cornerRadius: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return `${context.label}: ${context.parsed} proyectos`;
                                }
                            }
                        },
                        datalabels: {
                            display: isRadialChart,
                            color: 'white',
                            font: {
                                weight: 'bold',
                                size: 14
                            },
                            formatter: (value, context) => {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${percentage}%`;
                            }
                        }
                    },
                    scales: !isRadialChart ? {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)',
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    family: 'Inter'
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: 'Inter'
                                }
                            }
                        }
                    } : {}
                }
            });
        }
        
        // Initialize chart
        document.addEventListener('DOMContentLoaded', function() {
            createChart();
            
            // Chart type selector
            document.getElementById('chartTypeSelector').addEventListener('change', function(e) {
                createChart(e.target.value);
            });
            
            // Smooth scroll animations for progress bars
            setTimeout(() => {
                document.querySelectorAll('[style*="width:"]').forEach(bar => {
                    bar.style.transition = 'width 2s ease-in-out';
                });
            }, 500);
            
            // Animate counters
            function animateValue(element, start, end, duration) {
                let startTimestamp = null;
                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    const current = Math.floor(progress * (end - start) + start);
                    element.textContent = current.toLocaleString();
                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    }
                };
                window.requestAnimationFrame(step);
            }
            
            // Start counter animations
            setTimeout(() => {
                const counters = document.querySelectorAll('.stat-number');
                counters.forEach((counter, index) => {
                    const finalValue = parseInt(counter.textContent.replace(/[^0-9]/g, ''));
                    if (!isNaN(finalValue)) {
                        counter.textContent = '0';
                        setTimeout(() => {
                            animateValue(counter, 0, finalValue, 2000);
                        }, index * 200);
                    }
                });
            }, 800);
        });
        
        // Add sparkle effect on hover
        document.addEventListener('mousemove', function(e) {
            if (Math.random() < 0.1) {
                createSparkle(e.clientX, e.clientY);
            }
        });
        
        function createSparkle(x, y) {
            const sparkle = document.createElement('div');
            sparkle.className = 'fixed pointer-events-none z-50';
            sparkle.style.left = x + 'px';
            sparkle.style.top = y + 'px';
            sparkle.style.width = '4px';
            sparkle.style.height = '4px';
            sparkle.style.backgroundColor = '#ffffff';
            sparkle.style.borderRadius = '50%';
            sparkle.style.opacity = '0.8';
            sparkle.style.animation = 'sparkleAnimation 1s ease-out forwards';
            
            document.body.appendChild(sparkle);
            
            setTimeout(() => {
                sparkle.remove();
            }, 1000);
        }
        
        // Add sparkle animation CSS
        const sparkleStyle = document.createElement('style');
        sparkleStyle.textContent = `
            @keyframes sparkleAnimation {
                0% {
                    transform: translateY(0px) scale(1);
                    opacity: 0.8;
                }
                50% {
                    transform: translateY(-20px) scale(1.2);
                    opacity: 1;
                }
                100% {
                    transform: translateY(-40px) scale(0.8);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(sparkleStyle);
        
        // Real-time data simulation (optional)
        setInterval(() => {
            const liveIndicator = document.querySelector('.pulse-dot');
            if (liveIndicator) {
                liveIndicator.style.animation = 'none';
                setTimeout(() => {
                    liveIndicator.style.animation = 'pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite';
                }, 100);
            }
        }, 30000);
    </script>
@endpush