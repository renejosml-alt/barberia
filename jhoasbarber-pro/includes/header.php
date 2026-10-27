<?php require_once __DIR__ . '/../config/db.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' — ' : '' ?><?= SITE_NAME ?></title>
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/style.css">
</head>
<body>
<nav>
    <div class="nav-logo">✂ Jhoas<span>Barber Pro</span></div>
    <ul class="nav-links">
        <li><a href="<?= SITE_URL ?>/index.php">Inicio</a></li>
        <li><a href="<?= SITE_URL ?>/servicios.php">Servicios</a></li>
        <li><a href="<?= SITE_URL ?>/citas.php">Reservar Cita</a></li>
        <?php if (isLoggedIn()): ?>
            <?php if (isAdmin()): ?>
                <li><a href="<?= SITE_URL ?>/admin/index.php">⚙ Admin</a></li>
            <?php else: ?>
                <li><a href="<?= SITE_URL ?>/citas.php">Mis Citas</a></li>
            <?php endif; ?>
            <li><a href="<?= SITE_URL ?>/logout.php">Salir (<?= sanitize($_SESSION['nombre']) ?>)</a></li>
        <?php else: ?>
            <li><a href="<?= SITE_URL ?>/login.php">Iniciar Sesión</a></li>
            <li><a class="nav-btn" href="<?= SITE_URL ?>/registro.php">Registrarse</a></li>
        <?php endif; ?>
    </ul>
</nav>
