$(document).ready(function () {
    console.log("Inicializando filtros de proyectos...");

    // Inicializar DataTable
    const table = $("#proyectosTable").DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json",
        },
    });

    // Función para obtener el estado de una fila
    function getEstado(row) {
        const badge = $(row).find("td:eq(6) .badge"); // La columna de estado es la séptima (índice 6)
        const estado =
            badge.attr("data-estado") || badge.text().toLowerCase().trim();
        return estado;
    }

    // Configurar el filtro de DataTables
    $.fn.dataTable.ext.search = []; // Limpiar filtros existentes

    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        const activeChip = $(".status-chip.active");
        if (!activeChip.length || activeChip.data("estado") === "todos") {
            return true;
        }

        const row = table.row(dataIndex).node();
        const estado = getEstado(row);
        const filtroEstado = activeChip.data("estado").toLowerCase();

        console.log("Filtrando:", {
            estado: estado,
            filtro: filtroEstado,
            coincide: estado === filtroEstado,
        });

        return estado === filtroEstado;
    });

    // Manejar clicks en los chips de estado
    $(".status-chip").on("click", function (e) {
        e.preventDefault();
        const estadoSeleccionado = $(this).data("estado");
        console.log("Click en chip:", estadoSeleccionado);

        // Actualizar estado visual
        $(".status-chip").removeClass("active");
        $(this).addClass("active");

        // Forzar redibujado de la tabla
        table.draw();
    });

    // Activar el filtro "activo" por defecto
    $('.status-chip[data-estado="activo"]').click();
});
