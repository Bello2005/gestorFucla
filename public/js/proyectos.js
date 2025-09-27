$(document).ready(function() {
    console.log('Iniciando configuración de tabla y filtros...');

    // Inicializar DataTable
    var table = $('#proyectosTable').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        dom: 't<"bottom"lp>',
        order: [[0, 'asc']],
        pageLength: 10
    });

    // Variables para control de filtros
    var estadoActual = 'todos';
    var busquedaActual = '';

    // Función para obtener el estado de una fila
    function getEstado(row) {
        var badge = $(row).find('span[data-estado]');
        if (badge.length === 0) {
            badge = $(row).find('.badge[data-estado]');
        }
        return badge.attr('data-estado') || '';
    }

    // Función para contar estados
    function contarEstados() {
        var contadores = {
            'todos': 0,
            'activo': 0,
            'inactivo': 0,
            'cerrado': 0
        };

        $('#proyectosTable tbody tr:visible').each(function() {
            var estado = getEstado(this);
            if (estado && contadores.hasOwnProperty(estado)) {
                contadores[estado]++;
            }
            contadores['todos']++;
        });

        return contadores;
    }

    // Función para actualizar contadores en los chips
    function actualizarContadores() {
        var contadores = contarEstados();
        
        $('.status-chip').each(function() {
            var estado = $(this).attr('data-estado');
            var count = contadores[estado] || 0;
            $(this).find('.count').text('(' + count + ')');
        });
    }

    // Función principal de filtrado
    function aplicarFiltros() {
        console.log('Aplicando filtros - Estado:', estadoActual, 'Búsqueda:', busquedaActual);

        $('#proyectosTable tbody tr').each(function() {
            var fila = $(this);
            var estado = getEstado(this);
            var textoFila = fila.text().toLowerCase();
            
            // Verificar filtro de estado
            var coincideEstado = (estadoActual === 'todos') || (estado === estadoActual);
            
            // Verificar filtro de búsqueda
            var coincideBusqueda = !busquedaActual || textoFila.includes(busquedaActual.toLowerCase());
            
            // Mostrar u ocultar fila
            if (coincideEstado && coincideBusqueda) {
                fila.show();
            } else {
                fila.hide();
            }
        });

        // Actualizar contadores
        actualizarContadores();
    }

    // Event listener para clicks en chips de estado
    $('.status-chip').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var nuevoEstado = $(this).attr('data-estado');
        console.log('Click en chip:', nuevoEstado);

        // Actualizar estado visual
        $('.status-chip').removeClass('active');
        $(this).addClass('active');

        // Actualizar filtro
        estadoActual = nuevoEstado;
        
        // Aplicar filtros
        aplicarFiltros();
    });

    // Event listener para búsqueda
    $('#searchProjects').on('input', function() {
        busquedaActual = $(this).val().trim();
        aplicarFiltros();
    });

    // Configuración inicial
    $('.status-chip[data-estado="todos"]').addClass('active');
    estadoActual = 'todos';
    
    // Aplicar filtros iniciales
    setTimeout(function() {
        aplicarFiltros();
        console.log('Filtros iniciales aplicados');
    }, 500);

    // Debug: verificar elementos
    console.log('Elementos encontrados:');
    console.log('- Chips:', $('.status-chip').length);
    console.log('- Tabla:', $('#proyectosTable').length);
    console.log('- Buscador:', $('#searchProjects').length);
    
    // Debug: mostrar estados en tabla
    $('#proyectosTable tbody tr').each(function(i) {
        var estado = getEstado(this);
        console.log('Fila', i, 'estado:', estado);
    });

    // Tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});