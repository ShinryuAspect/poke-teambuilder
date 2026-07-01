const DB_NAME = 'PokeAppCacheDB';
const DB_VERSION = 3;
const STORE_NAME = 'pokemon_lists';
const STORE_ITEMS = 'item_lists';
const STORE_ABILITIES = 'ability_lists';
let db;

const movimientosPorSlot = {};
const statsBasePorSlot = {};
const pokemonContextPorSlot = {};
const SPRITE_UNKNOWN = '../assets/img/unknown.png';
const POKEAPI_VERSION_GROUP = 'scarlet-violet';

const NATURALEZAS = {
	Adamant: { plus: 'atk', minus: 'spa', name: 'Firme (Adamant)' },
	Bashful: { plus: null, minus: null, name: 'Tímida (Bashful)' },
	Bold: { plus: 'def', minus: 'atk', name: 'Osada (Bold)' },
	Brave: { plus: 'atk', minus: 'spe', name: 'Audaz (Brave)' },
	Calm: { plus: 'spd', minus: 'atk', name: 'Serena (Calm)' },
	Careful: { plus: 'spd', minus: 'spa', name: 'Cauta (Careful)' },
	Docile: { plus: null, minus: null, name: 'Dócil (Docile)' },
	Gentle: { plus: 'spd', minus: 'def', name: 'Amable (Gentle)' },
	Hardy: { plus: null, minus: null, name: 'Activa (Hardy)' },
	Hasty: { plus: 'spe', minus: 'def', name: 'Activa (Hasty)' },
	Impish: { plus: 'def', minus: 'spa', name: 'Agitada (Impish)' },
	Jolly: { plus: 'spe', minus: 'spa', name: 'Alegre (Jolly)' },
	Lax: { plus: 'def', minus: 'spd', name: 'Floja (Lax)' },
	Lonely: { plus: 'atk', minus: 'def', name: 'Huraña (Lonely)' },
	Mild: { plus: 'spa', minus: 'def', name: 'Afable (Mild)' },
	Modest: { plus: 'spa', minus: 'atk', name: 'Modesta (Modest)' },
	Naive: { plus: 'spe', minus: 'spd', name: 'Ingenua (Naive)' },
	Naughty: { plus: 'atk', minus: 'spd', name: 'Pícara (Naughty)' },
	Quiet: { plus: 'spa', minus: 'spe', name: 'Mansa (Quiet)' },
	Quirky: { plus: null, minus: null, name: 'Rara (Quirky)' },
	Rash: { plus: 'spa', minus: 'spd', name: 'Alocada (Rash)' },
	Relaxed: { plus: 'def', minus: 'spe', name: 'Plácida (Relaxed)' },
	Sassy: { plus: 'spd', minus: 'spe', name: 'Grosera (Sassy)' },
	Serious: { plus: null, minus: null, name: 'Seria (Serious)' },
	Timid: { plus: 'spe', minus: 'atk', name: 'Miedosa (Timid)' },
};

document.addEventListener('DOMContentLoaded', async () => {
	window.addEventListener(
		'keydown',
		(e) => {
			if (e.key === 'Escape') {
				e.preventDefault();
				e.stopPropagation();
				document
					.querySelectorAll('.sugerencias-dropdown')
					.forEach((dropdown) => {
						dropdown.innerHTML = '';
					});
				if (
					document.activeElement &&
					(document.activeElement.tagName === 'INPUT' ||
						document.activeElement.tagName === 'SELECT')
				) {
					if (document.activeElement.value === 'null') {
						document.activeElement.value = '';
					}
					document.activeElement.blur();
				}
			}
		},
		true,
	);

	try {
		db = await inicializarDB();
		await inicializarCachePokemon();
		await inicializarCacheObjetos();
		await inicializarCacheHabilidades();
	} catch (err) {
		console.error('Error al preparar la base de datos de caché:', err);
	}

	const pokemonInputs = document.querySelectorAll('.input-pokemon');
	pokemonInputs.forEach((input) => {
		const card = input.closest('.pokemon-card');
		const slotId = card.dataset.slot;
		const dropdown = input.nextElementSibling;

		input.addEventListener('keyup', () => {
			if (input.value === 'null') input.value = '';
		});

		input.addEventListener('input', async () => {
			const query = input.value.trim().toLowerCase();
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
				.filter((p) => p.includes(query))
				.slice(0, 5);
			renderSugerencias(filtrados, input, dropdown, slotId);
		});

		input.addEventListener('focus', async () => {
			const query = input.value.trim().toLowerCase();
			if (query.length >= 2) {
				const pokeCache = await obtenerPokemonDeDB();
				const filtrados = pokeCache
					.filter((p) => p.includes(query))
					.slice(0, 5);
				renderSugerencias(filtrados, input, dropdown, slotId);
			}
		});

		if (input.value.trim().length > 0) {
			cargarDatosPokemon(input.value.trim(), slotId);
		}
	});

	const itemInputs = document.querySelectorAll('.input-item');
	itemInputs.forEach((input) => {
		const dropdown = input.nextElementSibling;

		input.addEventListener('input', async (e) => {
			const query = e.target.value.trim().toLowerCase();
			if (query.length < 1) {
				dropdown.innerHTML = '';
				return;
			}

			const objetosCache = await obtenerObjetosDeDB();
			const filtrados = objetosCache
				.filter((item) => item.includes(query))
				.slice(0, 5);
			renderSugerenciasObjetos(filtrados, input, dropdown);
		});

		input.addEventListener('focus', async () => {
			const query = input.value.trim().toLowerCase();
			if (query.length >= 1) {
				const objetosCache = await obtenerObjetosDeDB();
				const filtrados = objetosCache
					.filter((item) => item.includes(query))
					.slice(0, 5);
				renderSugerenciasObjetos(filtrados, input, dropdown);
			}
		});
	});

	const abilityInputs = document.querySelectorAll('.input-ability');
	abilityInputs.forEach((input) => {
		const dropdown = input.nextElementSibling;

		input.addEventListener('input', async (e) => {
			const query = normalizarTextoBuscado(e.target.value);
			if (query.length < 1) {
				dropdown.innerHTML = '';
				return;
			}

			const habilidadesCache = await obtenerHabilidadesDeDB();
			const filtradas = habilidadesCache
				.filter((ability) =>
					normalizarTextoBuscado(ability).includes(query),
				)
				.slice(0, 5);
			renderSugerenciasHabilidades(filtradas, input, dropdown);
		});

		input.addEventListener('focus', async () => {
			const query = normalizarTextoBuscado(input.value);
			if (query.length >= 1) {
				const habilidadesCache = await obtenerHabilidadesDeDB();
				const filtradas = habilidadesCache
					.filter((ability) =>
						normalizarTextoBuscado(ability).includes(query),
					)
					.slice(0, 5);
				if (filtradas.length > 0) {
					renderSugerenciasHabilidades(filtradas, input, dropdown);
				}
			}
		});

		input.addEventListener('blur', () => {
			input.value = formatearEtiquetaApi(input.value);
		});
	});

	const moveInputs = document.querySelectorAll('.input-move');
	moveInputs.forEach((input) => {
		const card = input.closest('.pokemon-card');
		const slotId = card.dataset.slot;
		const dropdown = input.nextElementSibling;

		input.addEventListener('input', () => {
			const query = input.value.trim().toLowerCase();
			const movimientosLegales = movimientosPorSlot[slotId] || [];

			if (query.length < 1) {
				dropdown.innerHTML = '';
				return;
			}
			const filtrados = movimientosLegales
				.filter((move) => move.includes(query))
				.slice(0, 5);
			renderSugerenciasMovimientos(filtrados, input, dropdown);
		});

		input.addEventListener('focus', () => {
			const query = input.value.trim().toLowerCase();
			const movimientosLegales = movimientosPorSlot[slotId] || [];
			const filtrados = movimientosLegales
				.filter((move) => move.includes(query))
				.slice(0, 5);
			if (filtrados.length > 0) {
				renderSugerenciasMovimientos(filtrados, input, dropdown);
			}
		});
	});

	document.addEventListener('input', (e) => {
		if (
			e.target.classList.contains('input-ev') ||
			e.target.classList.contains('input-iv')
		) {
			const slotId = e.target.dataset.slot;
			calcularStatsDelSlot(slotId);
		}
	});

	document.addEventListener('change', (e) => {
		if (e.target.classList.contains('select-nature')) {
			const slotId = e.target.dataset.slot;
			calcularStatsDelSlot(slotId);
			return;
		}

		if (
			e.target.matches(`select[name^="pokemon"][name$="[gender]"]`) ||
			e.target.matches(`input[name^="pokemon"][name$="[shiny]"]`)
		) {
			const card = e.target.closest('.pokemon-card');
			if (card) {
				actualizarSpriteDelSlot(card.dataset.slot);
			}
		}
	});

	document.addEventListener('click', (e) => {
		document
			.querySelectorAll('.sugerencias-dropdown')
			.forEach((dropdown) => {
				const inputAsociado = dropdown.previousElementSibling;
				if (
					inputAsociado &&
					!inputAsociado.contains(e.target) &&
					!dropdown.contains(e.target)
				) {
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
			if (!database.objectStoreNames.contains(STORE_ITEMS)) {
				database.createObjectStore(STORE_ITEMS);
			}
			if (!database.objectStoreNames.contains(STORE_ABILITIES)) {
				database.createObjectStore(STORE_ABILITIES);
			}
		};
		request.onsuccess = (e) => resolve(e.target.result);
		request.onerror = (e) => reject(e.target.error);
	});
}

async function inicializarCachePokemon() {
	const cachedData = await obtenerPokemonDeDB();
	if (cachedData && cachedData.length > 0) return;

	try {
		const res = await fetch('https://pokeapi.co/api/v2/pokemon?limit=1025');
		const data = await res.json();
		const listaNombres = data.results.map((p) => p.name);

		const transaction = db.transaction([STORE_NAME], 'readwrite');
		transaction.objectStore(STORE_NAME).put(listaNombres, 'lista_maestra');
	} catch (err) {
		console.error('Error al poblar IndexedDB con Pokémon:', err);
	}
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

async function inicializarCacheObjetos() {
	const cachedData = await obtenerObjetosDeDB();
	if (cachedData && cachedData.length > 0) return;

	try {
		const res = await fetch('https://pokeapi.co/api/v2/item?limit=2176');
		const data = await res.json();
		const listaObjetos = data.results.map((i) => i.name);

		const transaction = db.transaction([STORE_ITEMS], 'readwrite');
		transaction.objectStore(STORE_ITEMS).put(listaObjetos, 'lista_objetos');
	} catch (err) {
		console.error('Error al poblar IndexedDB con Objetos:', err);
	}
}

async function inicializarCacheHabilidades() {
	const cachedData = await obtenerHabilidadesDeDB();
	if (cachedData && cachedData.length > 0) return;

	try {
		const res = await fetch('https://pokeapi.co/api/v2/ability?limit=1000');
		const data = await res.json();
		const listaHabilidades = data.results.map((ability) => ability.name);

		const transaction = db.transaction([STORE_ABILITIES], 'readwrite');
		transaction
			.objectStore(STORE_ABILITIES)
			.put(listaHabilidades, 'lista_habilidades');
	} catch (err) {
		console.error('Error al poblar IndexedDB con Habilidades:', err);
	}
}

function obtenerObjetosDeDB() {
	return new Promise((resolve, reject) => {
		const transaction = db.transaction([STORE_ITEMS], 'readonly');
		const store = transaction.objectStore(STORE_ITEMS);
		const request = store.get('lista_objetos');
		request.onsuccess = (e) => resolve(e.target.result || []);
		request.onerror = (e) => reject(e.target.error);
	});
}

function obtenerHabilidadesDeDB() {
	return new Promise((resolve, reject) => {
		const transaction = db.transaction([STORE_ABILITIES], 'readonly');
		const store = transaction.objectStore(STORE_ABILITIES);
		const request = store.get('lista_habilidades');
		request.onsuccess = (e) => resolve(e.target.result || []);
		request.onerror = (e) => reject(e.target.error);
	});
}

function normalizarTextoBuscado(texto) {
	return String(texto || '')
		.toLowerCase()
		.replace(/[-_]+/g, ' ')
		.replace(/\s+/g, ' ')
		.trim();
}

function formatearEtiquetaApi(texto) {
	return normalizarTextoBuscado(texto)
		.split(' ')
		.filter(Boolean)
		.map((palabra) => palabra.charAt(0).toUpperCase() + palabra.slice(1))
		.join(' ');
}

function renderSugerencias(lista, input, dropdown, slotId) {
	dropdown.innerHTML = '';
	if (lista.length === 0) {
		dropdown.innerHTML = `<div class="sugerencia-item empty">No se encontraron resultados</div>`;
		return;
	}
	lista.forEach((pokeName) => {
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

function renderSugerenciasObjetos(lista, input, dropdown) {
	dropdown.innerHTML = '';
	if (lista.length === 0) {
		dropdown.innerHTML = `<div class="sugerencia-item empty">Objeto no válido</div>`;
		return;
	}
	lista.forEach((itemName) => {
		const item = document.createElement('div');
		item.className = 'sugerencia-item';
		item.textContent = itemName.split('-').join(' ').toUpperCase();
		item.addEventListener('click', () => {
			input.value = item.textContent;
			dropdown.innerHTML = '';
		});
		dropdown.appendChild(item);
	});
}

function renderSugerenciasMovimientos(lista, input, dropdown) {
	dropdown.innerHTML = '';
	lista.forEach((moveName) => {
		const item = document.createElement('div');
		item.className = 'sugerencia-item';
		item.textContent = moveName.split('-').join(' ').toUpperCase();
		item.addEventListener('click', () => {
			input.value = item.textContent;
			dropdown.innerHTML = '';
		});
		dropdown.appendChild(item);
	});
}

function renderSugerenciasHabilidades(lista, input, dropdown) {
	dropdown.innerHTML = '';
	if (lista.length === 0) {
		dropdown.innerHTML = `<div class="sugerencia-item empty">Habilidad no válida</div>`;
		return;
	}

	lista.forEach((abilityName) => {
		const item = document.createElement('div');
		item.className = 'sugerencia-item';
		item.textContent = formatearEtiquetaApi(abilityName);
		item.addEventListener('click', () => {
			input.value = item.textContent;
			dropdown.innerHTML = '';
		});
		dropdown.appendChild(item);
	});
}

function extraerMovimientosLegales(pokemonMoves) {
	if (!Array.isArray(pokemonMoves)) {
		return [];
	}

	const movimientosFiltrados = pokemonMoves
		.filter(
			(moveEntry) =>
				Array.isArray(moveEntry.version_group_details) &&
				moveEntry.version_group_details.some(
					(detail) =>
						detail.version_group?.name === POKEAPI_VERSION_GROUP,
				),
		)
		.map((moveEntry) => moveEntry.move?.name)
		.filter(Boolean);

	if (movimientosFiltrados.length > 0) {
		return [...new Set(movimientosFiltrados)].sort((a, b) =>
			a.localeCompare(b),
		);
	}

	const movimientosAlternativos = pokemonMoves
		.map((moveEntry) => moveEntry.move?.name)
		.filter(Boolean);

	return [...new Set(movimientosAlternativos)].sort((a, b) =>
		a.localeCompare(b),
	);
}

function extraerHabilidadPrincipal(abilities) {
	if (!Array.isArray(abilities) || abilities.length === 0) {
		return '';
	}

	const habilidadPrioritaria =
		abilities.find((abilityEntry) => !abilityEntry.is_hidden) ||
		abilities[0];

	return formatearEtiquetaApi(habilidadPrioritaria?.ability?.name || '');
}

async function cargarDatosPokemon(pokemonName, slotId) {
	if (!pokemonName) return;

	const card = document.querySelector(`.pokemon-card[data-slot="${slotId}"]`);
	if (!card) return;

	const spriteImg = document.getElementById(`sprite-${slotId}`);
	const typesContainer = document.getElementById(`types-${slotId}`);
	const moveInputs = document.querySelectorAll(
		`[name^="pokemon[${slotId}][moves]"]`,
	);
	const abilityInput = document.querySelector(
		`input[name="pokemon[${slotId}][habilidad]"]`,
	);

	try {
		const res = await fetch(
			`https://pokeapi.co/api/v2/pokemon/${pokemonName.toLowerCase().trim()}`,
		);
		if (!res.ok) throw new Error();
		const data = await res.json();

		let speciesData = null;
		if (data.species && data.species.url) {
			try {
				const speciesRes = await fetch(data.species.url);
				if (speciesRes.ok) {
					speciesData = await speciesRes.json();
				}
			} catch (speciesErr) {
				console.warn(
					'No se pudo cargar la especie del Pokémon:',
					speciesErr,
				);
			}
		}

		pokemonContextPorSlot[slotId] = {
			frontDefault: data.sprites.front_default || SPRITE_UNKNOWN,
			frontShiny:
				data.sprites.front_shiny ||
				data.sprites.front_default ||
				SPRITE_UNKNOWN,
			frontFemale:
				data.sprites.front_female ||
				data.sprites.front_default ||
				SPRITE_UNKNOWN,
			frontShinyFemale:
				data.sprites.front_shiny_female ||
				data.sprites.front_shiny ||
				data.sprites.front_default ||
				SPRITE_UNKNOWN,
			genderRate: speciesData ? speciesData.gender_rate : null,
		};

		aplicarEstadoGeneroYSprite(slotId);

		typesContainer.innerHTML = '';
		data.types.forEach((t) => {
			const badge = document.createElement('span');
			badge.className = `badge type-${t.type.name}`;
			badge.textContent = t.type.name;
			typesContainer.appendChild(badge);
		});

		document.querySelector(
			`.select-nature[data-slot="${slotId}"]`,
		).disabled = false;

		const genderSel = document.querySelector(
			`select[name="pokemon[${slotId}][gender]"]`,
		);
		if (genderSel) genderSel.disabled = false;

		const shinyCb = document.querySelector(
			`input[name="pokemon[${slotId}][shiny]"]`,
		);
		if (shinyCb) shinyCb.disabled = false;

		document
			.querySelectorAll(
				`.input-ev[data-slot="${slotId}"], .input-iv[data-slot="${slotId}"]`,
			)
			.forEach((input) => {
				input.disabled = false;
			});

		movimientosPorSlot[slotId] = extraerMovimientosLegales(data.moves);
		if (abilityInput) {
			abilityInput.value = extraerHabilidadPrincipal(data.abilities);
		}

		moveInputs.forEach((moveInput) => {
			moveInput.disabled = false;
			moveInput.placeholder = '- Selecciona un movimiento';
		});

		statsBasePorSlot[slotId] = {
			hp: data.stats[0].base_stat,
			atk: data.stats[1].base_stat,
			def: data.stats[2].base_stat,
			spa: data.stats[3].base_stat,
			spd: data.stats[4].base_stat,
			spe: data.stats[5].base_stat,
		};

		Object.keys(statsBasePorSlot[slotId]).forEach((key) => {
			const baseCell = document.getElementById(`base-${key}-${slotId}`);
			if (baseCell) baseCell.textContent = statsBasePorSlot[slotId][key];
		});

		calcularStatsDelSlot(slotId);

		actualizarSpriteDelSlot(slotId);
	} catch (err) {
		resetearSlot(slotId);
	}
}

function aplicarEstadoGeneroYSprite(slotId) {
	const context = pokemonContextPorSlot[slotId];
	const genderSel = document.querySelector(
		`select[name="pokemon[${slotId}][gender]"]`,
	);
	const shinyCb = document.querySelector(
		`input[name="pokemon[${slotId}][shiny]"]`,
	);
	const genderHidden = document.querySelector(
		`.gender-hidden[data-slot="${slotId}"]`,
	);

	if (!context) {
		return;
	}

	const genderRate = context.genderRate;
	if (genderSel) {
		if (genderRate === -1) {
			genderSel.value = 'N';
			genderSel.disabled = true;
			if (genderHidden) {
				genderHidden.disabled = false;
				genderHidden.value = 'N';
			}
		} else if (genderRate === 0) {
			genderSel.value = 'M';
			genderSel.disabled = true;
			if (genderHidden) {
				genderHidden.disabled = false;
				genderHidden.value = 'M';
			}
		} else if (genderRate === 8) {
			genderSel.value = 'F';
			genderSel.disabled = true;
			if (genderHidden) {
				genderHidden.disabled = false;
				genderHidden.value = 'F';
			}
		} else {
			genderSel.disabled = false;
			if (!['M', 'F', 'N'].includes(genderSel.value)) {
				genderSel.value = 'N';
			}
			if (genderHidden) {
				genderHidden.disabled = true;
				genderHidden.value = genderSel.value;
			}
		}
	}

	if (shinyCb) {
		shinyCb.disabled = false;
	}
}

function actualizarSpriteDelSlot(slotId) {
	const context = pokemonContextPorSlot[slotId];
	const spriteImg = document.getElementById(`sprite-${slotId}`);
	if (!context || !spriteImg) return;

	const genderSel = document.querySelector(
		`select[name="pokemon[${slotId}][gender]"]`,
	);
	const shinyCb = document.querySelector(
		`input[name="pokemon[${slotId}][shiny]"]`,
	);
	const gender = genderSel ? genderSel.value : 'N';
	const shiny = shinyCb ? shinyCb.checked : false;

	let spriteUrl = context.frontDefault;

	if (shiny) {
		if (
			gender === 'F' &&
			context.frontShinyFemale &&
			context.frontShinyFemale !== SPRITE_UNKNOWN
		) {
			spriteUrl = context.frontShinyFemale;
		} else if (
			context.frontShiny &&
			context.frontShiny !== SPRITE_UNKNOWN
		) {
			spriteUrl = context.frontShiny;
		}
	} else if (
		gender === 'F' &&
		context.frontFemale &&
		context.frontFemale !== SPRITE_UNKNOWN
	) {
		spriteUrl = context.frontFemale;
	}

	spriteImg.src = spriteUrl || SPRITE_UNKNOWN;
	spriteImg.style.opacity = '1';
}

function calcularStatsDelSlot(slotId) {
	const baseStats = statsBasePorSlot[slotId];
	if (!baseStats) return;

	const selectNature = document.querySelector(
		`.select-nature[data-slot="${slotId}"]`,
	);
	const naturalezaSeleccionada = selectNature
		? selectNature.value
		: 'Serious';
	const modificadores = NATURALEZAS[naturalezaSeleccionada] || {
		plus: null,
		minus: null,
	};

	let evTotales = 0;
	const evInputs = document.querySelectorAll(
		`.input-ev[data-slot="${slotId}"]`,
	);

	evInputs.forEach((input) => {
		let val = parseInt(input.value) || 0;
		if (val < 0) val = 0;
		if (val > 252) val = 252;
		input.value = val;
		evTotales += val;
	});

	if (evTotales > 512) {
		let exceso = evTotales - 512;
		for (let i = evInputs.length - 1; i >= 0; i--) {
			let val = parseInt(evInputs[i].value) || 0;
			if (val >= exceso) {
				evInputs[i].value = val - exceso;
				exceso = 0;
				break;
			} else {
				exceso -= val;
				evInputs[i].value = 0;
			}
		}
		evTotales = 512;
	}

	const txtContador = document.getElementById(`ev-left-${slotId}`);
	if (txtContador) txtContador.textContent = 512 - evTotales;

	const keys = ['hp', 'atk', 'def', 'spa', 'spd', 'spe'];
	keys.forEach((key) => {
		const base = baseStats[key] || 0;
		const evInput = document.querySelector(
			`.input-ev[data-stat="${key}"][data-slot="${slotId}"]`,
		);
		const ivInput = document.querySelector(
			`.input-iv[data-stat="${key}"][data-slot="${slotId}"]`,
		);

		const ev = evInput ? parseInt(evInput.value) || 0 : 0;
		let iv = ivInput ? parseInt(ivInput.value) || 31 : 31;

		if (iv < 0) iv = 0;
		if (iv > 31) iv = 31;
		if (ivInput) ivInput.value = iv;

		let total = 0;

		if (key === 'hp') {
			if (base === 1) {
				total = 1;
			} else {
				total = Math.floor(2 * base + iv + Math.floor(ev / 4) + 110);
			}
		} else {
			let calc = Math.floor(2 * base + iv + Math.floor(ev / 4) + 5);
			let mod = 1.0;
			if (modificadores.plus === key) mod = 1.1;
			if (modificadores.minus === key) mod = 0.9;
			total = Math.floor(calc * mod);
		}

		const cellTotal = document.getElementById(`total-${key}-${slotId}`);
		if (cellTotal) {
			cellTotal.textContent = total;
			cellTotal.classList.remove('stat-plus', 'stat-minus');
			if (modificadores.plus === key)
				cellTotal.classList.add('stat-plus');
			if (modificadores.minus === key)
				cellTotal.classList.add('stat-minus');
		}
	});
}

function resetearSlot(slotId) {
	const spriteImg = document.getElementById(`sprite-${slotId}`);
	const typesContainer = document.getElementById(`types-${slotId}`);
	const moveInputs = document.querySelectorAll(
		`[name^="pokemon[${slotId}][moves]"]`,
	);
	const abilityInput = document.querySelector(
		`input[name="pokemon[${slotId}][habilidad]"]`,
	);

	spriteImg.src = SPRITE_UNKNOWN;
	spriteImg.style.opacity = '1';
	typesContainer.innerHTML = `<span class="badge type-unknown">???</span>`;

	delete movimientosPorSlot[slotId];
	delete statsBasePorSlot[slotId];
	delete pokemonContextPorSlot[slotId];
	if (abilityInput) {
		abilityInput.value = '';
	}

	moveInputs.forEach((moveInput) => {
		moveInput.disabled = true;
		moveInput.value = '';
		moveInput.placeholder = '- Bloqueado';
	});

	document.querySelector(`.select-nature[data-slot="${slotId}"]`).disabled =
		true;
	const genderSel = document.querySelector(
		`select[name="pokemon[${slotId}][gender]"]`,
	);
	const genderHidden = document.querySelector(
		`.gender-hidden[data-slot="${slotId}"]`,
	);
	if (genderSel) {
		genderSel.disabled = true;
		genderSel.value = 'N';
	}
	if (genderHidden) {
		genderHidden.disabled = false;
		genderHidden.value = 'N';
	}
	const shinyCb = document.querySelector(
		`input[name="pokemon[${slotId}][shiny]"]`,
	);
	if (shinyCb) {
		shinyCb.disabled = true;
		shinyCb.checked = false;
	}

	document
		.querySelectorAll(
			`.input-ev[data-slot="${slotId}"], .input-iv[data-slot="${slotId}"]`,
		)
		.forEach((input) => {
			input.disabled = true;
			input.value = input.classList.contains('input-ev') ? '0' : '31';
		});

	const txtContador = document.getElementById(`ev-left-${slotId}`);
	if (txtContador) txtContador.textContent = '512';

	const keys = ['hp', 'atk', 'def', 'spa', 'spd', 'spe'];
	keys.forEach((key) => {
		const baseCell = document.getElementById(`base-${key}-${slotId}`);
		const totalCell = document.getElementById(`total-${key}-${slotId}`);
		if (baseCell) baseCell.textContent = '0';
		if (totalCell) {
			totalCell.textContent = '0';
			totalCell.classList.remove('stat-plus', 'stat-minus');
		}
	});
}
