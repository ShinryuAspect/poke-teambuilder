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
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .register-container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #666; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .btn-submit { width: 100%; padding: 10px; background-color: #ef5350; color: #fff; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; }
        .btn-submit:hover { background-color: #e53935; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 4px; font-size: 14px; }
        .alert-danger { background-color: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
        .alert-success { background-color: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
        .login-link { text-align: center; margin-top: 15px; font-size: 14px; }
        .login-link a { color: #0288d1; text-decoration: none; }
    </style>
</head>
<body>

<div class="register-container">
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
            <input type="password" id="password" name="password" required minlength="8">
        </div>

        <div class="form-group">
            <label for="password_confirm">Confirmar Contraseña</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
        </div>

        <button type="submit" class="btn-submit">Registrarse</button>
    </form>

    <div class="login-link">
        ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
    </div>
</div>

</body>
</html>