<?php
$adminTitle = 'Gestión de Citas';
require_once 'includes/admin_header.php';
$db = getDB();

// Cambiar estado
if (isset($_GET['estado']) && isset($_GET['id'])) {
    $estados_validos = ['pendiente','confirmada','completada','cancelada'];
    $nuevo_estado = $_GET['estado'];
    $cita_id = (int)$_GET['id'];
    if (in_array($nuevo_estado, $estados_validos)) {
        $stmt = $db->prepare("UPDATE citas SET estado=? WHERE id=?");
        $stmt->bind_param('si', $nuevo_estado, $cita_id);
        $stmt->execute();
    }
    redirect('citas.php?msg=ok');
}

// Eliminar cita
if (isset($_GET['delete'])) {
    $cita_id = (int)$_GET['delete'];
    $db->prepare("DELETE FROM citas WHERE id=?")->execute([$cita_id]);
    redirect('citas.php?msg=deleted');
}

// Filtros
$filtro_estado = sanitize($_GET['filtro'] ?? '');
$where = $filtro_estado ? "WHERE c.estado='$filtro_estado'" : '';

$citas = $db->query("
    SELECT c.*, u.nombre AS cliente, u.telefono AS tel, b.nombre AS barbero, s.nombre AS servicio, s.precio
    FROM citas c
    JOIN usuarios u ON c.usuario_id = u.id
    JOIN barberos b ON c.barbero_id = b.id
    JOIN servicios s ON c.servicio_id = s.id
    $where
    ORDER BY c.fecha DESC, c.hora DESC
")->fetch_all(MYSQLI_ASSOC);
?>

<div class="admin-header">
    <h1>📅 Gestión de Citas</h1>
    <div style="display:flex;gap:10px;align-items:center;">
        <form method="GET" style="display:flex;gap:8px;">
            <select name="filtro" onchange="this.form.submit()" style="background:#2a2a2a;border:1px solid #444;color:#fff;padding:8px 12px;border-radius:5px;">
                <option value="">Todos los estados</option>
                <option value="pendiente"  <?= $filtro_estado=='pendiente'  ?'selected':'' ?>>Pendientes</option>
                <option value="confirmada" <?= $filtro_estado=='confirmada' ?'selected':'' ?>>Confirmadas</option>
                <option value="completada" <?= $filtro_estado=='completada' ?'selected':'' ?>>Completadas</option>
                <option value="cancelada"  <?= $filtro_estado=='cancelada'  ?'selected':'' ?>>Canceladas</option>
            </select>
        </form>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success">✅ Operación realizada correctamente.</div>
<?php endif; ?>

<div class="panel">
    <div class="panel-header">
        <h3>Total: <?= count($citas) ?> cita(s)</h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Barbero</th>
                    <th>Servicio</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Estado</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($citas)): ?>
                    <tr><td colspan="9" style="text-align:center;color:#666;padding:30px;">No hay citas con ese filtro.</td></tr>
                <?php else: ?>
                <?php foreach ($citas as $c): ?>
                <tr>
                    <td style="color:#666;">#<?= $c['id'] ?></td>
                    <td>
                        <?= sanitize($c['cliente']) ?>
                        <?php if ($c['tel']): ?><br><small style="color:#666;"><?= sanitize($c['tel']) ?></small><?php endif; ?>
                    </td>
                    <td>✂ <?= sanitize($c['barbero']) ?></td>
                    <td><?= sanitize($c['servicio']) ?></td>
                    <td><?= date('d/m/Y', strtotime($c['fecha'])) ?></td>
                    <td><?= substr($c['hora'],0,5) ?></td>
                    <td><span class="badge badge-<?= $c['estado'] ?>"><?= ucfirst($c['estado']) ?></span></td>
                    <td>$<?= number_format($c['precio'],2) ?></td>
                    <td style="white-space:nowrap;">
                        <?php if ($c['estado'] === 'pendiente'): ?>
                            <a href="?estado=confirmada&id=<?= $c['id'] ?>" class="btn btn-success btn-sm">✓ Confirmar</a>
                        <?php elseif ($c['estado'] === 'confirmada'): ?>
                            <a href="?estado=completada&id=<?= $c['id'] ?>" class="btn btn-info btn-sm">✓ Completar</a>
                        <?php endif; ?>
                        <?php if ($c['estado'] !== 'cancelada'): ?>
                            <a href="?estado=cancelada&id=<?= $c['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Cancelar cita?')">✕</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
