document.addEventListener('DOMContentLoaded', () => {
    let temporizadorDebounce;

    document.querySelectorAll('input[name$="[pokemon_name]"]').forEach(input => {
        
        const listaSugerencias = document.createElement('div');
        listaSugerencias.className = 'sugerencias-dropdown';
        input.parentNode.appendChild(listaSugerencias);

        input.addEventListener('input', (e) => {
            const textoBusqueda = e.target.value.trim();

            clearTimeout(temporizadorDebounce);

            if (textoBusqueda.length < 3) {
                listaSugerencias.innerHTML = '';
                return;
            }

            temporizadorDebounce = setTimeout(() => {
                buscarPokemonAsincrono(textoBusqueda, listaSugerencias, input);
            }, 300);
        });

        document.addEventListener('click', (e) => {
            if (e.target !== input) {
                listaSugerencias.innerHTML = '';
            }
        });
    });
});

function buscarPokemonAsincrono(query, contenedorLista, inputOriginal) {
    fetch(`buscar_pokemon.php?q=${encodeURIComponent(query)}`)
        .then(respuesta => {
            if (!respuesta.ok) throw new Error('Error en la red');
            return respuesta.json();
        })
        .then(datos => {
            renderizarSugerencias(datos, contenedorLista, inputOriginal);
        })
        .catch(error => console.error('Error al autocompletar:', error));
}

function renderizarSugerencias(listaPokemon, contenedorLista, inputOriginal) {
    contenedorLista.innerHTML = '';

    if (listaPokemon.length === 0) {
        const itemVacio = document.createElement('div');
        itemVacio.className = 'sugerencia-item empty';
        itemVacio.innerText = 'No se encontraron resultados';
        contenedorLista.appendChild(itemVacio);
        return;
    }

    listaPokemon.forEach(nombrePokemon => {
        const item = document.createElement('div');
        item.className = 'sugerencia-item';
        item.innerText = nombrePokemon;

        item.addEventListener('click', () => {
            inputOriginal.value = nombrePokemon;
            contenedorLista.innerHTML = '';
        });

        contenedorLista.appendChild(item);
    });
}