<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/Models/Equipo.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

$mensajeError = '';
$mensajeExito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreEquipo = isset($_POST['nombre_equipo']) ? trim(htmlspecialchars($_POST['nombre_equipo'])) : '';
    $formatoTier = isset($_POST['formato_tier']) ? trim(htmlspecialchars($_POST['formato_tier'])) : '';
    $pokemonPost = isset($_POST['pokemon']) ? $_POST['pokemon'] : [];

    if (empty($nombreEquipo) || empty($formatoTier)) {
        $mensajeError = "El nombre del equipo y la Tier son campos obligatorios.";
    } else {
        $equipoModel = new Equipo();
        $idUsuario = $_SESSION['usuario']['id'];

        if ($equipoModel->guardar($idUsuario, $nombreEquipo, $formatoTier, $pokemonPost)) {
            $mensajeExito = "¡Equipo completo guardado con éxito en la base de datos!";
        } else {
            $mensajeError = "Hubo un error crítico al procesar y guardar el equipo.";
        }
    }
}

$pageTitle = "Construir Equipo";
$cssFile = "dashboard";
$jsFile = null; 

require_once 'header.php';
?>

<div class="container">
    <div class="header-actions">
        <h2>🛠️ Nuevo Equipo Competitivo</h2>
        <a href="dashboard.php" class="btn-back">⬅ Volver al Dashboard</a>
    </div>

    <?php if (!empty($mensajeError)): ?>
        <div class="alert alert-danger"><?= $mensajeError ?></div>
    <?php endif; ?>

    <?php if (!empty($mensajeExito)): ?>
        <div class="alert alert-success"><?= $mensajeExito ?></div>
    <?php endif; ?>

    <form action="crear_equipo.php" method="POST">
        <div class="card">
            <div class="form-inline">
                <div class="form-group">
                    <label for="nombre_equipo">Nombre del Equipo</label>
                    <input type="text" id="nombre_equipo" name="nombre_equipo" placeholder="Ej: Mi Equipo Lluvia" required>
                </div>
                <div class="form-group">
                    <label for="formato_tier">Formato / Tier</label>
                    <select id="formato_tier" name="formato_tier" required>
                        <option value="Gen 9 OU">Gen 9 OU</option>
                        <option value="Gen 9 VGC">Gen 9 VGC Regulation G</option>
                        <option value="Gen 9 Ubers">Gen 9 Ubers</option>
                    </select>
                </div>
            </div>
        </div>

        <h3>Integrantes del Equipo (Slots)</h3>
        <div class="grid-pokemon">
            
            <?php
            for ($i = 0; $i < 6; $i++): 
                $esObligatorio = ($i === 0) ? 'required' : '';
            ?>
                <div class="pokemon-slot">
                    <h3>Pokémon #<?= ($i + 1) ?></h3>
                    
                    <div class="form-group">
                        <label>Especie (Nombre)</label>
                        <input type="text" name="pokemon[<?= $i ?>][pokemon_name]" placeholder="Ej: Dragapult" <?= $esObligatorio ?>>
                    </div>
                    
                    <div class="form-inline">
                        <div class="form-group">
                            <label>Objeto</label>
                            <input type="text" name="pokemon[<?= $i ?>][objeto]" placeholder="Choice Specs">
                        </div>
                        <div class="form-group">
                            <label>Habilidad</label>
                            <input type="text" name="pokemon[<?= $i ?>][habilidad]" placeholder="Infiltrator">
                        </div>
                    </div>
                    
                    <div class="form-inline">
                        <div class="form-group">
                            <label>Naturaleza</label>
                            <input type="text" name="pokemon[<?= $i ?>][naturaleza]" placeholder="Timid">
                        </div>
                        <div class="form-group">
                            <label>EVs (Repartición)</label>
                            <input type="text" name="pokemon[<?= $i ?>][evs_reparticion]" placeholder="252 SpA / 252 Spe">
                        </div>
                    </div>
                    
                    <div class="moves-grid">
                        <input type="text" name="pokemon[<?= $i ?>][movimiento_1]" placeholder="Movimiento 1">
                        <input type="text" name="pokemon[<?= $i ?>][movimiento_2]" placeholder="Movimiento 2">
                        <input type="text" name="pokemon[<?= $i ?>][movimiento_3]" placeholder="Movimiento 3">
                        <input type="text" name="pokemon[<?= $i ?>][movimiento_4]" placeholder="Movimiento 4">
                    </div>
                </div>
            <?php endfor; ?>

        </div>

        <br>
        <button type="submit" class="btn-save">💾 Guardar Equipo Completo</button>
    </form>
</div>

<?php require_once 'footer.php'; ?>