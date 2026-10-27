<?php
$pageTitle = 'Iniciar Sesión';
require_once 'includes/header.php';

if (isLoggedIn()) {
    redirect(isAdmin() ? 'admin/index.php' : 'citas.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if (!$email || !$pass) {
        $error = 'Por favor ingresa tu email y contraseña.';
    } else {
        $db   = getDB();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($pass, $user['password'])) {
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['nombre']     = $user['nombre'];
            $_SESSION['rol']        = $user['rol'];
            $dest = $_GET['redirect'] ?? ($user['rol'] === 'admin' ? 'admin/index.php' : 'citas.php');
            redirect($dest);
        } else {
            $error = 'Email o contraseña incorrectos.';
        }
    }
}
?>

<section style="padding: 80px 20px;">
    <div class="form-wrap">
        <div style="text-align:center; font-size:3rem; margin-bottom:10px;">✂</div>
        <h2 class="form-title">Iniciar Sesión</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success">✅ Registro exitoso. ¡Ya puedes iniciar sesión!</div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="tu@email.com" required value="<?= sanitize($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-gold" style="width:100%;">Entrar →</button>
        </form>

        <div class="form-footer">
            ¿No tienes cuenta? <a href="registro.php">Regístrate gratis</a>
        </div>

        <div class="form-footer" style="margin-top:15px; padding-top:15px; border-top:1px solid #2a2a2a; font-size:.8rem; color:#555;">
            Admin demo: admin@jhoasbarber.com / admin123
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
