document.addEventListener("DOMContentLoaded", function () {
    const button = document.getElementById("toggle-password");
    const field = document.querySelector('input[type="password"]');
    if (!field || !button) {
        return;
    }
    button.addEventListener("click", function () {
        const isHidden = field.type === "password";
        field.type = isHidden ? "text" : "password";
        button.setAttribute(
            "aria-label",
            isHidden ? "Masquer le mot de passe" : "Afficher le mot de passe",
        );
    });
});
