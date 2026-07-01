<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/Models/Equipo.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

$idEquipo = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$usuarioLogueado = $_SESSION['usuario'];

$equipoModel = new Equipo();
$datos = $equipoModel->obtenerDetallesEquipo($idEquipo, $usuarioLogueado['id']);

if (empty($datos)) {
    header('Location: dashboard.php');
    exit();
}

$equipoInfo = $datos['info'];
$pokemonGuardados = $datos['pokemon'];

$pageTitle = "Editar Equipo - Showdown Style";
$cssFile = "teamBuilder";
$jsFile = "teamBuilder";

require_once 'header.php';
?>

<div class="builder-container">
    <div class="header-actions">
        <div>
            <h2>✏️ Editar Equipo Competitivo</h2>
            <p>Modifica los integrantes, movimientos u objetos de tu estrategia.</p>
        </div>
        <a href="dashboard.php" class="btn-back">⬅️ Cancelar y Volver</a>
    </div>

    <form id="form-teambuilder" action="actualizar_equipo.php" method="POST">
        <input type="hidden" name="id_equipo" value="<?= htmlspecialchars($equipoInfo['id'] ?? '') ?>">
        
        <div class="builder-header card">
            <div class="input-row">
                <div class="form-group">
                    <label for="nombre_equipo">🏷️ Nombre del Equipo</label>
                    <input type="text" id="nombre_equipo" name="nombre_equipo" class="input-team-name" 
                           placeholder="Ej: Mi Equipo de Lluvia" required 
                           value="<?= htmlspecialchars($equipoInfo['nombre_equipo'] ?? '') ?>">
                </div>
            </div>
        </div>

        <div class="pokemon-grid">
            <?php 
            for ($i = 1; $i <= 6; $i++): 
                $pkmn = $pokemonGuardados[$i - 1] ?? null; 
                $hasPokemon = ($pkmn !== null);
                $isDisabled = !$hasPokemon ? 'disabled' : '';
                $pkmnName = $hasPokemon ? htmlspecialchars($pkmn['pokemon_name'] ?? '') : '';
                $pkmnItem = $hasPokemon ? htmlspecialchars($pkmn['objeto'] ?? '') : '';
                $pkmnGender = $hasPokemon ? strtoupper((string)($pkmn['gender'] ?? 'N')) : 'N';
                $pkmnShiny = $hasPokemon && !empty($pkmn['shiny']);
                
                $spriteSrc = BASE_URL . "public/assets/img/unknown.png";
            ?>
                <div class="pokemon-card" data-slot="<?= $i ?>">
                    <div class="card-header">
                        <span class="slot-number">#<?= $i ?></span>
                        <div class="pokemon-sprite-container">
                            <img src="<?= $spriteSrc ?>" class="poke-sprite" id="sprite-<?= $i ?>" alt="?">
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="form-group relative">
                            <label>Pokémon</label>
                            <input type="text" name="pokemon[<?= $i ?>][name]" class="input-pokemon" 
                                   placeholder="Buscar Pokémon..." autocomplete="off" value="<?= $pkmnName ?>">
                            <div class="sugerencias-dropdown"></div>
                        </div>

                        <div class="type-badges" id="types-<?= $i ?>">
                            <span class="badge type-unknown">???</span>
                        </div>

                        <div class="form-group relative">
                            <label>Objeto</label>
                            <input type="text" name="pokemon[<?= $i ?>][item]" class="input-item" 
                                   placeholder="Ninguno" autocomplete="off" value="<?= $pkmnItem ?>">
                            <div class="sugerencias-dropdown"></div>
                        </div>

                        <div class="moves-group">
                            <label>Movimientos (Moveset)</label>
                            <?php 
                            for ($m = 1; $m <= 4; $m++): 
                                $moveValue = $hasPokemon ? htmlspecialchars($pkmn["movimiento_$m"] ?? '') : '';
                                $placeholderText = !$hasPokemon ? '- Bloqueado' : "- Selecciona un movimiento";
                            ?>
                                <div class="move-selector-wrapper relative">
                                    <input type="text" name="pokemon[<?= $i ?>][moves][<?= $m ?>]" class="input-move" 
                                           placeholder="<?= $placeholderText ?>" autocomplete="off" 
                                           <?= $isDisabled ?> value="<?= $moveValue ?>">
                                    <div class="sugerencias-dropdown"></div>
                                </div>
                            <?php endfor; ?>
                        </div>

                        <div class="stats-and-details-panel">
                            <div class="details-row">
                                <div class="form-group">
                                    <label>Naturaleza</label>
                                    <select name="pokemon[<?= $i ?>][nature]" class="select-nature" data-slot="<?= $i ?>" <?= $isDisabled ?>>
                                        <?php
                                        $natures = [
                                            'Adamant' => 'Firme (Adamant)', 'Bashful' => 'Tímida (Bashful)',
                                            'Bold' => 'Osada (Bold)', 'Brave' => 'Audaz (Brave)',
                                            'Calm' => 'Serena (Calm)', 'Careful' => 'Cauta (Careful)',
                                            'Docile' => 'Dócil (Docile)', 'Gentle' => 'Amable (Gentle)',
                                            'Hardy' => 'Fuerte (Hardy)', 'Hasty' => 'Activa (Hasty)',
                                            'Impish' => 'Agitada (Impish)', 'Jolly' => 'Alegre (Jolly)',
                                            'Lax' => 'Floja (Lax)', 'Lonely' => 'Huraña (Lonely)',
                                            'Mild' => 'Afable (Mild)', 'Modest' => 'Modesta (Modest)',
                                            'Naive' => 'Ingenua (Naive)', 'Naughty' => 'Pícara (Naughty)',
                                            'Quiet' => 'Mansa (Quiet)', 'Quirky' => 'Rara (Quirky)',
                                            'Rash' => 'Alocada (Rash)', 'Relaxed' => 'Plácida (Relaxed)',
                                            'Sassy' => 'Grosera (Sassy)', 'Serious' => 'Seria (Serious)',
                                            'Timid' => 'Miedosa (Timid)'
                                        ];
                                        $selectedNature = $hasPokemon ? ($pkmn['naturaleza'] ?? 'Serious') : 'Serious';
                                        foreach ($natures as $key => $label):
                                        ?>
                                            <option value="<?= $key ?>" <?= $selectedNature === $key ? 'selected' : '' ?>><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group" style="max-width: 70px;">
                                    <label>Sexo</label>
                                    <select name="pokemon[<?= $i ?>][gender]" <?= $isDisabled ?>>
                                        <option value="N" <?= $pkmnGender === 'N' ? 'selected' : '' ?>>⚪ Ninguno</option>
                                        <option value="M" <?= $pkmnGender === 'M' ? 'selected' : '' ?>>♂ Macho</option>
                                        <option value="F" <?= $pkmnGender === 'F' ? 'selected' : '' ?>>♀ Hembra</option>
                                    </select>
                                    <input type="hidden" name="pokemon[<?= $i ?>][gender]" value="<?= $pkmnGender ?>" class="gender-hidden" data-slot="<?= $i ?>">
                                </div>

                                <div class="form-group checkbox-group">
                                    <label class="shiny-label">
                                        <input type="checkbox" name="pokemon[<?= $i ?>][shiny]" value="1" <?= $isDisabled ?> <?= $pkmnShiny ? 'checked' : '' ?>> ✨ Shiny
                                    </label>
                                </div>
                            </div>

                            <div class="stats-table-container">
                                <div class="ev-total-counter">EVs Restantes: <span id="ev-left-<?= $i ?>">512</span> / 512</div>
                                <table class="stats-table">
                                    <thead>
                                        <tr>
                                            <th>Stat</th>
                                            <th>Base</th>
                                            <th>EVs</th>
                                            <th>IVs</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $statsKeys = ['hp' => 'PS', 'atk' => 'Atk', 'def' => 'Def', 'spa' => 'SpA', 'spd' => 'SpD', 'spe' => 'Vel'];
                                        foreach ($statsKeys as $key => $label): 
                                        ?>
                                        <tr>
                                            <td><strong><?= $label ?></strong></td>
                                            <td id="base-<?= $key ?>-<?= $i ?>">0</td>
                                            <td>
                                                <input type="number" name="pokemon[<?= $i ?>][evs][<?= $key ?>]" class="input-ev" data-stat="<?= $key ?>" data-slot="<?= $i ?>" min="0" max="252" value="<?= $hasPokemon ? (int)($pkmn['ev_'.$key] ?? 0) : 0 ?>" <?= $isDisabled ?>>
                                            </td>
                                            <td>
                                                <input type="number" name="pokemon[<?= $i ?>][ivs][<?= $key ?>]" 
                                                       class="input-iv" data-stat="<?= $key ?>" data-slot="<?= $i ?>"
                                                       min="0" max="31" value="31" <?= $isDisabled ?>>
                                            </td>
                                            <td id="total-<?= $key ?>-<?= $i ?>" class="stat-total-calculated">0</td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endfor; ?>
        </div>

        <div class="builder-footer">
            <button type="submit" class="btn-save-team">💾 Actualizar Cambios del Equipo</button>
        </div>
    </form>
</div>

<?php require_once 'footer.php'; ?>