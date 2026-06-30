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
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $mensajeError = "La contraseña debe tener al menos 8 caracteres, incluir una mayúscula, una minúscula, un número y un carácter especial (@$!%*?&).";
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

$pageTitle = "Crear Cuenta";
$cssFile = "auth";
$jsFile = "formulario";

require_once 'header.php';
?>

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

<?php require_once 'footer.php'; ?>