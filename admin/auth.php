<?php
/**
 * auth.php — Middleware Proteksi Admin PAUDqu ASY SYUHADA
 * 
 * Sertakan file ini di SETIAP halaman admin:
 *   require_once 'auth.php';
 * 
 * Fitur:
 *   - Cek sesi login
 *   - Session timeout otomatis (30 menit idle)
 *   - Regenerasi session ID berkala (cegah session fixation)
 *   - CSRF token generator & validator
 */

// Pastikan session aktif
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,          // tutup saat browser ditutup
        'path'     => '/',
        'secure'   => false,      // ganti true jika pakai HTTPS
        'httponly' => true,        // JS tidak bisa baca cookie
        'samesite' => 'Strict',
    ]);
    session_start();
}

// ── 1. Cek Login ──────────────────────────────────────────────────
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// ── 2. Session Timeout (30 menit idle) ───────────────────────────
define('SESSION_TIMEOUT', 1800); // detik

if (isset($_SESSION['last_activity'])) {
    $idle = time() - $_SESSION['last_activity'];
    if ($idle > SESSION_TIMEOUT) {
        session_unset();
        session_destroy();
        header('Location: login.php?timeout=1');
        exit;
    }
}
$_SESSION['last_activity'] = time();

// ── 3. Regenerasi Session ID setiap 10 menit ─────────────────────
if (!isset($_SESSION['last_regen'])) {
    $_SESSION['last_regen'] = time();
}
if ((time() - $_SESSION['last_regen']) > 600) {
    session_regenerate_id(true);
    $_SESSION['last_regen'] = time();
}

// ── 4. CSRF Token ─────────────────────────────────────────────────
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Output hidden CSRF input field — pakai di dalam <form>
 *   echo csrf_field();
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="'
         . htmlspecialchars($_SESSION['csrf_token']) . '">';
}

/**
 * Validasi CSRF token — panggil di awal handler POST
 *   csrf_verify();
 */
function csrf_verify(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        die('<p style="font-family:sans-serif;padding:40px;">❌ Permintaan tidak valid (CSRF). Silakan <a href="index.php">kembali ke dashboard</a>.</p>');
    }
    // Rotate token setelah POST berhasil divalidasi
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ── 5. Helper: nama & role admin dari sesi ────────────────────────
$adminUser = $_SESSION['admin_user'] ?? 'Admin';
$adminRole = $_SESSION['admin_role'] ?? 'Operator';
?>
