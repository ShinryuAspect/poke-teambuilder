document.addEventListener('DOMContentLoaded', () => {
    const formulario = document.querySelector('form');
    
    if (!formulario) return;

    formulario.addEventListener('submit', (evento) => {
        document.querySelectorAll('.error-feedback').forEach(el => el.remove());
        
        let contenedorErrores = [];
        
        const usernameInput = document.getElementById('username');
        if (usernameInput && usernameInput.value.trim() === '') {
            marcarError(usernameInput, 'El nombre de usuario es obligatorio.');
            contenedorErrores.push(true);
        }

        const emailInput = document.getElementById('email');
        if (emailInput) {
            const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!regexEmail.test(emailInput.value.trim())) {
                marcarError(emailInput, 'Introduce un correo electrónico válido.');
                contenedorErrores.push(true);
            }
        }

        const loginInput = document.getElementById('login_input');
        if (loginInput && loginInput.value.trim() === '') {
            marcarError(loginInput, 'Introduce tu usuario o correo.');
            contenedorErrores.push(true);
        }

        const passwordInput = document.getElementById('password');
        if (passwordInput && passwordInput.value.length < 8) {
            marcarError(passwordInput, 'La contraseña debe tener al menos 8 caracteres.');
            contenedorErrores.push(true);
        }

        const passwordConfirmInput = document.getElementById('password_confirm');
        if (passwordConfirmInput && passwordInput) {
            if (passwordInput.value !== passwordConfirmInput.value) {
                marcarError(passwordConfirmInput, 'Las contraseñas no coinciden.');
                contenedorErrores.push(true);
            }
        }

        if (contenedorErrores.length > 0) {
            evento.preventDefault();
        }
    });
});

function marcarError(elementoInput, mensaje) {
    const errorDiv = document.createElement('span');
    errorDiv.className = 'error-feedback';
    errorDiv.innerText = mensaje;
    elementoInput.parentNode.appendChild(errorDiv);
    elementoInput.style.borderColor = '#c62828';
}
