<?php
$adminTitle = 'Gestión de Barberos';
require_once 'includes/admin_header.php';
$db = getDB();

$error = $success = '';

// Guardar barbero
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id           = (int)($_POST['id'] ?? 0);
    $nombre       = sanitize($_POST['nombre']        ?? '');
    $especialidad = sanitize($_POST['especialidad']  ?? '');
    $descripcion  = sanitize($_POST['descripcion']   ?? '');
    $activo       = isset($_POST['activo']) ? 1 : 0;

    if (!$nombre) {
        $error = 'El nombre del barbero es obligatorio.';
    } else {
        if ($id > 0) {
            $stmt = $db->prepare("UPDATE barberos SET nombre=?,especialidad=?,descripcion=?,activo=? WHERE id=?");
            $stmt->bind_param('sssii', $nombre, $especialidad, $descripcion, $activo, $id);
        } else {
            $stmt = $db->prepare("INSERT INTO barberos (nombre,especialidad,descripcion,activo) VALUES (?,?,?,?)");
            $stmt->bind_param('sssi', $nombre, $especialidad, $descripcion, $activo);
        }
        $stmt->execute() ? $success = 'Barbero guardado correctamente.' : ($error = 'Error al guardar.');
    }
}

// Eliminar
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $chk = $db->prepare("SELECT COUNT(*) FROM citas WHERE barbero_id=?");
    $chk->bind_param('i', $id);
    $chk->execute();
    if ($chk->get_result()->fetch_row()[0] > 0) {
        // Solo desactivar si tiene citas
        $db->prepare("UPDATE barberos SET activo=0 WHERE id=?")->execute([$id]);
        redirect('barberos.php?msg=deactivated');
    } else {
        $db->prepare("DELETE FROM barberos WHERE id=?")->execute([$id]);
        redirect('barberos.php?msg=deleted');
    }
}

// Editar
$editando = null;
if (isset($_GET['edit'])) {
    $s = $db->prepare("SELECT * FROM barberos WHERE id=?");
    $s->bind_param('i', $_GET['edit']);
    $s->execute();
    $editando = $s->get_result()->fetch_assoc();
}

$barberos = $db->query("SELECT b.*, (SELECT COUNT(*) FROM citas c WHERE c.barbero_id=b.id) AS total_citas FROM barberos b ORDER BY b.nombre")->fetch_all(MYSQLI_ASSOC);
$barberIcons = ['👨','👱','🧔','👲','🧑'];
?>

<div class="admin-header">
    <h1>👤 Gestión de Barberos</h1>
</div>

<div class="grid grid-2" style="align-items:start; gap:25px;">

    <!-- FORMULARIO -->
    <div class="panel">
        <div class="panel-header">
            <h3><?= $editando ? '✏ Editar Barbero' : '➕ Nuevo Barbero' ?></h3>
            <?php if ($editando): ?><a href="barberos.php" class="btn btn-sm" style="border:1px solid #444;color:#aaa;">Cancelar</a><?php endif; ?>
        </div>
        <div class="panel-body">
            <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

            <form method="POST" class="admin-form">
                <?php if ($editando): ?><input type="hidden" name="id" value="<?= $editando['id'] ?>"><?php endif; ?>
                <div class="form-group">
                    <label>Nombre del barbero *</label>
                    <input type="text" name="nombre" required value="<?= sanitize($editando['nombre'] ?? $_POST['nombre'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Especialidad</label>
                    <input type="text" name="especialidad" placeholder="Ej: Fade, Cortes clásicos..." value="<?= sanitize($editando['especialidad'] ?? $_POST['especialidad'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea name="descripcion" placeholder="Descripción breve del barbero..."><?= sanitize($editando['descripcion'] ?? $_POST['descripcion'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="activo" value="1" <?= ($editando['activo'] ?? 1) ? 'checked' : '' ?>>
                        Barbero activo (disponible para citas)
                    </label>
                </div>
                <button type="submit" class="btn btn-gold" style="width:100%;">
                    <?= $editando ? '💾 Guardar Cambios' : '➕ Agregar Barbero' ?>
                </button>
            </form>
        </div>
    </div>

    <!-- LISTA BARBEROS -->
    <div>
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success">✅ <?= $_GET['msg']==='deleted'?'Barbero eliminado.':'Barbero desactivado (tiene citas asociadas).' ?></div>
        <?php endif; ?>
        <?php foreach ($barberos as $i => $b): ?>
        <div class="card mb-20" style="margin-bottom:15px; border-color:<?= $b['activo']?'#333':'#c0392b' ?>;">
            <div class="flex-between">
                <div style="display:flex;align-items:center;gap:15px;">
                    <div style="font-size:2rem;"><?= $barberIcons[$i % count($barberIcons)] ?></div>
                    <div>
                        <strong style="color:#fff;font-size:1rem;"><?= sanitize($b['nombre']) ?></strong><br>
                        <span style="color:#c9a84c;font-size:.85rem;">✂ <?= sanitize($b['especialidad']) ?></span><br>
                        <span style="color:#666;font-size:.8rem;">📅 <?= $b['total_citas'] ?> cita(s) total</span>
                    </div>
                </div>
                <span class="badge" style="background:<?= $b['activo']?'#1a3a2a':'#3a1a1a' ?>;color:<?= $b['activo']?'#27ae60':'#c0392b' ?>">
                    <?= $b['activo']?'Activo':'Inactivo' ?>
                </span>
            </div>
            <?php if ($b['descripcion']): ?>
                <p style="color:#999;font-size:.85rem;margin-top:10px;"><?= sanitize($b['descripcion']) ?></p>
            <?php endif; ?>
            <div style="margin-top:12px;display:flex;gap:8px;">
                <a href="?edit=<?= $b['id'] ?>" class="btn btn-info btn-sm">✏ Editar</a>
                <a href="?delete=<?= $b['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar/desactivar este barbero?')">🗑 Eliminar</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
