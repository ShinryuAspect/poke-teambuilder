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
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>

<div class="auth-container">
    <h2>Iniciar Sesión</h2>

    <?php if (!empty($mensajeError)): ?>
        <div class="alert alert-danger"><?= $mensajeError ?></div>
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

    <div class="auth-link">
        ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
    </div>
</div>

<script src="../assets/js/formulario.js"></script>
</body>
</html>