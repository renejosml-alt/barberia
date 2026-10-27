<?php
$adminTitle = 'Gestión de Servicios';
require_once 'includes/admin_header.php';
$db = getDB();

$error = $success = '';

// Guardar servicio (nuevo o editar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = (int)($_POST['id'] ?? 0);
    $nombre      = sanitize($_POST['nombre']      ?? '');
    $descripcion = sanitize($_POST['descripcion'] ?? '');
    $precio      = floatval($_POST['precio']      ?? 0);
    $duracion    = (int)($_POST['duracion_min']   ?? 30);
    $activo      = isset($_POST['activo']) ? 1 : 0;

    if (!$nombre || $precio <= 0) {
        $error = 'Nombre y precio son obligatorios.';
    } else {
        if ($id > 0) {
            $stmt = $db->prepare("UPDATE servicios SET nombre=?,descripcion=?,precio=?,duracion_min=?,activo=? WHERE id=?");
            $stmt->bind_param('ssdiii', $nombre, $descripcion, $precio, $duracion, $activo, $id);
        } else {
            $stmt = $db->prepare("INSERT INTO servicios (nombre,descripcion,precio,duracion_min,activo) VALUES (?,?,?,?,?)");
            $stmt->bind_param('ssdii', $nombre, $descripcion, $precio, $duracion, $activo);
        }
        $stmt->execute() ? $success = 'Servicio guardado correctamente.' : ($error = 'Error al guardar.');
    }
}

// Eliminar
if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM servicios WHERE id=?")->execute([(int)$_GET['delete']]);
    redirect('servicios.php?msg=deleted');
}

// Editar
$editando = null;
if (isset($_GET['edit'])) {
    $s = $db->prepare("SELECT * FROM servicios WHERE id=?");
    $s->bind_param('i', $_GET['edit']);
    $s->execute();
    $editando = $s->get_result()->fetch_assoc();
}

$servicios = $db->query("SELECT * FROM servicios ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
?>

<div class="admin-header">
    <h1>✂ Gestión de Servicios</h1>
</div>

<div class="grid grid-2" style="align-items:start; gap:25px;">

    <!-- FORMULARIO -->
    <div class="panel">
        <div class="panel-header">
            <h3><?= $editando ? '✏ Editar Servicio' : '➕ Nuevo Servicio' ?></h3>
            <?php if ($editando): ?><a href="servicios.php" class="btn btn-sm" style="border:1px solid #444;color:#aaa;">Cancelar</a><?php endif; ?>
        </div>
        <div class="panel-body">
            <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

            <form method="POST" class="admin-form">
                <?php if ($editando): ?><input type="hidden" name="id" value="<?= $editando['id'] ?>"><?php endif; ?>
                <div class="form-group">
                    <label>Nombre del servicio *</label>
                    <input type="text" name="nombre" required value="<?= sanitize($editando['nombre'] ?? $_POST['nombre'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea name="descripcion"><?= sanitize($editando['descripcion'] ?? $_POST['descripcion'] ?? '') ?></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Precio ($) *</label>
                        <input type="number" name="precio" step="0.01" min="0" required value="<?= $editando['precio'] ?? $_POST['precio'] ?? '' ?>">
                    </div>
                    <div class="form-group">
                        <label>Duración (minutos)</label>
                        <input type="number" name="duracion_min" min="5" value="<?= $editando['duracion_min'] ?? $_POST['duracion_min'] ?? 30 ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="activo" value="1" <?= ($editando['activo'] ?? 1) ? 'checked' : '' ?>>
                        Servicio activo (visible en el sitio)
                    </label>
                </div>
                <button type="submit" class="btn btn-gold" style="width:100%;">
                    <?= $editando ? '💾 Guardar Cambios' : '➕ Agregar Servicio' ?>
                </button>
            </form>
        </div>
    </div>

    <!-- LISTA DE SERVICIOS -->
    <div class="panel">
        <div class="panel-header"><h3>📋 Servicios (<?= count($servicios) ?>)</h3></div>
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success" style="margin:15px;">✅ Operación realizada.</div>
        <?php endif; ?>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Nombre</th><th>Precio</th><th>Min</th><th>Estado</th><th>Acciones</th></tr></thead>
                <tbody>
                    <?php foreach ($servicios as $s): ?>
                    <tr>
                        <td><strong><?= sanitize($s['nombre']) ?></strong><br><small style="color:#666;"><?= sanitize(substr($s['descripcion'],0,40)) ?>...</small></td>
                        <td>$<?= number_format($s['precio'],2) ?></td>
                        <td><?= $s['duracion_min'] ?></td>
                        <td><span class="badge" style="background:<?= $s['activo']?'#1a3a2a':'#3a1a1a' ?>;color:<?= $s['activo']?'#27ae60':'#c0392b' ?>"><?= $s['activo']?'Activo':'Inactivo' ?></span></td>
                        <td>
                            <a href="?edit=<?= $s['id'] ?>" class="btn btn-info btn-sm">✏</a>
                            <a href="?delete=<?= $s['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este servicio?')">🗑</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
