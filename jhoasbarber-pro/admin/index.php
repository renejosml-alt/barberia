<?php
$adminTitle = 'Dashboard';
require_once 'includes/admin_header.php';

$db = getDB();

// Estadísticas
$total_citas     = $db->query("SELECT COUNT(*) FROM citas")->fetch_row()[0];
$citas_hoy       = $db->query("SELECT COUNT(*) FROM citas WHERE fecha = CURDATE()")->fetch_row()[0];
$total_clientes  = $db->query("SELECT COUNT(*) FROM usuarios WHERE rol='cliente'")->fetch_row()[0];
$citas_pendientes= $db->query("SELECT COUNT(*) FROM citas WHERE estado='pendiente'")->fetch_row()[0];

// Próximas citas
$proximas = $db->query("
    SELECT c.*, u.nombre AS cliente, b.nombre AS barbero, s.nombre AS servicio, s.precio
    FROM citas c
    JOIN usuarios u ON c.usuario_id = u.id
    JOIN barberos b ON c.barbero_id = b.id
    JOIN servicios s ON c.servicio_id = s.id
    WHERE c.fecha >= CURDATE() AND c.estado != 'cancelada'
    ORDER BY c.fecha ASC, c.hora ASC
    LIMIT 8
")->fetch_all(MYSQLI_ASSOC);
?>

<!-- DASHBOARD -->
<div class="admin-header">
    <h1>📊 Dashboard</h1>
    <span style="color:#888; font-size:.9rem;">Bienvenido, <?= sanitize($_SESSION['nombre']) ?> — <?= date('d/m/Y') ?></span>
</div>

<!-- STATS -->
<div class="stats-grid">
    <div class="stat-card gold">
        <h3>Total de Citas</h3>
        <div class="stat-num"><?= $total_citas ?></div>
        <div class="stat-icon">📅</div>
    </div>
    <div class="stat-card green">
        <h3>Citas Hoy</h3>
        <div class="stat-num"><?= $citas_hoy ?></div>
        <div class="stat-icon">📆</div>
    </div>
    <div class="stat-card blue">
        <h3>Clientes</h3>
        <div class="stat-num"><?= $total_clientes ?></div>
        <div class="stat-icon">👤</div>
    </div>
    <div class="stat-card red">
        <h3>Pendientes</h3>
        <div class="stat-num"><?= $citas_pendientes ?></div>
        <div class="stat-icon">⏳</div>
    </div>
</div>

<!-- PRÓXIMAS CITAS -->
<div class="panel">
    <div class="panel-header">
        <h3>📅 Próximas Citas</h3>
        <a href="citas.php" class="btn btn-gold btn-sm">Ver todas →</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Barbero</th>
                    <th>Servicio</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Estado</th>
                    <th>Precio</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($proximas)): ?>
                    <tr><td colspan="7" style="text-align:center;color:#666;padding:30px;">No hay citas próximas.</td></tr>
                <?php else: ?>
                    <?php foreach ($proximas as $c): ?>
                    <tr>
                        <td><?= sanitize($c['cliente']) ?></td>
                        <td>✂ <?= sanitize($c['barbero']) ?></td>
                        <td><?= sanitize($c['servicio']) ?></td>
                        <td><?= date('d/m/Y', strtotime($c['fecha'])) ?></td>
                        <td><?= substr($c['hora'],0,5) ?></td>
                        <td><span class="badge badge-<?= $c['estado'] ?>"><?= ucfirst($c['estado']) ?></span></td>
                        <td>$<?= number_format($c['precio'],2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ACCIONES RÁPIDAS -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px,1fr));">
    <a href="citas.php"     style="text-decoration:none;"><div class="stat-card gold" style="cursor:pointer;"><div class="stat-icon">📅</div><h3 style="color:#c9a84c;">Gestionar Citas</h3></div></a>
    <a href="servicios.php" style="text-decoration:none;"><div class="stat-card green" style="cursor:pointer;"><div class="stat-icon">✂</div><h3 style="color:#27ae60;">Gestionar Servicios</h3></div></a>
    <a href="barberos.php"  style="text-decoration:none;"><div class="stat-card blue" style="cursor:pointer;"><div class="stat-icon">👤</div><h3 style="color:#2980b9;">Gestionar Barberos</h3></div></a>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
