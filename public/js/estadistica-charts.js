document.addEventListener("DOMContentLoaded", function () {
    try {
        // Obtener elementos del DOM
        const ctx = document.getElementById("mainChart").getContext("2d");
        const rawData = document.getElementById("chartData");
        const rawLabels = document.getElementById("chartLabels");
        const selector = document.getElementById("chartTypeSelector");

        // Validar elementos
        if (!ctx || !rawData || !rawLabels) {
            throw new Error(
                "No se encontraron los elementos necesarios en el DOM"
            );
        }

        // Parsear datos
        const data = JSON.parse(rawData.textContent);
        const labels = JSON.parse(rawLabels.textContent);

        console.log("Datos cargados:", { data, labels });

        // Colores para el gráfico
        const colors = {
            primary: "#4361ee", // Azul
            success: "#16a34a", // Verde
            warning: "#ea580c", // Naranja
            danger: "#dc2626", // Rojo
            info: "#2dd4bf", // Turquesa
            secondary: "#8b5cf6", // Púrpura
        };

        let currentChart = null;

        function createChart(type) {
            // Destruir gráfico existente si hay uno
            if (currentChart) {
                currentChart.destroy();
            }

            // Configuración base del gráfico
            const config = {
                type: type,
                data: {
                    labels: labels,
                    datasets: [
                        {
                            data: data,
                            backgroundColor: Object.values(colors).slice(
                                0,
                                data.length
                            ),
                            borderColor:
                                type === "line"
                                    ? colors.primary
                                    : Object.values(colors).slice(
                                          0,
                                          data.length
                                      ),
                            borderWidth: type === "line" ? 2 : 1,
                            tension: 0.1,
                            fill: type === "line" ? false : true,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: ["pie", "doughnut"].includes(type),
                            position: "bottom",
                            labels: {
                                font: {
                                    size: 13,
                                    family: "'system-ui', sans-serif",
                                },
                                padding: 15,
                            },
                        },
                        title: {
                            display: true,
                            text: "Distribución de Proyectos por Estado",
                            font: {
                                size: 16,
                                family: "'system-ui', sans-serif",
                                weight: "bold",
                            },
                            padding: { bottom: 15 },
                        },
                        tooltip: {
                            backgroundColor: "rgba(0,0,0,0.8)",
                            padding: 12,
                            titleFont: {
                                size: 14,
                            },
                            bodyFont: {
                                size: 13,
                            },
                            callbacks: {
                                label: function (context) {
                                    return ` ${context.label}: ${context.parsed} proyecto(s)`;
                                },
                            },
                        },
                    },
                    scales: ["bar", "line"].includes(type)
                        ? {
                              y: {
                                  beginAtZero: true,
                                  ticks: {
                                      stepSize: 1,
                                      font: {
                                          size: 12,
                                      },
                                  },
                                  grid: {
                                      color: "rgba(0,0,0,0.1)",
                                  },
                              },
                              x: {
                                  ticks: {
                                      font: {
                                          size: 12,
                                      },
                                  },
                                  grid: {
                                      display: false,
                                  },
                              },
                          }
                        : undefined,
                },
            };

            // Crear nuevo gráfico
            currentChart = new Chart(ctx, config);
        }

        // Inicializar gráfico
        createChart("bar");

        // Manejar cambios en el selector de tipo
        if (selector) {
            selector.addEventListener("change", (e) =>
                createChart(e.target.value)
            );
        }
    } catch (error) {
        console.error("Error al inicializar el gráfico:", error);
        // Mostrar mensaje de error en el canvas
        const ctx = document.getElementById("mainChart").getContext("2d");
        ctx.font = "14px system-ui";
        ctx.fillStyle = "#dc2626";
        ctx.fillText("Error al cargar el gráfico: " + error.message, 10, 30);
    }
});
