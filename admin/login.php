<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => false,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

$error   = '';
$timeout = isset($_GET['timeout']);

// ── CSRF Token ────────────────────────────────────────────────────
if (empty($_SESSION['csrf_token_login'])) {
    $_SESSION['csrf_token_login'] = bin2hex(random_bytes(32));
}

// ── Proteksi Brute Force ──────────────────────────────────────────
$maxAttempts  = 5;
$lockoutTime  = 600; // 10 menit

if (!isset($_SESSION['login_attempts']))     $_SESSION['login_attempts']     = 0;
if (!isset($_SESSION['login_last_attempt'])) $_SESSION['login_last_attempt'] = 0;

$isLockedOut      = false;
$remainingSeconds = 0;

if ($_SESSION['login_attempts'] >= $maxAttempts) {
    $elapsed = time() - $_SESSION['login_last_attempt'];
    if ($elapsed < $lockoutTime) {
        $isLockedOut      = true;
        $remainingSeconds = $lockoutTime - $elapsed;
    } else {
        $_SESSION['login_attempts'] = 0; // reset setelah lockout habis
    }
}

// Jika sudah login, langsung ke dashboard
if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

// Menghubungkan ke database
require_once '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$isLockedOut) {
    // Validasi CSRF
    $csrfOk = isset($_POST['csrf_token_login'])
              && hash_equals($_SESSION['csrf_token_login'], $_POST['csrf_token_login']);
    if (!$csrfOk) {
        $error = 'Permintaan tidak valid. Silakan muat ulang halaman.';
        goto render_page;
    }
    $_SESSION['csrf_token_login'] = bin2hex(random_bytes(32)); // rotate

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Prepared statement mencegah SQL Injection
    $stmt = $conn->prepare("SELECT id, username, password FROM admin WHERE username = ?");
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Verifikasi password dengan bcrypt (aman)
            if (password_verify($password, $row['password'])) {
                // Login berhasil: reset counter, regenerate sesi (cegah session fixation)
                $_SESSION['login_attempts'] = 0;
                session_regenerate_id(true);
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user']      = $row['username'];
                $_SESSION['admin_id']        = $row['id'];
                $_SESSION['login_time']      = time();
                header('Location: index.php');
                exit;
            } else {
                $_SESSION['login_attempts']++;
                $_SESSION['login_last_attempt'] = time();
                $error = 'Username atau password salah!';
                if ($_SESSION['login_attempts'] >= $maxAttempts) {
                    $isLockedOut      = true;
                    $remainingSeconds = $lockoutTime;
                }
            }
        } else {
            $_SESSION['login_attempts']++;
            $_SESSION['login_last_attempt'] = time();
            $error = 'Username atau password salah!';
        }
        $stmt->close();
    } else {
        $error = "Terjadi kesalahan pada koneksi database.";
    }
}

render_page:
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — PAUDqu ASY SYUHADA</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --login-primary: #4f46e5;
            --login-secondary: #0ea5e9;
            --login-bg: #f8fafc;
            --login-text: #1e293b;
        }

        body {
            min-height: 100vh;
            background: radial-gradient(circle at top right, #eef2ff 0%, #f8fafc 50%, #f0fdf4 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            font-weight: 700;
            font-size: 0.875rem;
            margin-bottom: 24px;
            padding: 8px 16px;
            background: white;
            border-radius: 99px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            transition: all 0.3s;
        }

        .back-link:hover {
            color: var(--login-primary);
            transform: translateX(-4px);
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        }

        .login-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 2rem;
            padding: 3rem 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        }

        .login-icon {
            width: 64px;
            height: 64px;
            border-radius: 1.25rem;
            background: linear-gradient(135deg, #4f46e5, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4);
        }

        .school-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #eef2ff;
            color: #4f46e5;
            padding: 0.5rem 1rem;
            border-radius: 99px;
            font-size: 0.75rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .login-title {
            font-family: 'Outfit', sans-serif;
            font-size: 2rem;
            font-weight: 900;
            color: #1e293b;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .login-sub {
            color: #64748b;
            font-size: 0.95rem;
            margin-bottom: 2.5rem;
        }

        .error-box {
            background: #fef2f2;
            border: 1px solid #fee2e2;
            color: #ef4444;
            padding: 1rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            font-weight: 700;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
        }

        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 0.5rem;
        }

        .form-input-wrap {
            position: relative;
        }

        .form-input-icon {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .form-input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3rem;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            font-family: inherit;
            font-size: 1rem;
            color: #1e293b;
            transition: all 0.3s;
            outline: none;
        }

        .form-input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .toggle-pass {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 0.25rem;
        }

        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #4f46e5, #8b5cf6);
            color: white;
            border: none;
            border-radius: 99px;
            font-weight: 800;
            font-size: 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4);
            transition: all 0.3s;
            margin-top: 2rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(79, 70, 229, 0.5);
        }

        .btn-login:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .secured-note {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.75rem;
            color: #94a3b8;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .secured-note i { color: #10b981; }

        @media (max-width: 480px) {
            .login-card { padding: 2.5rem 1.5rem; }
            .login-title { font-size: 1.75rem; }
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <a href="../index.php" class="back-link">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
    </a>

    <div class="login-card">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <img src="../Img/logo%20Paudqu%20asy%20syuhada%20_page-0001.jpg" alt="Logo" style="height: 100px; width: auto; border-radius: 15px; box-shadow: 0 10px 20px rgba(0,0,0,0.1);">
        </div>

        <h1 class="login-title">Admin Panel</h1>
        <p class="login-sub">Masukkan kredensial Anda untuk melanjutkan</p>

        <?php if ($timeout): ?>
        <div class="error-box" style="background:#fffbeb; border-color:#fef3c7; color:#d97706;">
            <i class="fa-solid fa-clock"></i>
            Sesi Anda telah berakhir. Silakan login kembali.
        </div>
        <?php endif; ?>

        <?php if ($isLockedOut): ?>
        <div class="error-box" style="background:#fff7ed; border-color:#ffedd5; color:#ea580c;">
            <i class="fa-solid fa-lock"></i>
            Terlalu banyak percobaan. Tunggu <?= ceil($remainingSeconds / 60) ?> menit.
        </div>
        <?php elseif ($error): ?>
        <div class="error-box">
            <i class="fa-solid fa-circle-xmark"></i>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <input type="hidden" name="csrf_token_login" value="<?= htmlspecialchars($_SESSION['csrf_token_login']) ?>">
            
            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <div class="form-input-wrap">
                    <i class="fa-solid fa-user form-input-icon"></i>
                    <input type="text" id="username" name="username" class="form-input"
                           placeholder="admin" autocomplete="username" required
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="form-input-wrap">
                    <i class="fa-solid fa-lock form-input-icon"></i>
                    <input type="password" id="password" name="password" class="form-input"
                           placeholder="••••••••" autocomplete="current-password" required>
                    <button type="button" class="toggle-pass" onclick="togglePass()">
                        <i class="fa-solid fa-eye" id="passIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-login" <?= $isLockedOut ? 'disabled' : '' ?>>
                <i class="fa-solid fa-right-to-bracket"></i> Masuk ke Dashboard
            </button>
        </form>

        <p class="secured-note">
            <i class="fa-solid fa-lock"></i> Sistem Keamanan Terenkripsi
        </p>
    </div>
</div>

<script>
function togglePass() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('passIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fa-solid fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fa-solid fa-eye';
    }
}
</script>
</body>
</html>
