<?php
$pageTitle = 'Servicios';
require_once 'includes/header.php';
$db = getDB();
$servicios = $db->query("SELECT * FROM servicios WHERE activo = 1 ORDER BY precio ASC")->fetch_all(MYSQLI_ASSOC);
$iconos = ['💈','✂','🪒','👔','⭐','🏆','💇','🎨'];
?>

<div class="page-header container" style="margin-bottom:0;">
    <h1>✂ Nuestros Servicios</h1>
    <a href="citas.php" class="btn btn-gold">📅 Reservar Ahora</a>
</div>

<section>
    <div class="container">
        <?php if (empty($servicios)): ?>
            <div class="alert alert-info">No hay servicios disponibles en este momento.</div>
        <?php else: ?>
        <div class="grid grid-3">
            <?php foreach ($servicios as $i => $s): ?>
            <div class="card">
                <div class="card-icon"><?= $iconos[$i % count($iconos)] ?></div>
                <h3><?= sanitize($s['nombre']) ?></h3>
                <p><?= sanitize($s['descripcion']) ?></p>
                <div class="price">$<?= number_format($s['precio'], 2) ?></div>
                <div class="duration">⏱ Duración: <?= $s['duracion_min'] ?> minutos</div>
                <div style="margin-top:18px;">
                    <a href="citas.php?servicio_id=<?= $s['id'] ?>" class="btn btn-outline btn-sm">Reservar este servicio</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="card mt-20" style="border-color:#c9a84c; text-align:center; padding:30px;">
            <h3 style="font-size:1.3rem; margin-bottom:10px;">¿Tienes alguna pregunta?</h3>
            <p style="color:#aaa; margin-bottom:15px;">Nuestro equipo está listo para asesorarte en el mejor servicio para ti.</p>
            <a href="citas.php" class="btn btn-gold">Reservar Cita</a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
