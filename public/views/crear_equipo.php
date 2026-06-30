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
                    <input type="text" id="nombre_equipo" name="nombre_equipo" class="input-team-name" placeholder="Ej: Mi Equipo de Lluvia" required>
                </div>
                <div class="form-group" style="max-width: 250px;">
                    <label for="formato_tier">🏆 Formato / Tier</label>
                    <select id="formato_tier" name="formato_tier">
                        <option value="Gen 9 OU">Gen 9 OU</option>
                        <option value="Gen 9 Ubers">Gen 9 Ubers</option>
                        <option value="Gen 9 VGC">Gen 9 VGC</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="pokemon-grid">
            <?php for ($i = 1; $i <= 6; $i++): ?>
                <div class="pokemon-card" data-slot="<?= $i ?>">
                    <div class="card-header">
                        <span class="slot-number">#<?= $i ?></span>
                        <div class="pokemon-sprite-container">
                            <img src="<?= BASE_URL ?>public/assets/img/unknown.png" class="poke-sprite" id="sprite-<?= $i ?>" alt="?">
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
                            <label>Objeto Helado</label>
                            <input type="text" name="pokemon[<?= $i ?>][item]" class="input-item" placeholder="Ninguno" autocomplete="off">
                            <div class="sugerencias-dropdown"></div>
                        </div>

                        <div class="moves-group">
                            <label>Movimientos (Moveset)</label>
                            <?php for ($m = 1; $m <= 4; $m++): ?>
                                <div class="move-selector-wrapper relative">
                                    <input type="text" name="pokemon[<?= $i ?>][moves][<?= $m ?>]" class="input-move" placeholder="- Movimiento <?= $m ?>" autocomplete="off" disabled>
                                    <div class="sugerencias-dropdown"></div>
                                </div>
                            <?php endfor; ?>
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