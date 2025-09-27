document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const filtrosModal = document.getElementById('filtrosModal');
    const btnFiltros = document.getElementById('btnFiltros');
    const filtrosForm = document.getElementById('filtrosForm');
    const btnAplicar = document.getElementById('aplicarFiltros');
    const btnLimpiar = document.getElementById('limpiarFiltros');
    const btnSavePreset = document.getElementById('savePreset');
    const presetSelect = document.getElementById('savedPresets');
    
    // Inicializar el modal
    const modal = new bootstrap.Modal(filtrosModal);
    
    // Abrir modal al hacer click en el botón de filtros
    btnFiltros.addEventListener('click', () => modal.show());
    
    // Función para obtener los valores del formulario
    function getFormValues() {
        const formData = new FormData(filtrosForm);
        return {
            estados: formData.getAll('estados[]'),
            fechaInicio: formData.get('fechaInicio'),
            fechaFin: formData.get('fechaFin'),
            montoMin: formData.get('montoMin'),
            montoMax: formData.get('montoMax'),
            entidad: formData.get('entidad')
        };
    }
    
    // Función para aplicar los filtros a la tabla
    function aplicarFiltros(filtros) {
        if (!$.fn.DataTable.isDataTable('#proyectosTable')) {
            return;
        }
        const dataTable = $('#proyectosTable').DataTable();
        
        // Actualizar el filtro avanzado
        dataTable.settings()[0].customFilter = filtros;
        
        // Redibujar la tabla
        dataTable.draw();
            
            // Filtrar por estado
            if (filtros.estados.length > 0 && !filtros.estados.includes(row.estado.toLowerCase())) {
                return false;
            }
            
            // Filtrar por fecha
            if (filtros.fechaInicio && new Date(row.fecha_inicio) < new Date(filtros.fechaInicio)) {
                return false;
            }
            if (filtros.fechaFin && new Date(row.fecha_fin) > new Date(filtros.fechaFin)) {
                return false;
            }
            
            // Filtrar por monto
            const monto = parseFloat(row.monto.replace(/[^0-9.-]+/g, ''));
            if (filtros.montoMin && monto < parseFloat(filtros.montoMin)) {
                return false;
            }
            if (filtros.montoMax && monto > parseFloat(filtros.montoMax)) {
                return false;
            }
            
            // Filtrar por entidad
            if (filtros.entidad && row.entidad_contratante !== filtros.entidad) {
                return false;
            }
            
            return true;
        });
        
        // Redibujar la tabla
        dataTable.draw();
    }
    
    // Evento para aplicar filtros
    btnAplicar.addEventListener('click', () => {
        const filtros = getFormValues();
        aplicarFiltros(filtros);
        
        // Guardar filtros en localStorage
        localStorage.setItem('proyectosFiltrosAvanzados', JSON.stringify(filtros));
        
        modal.hide();
        
        // Mostrar toast
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-white bg-info border-0';
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    Filtros aplicados
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
    });
    
    // Evento para limpiar filtros
    btnLimpiar.addEventListener('click', () => {
        filtrosForm.reset();
        localStorage.removeItem('proyectosFiltrosAvanzados');
        
        // Limpiar filtros de la tabla
        const dataTable = $('#proyectosTable').DataTable();
        $.fn.dataTable.ext.search.pop();
        dataTable.draw();
    });
    
    // Manejo de presets
    btnSavePreset.addEventListener('click', () => {
        const presetName = document.getElementById('presetName').value.trim();
        if (!presetName) return;
        
        const presets = JSON.parse(localStorage.getItem('proyectosPresets') || '{}');
        presets[presetName] = getFormValues();
        localStorage.setItem('proyectosPresets', JSON.stringify(presets));
        
        // Actualizar lista de presets
        actualizarListaPresets();
    });
    
    // Función para actualizar la lista de presets
    function actualizarListaPresets() {
        const presets = JSON.parse(localStorage.getItem('proyectosPresets') || '{}');
        presetSelect.innerHTML = '<option value="">Seleccionar preset...</option>';
        
        Object.keys(presets).forEach(name => {
            const option = document.createElement('option');
            option.value = name;
            option.textContent = name;
            presetSelect.appendChild(option);
        });
    }
    
    // Cargar preset seleccionado
    presetSelect.addEventListener('change', () => {
        const presetName = presetSelect.value;
        if (!presetName) return;
        
        const presets = JSON.parse(localStorage.getItem('proyectosPresets') || '{}');
        const preset = presets[presetName];
        
        // Aplicar valores del preset al formulario
        const estadosInputs = document.querySelectorAll('input[name="estados[]"]');
        estadosInputs.forEach(input => {
            input.checked = preset.estados.includes(input.value);
        });
        
        document.getElementById('fechaInicio').value = preset.fechaInicio || '';
        document.getElementById('fechaFin').value = preset.fechaFin || '';
        document.getElementById('montoMin').value = preset.montoMin || '';
        document.getElementById('montoMax').value = preset.montoMax || '';
        document.getElementById('entidad').value = preset.entidad || '';
    });
    
    // Cargar filtros guardados al iniciar
    const filtrosGuardados = localStorage.getItem('proyectosFiltrosAvanzados');
    if (filtrosGuardados) {
        const filtros = JSON.parse(filtrosGuardados);
        aplicarFiltros(filtros);
    }
    
    // Inicializar lista de presets
    actualizarListaPresets();
});