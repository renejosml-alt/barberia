<?php
require_once __DIR__ . '/../config/db.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($adminTitle) ? sanitize($adminTitle) . ' — Admin — ' : 'Admin — ' ?><?= SITE_NAME ?></title>
    <link rel="stylesheet" href="<?= SITE_URL ?>/../assets/style.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/../assets/admin.css">
</head>
<body class="admin-body">

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <h2>✂ JhoasBarber</h2>
        <small>Panel de Administración</small>
    </div>
    <nav class="sidebar-nav">
        <a href="index.php"    class="<?= basename($_SERVER['PHP_SELF'])=='index.php'    ?'active':'' ?>"><span class="icon">📊</span><span>Dashboard</span></a>
        <a href="citas.php"    class="<?= basename($_SERVER['PHP_SELF'])=='citas.php'    ?'active':'' ?>"><span class="icon">📅</span><span>Citas</span></a>
        <a href="servicios.php"class="<?= basename($_SERVER['PHP_SELF'])=='servicios.php'?'active':'' ?>"><span class="icon">✂</span><span>Servicios</span></a>
        <a href="barberos.php" class="<?= basename($_SERVER['PHP_SELF'])=='barberos.php' ?'active':'' ?>"><span class="icon">👤</span><span>Barberos</span></a>
        <a href="../index.php"><span class="icon">🌐</span><span>Ver sitio</span></a>
    </nav>
    <div class="sidebar-footer">
        <a href="../logout.php">🚪 <span>Cerrar sesión</span></a>
    </div>
</aside>

<main class="admin-main">
