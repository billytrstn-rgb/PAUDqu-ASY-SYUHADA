<?php
require_once 'auth.php';
require_once '../koneksi.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $id      = intval($_POST['id'] ?? 0);
    $judul   = trim($_POST['judul'] ?? '');
    $isi     = trim($_POST['isi'] ?? '');
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    $status  = $_POST['status'] ?? 'Draft';
    
    $foto = '';
    if (!empty($_FILES['foto']['name'])) {
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp'])) {
            $newName = uniqid('berita_') . '.' . $ext;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], '../Img/' . $newName)) {
                $foto = $newName;
            }
        }
    }

    if ($id > 0) {
        if ($foto) {
            $stmt = $conn->prepare("UPDATE berita SET judul=?, isi=?, tanggal=?, status=?, foto=? WHERE id=?");
            $stmt->bind_param("sssssi", $judul, $isi, $tanggal, $status, $foto, $id);
        } else {
            $stmt = $conn->prepare("UPDATE berita SET judul=?, isi=?, tanggal=?, status=? WHERE id=?");
            $stmt->bind_param("ssssi", $judul, $isi, $tanggal, $status, $id);
        }
        $stmt->execute();
        $message = 'Berita berhasil diperbarui!';
    } else {
        $stmt = $conn->prepare("INSERT INTO berita (judul, isi, tanggal, status, foto) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $judul, $isi, $tanggal, $status, $foto);
        $stmt->execute();
        $message = 'Berita baru berhasil ditambahkan!';
    }
    $messageType = 'success';
}

if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM berita WHERE id = $id");
    header('Location: berita.php?msg=deleted');
    exit;
}
if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $message = 'Berita berhasil dihapus.';
}

$editData = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM berita WHERE id = $id");
    if ($res) $editData = $res->fetch_assoc();
}

$list = mysqli_query($conn, "SELECT * FROM berita ORDER BY tanggal DESC");
?>
<?php
$page_title = 'Manajemen Berita';
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="main-content" id="mainContent">
    <?php include 'includes/topbar.php'; ?>
    <main class="dashboard-body">
            <?php if($message): ?><div class="alert"><?= $message ?></div><?php endif; ?>

            <div class="form-card">
                <h3><?= $editData ? 'Edit Berita' : 'Tulis Berita Baru' ?></h3>
                <form method="POST" action="berita.php" enctype="multipart/form-data" style="margin-top:15px;">
                    <?= csrf_field() ?>
                    <?php if($editData): ?><input type="hidden" name="id" value="<?= $editData['id'] ?>"><?php endif; ?>
                    
                    <div class="form-group"><label class="form-label">Judul Berita</label>
                        <input type="text" name="judul" class="form-input" required value="<?= htmlspecialchars($editData['judul']??'') ?>">
                    </div>
                    
                    <div class="form-group"><label class="form-label">Isi Berita</label>
                        <textarea name="isi" rows="6" required><?= htmlspecialchars($editData['isi']??'') ?></textarea>
                    </div>
                    
                    <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:15px;">
                        <div class="form-group"><label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-input" required value="<?= $editData['tanggal']??date('Y-m-d') ?>">
                        </div>
                        <div class="form-group"><label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="Publikasi" <?= ($editData['status']??'')=='Publikasi'?'selected':'' ?>>Publikasi</option>
                                <option value="Draft" <?= ($editData['status']??'')=='Draft'?'selected':'' ?>>Draft</option>
                            </select>
                        </div>
                        <div class="form-group"><label class="form-label">Foto Utama (Opsional)</label>
                            <input type="file" name="foto" class="form-input" accept=".jpg,.jpeg,.png">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-submit"><?= $editData ? 'Simpan Perubahan' : 'Terbitkan Berita' ?></button>
                    <?php if($editData): ?><a href="berita.php" style="margin-left:10px;">Batal</a><?php endif; ?>
                </form>
            </div>

            <div class="card">
                <div class="section-header">
                    <h2>Daftar Berita</h2>
                </div>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Judul Berita</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($list)): ?>
                            <tr>
                                <td>
                                    <div style="font-weight:700; color:var(--clr-text-main)"><?= date('d M Y', strtotime($row['tanggal'])) ?></div>
                                </td>
                                <td>
                                    <div class="td-name"><?= htmlspecialchars($row['judul']) ?></div>
                                    <small style="color:var(--clr-text-muted)"><?= substr(strip_tags($row['isi']), 0, 50) ?>...</small>
                                </td>
                                <td>
                                    <span class="badge <?= $row['status'] == 'Publikasi' ? 'badge-green' : 'badge-orange' ?>">
                                        <?= $row['status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="berita.php?edit=<?= $row['id'] ?>" class="btn-edit"><i class="fa-solid fa-pen"></i></a>
                                    <a href="berita.php?hapus=<?= $row['id'] ?>" class="btn-hapus" onclick="return confirm('Hapus berita ini?')"><i class="fa-solid fa-trash"></i></a>
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
