<?php
require_once 'auth.php';
require_once '../koneksi.php';

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    
    // Handle File Upload for foto_hero
    if (!empty($_FILES['foto_hero']['name'])) {
        $ext = strtolower(pathinfo($_FILES['foto_hero']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];
        if (in_array($ext, $allowed)) {
            $newName = 'hero_' . time() . '.' . $ext;
            $dest = '../Img/' . $newName;
            if (move_uploaded_file($_FILES['foto_hero']['tmp_name'], $dest)) {
                $stmt = $conn->prepare("UPDATE pengaturan SET nilai=? WHERE kunci='foto_hero'");
                $stmt->bind_param("s", $newName);
                $stmt->execute();
                $stmt->close();
            }
        } else {
            $message = 'Format foto hero tidak didukung (gunakan JPG, PNG, WEBP).';
            $messageType = 'error';
        }
    }

    // Update text fields
    $stmt = $conn->prepare("UPDATE pengaturan SET nilai=? WHERE kunci=?");
    foreach ($_POST as $kunci => $nilai) {
        if ($kunci === 'csrf_token' || $kunci === 'foto_hero') continue;
        $stmt->bind_param("ss", $nilai, $kunci);
        $stmt->execute();
    }
    $stmt->close();

    if (!$message) {
        $message = 'Pengaturan berhasil diperbarui!';
        $messageType = 'success';
    }
}

// Ambil data pengaturan
$set = [];
$q_set = mysqli_query($conn, "SELECT kunci, nilai FROM pengaturan");
if ($q_set) {
    while($r = mysqli_fetch_assoc($q_set)){
        $set[$r['kunci']] = $r['nilai'];
    }
}
function s($kunci) { global $set; return htmlspecialchars($set[$kunci] ?? ''); }
?>
<?php
$page_title = 'Pengaturan Website';
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="main-content" id="mainContent">
    <?php include 'includes/topbar.php'; ?>
    <main class="dashboard-body">

            <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?>">
                <i class="fa-solid <?= $messageType === 'success' ? 'fa-circle-check' : 'fa-circle-xmark' ?>"></i>
                <?= htmlspecialchars($message) ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="pengaturan.php" enctype="multipart/form-data" class="settings-form">
                <?= csrf_field() ?>
                
                <!-- IDENTITAS SEKOLAH -->
                <div class="card">
                    <div class="section-header">
                        <h2><i class="fa-solid fa-school"></i> Identitas Sekolah</h2>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Nama Sekolah</label>
                            <input type="text" name="nama_sekolah" class="form-input" value="<?= s('nama_sekolah') ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Slogan / Tagline</label>
                            <input type="text" name="tagline" class="form-input" value="<?= s('tagline') ?>" required>
                        </div>
                        <div class="form-group full">
                            <label class="form-label">Tentang PAUDqu</label>
                            <textarea name="tentang" class="form-textarea" required><?= s('tentang') ?></textarea>
                        </div>
                        <div class="form-group full">
                            <label class="form-label">Visi Sekolah</label>
                            <textarea name="visi" class="form-textarea" required><?= s('visi') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Foto Hero (Banner Depan)</label>
                            <div style="display:flex; align-items:center; gap:15px; margin-bottom:10px;">
                                <img src="../Img/<?= s('foto_hero') ?>" alt="Hero" style="height:60px; border-radius:8px; border:1px solid var(--clr-border);">
                                <input type="file" name="foto_hero" class="form-input" accept=".jpg,.jpeg,.png,.webp">
                            </div>
                            <small class="text-muted">Abaikan jika tidak ingin mengganti foto banner.</small>
                        </div>
                    </div>
                </div>

                <!-- STATISTIK & KONTAK -->
                <div class="grid-2">
                    <div class="card">
                        <div class="section-header">
                            <h2><i class="fa-solid fa-chart-line"></i> Statistik Dashboard</h2>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Total Siswa Aktif</label>
                                <input type="number" name="jumlah_siswa" class="form-input" value="<?= s('jumlah_siswa') ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Total Alumni</label>
                                <input type="number" name="jumlah_alumni" class="form-input" value="<?= s('jumlah_alumni') ?>">
                            </div>
                        </div>
                        <small class="text-muted">Angka ini akan ditampilkan di statistik halaman depan website.</small>
                    </div>

                    <div class="card">
                        <div class="section-header">
                            <h2><i class="fa-solid fa-phone"></i> Kontak WhatsApp</h2>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nomor WhatsApp (Gunakan kode negara, misal: 62812...)</label>
                            <input type="text" name="whatsapp" class="form-input" value="<?= s('whatsapp') ?>">
                        </div>
                    </div>
                </div>

                <!-- LOKASI & MAPS -->
                <div class="card">
                    <div class="section-header">
                        <h2><i class="fa-solid fa-location-dot"></i> Lokasi & Google Maps</h2>
                    </div>
                    <div class="form-grid">
                        <div class="form-group full">
                            <label class="form-label">Alamat Lengkap Sekolah</label>
                            <textarea name="alamat" class="form-textarea"><?= s('alamat') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Link Google Maps (URL Pendek)</label>
                            <input type="text" name="maps_link" class="form-input" value="<?= s('maps_link') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Iframe Embed Maps (src URL)</label>
                            <input type="text" name="maps_embed" class="form-input" value="<?= s('maps_embed') ?>">
                        </div>
                    </div>
                </div>

                
                <div style="text-align:right; margin-bottom: 50px;">
                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Semua Pengaturan
                    </button>
                </div>
            </form>

        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
