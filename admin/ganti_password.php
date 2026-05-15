<?php
require_once 'auth.php';
require_once '../koneksi.php';

$success = '';
$error   = '';

// Ambil data admin saat ini
$id   = $_SESSION['admin_id'] ?? 0;
$q_admin = $conn->prepare("SELECT username FROM admin WHERE id = ?");
$q_admin->bind_param("i", $id);
$q_admin->execute();
$admin_data = $q_admin->get_result()->fetch_assoc();
$q_admin->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $newUsername = trim($_POST['username'] ?? '');
    $passLama    = $_POST['pass_lama']   ?? '';
    $passBaru    = $_POST['pass_baru']   ?? '';
    $konfirm     = $_POST['konfirmasi']  ?? '';

    // Validasi Password Lama (Selalu diperlukan untuk perubahan apapun)
    $stmt = $conn->prepare("SELECT password FROM admin WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row || !password_verify($passLama, $row['password'])) {
        $error = 'Password lama salah. Verifikasi gagal.';
    } else {
        // 1. Cek jika ingin ganti Username
        if ($newUsername !== $admin_data['username']) {
            if (strlen($newUsername) < 4) {
                $error = 'Username minimal 4 karakter.';
            } else {
                $stmt2 = $conn->prepare("UPDATE admin SET username = ? WHERE id = ?");
                $stmt2->bind_param("si", $newUsername, $id);
                $stmt2->execute();
                $stmt2->close();
                $_SESSION['admin_user'] = $newUsername; // Update session
                $admin_data['username'] = $newUsername;
                $success = 'Username berhasil diperbarui! ';
            }
        }

        // 2. Cek jika ingin ganti Password
        if (!$error && !empty($passBaru)) {
            if (strlen($passBaru) < 8) {
                $error = 'Password baru minimal 8 karakter.';
            } elseif ($passBaru !== $konfirm) {
                $error = 'Konfirmasi password tidak cocok.';
            } else {
                $hash  = password_hash($passBaru, PASSWORD_DEFAULT);
                $stmt3 = $conn->prepare("UPDATE admin SET password = ? WHERE id = ?");
                $stmt3->bind_param("si", $hash, $id);
                $stmt3->execute();
                $stmt3->close();
                $success .= 'Password berhasil diubah!';
            }
        } elseif (!$error && empty($success)) {
            $error = 'Tidak ada perubahan yang dilakukan.';
        }
    }
}

$page_title = 'Pengaturan Akun';
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="main-content" id="mainContent">
    <?php include 'includes/topbar.php'; ?>
    <main class="dashboard-body">

        <div class="grid-2" style="max-width: 1000px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            
            <!-- INFORMASI AKUN -->
            <div class="card">
                <div class="section-header" style="margin-bottom: 2rem;">
                    <h2><i class="fa-solid fa-user-gear"></i> Identitas Admin</h2>
                </div>

                <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($success) ?>
                </div>
                <?php endif; ?>

                <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-xmark"></i> <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="ganti_password.php">
                    <?= csrf_field() ?>
                    
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label class="form-label">Username Saat Ini</label>
                        <div style="position: relative;">
                            <i class="fa-solid fa-at" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--clr-text-light);"></i>
                            <input type="text" name="username" class="form-input" style="padding-left: 45px;" 
                                   value="<?= htmlspecialchars($admin_data['username']) ?>" required>
                        </div>
                    </div>

                    <hr style="border: 0; border-top: 1px solid var(--clr-border); margin: 2rem 0;">

                    <div class="section-header" style="margin-bottom: 1.5rem;">
                        <h2><i class="fa-solid fa-shield-halved"></i> Keamanan</h2>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label class="form-label">Password Saat Ini (Wajib)</label>
                        <input type="password" name="pass_lama" class="form-input" placeholder="Konfirmasi identitas Anda" required>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label class="form-label">Password Baru (Kosongkan jika tidak ganti)</label>
                        <input type="password" name="pass_baru" class="form-input" placeholder="Minimal 8 karakter">
                    </div>

                    <div class="form-group" style="margin-bottom: 2rem;">
                        <label class="form-label">Ulangi Password Baru</label>
                        <input type="password" name="konfirmasi" class="form-input" placeholder="Konfirmasi password baru">
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; height: 50px; font-size: 1rem;">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan Akun
                    </button>
                </form>
            </div>

            <!-- TIPS KEAMANAN -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div class="card" style="background: linear-gradient(135deg, var(--clr-primary), var(--clr-secondary)); color: white; border: none;">
                    <h3 style="margin-bottom: 1rem;"><i class="fa-solid fa-lightbulb"></i> Tips Keamanan</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 0.8rem; display: flex; gap: 10px; align-items: start;">
                            <i class="fa-solid fa-check-circle" style="margin-top: 4px;"></i>
                            <span>Gunakan kombinasi huruf, angka, dan simbol untuk password.</span>
                        </li>
                        <li style="margin-bottom: 0.8rem; display: flex; gap: 10px; align-items: start;">
                            <i class="fa-solid fa-check-circle" style="margin-top: 4px;"></i>
                            <span>Jangan gunakan username yang terlalu mudah ditebak (seperti 'admin').</span>
                        </li>
                        <li style="display: flex; gap: 10px; align-items: start;">
                            <i class="fa-solid fa-check-circle" style="margin-top: 4px;"></i>
                            <span>Ganti kredensial Anda secara berkala setiap 3-6 bulan.</span>
                        </li>
                    </ul>
                </div>

                <div class="card">
                    <h3 style="margin-bottom: 1rem; color: var(--clr-text-main);"><i class="fa-solid fa-circle-info"></i> Info Akun</h3>
                    <p style="color: var(--clr-text-muted); font-size: 0.95rem; line-height: 1.6;">
                        Username digunakan untuk masuk ke dashboard ini. Jika Anda mengganti username, pastikan Anda mengingatnya untuk login berikutnya. 
                        Password baru akan aktif segera setelah Anda menekan tombol simpan.
                    </p>
                </div>
            </div>

        </div>

    </main>
</div>

<?php include 'includes/footer.php'; ?>
