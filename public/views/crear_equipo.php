<?php
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

$pageTitle = "Construir Equipo - Showdown Style";
$cssFile = "teamBuilder";
$jsFile = "teamBuilder";

require_once 'header.php';
?>

<div class="builder-container">
    <div class="header-actions">
        <div>
            <h2>🛠️ Constructor de Equipos</h2>
            <p>Diseña tu estrategia competitiva al estilo Showdown.</p>
        </div>
        <a href="dashboard.php" class="btn-back">⬅️ Volver al Dashboard</a>
    </div>

    <form id="form-teambuilder" action="guardar_equipo.php" method="POST">
        
        <div class="builder-header card">
            <div class="input-row">
                <div class="form-group">
                    <label for="nombre_equipo">🏷️ Nombre del Equipo</label>
                    <input type="text" id="nombre_equipo" name="nombre_equipo" class="input-team-name" placeholder="Ej: Mi Equipo de Lluvia" required value="">
                </div>
            </div>
        </div>

        <div class="pokemon-grid">
            <?php for ($i = 1; $i <= 6; $i++): 
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
                            <input type="text" name="pokemon[<?= $i ?>][name]" class="input-pokemon" placeholder="Buscar Pokémon..." autocomplete="off">
                            <div class="sugerencias-dropdown"></div>
                        </div>

                        <div class="type-badges" id="types-<?= $i ?>">
                            <span class="badge type-unknown">???</span>
                        </div>

                        <div class="form-group relative">
                            <label>Objeto</label>
                            <input type="text" name="pokemon[<?= $i ?>][item]" class="input-item" placeholder="Ninguno" autocomplete="off">
                            <div class="sugerencias-dropdown"></div>
                        </div>

                        <div class="moves-group">
                            <label>Movimientos (Moveset)</label>
                            <?php for ($m = 1; $m <= 4; $m++): ?>
                                <div class="move-selector-wrapper relative">
                                    <input type="text" name="pokemon[<?= $i ?>][moves][<?= $m ?>]" class="input-move" placeholder="- Bloqueado" autocomplete="off" disabled>
                                    <div class="sugerencias-dropdown"></div>
                                </div>
                            <?php endfor; ?>
                        </div>

                        <div class="stats-and-details-panel">
                            <div class="details-row">
                                <div class="form-group">
                                    <label>Naturaleza</label>
                                    <select name="pokemon[<?= $i ?>][nature]" class="select-nature" data-slot="<?= $i ?>" disabled>
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
                                        foreach ($natures as $key => $label):
                                        ?>
                                            <option value="<?= $key ?>" <?= $key === 'Serious' ? 'selected' : '' ?>><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group" style="max-width: 80px;">
                                    <label>Sexo</label>
                                    <select name="pokemon[<?= $i ?>][gender]" disabled>
                                        <option value="N" selected>⚪ Ninguno</option>
                                        <option value="M">♂ Macho</option>
                                        <option value="F">♀ Hembra</option>
                                    </select>
                                    <input type="hidden" name="pokemon[<?= $i ?>][gender]" value="N" class="gender-hidden" data-slot="<?= $i ?>">
                                </div>

                                <div class="form-group checkbox-group">
                                    <label class="shiny-label">
                                        <input type="checkbox" name="pokemon[<?= $i ?>][shiny]" value="1" disabled> ✨ Shiny
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
                                                <input type="number" name="pokemon[<?= $i ?>][evs][<?= $key ?>]" 
                                                       class="input-ev" data-stat="<?= $key ?>" data-slot="<?= $i ?>"
                                                       min="0" max="252" value="0" disabled>
                                            </td>
                                            <td>
                                                <input type="number" name="pokemon[<?= $i ?>][ivs][<?= $key ?>]" 
                                                       class="input-iv" data-stat="<?= $key ?>" data-slot="<?= $i ?>"
                                                       min="0" max="31" value="31" disabled>
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
            <button type="submit" class="btn-save-team">💾 Guardar Equipo Competitivo</button>
        </div>
    </form>
</div>

<?php require_once 'footer.php'; ?>