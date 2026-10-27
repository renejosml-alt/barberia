<?php
$pageTitle = 'Inicio';
require_once 'includes/header.php';

$db = getDB();

// Obtener servicios destacados
$servicios = $db->query("SELECT * FROM servicios WHERE activo = 1 LIMIT 6")->fetch_all(MYSQLI_ASSOC);

// Obtener barberos activos
$barberos = $db->query("SELECT * FROM barberos WHERE activo = 1")->fetch_all(MYSQLI_ASSOC);

$iconos = ['💈','✂','🪒','👔','⭐','🏆'];
$barberIcons = ['👨','👱','🧔','👲'];
?>

<!-- HERO -->
<section class="hero">
    <h1>✂ Jhoas<span>Barber Pro</span></h1>
    <p>El mejor corte, el mejor estilo. Tu barbería de confianza con los mejores barberos de la ciudad.</p>
    <a href="citas.php" class="btn btn-gold" style="margin-right:12px;">📅 Reservar Cita</a>
    <a href="servicios.php" class="btn btn-outline">Ver Servicios</a>
</section>

<!-- ¿POR QUÉ ELEGIRNOS? -->
<section class="alt">
    <div class="container">
        <div class="section-title">
            <h2>¿Por qué elegir JhoasBarber?</h2>
            <div class="line"></div>
        </div>
        <div class="grid grid-3">
            <div class="card text-center">
                <div class="card-icon">🏆</div>
                <h3>Experiencia Profesional</h3>
                <p>Barberos certificados con años de experiencia en cortes modernos y clásicos.</p>
            </div>
            <div class="card text-center">
                <div class="card-icon">⚡</div>
                <h3>Reserva Online Fácil</h3>
                <p>Agenda tu cita en segundos, elige tu barbero favorito y el horario que prefieras.</p>
            </div>
            <div class="card text-center">
                <div class="card-icon">💎</div>
                <h3>Productos Premium</h3>
                <p>Usamos solo productos de alta calidad para el cuidado de tu cabello y barba.</p>
            </div>
        </div>
    </div>
</section>

<!-- SERVICIOS -->
<section>
    <div class="container">
        <div class="section-title">
            <h2>Nuestros Servicios</h2>
            <p>Precios justos, resultados increíbles</p>
            <div class="line"></div>
        </div>
        <div class="grid grid-3">
            <?php foreach ($servicios as $i => $s): ?>
            <div class="card">
                <div class="card-icon"><?= $iconos[$i % count($iconos)] ?></div>
                <h3><?= sanitize($s['nombre']) ?></h3>
                <p><?= sanitize($s['descripcion']) ?></p>
                <div class="price">$<?= number_format($s['precio'], 2) ?></div>
                <div class="duration">⏱ <?= $s['duracion_min'] ?> minutos</div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-20">
            <a href="servicios.php" class="btn btn-outline">Ver todos los servicios →</a>
        </div>
    </div>
</section>

<!-- BARBEROS -->
<section class="alt">
    <div class="container">
        <div class="section-title">
            <h2>Nuestro Equipo</h2>
            <p>Maestros de la tijera y la navaja</p>
            <div class="line"></div>
        </div>
        <div class="grid grid-3">
            <?php foreach ($barberos as $i => $b): ?>
            <div class="barber-card">
                <div class="barber-avatar"><?= $barberIcons[$i % count($barberIcons)] ?></div>
                <h3><?= sanitize($b['nombre']) ?></h3>
                <div class="barber-specialty">✂ <?= sanitize($b['especialidad']) ?></div>
                <p><?= sanitize($b['descripcion'] ?? 'Barbero profesional.') ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section style="text-align:center; background: linear-gradient(135deg, #1a1205, #0d0d0d);">
    <div class="container">
        <h2 style="font-size:2.2rem; color:#c9a84c; margin-bottom:15px;">¿Listo para tu nuevo look?</h2>
        <p style="color:#aaa; font-size:1.1rem; margin-bottom:30px;">Reserva tu cita ahora y experimenta el mejor servicio de barbería.</p>
        <a href="<?= isLoggedIn() ? 'citas.php' : 'registro.php' ?>" class="btn btn-gold" style="font-size:1.1rem; padding:16px 40px;">
            📅 <?= isLoggedIn() ? 'Reservar mi Cita' : 'Crear cuenta y reservar' ?>
        </a>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
