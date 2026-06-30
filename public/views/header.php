<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

if (isset($_SESSION['usuario'])) {
    try {
        $db = Database::conectar();
        $stmt = $db->prepare("SELECT id FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['usuario']['id']]);
        
        if (!$stmt->fetch()) {
            $_SESSION = [];
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
            }
            session_destroy();
            
            header('Location: login.php');
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION = [];
        session_destroy();
        header('Location: login.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . " - Pokémon Team Builder" : "Pokémon Team Builder" ?></title>
    
    <link rel="stylesheet" href="../assets/css/variables.css">
    
    <?php if (isset($cssFile)): ?>
        <link rel="stylesheet" href="../assets/css/<?= $cssFile ?>.css">
    <?php endif; ?>
</head>
<body>