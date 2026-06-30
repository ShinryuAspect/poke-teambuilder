<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/Models/Usuario.php';

$mensajeError = '';
$mensajeExito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim(htmlspecialchars($_POST['username'])) : '';
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

    if (empty($username) || empty($email) || empty($password)) {
        $mensajeError = "Todos los campos son obligatorios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensajeError = "El formato del correo electrónico no es válido.";
    } elseif (strlen($password) < 8) {
        $mensajeError = "La contraseña debe tener al menos 8 caracteres.";
    } elseif ($password !== $password_confirm) {
        $mensajeError = "Las contraseñas no coinciden.";
    } else {
        $usuarioModel = new Usuario();
        if ($usuarioModel->registrar($username, $email, $password)) {
            $mensajeExito = "¡Registro completado con éxito! Ya puedes iniciar sesión.";
        } else {
            $mensajeError = "El nombre de usuario o el correo electrónico ya están registrados.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Pokémon Team Builder</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>

<div class="auth-container">
    <h2>Crear Cuenta</h2>

    <?php if (!empty($mensajeError)): ?>
        <div class="alert alert-danger"><?= $mensajeError ?></div>
    <?php endif; ?>

    <?php if (!empty($mensajeExito)): ?>
        <div class="alert alert-success"><?= $mensajeExito ?></div>
    <?php endif; ?>

    <form action="registro.php" method="POST" novalidate>
        <div class="form-group">
            <label for="username">Nombre de Usuario</label>
            <input type="text" id="username" name="username" required 
                   value="<?= isset($username) && empty($mensajeExito) ? $username : '' ?>">
        </div>

        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" id="email" name="email" required 
                   value="<?= isset($email) && empty($mensajeExito) ? $email : '' ?>">
        </div>

        <div class="form-group">
            <label for="password">Contraseña (Mín. 8 caracteres)</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="password_confirm">Confirmar Contraseña</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
        </div>

        <button type="submit" class="btn-submit">Registrarse</button>
    </form>

    <div class="auth-link">
        ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
    </div>
</div>

<script src="../assets/js/formulario.js"></script>
</body>
</html>