document.addEventListener('DOMContentLoaded', () => {
    const botonesEliminar = document.querySelectorAll('.action-delete');

    botonesEliminar.forEach(boton => {
        boton.addEventListener('click', (evento) => {
            const confirmacion = confirm('¿Seguro que deseas eliminar este equipo competitivo? Esta acción no se puede deshacer.');
            
            if (!confirmacion) {
                evento.preventDefault();
            }
        });
    });
});
