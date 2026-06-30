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

$pageTitle = "Panel de Control";
$cssFile = "dashboard";
$jsFile = "dashboard";

require_once 'header.php';
?>

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
                                   class="btn-delete action-delete">
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

<?php require_once 'footer.php'; ?>