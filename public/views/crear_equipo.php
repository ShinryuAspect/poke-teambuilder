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
            $mensajeExito = "¡Equipo guardado con éxito en la base de datos!";
        } else {
            $mensajeError = "Hubo un error crítico al procesar y guardar el equipo.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Equipo - Pokémon Team Builder</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f4f9; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; }
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn-back { padding: 8px 16px; background-color: #666; color: #fff; text-decoration: none; border-radius: 4px; }
        .card { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .form-inline { display: flex; gap: 15px; margin-bottom: 10px; }
        .form-group { flex: 1; display: flex; flex-direction: column; margin-bottom: 15px; }
        .form-group label { font-weight: bold; margin-bottom: 5px; color: #444; font-size: 14px; }
        .form-group input, .form-group select { padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; }
        .grid-pokemon { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .pokemon-slot { background: #fdfdfd; border: 1px solid #e0e0e0; border-top: 4px solid #ef5350; padding: 15px; border-radius: 6px; }
        .pokemon-slot h3 { margin-top: 0; color: #ef5350; font-size: 16px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .moves-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px; }
        .btn-save { width: 100%; padding: 12px; background-color: #2e7d32; color: white; border: none; border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer; }
        .btn-save:hover { background-color: #1b5e20; }
        .alert { padding: 12px; margin-bottom: 15px; border-radius: 4px; }
        .alert-danger { background-color: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
        .alert-success { background-color: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
    </style>
</head>
<body>

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
            
            <div class="pokemon-slot">
                <h3>Pokémon #1</h3>
                <div class="form-group">
                    <label>Especie (Nombre)</label>
                    <input type="text" name="pokemon[0][pokemon_name]" placeholder="Ej: Dragapult" required>
                </div>
                <div class="form-inline">
                    <div class="form-group">
                        <label>Objeto</label>
                        <input type="text" name="pokemon[0][objeto]" placeholder="Choice Specs">
                    </div>
                    <div class="form-group">
                        <label>Habilidad</label>
                        <input type="text" name="pokemon[0][habilidad]" placeholder="Infiltrator">
                    </div>
                </div>
                <div class="form-inline">
                    <div class="form-group">
                        <label>Naturaleza</label>
                        <input type="text" name="pokemon[0][naturaleza]" placeholder="Timid">
                    </div>
                    <div class="form-group">
                        <label>EVs (Repartición)</label>
                        <input type="text" name="pokemon[0][evs_reparticion]" placeholder="252 SpA / 252 Spe">
                    </div>
                </div>
                <div class="moves-grid">
                    <input type="text" name="pokemon[0][movimiento_1]" placeholder="Draco Meteor">
                    <input type="text" name="pokemon[0][movimiento_2]" placeholder="Shadow Ball">
                    <input type="text" name="pokemon[0][movimiento_3]" placeholder="Flamethrower">
                    <input type="text" name="pokemon[0][movimiento_4]" placeholder="U-turn">
                </div>
            </div>

            <div class="pokemon-slot">
                <h3>Pokémon #2</h3>
                <div class="form-group">
                    <label>Especie (Nombre)</label>
                    <input type="text" name="pokemon[1][pokemon_name]" placeholder="Ej: Gholdengo">
                </div>
                <div class="form-inline">
                    <div class="form-group">
                        <label>Objeto</label>
                        <input type="text" name="pokemon[1][objeto]" placeholder="Air Balloon">
                    </div>
                    <div class="form-group">
                        <label>Habilidad</label>
                        <input type="text" name="pokemon[1][habilidad]" placeholder="Good as Gold">
                    </div>
                </div>
                <div class="form-inline">
                    <div class="form-group">
                        <label>Naturaleza</label>
                        <input type="text" name="pokemon[1][naturaleza]" placeholder="Modest">
                    </div>
                    <div class="form-group">
                        <label>EVs (Repartición)</label>
                        <input type="text" name="pokemon[1][evs_reparticion]" placeholder="252 SpA / 4 SpD / 252 Spe">
                    </div>
                </div>
                <div class="moves-grid">
                    <input type="text" name="pokemon[1][movimiento_1]" placeholder="Make It Rain">
                    <input type="text" name="pokemon[1][movimiento_2]" placeholder="Shadow Ball">
                    <input type="text" name="pokemon[1][movimiento_3]" placeholder="Nasty Plot">
                    <input type="text" name="pokemon[1][movimiento_4]" placeholder="Recover">
                </div>
            </div>

        </div>

        <br>
        <button type="submit" class="btn-save">💾 Guardar Equipo Completo</button>
    </form>
</div>

</body>
</html>