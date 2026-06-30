let pokeCache = [];

document.addEventListener('DOMContentLoaded', async () => {
    await precargarListaPokemon();

    const pokemonInputs = document.querySelectorAll('.input-pokemon');

    pokemonInputs.forEach(input => {
        const card = input.closest('.pokemon-card');
        const slotId = card.dataset.slot;
        const dropdown = input.nextElementSibling;

        input.addEventListener('input', (e) => {
            const query = e.target.value.trim().toLowerCase();

            if (query.length === 0) {
                dropdown.innerHTML = '';
                resetearSlot(slotId);
                return;
            }

            if (query.length < 2) {
                dropdown.innerHTML = '';
                return;
            }

            const filtrados = pokeCache
                .filter(p => p.includes(query))
                .slice(0, 5);

            renderSugerencias(filtrados, input, dropdown, slotId);
        });

        input.addEventListener('focus', (e) => {
            const query = e.target.value.trim().toLowerCase();
            if (query.length >= 2) {
                const filtrados = pokeCache.filter(p => p.includes(query)).slice(0, 5);
                renderSugerencias(filtrados, input, dropdown, slotId);
            }
        });

        document.addEventListener('click', (e) => {
            if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.innerHTML = '';
            }
        });
    });
});

async function precargarListaPokemon() {
    try {
        const res = await fetch('https://pokeapi.co/api/v2/pokemon?limit=1025');
        const data = await res.json();
        pokeCache = data.results.map(p => p.name);
        console.log(`📦 [Caché] ${pokeCache.length} Pokémon listos en memoria local.`);
    } catch (err) {
        console.error("No se pudo precargar la lista de Pokémon:", err);
    }
}

function renderSugerencias(lista, input, dropdown, slotId) {
    dropdown.innerHTML = '';
    
    if (lista.length === 0) {
        dropdown.innerHTML = `<div class="sugerencia-item empty">No se encontraron resultados</div>`;
        return;
    }

    lista.forEach(pokeName => {
        const item = document.createElement('div');
        item.className = 'sugerencia-item';
        item.textContent = pokeName.charAt(0).toUpperCase() + pokeName.slice(1);
        
        item.addEventListener('click', () => {
            input.value = item.textContent;
            dropdown.innerHTML = '';
            cargarDatosPokemon(pokeName, slotId);
        });
        dropdown.appendChild(item);
    });
}

async function cargarDatosPokemon(pokeName, slotId) {
    const spriteImg = document.getElementById(`sprite-${slotId}`);
    const typesContainer = document.getElementById(`types-${slotId}`);
    const moveInputs = document.querySelectorAll(`[name^="pokemon[${slotId}][moves]"]`);

    try {
        spriteImg.style.opacity = '0.4';

        const res = await fetch(`https://pokeapi.co/api/v2/pokemon/${pokeName.toLowerCase()}`);
        if (!res.ok) throw new Error();
        const data = await res.json();

        spriteImg.src = data.sprites.front_default || '../assets/img/unknown.png';
        spriteImg.style.opacity = '1';

        typesContainer.innerHTML = '';
        data.types.forEach(t => {
            const badge = document.createElement('span');
            badge.className = `badge type-${t.type.name}`;
            badge.textContent = t.type.name;
            typesContainer.appendChild(badge);
        });

        const listaMovimientos = data.moves.map(m => m.move.name);

        moveInputs.forEach(moveInput => {
            moveInput.disabled = false;
            moveInput.value = '';
            moveInput.placeholder = "- Selecciona un movimiento";
            configurarAutocompletadoMovimientos(moveInput, listaMovimientos);
        });

    } catch (err) {
        resetearSlot(slotId);
    }
}

function resetearSlot(slotId) {
    const spriteImg = document.getElementById(`sprite-${slotId}`);
    const typesContainer = document.getElementById(`types-${slotId}`);
    const moveInputs = document.querySelectorAll(`[name^="pokemon[${slotId}][moves]"]`);

    spriteImg.src = '../assets/img/unknown.png';
    spriteImg.style.opacity = '1';

    typesContainer.innerHTML = `<span class="badge type-unknown">???</span>`;

    moveInputs.forEach(moveInput => {
        moveInput.disabled = true;
        moveInput.value = '';
        moveInput.placeholder = "- Bloqueado";
        
        const nuevoInput = moveInput.cloneNode(true);
        moveInput.parentNode.replaceChild(nuevoInput, moveInput);
    });
}

function configurarAutocompletadoMovimientos(input, movimientosLegales) {
    const dropdown = input.nextElementSibling;

    const newInput = input.cloneNode(true);
    input.parentNode.replaceChild(newInput, input);

    newInput.addEventListener('input', (e) => {
        const query = e.target.value.trim().toLowerCase();
        if (query.length < 1) {
            dropdown.innerHTML = '';
            return;
        }

        const filtrados = movimientosLegales
            .filter(move => move.includes(query))
            .slice(0, 5);

        dropdown.innerHTML = '';
        filtrados.forEach(moveName => {
            const item = document.createElement('div');
            item.className = 'sugerencia-item';
            item.textContent = moveName.split('-').join(' ').toUpperCase();
            
            item.addEventListener('click', () => {
                newInput.value = item.textContent;
                dropdown.innerHTML = '';
            });
            dropdown.appendChild(item);
        });
    });

    newInput.addEventListener('focus', () => {
        const query = newInput.value.trim().toLowerCase();
        const filtrados = movimientosLegales.filter(move => move.includes(query)).slice(0, 5);
        if(filtrados.length > 0 && query.length > 0) {
            dropdown.innerHTML = '';
            filtrados.forEach(moveName => {
                const item = document.createElement('div');
                item.className = 'sugerencia-item';
                item.textContent = moveName.split('-').join(' ').toUpperCase();
                item.addEventListener('click', () => {
                    newInput.value = item.textContent;
                    dropdown.innerHTML = '';
                });
                dropdown.appendChild(item);
            });
        }
    });

    document.addEventListener('click', (e) => {
        if (!newInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.innerHTML = '';
        }
    });
}