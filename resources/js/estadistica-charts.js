// Script para renderizar gráficos en la vista de Estadística

document.addEventListener("DOMContentLoaded", function () {
    const chartTypeSelector = document.getElementById("chartTypeSelector");
    let chartInstance = null;

    function renderChart(type, data, labels) {
        const ctx = document.getElementById("mainChart").getContext("2d");
        if (chartInstance) chartInstance.destroy();
        chartInstance = new Chart(ctx, {
            type: type,
            data: {
                labels: labels,
                datasets: [
                    {
                        label: "Proyectos por Estado",
                        data: data,
                        backgroundColor: [
                            "#007bff",
                            "#28a745",
                            "#ffc107",
                            "#dc3545",
                            "#6c757d",
                            "#17a2b8",
                        ],
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: "top" },
                    title: { display: true, text: "Proyectos por Estado" },
                },
            },
        });
    }

    // Obtener datos desde el template
    const chartData = JSON.parse(
        document.getElementById("chartData").textContent
    );
    const chartLabels = JSON.parse(
        document.getElementById("chartLabels").textContent
    );

    // Inicializar con tipo de gráfico por defecto
    renderChart("bar", chartData, chartLabels);

    chartTypeSelector.addEventListener("change", function (e) {
        renderChart(e.target.value, chartData, chartLabels);
    });
});
