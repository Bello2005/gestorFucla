console.log('Iniciando script de filtrado...');

$(document).ready(function() {
    console.log('DOM listo');
    
    // Elementos del DOM
    const statusChips = document.querySelectorAll(".status-chip");
    const tableElement = document.querySelector("#proyectosTable");
    
    console.log('Status chips encontrados:', statusChips.length);
    console.log('Tabla encontrada:', !!tableElement);

    if (!tableElement) {
        console.error("No se encontró la tabla de proyectos");
        return;
    }

    // Inicializar DataTable
    const table = $(tableElement).DataTable({
        order: [], // Sin orden por defecto
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        }
    });

    console.log('DataTable inicializada');

    // Función para obtener el texto del estado de una celda
    function getEstadoFromCell(cell) {
        const badge = cell.querySelector('.badge');
        if (badge) {
            const estado = badge.textContent.trim().toLowerCase();
            console.log('Estado encontrado en celda:', estado);
            return estado;
        }
        console.log('No se encontró badge en la celda');
        return '';
    }

    // Función para actualizar la apariencia de los chips
    function updateChipsAppearance(activeChip) {
        console.log('Actualizando apariencia, chip activo:', activeChip?.dataset?.estado);
        statusChips.forEach(chip => {
            chip.classList.remove("active");
        });
        if (activeChip) {
            activeChip.classList.add("active");
        }
    }

    // Configurar el filtro de DataTables
    $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(fn => fn.name !== 'estadoFilter');

    $.fn.dataTable.ext.search.push(
        function estadoFilter(settings, data, dataIndex) {
            const selectedChip = document.querySelector('.status-chip.active');
            if (!selectedChip || selectedChip.dataset.estado === 'todos') {
                return true;
            }

            const row = table.row(dataIndex).node();
            const estadoCell = row.querySelector('td:nth-child(7)'); // Columna de estado
            const estado = getEstadoFromCell(estadoCell);
            
            const matches = estado === selectedChip.dataset.estado.toLowerCase();
            console.log('Filtrando fila:', { estado, esperado: selectedChip.dataset.estado, matches });
            
            return matches;
        }
    );

    // Manejar clicks en los chips de estado
    statusChips.forEach(chip => {
        chip.addEventListener("click", function(e) {
            e.preventDefault();
            console.log('Click en chip:', this.dataset.estado);
            
            // Actualizar apariencia
            updateChipsAppearance(this);
            
            // Aplicar filtro
            table.draw();
        });
    });

    // Activar el filtro de "activos" por defecto
    const defaultChip = document.querySelector('[data-estado="activo"]');
    if (defaultChip) {
        console.log('Activando filtro por defecto: activo');
        updateChipsAppearance(defaultChip);
        table.draw();
    }
});
});

            // Si no hay estados seleccionados, seleccionar "activo" por defecto
            if (selectedStates.size === 0) {
                selectedStates.add("activo");
                document
                    .querySelector('[data-estado="activo"]')
                    .classList.add("active");
            }

            // Guardar la selección en localStorage
            localStorage.setItem(
                "proyectosEstadosFiltro",
                JSON.stringify(Array.from(selectedStates))
            );

            updateFilters();
        });
    });

    // Cargar filtros guardados
    const savedFilters = localStorage.getItem("proyectosEstadosFiltro");
    if (savedFilters) {
        selectedStates = new Set(JSON.parse(savedFilters));
        statusChips.forEach((chip) => {
            if (selectedStates.has(chip.dataset.estado)) {
                chip.classList.add("active");
            }
        });
        updateFilters();

        // Mostrar toast de notificación
        const toast = document.createElement("div");
        toast.className =
            "toast align-items-center text-white bg-info border-0";
        toast.setAttribute("role", "alert");
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    Mostrando: ${Array.from(selectedStates).join(
                        ", "
                    )} (guardado)
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
    } else {
        // Activar "Activos" por defecto
        document
            .querySelector('[data-estado="activo"]')
            .classList.add("active");
        updateFilters();
    }
});
