<?php
// =====================================================
// CONFIGURACIÓN DE BASE DE DATOS — JhoasBarber Pro
// =====================================================
// IMPORTANTE: Cambia estos valores ANTES de subir a InfinityFree
// con los datos que te da el panel de control (VistaPanel)

define('DB_HOST',   'sql102.infinityfree.com'); // InfinityFree hostname
define('DB_USER',   'if0_43272463');             // Usuario MySQL
define('DB_PASS',   'VXgzGyZtCjhpn');           // Contraseña MySQL
define('DB_NAME',   'if0_43272463_barberia');    // Nombre de la base de datos

define('SITE_NAME', 'JhoasBarber Pro');
define('SITE_URL',  '');  // Dejar vacío si está en htdocs raíz

// Conexión global
function getDB() {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $conn->set_charset('utf8mb4');
        if ($conn->connect_error) {
            die('<div style="background:#c0392b;color:#fff;padding:20px;font-family:sans-serif;">
                ❌ Error de conexión a la base de datos.<br>
                Verifica las credenciales en config/db.php<br>
                <small>' . $conn->connect_error . '</small>
            </div>');
        }
    }
    return $conn;
}

// Sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helpers
function isLoggedIn() {
    return isset($_SESSION['usuario_id']);
}

function isAdmin() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
