<?php
$pageTitle = 'Crear Cuenta';
require_once 'includes/header.php';

if (isLoggedIn()) redirect('index.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = sanitize($_POST['nombre']    ?? '');
    $email    = sanitize($_POST['email']     ?? '');
    $telefono = sanitize($_POST['telefono']  ?? '');
    $pass     = $_POST['password']           ?? '';
    $pass2    = $_POST['password2']          ?? '';

    if (!$nombre || !$email || !$pass) {
        $error = 'Los campos Nombre, Email y Contraseña son obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Ingresa un email válido.';
    } elseif (strlen($pass) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } elseif ($pass !== $pass2) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        $db = getDB();
        $chk = $db->prepare("SELECT id FROM usuarios WHERE email=?");
        $chk->bind_param('s', $email);
        $chk->execute();
        if ($chk->get_result()->num_rows > 0) {
            $error = 'Ese email ya está registrado. <a href="login.php">Inicia sesión</a>.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO usuarios (nombre, email, telefono, password, rol) VALUES (?,?,?,?,'cliente')");
            $stmt->bind_param('ssss', $nombre, $email, $telefono, $hash);
            if ($stmt->execute()) {
                redirect('login.php?registered=1');
            } else {
                $error = 'Error al crear la cuenta. Inténtalo de nuevo.';
            }
        }
    }
}
?>

<section style="padding: 80px 20px;">
    <div class="form-wrap">
        <div style="text-align:center; font-size:3rem; margin-bottom:10px;">✂</div>
        <h2 class="form-title">Crear Cuenta</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nombre completo *</label>
                <input type="text" name="nombre" placeholder="Tu nombre" required value="<?= sanitize($_POST['nombre'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" placeholder="tu@email.com" required value="<?= sanitize($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Teléfono</label>
                <input type="tel" name="telefono" placeholder="555-1234" value="<?= sanitize($_POST['telefono'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Contraseña * (mín. 6 caracteres)</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <div class="form-group">
                <label>Confirmar contraseña *</label>
                <input type="password" name="password2" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-gold" style="width:100%;">Crear mi cuenta ✓</button>
        </form>

        <div class="form-footer">
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
