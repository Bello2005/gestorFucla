// Configuración de debugging
const DEBUG = true;

// Función para logging
function log(message, data = null) {
    if (!DEBUG) return;
    console.log(`[Debug] ${message}`, data || "");
}

// Función para mostrar errores
function handleError(error) {
    log("Error:", error);
    alert("Error al guardar los cambios: " + error.message);
}

// Función para formatear valores numéricos
function formatCurrency(number) {
    return new Intl.NumberFormat("es-CO", {
        style: "currency",
        currency: "COP",
        minimumFractionDigits: 2,
    }).format(number);
}

$(document).ready(function () {
    log("Inicializando formulario de edición");

    // Verificar jQuery y elementos del DOM
    log("jQuery version:", $.fn.jquery);
    log("Formulario encontrado:", $("#proyectForm").length);
    log("Token CSRF:", $('meta[name="csrf-token"]').attr("content"));
    log("Token en formulario:", $('input[name="_token"]').val());

    // Manejo del formulario
    $("#proyectForm").on("submit", function (e) {
        e.preventDefault();
        log("Formulario interceptado");

        // Confirmar antes de enviar
        if (
            !confirm(
                "¿Está seguro de que desea guardar los cambios en este proyecto?"
            )
        ) {
            log("Envío cancelado por el usuario");
            return false;
        }

        const form = this;
        const submitBtn = $("#submitButton");
        const formData = new FormData(form);

        // Log de todos los campos
        log("=== Datos del formulario ===");
        for (let [key, value] of formData.entries()) {
            log(
                `Campo: ${key}`,
                value instanceof File ? `File: ${value.name}` : value
            );
        }
        log("=========================");

        // Deshabilitar botón
        submitBtn
            .prop("disabled", true)
            .html('<i class="fas fa-spinner fa-spin me-1"></i> Guardando...');

        // Enviar formulario
        $.ajax({
            url: form.action,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        })
            .done(function (response) {
                log("Respuesta exitosa:", response);
                window.location.href = form.dataset.returnUrl || "/proyectos";
            })
            .fail(function (xhr, status, error) {
                log("Error en la petición:", { xhr, status, error });

                let errorMessage = "Error al guardar los cambios. ";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage += xhr.responseJSON.message;
                }
                alert(errorMessage);

                // Rehabilitar botón
                submitBtn
                    .prop("disabled", false)
                    .html('<i class="fas fa-save me-1"></i> Guardar Cambios');
            });
    });

    // Formateo de campos numéricos
    $("#valor_total")
        .on("input", function () {
            const value = this.value.replace(/[^\d.-]/g, "");
            const number = parseFloat(value);

            if (!isNaN(number)) {
                this.value = number.toFixed(2);
                $(this)
                    .closest(".input-group")
                    .next(".valor-total-formatted")
                    .text(formatCurrency(number));
            }
        })
        .trigger("input");

    $("#plazo")
        .on("input", function () {
            const value = this.value.replace(/[^\d.-]/g, "");
            const number = parseFloat(value);

            if (!isNaN(number)) {
                this.value = number.toFixed(2);
            }
        })
        .trigger("input");
});
