<?php
$page_title = $page_title ?? 'Torneo Sportivo';
$active_page = $active_page ?? '';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - Torneo Sportivo</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <div class="header-inner">
        <a href="index.html" class="logo">
            <span class="logo-icon">🏆</span>
            <h1>Torneo Sportivo d'Istituto</h1>
        </a>
        <nav>
            <a href="index.html">Home</a>
            <a href="rose.php" class="<?= $active_page === 'rose' ? 'active' : '' ?>">Squadre</a>
            <a href="calendario.php" class="<?= $active_page === 'calendario' ? 'active' : '' ?>">Calendario</a>
            <a href="classifica.php" class="<?= $active_page === 'classifica' ? 'active' : '' ?>">Classifica</a>
            <a href="albo_oro.php" class="<?= $active_page === 'albo' ? 'active' : '' ?>">Albo d'Oro</a>
        </nav>
    </div>
</header>

<main>