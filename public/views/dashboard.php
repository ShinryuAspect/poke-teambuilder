<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/Models/Equipo.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

$usuarioLogueado = $_SESSION['usuario'];
$equipoModel = new Equipo();

$mensajeError = '';
$mensajeExito = '';

if (isset($_GET['action']) && $_GET['action'] === 'eliminar' && isset($_GET['id'])) {
    $idEquipoAEliminar = (int)$_GET['id'];
    
    if ($equipoModel->eliminar($idEquipoAEliminar, $usuarioLogueado['id'])) {
        $mensajeExito = "El equipo ha sido eliminado correctamente.";
    } else {
        $mensajeError = "No se pudo eliminar el equipo o no tienes permisos para hacerlo.";
    }
}

$listaEquipos = $equipoModel->obtenerPorUsuario($usuarioLogueado['id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Pokémon Team Builder</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f4f9; margin: 0; padding: 20px; }
        .dashboard-header { display: flex; justify-content: space-between; align-items: center; background: #fff; padding: 15px 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        h1 { color: #333; margin: 0; font-size: 24px; }
        .user-info { font-size: 16px; color: #666; }
        .btn-logout { padding: 6px 12px; background-color: #ef5350; color: #fff; text-decoration: none; border-radius: 4px; font-weight: bold; margin-left: 10px; }
        .btn-logout:hover { background-color: #e53935; }
        .btn-create { padding: 10px 20px; background-color: #2e7d32; color: #fff; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block; margin-bottom: 20px; }
        .btn-create:hover { background-color: #1b5e20; }
        .main-content { margin-top: 25px; }
        .alert { padding: 12px; margin-bottom: 15px; border-radius: 4px; }
        .alert-danger { background-color: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
        .alert-success { background-color: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
        
        .table-container { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 12px 15px; border-bottom: 1px solid #eee; }
        th { background-color: #f8f9fa; color: #555; text-transform: uppercase; font-size: 13px; letter-spacing: 0.5px; }
        .tier-badge { display: inline-block; padding: 3px 8px; font-size: 12px; font-weight: bold; border-radius: 4px; background: #ede7f6; color: #5e35b1; }
        .btn-delete { color: #c62828; text-decoration: none; font-weight: bold; font-size: 14px; }
        .btn-delete:hover { text-decoration: underline; }
        .empty-state { text-align: center; padding: 40px; color: #888; font-style: italic; }
    </style>
</head>
<body>

<div class="dashboard-header">
    <h1>🎒 Pokémon Team Builder</h1>
    <div class="user-info">
        Bienvenido, <strong><?= htmlspecialchars($usuarioLogueado['username']) ?></strong> 
        | <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
    </div>
</div>

<div class="main-content">
    
    <?php if (!empty($mensajeError)): ?>
        <div class="alert alert-danger"><?= $mensajeError ?></div>
    <?php endif; ?>

    <?php if (!empty($mensajeExito)): ?>
        <div class="alert alert-success"><?= $mensajeExito ?></div>
    <?php endif; ?>

    <a href="crear_equipo.php" class="btn-create">+ Crear Nuevo Equipo</a>

    <div class="table-container">
        <h2>Mis Equipos Guardados</h2>
        
        <?php if (count($listaEquipos) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nombre del Equipo</th>
                        <th>Formato / Tier</th>
                        <th>Fecha de Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listaEquipos as $equipo): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($equipo['nombre_equipo']) ?></strong></td>
                            <td><span class="tier-badge"><?= htmlspecialchars($equipo['formato_tier']) ?></span></td>
                            <td><?= date('d/m/Y H:i', strtotime($equipo['fecha_creacion'])) ?></td>
                            <td>
                                <a href="dashboard.php?action=eliminar&id=<?= $equipo['id'] ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('¿Seguro que deseas eliminar este equipo?');">
                                   ❌ Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <p>Aún no has guardado ningún equipo. ¡Haz clic en "+ Crear Nuevo Equipo" para empezar!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>