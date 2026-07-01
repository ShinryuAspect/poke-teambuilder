<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/Models/Equipo.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioLogueado = $_SESSION['usuario'];
    $equipoModel = new Equipo();

    $nombreEquipo = trim($_POST['nombre_equipo'] ?? '');
    $rawPokemon = $_POST['pokemon'] ?? [];

    if (empty($nombreEquipo)) {
        $nombreEquipo = 'Equipo sin nombre';
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
            'pokemon_name'   => $data['name'],
            'objeto'         => $data['item'] ?? '',
            'habilidad'      => $habilidad,
            'naturaleza'     => '',
            'movimiento_1'   => $m1,
            'movimiento_2'   => $m2,
            'movimiento_3'   => $m3,
            'movimiento_4'   => $m4,
            'gender'         => $gender,
            'shiny'          => $shiny,
            'evs_reparticion'=> '',
            'ivs_reparticion'=> ''
        ];
    }

    $guardadoExitoso = $equipoModel->guardar($usuarioLogueado['id'], $nombreEquipo, $pokemonList);

    if ($guardadoExitoso) {
        header('Location: dashboard.php?msg=success');
    } else {
        header('Location: dashboard.php?msg=error');
    }
    exit();
} else {
    header('Location: dashboard.php');
    exit();
}