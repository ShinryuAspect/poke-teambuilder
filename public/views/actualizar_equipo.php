<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/models/Equipo.php';

if (!isset($_SESSION['usuario']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit();
}

$usuarioLogueado = $_SESSION['usuario'];
$equipoModel = new Equipo();

$idEquipo = isset($_POST['id_equipo']) ? (int)$_POST['id_equipo'] : 0;
$nombreEquipo = trim($_POST['nombre_equipo'] ?? '');
$rawPokemon = $_POST['pokemon'] ?? [];

if (empty($nombreEquipo)) {
    $nombreEquipo = 'Equipo sin nombre';
}

if ($idEquipo <= 0) {
    header('Location: dashboard.php?status=error_id');
    exit();
}

$pokemonList = [];

foreach ($rawPokemon as $slot => $data) {
    if (empty($data['name'])) {
        continue;
    }

    $m1 = $data['moves'][1] ?? '';
    $m2 = $data['moves'][2] ?? '';
    $m3 = $data['moves'][3] ?? '';
    $m4 = $data['moves'][4] ?? '';
    $habilidad = trim($data['habilidad'] ?? '');
    $gender = strtoupper($data['gender'] ?? 'N');
    if (!in_array($gender, ['N', 'M', 'F'], true)) {
        $gender = 'N';
    }
    $shiny = !empty($data['shiny']) ? 1 : 0;

    $pokemonList[] = [
        'pokemon_name'    => $data['name'],
        'objeto'          => $data['item'] ?? '',
        'habilidad'       => $habilidad,
        'naturaleza'      => '',
        'movimiento_1'    => $m1,
        'movimiento_2'    => $m2,
        'movimiento_3'    => $m3,
        'movimiento_4'    => $m4,
        'gender'          => $gender,
        'shiny'           => $shiny,
        'evs_reparticion' => '',
        'ivs_reparticion' => ''
    ];
}

$resultado = $equipoModel->actualizar($idEquipo, $usuarioLogueado['id'], $nombreEquipo, $pokemonList);

if ($resultado) {
    header('Location: dashboard.php?status=updated');
} else {
    header('Location: dashboard.php?status=error_db');
}

exit();
