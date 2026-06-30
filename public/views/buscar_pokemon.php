<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/Services/SmogonService.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso denegado. Sesión no válida.']);
    exit();
}

$query = isset($_GET['q']) ? trim(htmlspecialchars($_GET['q'])) : '';

if (strlen($query) < 3) {
    echo json_encode([]);
    exit();
}

$smogonService = new SmogonService();
$resultados = $smogonService->buscarEspecie($query);

echo json_encode($resultados);
exit();
