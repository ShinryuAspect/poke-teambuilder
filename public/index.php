<?php
require_once __DIR__ . '/../config/config.php';

if (isset($_SESSION['usuario'])) {
    header('Location: views/dashboard.php');
    exit();
} else {
    header('Location: views/login.php');
    exit();
}
