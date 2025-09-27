"use strict";

document.addEventListener("DOMContentLoaded", function () {
    console.log("DOM Cargado");

    const form = document.getElementById("editForm");
    const submitButton = document.getElementById("submitButton");

    // Verificar elementos críticos
    if (!form || !submitButton) {
        console.error("Elementos críticos del formulario no encontrados:", {
            form: !!form,
            submitButton: !!submitButton,
        });
        return;
    }

    // Obtener ID del proyecto del formulario
    const projectId = form.getAttribute("data-project-id");
    if (!projectId) {
        console.error("No se encontró el ID del proyecto en el formulario");
        return;
    }

    console.log("Formulario inicializado correctamente:", {
        formId: form.id,
        projectId: projectId,
        action: form.action,
    });

    // Verificar token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error("No se encontró el token CSRF");
        return;
    }

    // Manejar el envío del formulario
    form.addEventListener("submit", function (event) {
        event.preventDefault();
        event.stopPropagation();
        console.log("Envío de formulario detectado");

        // Verificar que la URL sea correcta para actualizar
        const url = form.action;
        if (!url.includes("/proyectos/")) {
            console.error("URL incorrecta para actualización:", url);
            alert("Error: URL de actualización incorrecta");
            return;
        }

        // Verificar que no sea una URL de eliminación
        if (url.includes("/delete") || url.includes("/destroy")) {
            console.error("URL parece ser de eliminación:", url);
            alert("Error: URL incorrecta detectada");
            return;
        }

        if (confirm("¿Está seguro de que desea guardar los cambios?")) {
            console.log("Confirmación aceptada");

            // Crear FormData para enviar los datos
            const formData = new FormData(form);

            // Verificar y registrar todos los campos del formulario
            console.log("Contenido del FormData antes del envío:");
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }

            // Validación exhaustiva de campos críticos
            const method = formData.get("_method");
            const isEdit = formData.get("is_edit");
            const projectId = formData.get("proyecto_id");

            console.log("Validando campos críticos:", {
                method,
                isEdit,
                projectId,
                url,
            });

            if (method !== "PUT") {
                console.error("Método HTTP incorrecto:", method);
                alert("Error: El método HTTP debe ser PUT");
                return;
            }

            if (isEdit !== "1") {
                console.error("Campo is_edit incorrecto:", isEdit);
                alert("Error: El formulario debe estar en modo edición");
                return;
            }

            if (!projectId || !url.includes(projectId)) {
                console.error("ID de proyecto no coincide:", {
                    formId: projectId,
                    urlId: url.split("/").pop(),
                });
                alert("Error: El ID del proyecto no coincide con la URL");
                return;
            }

            // Desactivar el botón solo si existe
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML =
                    '<i class="fas fa-spinner fa-spin me-1"></i> Guardando...';
            }

            // Log de seguridad pre-envío
            console.log("Preparando envío de formulario:", {
                url: form.action,
                method: "POST",
                formData: Object.fromEntries(formData),
                validationsPassed: true,
            });

            // Enviar la petición usando fetch con manejo mejorado de errores
            fetch(form.action, {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": csrfToken.content,
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-Form-Action": "update",
                },
                credentials: "same-origin",
            })
                .then((response) => {
                    // Log detallado de la respuesta
                    console.log("Respuesta del servidor:", {
                        status: response.status,
                        statusText: response.statusText,
                        headers: Array.from(response.headers.entries()),
                        ok: response.ok,
                    });

                    // Si la respuesta no es exitosa, lanzar error
                    if (!response.ok) {
                        throw new Error(
                            `HTTP error! status: ${response.status}`
                        );
                    }

                    const contentType = response.headers.get("content-type");
                    if (
                        contentType &&
                        contentType.indexOf("application/json") !== -1
                    ) {
                        return response.json().then((data) => {
                            if (!response.ok) {
                                console.error("Error del servidor:", data);
                                throw new Error(
                                    data.message ||
                                        "Error en la respuesta del servidor"
                                );
                            }
                            return data;
                        });
                    } else {
                        if (!response.ok) {
                            console.error(
                                "Error no-JSON del servidor:",
                                response
                            );
                            throw new Error(
                                `Error ${response.status}: ${response.statusText}`
                            );
                        }
                        if (response.redirected) {
                            window.location.href = response.url;
                            return { message: "Redirigiendo..." };
                        }
                        return response.text();
                    }
                })
                .then((data) => {
                    console.log("Respuesta del servidor:", data);
                    if (data.id) {
                        window.location.href = `/proyectos/${data.id}`;
                    } else {
                        window.location.href = "/proyectos";
                    }
                })
                .catch((error) => {
                    console.error("Error detallado:", error);
                    console.error("Stack trace:", error.stack);
                    alert(
                        error.message ||
                            "Error al guardar los cambios. Por favor, intente nuevamente."
                    );

                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.innerHTML =
                            '<i class="fas fa-save me-1"></i> Guardar Cambios';
                    }
                });
        }
    });
});
