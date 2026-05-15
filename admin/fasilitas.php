<?php
require_once 'auth.php';
require_once '../koneksi.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $id        = intval($_POST['id'] ?? 0);
    $nama      = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $ikon      = trim($_POST['ikon'] ?? 'fa-building');
    $warna     = trim($_POST['warna'] ?? 'blue');
    $urutan    = intval($_POST['urutan'] ?? 0);
    
    // Foto upload
    $foto = '';
    if (!empty($_FILES['foto']['name'])) {
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp'])) {
            $newName = uniqid('fas_') . '.' . $ext;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], '../Img/' . $newName)) {
                $foto = $newName;
            }
        }
    }

    if ($id > 0) {
        if ($foto) {
            $stmt = $conn->prepare("UPDATE fasilitas SET nama=?, deskripsi=?, ikon=?, warna=?, urutan=?, foto=? WHERE id=?");
            $stmt->bind_param("ssssisi", $nama, $deskripsi, $ikon, $warna, $urutan, $foto, $id);
        } else {
            $stmt = $conn->prepare("UPDATE fasilitas SET nama=?, deskripsi=?, ikon=?, warna=?, urutan=? WHERE id=?");
            $stmt->bind_param("ssssii", $nama, $deskripsi, $ikon, $warna, $urutan, $id);
        }
        $stmt->execute();
        $message = 'Fasilitas berhasil diperbarui!';
    } else {
        $stmt = $conn->prepare("INSERT INTO fasilitas (nama, deskripsi, ikon, warna, urutan, foto) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssis", $nama, $deskripsi, $ikon, $warna, $urutan, $foto);
        $stmt->execute();
        $message = 'Fasilitas baru berhasil ditambahkan!';
    }
}

if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM fasilitas WHERE id = $id");
    header('Location: fasilitas.php?msg=deleted');
    exit;
}
if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $message = 'Fasilitas berhasil dihapus.';
}

$editData = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM fasilitas WHERE id = $id");
    if ($res) $editData = $res->fetch_assoc();
}

$list = mysqli_query($conn, "SELECT * FROM fasilitas ORDER BY urutan ASC, id ASC");
?>
<?php
$page_title = 'Manajemen Fasilitas';
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="main-content" id="mainContent">
    <?php include 'includes/topbar.php'; ?>
    <main class="dashboard-body">
            <?php if($message): ?><div class="alert alert-success"><?= $message ?></div><?php endif; ?>

            <div class="form-card">
                <h3><?= $editData ? 'Edit Fasilitas' : 'Tambah Fasilitas Baru' ?></h3>
                <form method="POST" action="fasilitas.php" enctype="multipart/form-data" style="margin-top:15px;">
                    <?= csrf_field() ?>
                    <?php if($editData): ?><input type="hidden" name="id" value="<?= $editData['id'] ?>"><?php endif; ?>
                    
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                        <div class="form-group"><label class="form-label">Nama Fasilitas</label>
                            <input type="text" name="nama" class="form-input" required value="<?= htmlspecialchars($editData['nama']??'') ?>">
                        </div>
                        <div class="form-group"><label class="form-label">Urutan Tampil</label>
                            <input type="number" name="urutan" class="form-input" value="<?= $editData['urutan']??0 ?>">
                        </div>
                    </div>
                    
                    <div class="form-group"><label class="form-label">Deskripsi Singkat</label>
                        <textarea name="deskripsi" rows="2" required><?= htmlspecialchars($editData['deskripsi']??'') ?></textarea>
                    </div>
                    
                    <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:15px;">
                        <div class="form-group"><label class="form-label">Ikon (FontAwesome)</label>
                            <input type="text" name="ikon" class="form-input" value="<?= htmlspecialchars($editData['ikon']??'fa-building') ?>">
                        </div>
                        <div class="form-group"><label class="form-label">Warna</label>
                            <select name="warna" class="form-select">
                                <option value="blue" <?= ($editData['warna']??'')=='blue'?'selected':'' ?>>Blue</option>
                                <option value="green" <?= ($editData['warna']??'')=='green'?'selected':'' ?>>Green</option>
                                <option value="orange" <?= ($editData['warna']??'')=='orange'?'selected':'' ?>>Orange</option>
                                <option value="pink" <?= ($editData['warna']??'')=='pink'?'selected':'' ?>>Pink</option>
                            </select>
                        </div>
                        <div class="form-group"><label class="form-label">Foto Fasilitas</label>
                            <input type="file" name="foto" class="form-input" accept=".jpg,.jpeg,.png">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-submit"><?= $editData ? 'Simpan Perubahan' : 'Tambah Fasilitas' ?></button>
                    <?php if($editData): ?><a href="fasilitas.php" style="margin-left:10px;">Batal</a><?php endif; ?>
                </form>
            </div>

            <div class="card">
                <div class="section-header">
                    <h2>Daftar Fasilitas Sekolah</h2>
                </div>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Foto / Ikon</th>
                                <th>Nama Fasilitas / Deskripsi</th>
                                <th>Urutan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($list)): ?>
                            <tr>
                                <td>
                                    <?php if($row['foto']): ?>
                                        <img src="../Img/<?= htmlspecialchars($row['foto']) ?>" class="img-preview" alt="Fasilitas">
                                    <?php else: ?>
                                        <div class="avatar-circle" style="background:var(--clr-primary-soft); color:var(--clr-primary);">
                                            <i class="fa-solid <?= htmlspecialchars($row['ikon']) ?>"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="td-name"><?= htmlspecialchars($row['nama']) ?></div>
                                    <small style="color:var(--clr-text-muted)"><?= htmlspecialchars($row['deskripsi']) ?></small>
                                </td>
                                <td><strong><?= $row['urutan'] ?></strong></td>
                                <td>
                                    <a href="fasilitas.php?edit=<?= $row['id'] ?>" class="btn-edit"><i class="fa-solid fa-pen"></i></a>
                                    <a href="fasilitas.php?hapus=<?= $row['id'] ?>" class="btn-hapus" onclick="return confirm('Hapus fasilitas ini?')"><i class="fa-solid fa-trash"></i></a>
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
