"use strict";

// Formatear números con separadores de miles y decimales
function formatNumber(number) {
    return new Intl.NumberFormat("es-CO", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(number);
}

// Formatear moneda
function formatCurrency(number) {
    return new Intl.NumberFormat("es-CO", {
        style: "currency",
        currency: "COP",
        minimumFractionDigits: 2,
    }).format(number);
}

// Función principal
document.addEventListener("DOMContentLoaded", function () {
    console.log("Inicializando formulario...");

    // Referencias a elementos
    const form = document.getElementById("proyectForm");
    const submitButton = document.getElementById("submitButton");
    const valorTotalInput = document.getElementById("valor_total");
    const plazoInput = document.getElementById("plazo");
    const valorTotalFormatted = document.querySelector(
        ".valor-total-formatted"
    );

    if (!form) {
        console.error("No se encontró el formulario");
        return;
    }

    // Función para formatear inputs numéricos
    function formatInput(input, isMonetary = false) {
        if (!input) return;

        const value = input.value.replace(/[^\d.-]/g, "");
        const number = parseFloat(value);

        if (!isNaN(number)) {
            input.value = number.toFixed(2);

            if (isMonetary && valorTotalFormatted) {
                valorTotalFormatted.textContent = formatCurrency(number);
            }
        }
    }

    // Configurar manejadores de eventos para campos numéricos
    if (valorTotalInput) {
        valorTotalInput.addEventListener("input", () =>
            formatInput(valorTotalInput, true)
        );
        formatInput(valorTotalInput, true); // Formatear valor inicial
    }

    if (plazoInput) {
        plazoInput.addEventListener("input", () => formatInput(plazoInput));
        formatInput(plazoInput); // Formatear valor inicial
    }

    // Manejar envío del formulario
    if (form) {
        form.addEventListener("submit", function (event) {
            console.log("Formulario enviado");
            const confirmMsg =
                "¿Está seguro de que desea guardar los cambios en este proyecto?";

            if (!confirm(confirmMsg)) {
                event.preventDefault();
                return false;
            }

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML =
                    '<i class="fas fa-spinner fa-spin me-1"></i> Guardando...';
            }

            return true;
        });
    }
});
