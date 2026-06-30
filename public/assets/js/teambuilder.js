const DB_NAME = 'PokeAppCacheDB';
const DB_VERSION = 1;
const STORE_NAME = 'pokemon_lists';
let db;

document.addEventListener('DOMContentLoaded', async () => {
    try {
        db = await inicializarDB();
        await inicializarCachePokemon();
    } catch (err) {
        console.error("Error al preparar la base de datos de caché:", err);
    }

    const pokemonInputs = document.querySelectorAll('.input-pokemon');

    pokemonInputs.forEach(input => {
        const card = input.closest('.pokemon-card');
        const slotId = card.dataset.slot;
        const dropdown = input.nextElementSibling;

        input.addEventListener('input', async (e) => {
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

            const pokeCache = await obtenerPokemonDeDB();
            const filtrados = pokeCache
                .filter(p => p.includes(query))
                .slice(0, 5);

            renderSugerencias(filtrados, input, dropdown, slotId);
        });

        input.addEventListener('focus', async () => {
            const query = input.value.trim().toLowerCase();
            if (query.length >= 2) {
                const pokeCache = await obtenerPokemonDeDB();
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

function inicializarDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, DB_VERSION);

        request.onupgradeneeded = (e) => {
            const database = e.target.result;
            if (!database.objectStoreNames.contains(STORE_NAME)) {
                database.createObjectStore(STORE_NAME);
            }
        };

        request.onsuccess = (e) => resolve(e.target.result);
        request.onerror = (e) => reject(e.target.error);
    });
}

async function inicializarCachePokemon() {
    const cachedData = await obtenerPokemonDeDB();

    if (cachedData && cachedData.length > 0) {
        console.log(`📦 [IndexedDB] ${cachedData.length} Pokémon restaurados desde la base de datos local.`);
        return;
    }

    console.log("🌐 [PokeAPI] Descargando catálogo por primera vez...");
    try {
        const res = await fetch('https://pokeapi.co/api/v2/pokemon?limit=1025');
        const data = await res.json();
        const listaNombres = data.results.map(p => p.name);

        await guardarPokemonEnDB(listaNombres);
        console.log("💾 [IndexedDB] Catálogo guardado con éxito en el almacenamiento local.");
    } catch (err) {
        console.error("Error al poblar la base de datos desde la API:", err);
    }
}

function guardarPokemonEnDB(lista) {
    return new Promise((resolve, reject) => {
        const transaction = db.transaction([STORE_NAME], 'readwrite');
        const store = transaction.objectStore(STORE_NAME);
        const request = store.put(lista, 'lista_maestra');

        request.onsuccess = () => resolve();
        request.onerror = (e) => reject(e.target.error);
    });
}

function obtenerPokemonDeDB() {
    return new Promise((resolve, reject) => {
        const transaction = db.transaction([STORE_NAME], 'readonly');
        const store = transaction.objectStore(STORE_NAME);
        const request = store.get('lista_maestra');

        request.onsuccess = (e) => resolve(e.target.result || []);
        request.onerror = (e) => reject(e.target.error);
    });
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