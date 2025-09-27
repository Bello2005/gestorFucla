document.addEventListener("DOMContentLoaded", function () {
    // Elementos del DOM
    const tableElement = document.querySelector("#proyectosTable");
    if (!tableElement || tableElement.tagName.toLowerCase() !== "table") {
        console.error(
            "Error: El elemento #proyectosTable no existe o no es una tabla"
        );
        return;
    }

    const statusChips = document.querySelectorAll(".status-chip");
    let selectedStates = new Set(["activo"]); // Por defecto, mostrar activos

    // Configuración base de DataTables
    const dataTableConfig = {
        language: {
            emptyTable: "No hay datos disponibles en la tabla",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "Mostrando 0 a 0 de 0 registros",
            infoFiltered: "(filtrado de _MAX_ registros totales)",
            lengthMenu: "Mostrar _MENU_ registros",
            loadingRecords: "Cargando...",
            processing: "Procesando...",
            search: "Buscar:",
            zeroRecords: "No se encontraron registros coincidentes",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "Siguiente",
                previous: "Anterior",
            },
            aria: {
                sortAscending:
                    ": activar para ordenar la columna ascendentemente",
                sortDescending:
                    ": activar para ordenar la columna descendentemente",
            },
        },
        pageLength: 10,
        dom:
            "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        processing: true,
        serverSide: false,
        ordering: true,
        order: [[0, "asc"]],
        responsive: true,
        stateSave: true,
        searchDelay: 500,
        columnDefs: [
            {
                targets: [7], // Columna de acciones
                orderable: false,
                searchable: false,
            },
            {
                targets: [5], // Columna de valor total
                className: "text-end",
                render: function (data, type, row) {
                    if (type === "display" && data) {
                        const value = parseFloat(
                            data.replace(/[^0-9.-]+/g, "")
                        );
                        if (!isNaN(value)) {
                            return new Intl.NumberFormat("es-CO", {
                                style: "currency",
                                currency: "COP",
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0,
                            }).format(value);
                        }
                    }
                    return data;
                },
            },
        ],
        drawCallback: function (settings) {
            // Reactivar tooltips después de cada redibujado
            const tooltips = $('[data-bs-toggle="tooltip"]');
            if (tooltips.length > 0) {
                tooltips.tooltip();
            }

            // Verificar si hay datos
            const api = this.api();
            $(".alert.alert-info").remove(); // Remover alertas anteriores
            if (api.rows().count() === 0) {
                $(this)
                    .parent()
                    .append(
                        '<div class="alert alert-info text-center my-3">No hay proyectos disponibles con los filtros actuales.</div>'
                    );
            }
        },
    };

    // Función para mostrar mensajes
    function showMessage(message, type = "info") {
        const alertDiv = document.createElement("div");
        alertDiv.className = `alert alert-${type} alert-dismissible fade show mt-3`;
        alertDiv.role = "alert";
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        tableElement.parentNode.insertBefore(
            alertDiv,
            tableElement.nextSibling
        );

        // Auto-ocultar después de 5 segundos
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    // Función para actualizar la apariencia de los chips
    function updateChipsAppearance() {
        statusChips.forEach((chip) => {
            const estado = chip.dataset.estado;
            if (selectedStates.has(estado)) {
                chip.classList.add("active");
            } else {
                chip.classList.remove("active");
            }
        });
    }

    // Inicializar DataTable
    let dataTable;
    try {
        // Destruir instancia existente si la hay
        if ($.fn.DataTable.isDataTable(tableElement)) {
            $(tableElement).DataTable().destroy();
        }

        // Inicializar nueva instancia
        dataTable = $(tableElement).DataTable(dataTableConfig);

        // Verificar si hay datos
        if (dataTable.data().length === 0) {
            showMessage(
                "No hay proyectos disponibles en este momento.",
                "warning"
            );
        }

        // Configurar filtro de estado
        $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(
            (callback) => callback.name !== "estadoFilter"
        );

        $.fn.dataTable.ext.search.push(function estadoFilter(
            settings,
            data,
            dataIndex
        ) {
            if (selectedStates.size === 0) return true;

            const estadoCell = $(dataTable.row(dataIndex).node()).find(
                "td:eq(6)"
            );
            if (!estadoCell.length) return true;

            const estado = estadoCell
                .find(".badge")
                .text()
                .trim()
                .toLowerCase();
            return selectedStates.has("todos") || selectedStates.has(estado);
        });

        // Evento click para los chips de estado
        statusChips.forEach((chip) => {
            chip.addEventListener("click", function (e) {
                e.preventDefault();
                const estado = this.dataset.estado.toLowerCase();

                // Limpiar estados anteriores
                selectedStates.clear();

                // Añadir solo el estado seleccionado
                selectedStates.add(estado);

                // Actualizar apariencia y guardar estado
                updateChipsAppearance();
                localStorage.setItem(
                    "proyectosEstadosFiltro",
                    JSON.stringify(Array.from(selectedStates))
                );
                dataTable.draw();
            });
        });

        // Cargar filtros guardados
        const savedFilters = localStorage.getItem("proyectosEstadosFiltro");
        if (savedFilters) {
            try {
                const filters = JSON.parse(savedFilters);
                selectedStates = new Set(filters);
                updateChipsAppearance();
                dataTable.draw();
            } catch (e) {
                console.error("Error al cargar filtros guardados:", e);
                selectedStates = new Set(["activo"]);
                updateChipsAppearance();
            }
        }
    } catch (error) {
        console.error("Error al inicializar DataTable:", error);
        showMessage(
            "Ocurrió un error al cargar la tabla. Por favor, recarga la página.",
            "danger"
        );
    }
});
