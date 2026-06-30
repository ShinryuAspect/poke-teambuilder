<?php
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

$usuarioLogueado = $_SESSION['usuario'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Pokémon Team Builder</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 20px; }
        .dashboard-header { display: flex; justify-content: space-between; align-items: center; background: #fff; padding: 15px 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        h1 { color: #333; margin: 0; font-size: 24px; }
        .user-info { font-size: 16px; color: #666; }
        .btn-logout { padding: 8px 16px; background-color: #ef5350; color: #fff; text-decoration: none; border-radius: 4px; font-weight: bold; }
        .btn-logout:hover { background-color: #e53935; }
        .main-content { margin-top: 30px; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); min-height: 300px; }
        .welcome-box { border-left: 4px solid #0288d1; padding-left: 15px; margin-bottom: 20px; }
        .role-badge { display: inline-block; padding: 3px 8px; font-size: 12px; font-weight: bold; border-radius: 12px; background: #e1f5fe; color: #0288d1; text-transform: uppercase; }
    </style>
</head>
<body>

<div class="dashboard-header">
    <h1>🎒 Pokémon Team Builder</h1>
    <div class="user-info">
        Bienvenido, <strong><?= htmlspecialchars($usuarioLogueado['username']) ?></strong> 
        <span class="role-badge"><?= htmlspecialchars($usuarioLogueado['rol']) ?></span>
        | <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
    </div>
</div>

<div class="main-content">
    <div class="welcome-box">
        <h2>Panel de Gestión de Equipos</h2>
        <p>Tu sesión se ha iniciado correctamente y de forma segura utilizando tokens nativos de PHP 8.</p>
    </div>
    
    <p style="color: #888; font-style: italic;">Próximamente: Aquí podrás crear tus equipos competitivos y conectar con la API de Smogon.</p>
</div>

</body>
</html>