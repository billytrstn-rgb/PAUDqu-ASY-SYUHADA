<?php
require_once 'auth.php';
require_once '../koneksi.php';

$message = '';
$messageType = '';

// Handle Tambah / Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $id            = intval($_POST['id'] ?? 0);
    $nama          = trim($_POST['nama'] ?? '');
    $nis           = trim($_POST['nis'] ?? '');
    $tanggal_lahir = !empty($_POST['tanggal_lahir']) ? $_POST['tanggal_lahir'] : null;
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? 'L';
    $nama_ortu     = trim($_POST['nama_ortu'] ?? '');
    $telepon       = trim($_POST['telepon'] ?? '');
    $status        = $_POST['status'] ?? 'Aktif';
    $tahun_masuk   = intval($_POST['tahun_masuk'] ?? date('Y'));
    
    // Foto upload
    $foto = '';
    if (!empty($_FILES['foto']['name'])) {
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp'])) {
            $newName = uniqid('siswa_') . '.' . $ext;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], '../Img/' . $newName)) {
                $foto = $newName;
            }
        }
    }

    try {
        if ($id > 0) {
            // Edit
            if ($foto) {
                $stmt = $conn->prepare("UPDATE siswa SET nama=?, nis=?, tanggal_lahir=?, jenis_kelamin=?, nama_ortu=?, telepon=?, status=?, tahun_masuk=?, foto=? WHERE id=?");
                $stmt->bind_param("sssssssiis", $nama, $nis, $tanggal_lahir, $jenis_kelamin, $nama_ortu, $telepon, $status, $tahun_masuk, $foto, $id);
            } else {
                $stmt = $conn->prepare("UPDATE siswa SET nama=?, nis=?, tanggal_lahir=?, jenis_kelamin=?, nama_ortu=?, telepon=?, status=?, tahun_masuk=? WHERE id=?");
                $stmt->bind_param("sssssssii", $nama, $nis, $tanggal_lahir, $jenis_kelamin, $nama_ortu, $telepon, $status, $tahun_masuk, $id);
            }
            if ($stmt->execute()) {
                $message = 'Data siswa berhasil diperbarui!';
                $messageType = 'success';
            }
        } else {
            // Tambah
            $stmt = $conn->prepare("INSERT INTO siswa (nama, nis, tanggal_lahir, jenis_kelamin, nama_ortu, telepon, status, tahun_masuk, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssis", $nama, $nis, $tanggal_lahir, $jenis_kelamin, $nama_ortu, $telepon, $status, $tahun_masuk, $foto);
            if ($stmt->execute()) {
                $message = 'Siswa baru berhasil ditambahkan!';
                $messageType = 'success';
            }
        }
        if (isset($stmt)) $stmt->close();
    } catch (Exception $e) {
        $message = 'Gagal menyimpan data: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// Handle Hapus
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    if ($conn->query("DELETE FROM siswa WHERE id = $id")) {
        header('Location: siswa.php?msg=deleted');
        exit;
    }
}
if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $message = 'Data siswa berhasil dihapus.';
    $messageType = 'success';
}

// Ambil data form edit
$editData = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM siswa WHERE id = $id");
    if ($res) $editData = $res->fetch_assoc();
}

$siswaList = mysqli_query($conn, "SELECT * FROM siswa ORDER BY id DESC");
?>
<?php
$page_title = 'Manajemen Data Siswa';
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="main-content" id="mainContent">
    <?php include 'includes/topbar.php'; ?>
    <main class="dashboard-body">
            <?php if($message): ?><div class="alert alert-success"><?= $message ?></div><?php endif; ?>

            <div class="form-card">
                <h3><?= $editData ? 'Edit Siswa' : 'Tambah Siswa Baru' ?></h3>
                <form method="POST" action="siswa.php" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <?php if($editData): ?><input type="hidden" name="id" value="<?= $editData['id'] ?>"><?php endif; ?>
                    <div class="form-grid">
                        <div class="form-group"><label class="form-label">Nama Lengkap</label><input type="text" name="nama" class="form-input" required value="<?= $editData['nama']??'' ?>"></div>
                        <div class="form-group"><label class="form-label">NIS (Opsional)</label><input type="text" name="nis" class="form-input" value="<?= $editData['nis']??'' ?>"></div>
                        <div class="form-group"><label class="form-label">Tgl Lahir</label><input type="date" name="tanggal_lahir" class="form-input" value="<?= $editData['tanggal_lahir']??'' ?>"></div>
                        <div class="form-group">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="L" <?= ($editData['jenis_kelamin']??'')=='L'?'selected':'' ?>>Laki-laki</option>
                                <option value="P" <?= ($editData['jenis_kelamin']??'')=='P'?'selected':'' ?>>Perempuan</option>
                            </select>
                        </div>
                        <div class="form-group"><label class="form-label">Nama Orang Tua</label><input type="text" name="nama_ortu" class="form-input" value="<?= $editData['nama_ortu']??'' ?>"></div>
                        <div class="form-group"><label class="form-label">Telepon/WA</label><input type="text" name="telepon" class="form-input" value="<?= $editData['telepon']??'' ?>"></div>
                        <div class="form-group"><label class="form-label">Tahun Masuk</label><input type="number" name="tahun_masuk" class="form-input" value="<?= $editData['tahun_masuk']??date('Y') ?>"></div>
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="Aktif" <?= ($editData['status']??'')=='Aktif'?'selected':'' ?>>Aktif</option>
                                <option value="Alumni" <?= ($editData['status']??'')=='Alumni'?'selected':'' ?>>Alumni</option>
                            </select>
                        </div>
                        <div class="form-group"><label class="form-label">Foto (Opsional)</label><input type="file" name="foto" class="form-input" accept=".jpg,.jpeg,.png"></div>
                    </div>
                    <button type="submit" class="btn-submit"><?= $editData ? 'Simpan Perubahan' : 'Tambah Siswa' ?></button>
                    <?php if($editData): ?><a href="siswa.php" style="margin-left:10px;">Batal</a><?php endif; ?>
                </form>
            </div>

            <div class="card">
                <div class="section-header">
                    <h2>Daftar Siswa</h2>
                </div>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama / NIS</th>
                                <th>L/P</th>
                                <th>Orang Tua / HP</th>
                                <th>Tahun Masuk</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while($row = mysqli_fetch_assoc($siswaList)): 
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <div class="td-name"><?= htmlspecialchars($row['nama']) ?></div>
                                    <small style="color:var(--clr-text-muted)"><?= htmlspecialchars($row['nis'] ?: '-') ?></small>
                                </td>
                                <td>
                                    <span class="badge <?= $row['jenis_kelamin'] == 'L' ? 'badge-blue' : 'badge-pink' ?>">
                                        <?= $row['jenis_kelamin'] ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="font-size:0.9rem; font-weight:700;"><?= htmlspecialchars($row['nama_ortu']) ?></div>
                                    <small style="color:var(--clr-text-muted)"><?= htmlspecialchars($row['telepon']) ?></small>
                                </td>
                                <td><strong><?= $row['tahun_masuk'] ?></strong></td>
                                <td>
                                    <span class="badge <?= $row['status'] == 'Aktif' ? 'badge-green' : 'badge-orange' ?>">
                                        <?= $row['status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="siswa.php?edit=<?= $row['id'] ?>" class="btn-edit"><i class="fa-solid fa-pen"></i></a>
                                    <a href="siswa.php?hapus=<?= $row['id'] ?>" class="btn-hapus" onclick="return confirm('Hapus siswa ini?')"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
