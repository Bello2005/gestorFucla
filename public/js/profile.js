document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("editProfileForm");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(form);
        const currentName = form
            .querySelector("#profileName")
            .defaultValue.trim();
        const currentEmail = form
            .querySelector(".current-email strong")
            .textContent.trim();
        const newName = formData.get("name").trim();
        const newEmail = formData.get("new_email").trim();
        const newPassword = formData.get("new_password").trim();
        const newPasswordConfirmation = formData
            .get("new_password_confirmation")
            .trim();
        const currentPassword = formData.get("current_password").trim();

        // Detectar cambios
        const nameChanged = newName !== currentName;
        const emailChanged = newEmail.length > 0 && newEmail !== currentEmail;
        const passwordChanged = newPassword.length > 0;

        // Si no hay ningún cambio, no enviar
        if (!nameChanged && !emailChanged && !passwordChanged) {
            alert("No hay cambios para guardar.");
            return;
        }

        // Si el correo se quiere cambiar, debe ser diferente
        if (newEmail.length > 0 && newEmail === currentEmail) {
            alert(
                "El nuevo correo debe ser diferente al actual, o déjalo en blanco para mantenerlo."
            );
            return;
        }

        // Si se cambia contraseña o correo, se requiere contraseña actual
        if ((passwordChanged || emailChanged) && !currentPassword) {
            alert(
                "La contraseña actual es requerida para cambiar la contraseña o el correo electrónico."
            );
            return;
        }

        // Validar confirmación de contraseña
        if (passwordChanged && newPassword !== newPasswordConfirmation) {
            alert("La nueva contraseña y su confirmación no coinciden.");
            return;
        }

        // Enviar solicitud
        fetch("/perfil/actualizar", {
            method: "POST",
            body: formData,
            headers: {
                Accept: "application/json",
            },
            credentials: "same-origin",
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    if (data.email_verification_sent) {
                        alert(
                            "Se ha enviado un enlace de verificación a tu nuevo correo electrónico. " +
                                "Por favor, revisa tu bandeja de entrada y sigue las instrucciones para completar el cambio."
                        );
                    }

                    if (data.profile_updated) {
                        alert("Tu perfil ha sido actualizado correctamente.");
                        location.reload();
                    }
                } else {
                    alert(data.message || "Error al actualizar el perfil.");
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert(
                    "Error al procesar la solicitud. Por favor, intenta nuevamente."
                );
            });
    });
});
