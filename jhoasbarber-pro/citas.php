<?php
$pageTitle = 'Reservar Cita';
require_once 'includes/header.php';

if (!isLoggedIn()) {
    redirect('login.php?redirect=citas.php');
}

$db = getDB();
$error   = '';
$success = '';

// Procesar reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $barbero_id  = (int)($_POST['barbero_id']  ?? 0);
    $servicio_id = (int)($_POST['servicio_id'] ?? 0);
    $fecha       = sanitize($_POST['fecha']    ?? '');
    $hora        = sanitize($_POST['hora']     ?? '');
    $notas       = sanitize($_POST['notas']    ?? '');

    if (!$barbero_id || !$servicio_id || !$fecha || !$hora) {
        $error = 'Por favor completa todos los campos obligatorios.';
    } elseif (strtotime($fecha) < strtotime('today')) {
        $error = 'La fecha de la cita no puede ser en el pasado.';
    } else {
        // Verificar disponibilidad
        $chk = $db->prepare("SELECT id FROM citas WHERE barbero_id=? AND fecha=? AND hora=? AND estado NOT IN ('cancelada')");
        $chk->bind_param('iss', $barbero_id, $fecha, $hora);
        $chk->execute();
        if ($chk->get_result()->num_rows > 0) {
            $error = 'Ese horario ya está ocupado. Por favor elige otro.';
        } else {
            $stmt = $db->prepare("INSERT INTO citas (usuario_id, barbero_id, servicio_id, fecha, hora, notas) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param('iiisss', $_SESSION['usuario_id'], $barbero_id, $servicio_id, $fecha, $hora, $notas);
            if ($stmt->execute()) {
                $success = '✅ ¡Cita reservada exitosamente! Te esperamos el ' . date('d/m/Y', strtotime($fecha)) . ' a las ' . $hora . '.';
            } else {
                $error = 'Error al guardar la cita. Inténtalo nuevamente.';
            }
        }
    }
}

$barberos  = $db->query("SELECT * FROM barberos WHERE activo=1 ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
$servicios = $db->query("SELECT * FROM servicios WHERE activo=1 ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);

// Mis citas
$stmt = $db->prepare("
    SELECT c.*, b.nombre AS barbero, s.nombre AS servicio, s.precio
    FROM citas c
    JOIN barberos b ON c.barbero_id = b.id
    JOIN servicios s ON c.servicio_id = s.id
    WHERE c.usuario_id = ?
    ORDER BY c.fecha DESC, c.hora DESC
    LIMIT 10
");
$stmt->bind_param('i', $_SESSION['usuario_id']);
$stmt->execute();
$mis_citas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Cancelar cita
if (isset($_GET['cancelar']) && is_numeric($_GET['cancelar'])) {
    $cita_id = (int)$_GET['cancelar'];
    $del = $db->prepare("UPDATE citas SET estado='cancelada' WHERE id=? AND usuario_id=?");
    $del->bind_param('ii', $cita_id, $_SESSION['usuario_id']);
    $del->execute();
    redirect('citas.php?msg=cancelada');
}

$preselect_servicio = (int)($_GET['servicio_id'] ?? 0);
?>

<div class="page-header container">
    <h1>📅 Reservar Cita</h1>
</div>

<section>
    <div class="container">
        <div class="grid grid-2" style="align-items:start; gap:30px;">

            <!-- FORMULARIO DE RESERVA -->
            <div class="form-wrap" style="max-width:100%;">
                <h2 class="form-title">Nueva Reserva</h2>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?= $error ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                <?php if (isset($_GET['msg']) && $_GET['msg'] === 'cancelada'): ?>
                    <div class="alert alert-info">Cita cancelada correctamente.</div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Barbero *</label>
                        <select name="barbero_id" required>
                            <option value="">— Selecciona un barbero —</option>
                            <?php foreach ($barberos as $b): ?>
                                <option value="<?= $b['id'] ?>"><?= sanitize($b['nombre']) ?> — <?= sanitize($b['especialidad']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Servicio *</label>
                        <select name="servicio_id" required>
                            <option value="">— Selecciona un servicio —</option>
                            <?php foreach ($servicios as $s): ?>
                                <option value="<?= $s['id'] ?>" <?= $preselect_servicio == $s['id'] ? 'selected' : '' ?>>
                                    <?= sanitize($s['nombre']) ?> — $<?= number_format($s['precio'], 2) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Fecha *</label>
                        <input type="date" name="fecha" required min="<?= date('Y-m-d') ?>" value="<?= sanitize($_POST['fecha'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Hora *</label>
                        <select name="hora" required>
                            <option value="">— Selecciona la hora —</option>
                            <?php
                            $horas = ['08:00','08:30','09:00','09:30','10:00','10:30','11:00','11:30',
                                      '12:00','12:30','13:00','13:30','14:00','14:30','15:00','15:30',
                                      '16:00','16:30','17:00','17:30','18:00'];
                            foreach ($horas as $h): ?>
                                <option value="<?= $h ?>"><?= $h ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Notas adicionales</label>
                        <textarea name="notas" placeholder="Ej: Me gustaría un fade con diseño en los lados..."><?= sanitize($_POST['notas'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-gold" style="width:100%;">📅 Confirmar Reserva</button>
                </form>
            </div>

            <!-- MIS CITAS -->
            <div>
                <h2 style="color:#c9a84c; margin-bottom:20px;">📋 Mis Citas</h2>
                <?php if (empty($mis_citas)): ?>
                    <div class="card text-center">
                        <div style="font-size:3rem; margin-bottom:15px;">📭</div>
                        <p style="color:#888;">Aún no tienes citas registradas.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($mis_citas as $c): ?>
                    <div class="card mb-20" style="margin-bottom:15px;">
                        <div class="flex-between" style="margin-bottom:10px;">
                            <strong style="color:#fff;"><?= sanitize($c['servicio']) ?></strong>
                            <span class="badge badge-<?= $c['estado'] ?>"><?= ucfirst($c['estado']) ?></span>
                        </div>
                        <p style="color:#aaa; font-size:.9rem;">
                            ✂ <?= sanitize($c['barbero']) ?><br>
                            📅 <?= date('d/m/Y', strtotime($c['fecha'])) ?> · ⏰ <?= substr($c['hora'],0,5) ?><br>
                            💰 $<?= number_format($c['precio'], 2) ?>
                        </p>
                        <?php if ($c['estado'] === 'pendiente' || $c['estado'] === 'confirmada'): ?>
                            <div style="margin-top:12px;">
                                <a href="citas.php?cancelar=<?= $c['id'] ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirm('¿Cancelar esta cita?')">✕ Cancelar</a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
