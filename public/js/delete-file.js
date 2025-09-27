// Manejar eliminación de archivos
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".delete-file-btn").forEach((button) => {
        button.addEventListener("click", async function (e) {
            e.preventDefault();

            const fileType = this.dataset.fileType;
            const url = this.dataset.fileUrl;
            const csrfToken = document.querySelector(
                'meta[name="csrf-token"]'
            ).content;

            if (
                !confirm(`¿Está seguro de que desea eliminar este ${fileType}?`)
            ) {
                return;
            }

            try {
                const response = await fetch(url, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        Accept: "application/json",
                        "Content-Type": "application/json",
                    },
                    credentials: "same-origin",
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(
                        data.message || `Error al eliminar ${fileType}`
                    );
                }

                // Eliminar el contenedor del archivo
                const fileContainer = this.closest(".mt-3");
                if (fileContainer) {
                    fileContainer.remove();
                }

                // Mostrar mensaje de éxito
                alert(data.message || `${fileType} eliminado exitosamente`);
            } catch (error) {
                console.error("Error al eliminar archivo:", error);
                alert(`Error al eliminar ${fileType}: ${error.message}`);
            }
        });
    });
});
