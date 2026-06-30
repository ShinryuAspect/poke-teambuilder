<?php require_once __DIR__ . '/../../config/config.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . " - Pokémon Team Builder" : "Pokémon Team Builder" ?></title>
    <?php if (isset($cssFile)): ?>
        <link rel="stylesheet" href="../assets/css/<?= $cssFile ?>.css">
    <?php endif; ?>
</head>
<body>