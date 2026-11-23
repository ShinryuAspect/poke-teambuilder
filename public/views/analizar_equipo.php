<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/models/Equipo.php';
require_once __DIR__ . '/../../src/services/GeminiService.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Sesión no válida.']);
    exit();
}

$idEquipo = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($idEquipo <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de equipo no válido.']);
    exit();
}

$equipoModel = new Equipo();
// $datosEquipo = $equipoModel->obtenerDetallesCompletos($idEquipo, $_SESSION['usuario']['id']);

// 2. Simulación de respuesta o llamada al servicio de IA:
// (Sustituye esto por tu lógica real de prompt con Gemini)
$promptEjemplo = "Analiza este equipo competitivo...";
$analisisIA = "¡Análisis táctico completado! Este equipo tiene una excelente sinergia ofensiva, aunque debes tener cuidado con las debilidades al tipo Tierra.";

echo json_encode(['analisis' => $analisisIA]);
exit();
