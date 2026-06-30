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

    const modal = document.getElementById('modal-ia');
    const btnCerrar = document.querySelector('.close-modal');
    const loader = document.getElementById('ia-loader');
    const resultadoContenedor = document.getElementById('ia-resultado');
    const botonesAnalizar = document.querySelectorAll('.action-analyze');

    botonesAnalizar.forEach(boton => {
        boton.addEventListener('click', () => {
            const idEquipo = boton.getAttribute('data-id');

            modal.style.display = 'block';
            loader.style.display = 'block';
            resultadoContenedor.style.innerHTML = '';
            resultadoContenedor.style.display = 'none';

            fetch(`analizar_equipo.php?id=${idEquipo}`)
                .then(respuesta => {
                    if (!respuesta.ok) throw new Error('Error en el servidor de IA');
                    return respuesta.json();
                })
                .then(datos => {
                    loader.style.display = 'none';
                    
                    resultadoContenedor.style.display = 'block';
                    if (datos.analisis) {
                        resultadoContenedor.innerText = datos.analisis;
                    } else if (datos.error) {
                        resultadoContenedor.innerHTML = `<p class="alert alert-danger">❌ Error: ${datos.error}</p>`;
                    }
                })
                .catch(error => {
                    loader.style.display = 'none';
                    resultadoContenedor.style.display = 'block';
                    resultadoContenedor.innerHTML = `<p class="alert alert-danger">❌ Error crítico: No se pudo conectar con el motor de IA.</p>`;
                    console.error(error);
                });
        });
    });

    btnCerrar.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});