<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/Models/Usuario.php';

if (isset($_SESSION['usuario'])) {
    header('Location: dashboard.php');
    exit();
}

$mensajeError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = isset($_POST['login_input']) ? trim(htmlspecialchars($_POST['login_input'])) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($usernameOrEmail) || empty($password)) {
        $mensajeError = "Por favor, rellena todos los campos.";
    } else {
        $usuarioModel = new Usuario();
        $resultado = $usuarioModel->login($usernameOrEmail, $password);

        if ($resultado !== false) {
            session_regenerate_id(true);

            $_SESSION['usuario'] = $resultado;

            header('Location: dashboard.php');
            exit();
        } else {
            $mensajeError = "El nombre de usuario/email o la contraseña son incorrectos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Pokémon Team Builder</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #666; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .btn-submit { width: 100%; padding: 10px; background-color: #ef5350; color: #fff; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; }
        .btn-submit:hover { background-color: #e53935; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 4px; font-size: 14px; background-color: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
        .register-link { text-align: center; margin-top: 15px; font-size: 14px; }
        .register-link a { color: #0288d1; text-decoration: none; }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Iniciar Sesión</h2>

    <?php if (!empty($mensajeError)): ?>
        <div class="alert"><?= $mensajeError ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST" novalidate>
        <div class="form-group">
            <label for="login_input">Usuario o Correo Electrónico</label>
            <input type="text" id="login_input" name="login_input" required 
                   value="<?= isset($usernameOrEmail) ? $usernameOrEmail : '' ?>">
        </div>

        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn-submit">Entrar</button>
    </form>

    <div class="register-link">
        ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
    </div>
</div>

</body>
</html>