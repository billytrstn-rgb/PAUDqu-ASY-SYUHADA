<?php
require_once 'auth.php';
require_once '../koneksi.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $id         = intval($_POST['id'] ?? 0);
    $judul      = trim($_POST['judul'] ?? '');
    $urutan     = intval($_POST['urutan'] ?? 0);
    
    $foto = '';
    if (!empty($_FILES['foto']['name'])) {
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp'])) {
            $newName = uniqid('galeri_') . '.' . $ext;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], '../Img/' . $newName)) {
                $foto = $newName;
            }
        }
    }

    if ($id > 0) {
        if ($foto) {
            $stmt = $conn->prepare("UPDATE galeri SET judul=?, urutan=?, foto=? WHERE id=?");
            $stmt->bind_param("sisi", $judul, $urutan, $foto, $id);
        } else {
            $stmt = $conn->prepare("UPDATE galeri SET judul=?, urutan=? WHERE id=?");
            $stmt->bind_param("sii", $judul, $urutan, $id);
        }
        $stmt->execute();
        $message = 'Galeri berhasil diperbarui!';
    } else {
        $stmt = $conn->prepare("INSERT INTO galeri (judul, urutan, foto) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $judul, $urutan, $foto);
        $stmt->execute();
        $message = 'Foto baru berhasil ditambahkan ke galeri!';
    }
}

if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM galeri WHERE id = $id");
    header('Location: galeri.php?msg=deleted');
    exit;
}
if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $message = 'Foto galeri berhasil dihapus.';
}

$editData = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM galeri WHERE id = $id");
    if ($res) $editData = $res->fetch_assoc();
}

$list = mysqli_query($conn, "SELECT * FROM galeri ORDER BY urutan ASC, id DESC");
?>
<?php
$page_title = 'Manajemen Galeri';
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="main-content" id="mainContent">
    <?php include 'includes/topbar.php'; ?>
    <main class="dashboard-body">
            <?php if($message): ?><div class="alert"><?= $message ?></div><?php endif; ?>

            <div class="form-card">
                <h3><?= $editData ? 'Edit Foto Galeri' : 'Tambah Foto Galeri' ?></h3>
                <form method="POST" action="galeri.php" enctype="multipart/form-data" style="margin-top:15px;">
                    <?= csrf_field() ?>
                    <?php if($editData): ?><input type="hidden" name="id" value="<?= $editData['id'] ?>"><?php endif; ?>
                    
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                        <div class="form-group"><label class="form-label">Judul Foto</label>
                            <input type="text" name="judul" class="form-input" required value="<?= htmlspecialchars($editData['judul']??'') ?>">
                        </div>
                        <div class="form-group"><label class="form-label">Urutan Tampil (Angka)</label>
                            <input type="number" name="urutan" class="form-input" value="<?= $editData['urutan']??0 ?>">
                        </div>
                    </div>
                    
                    
                    <div class="form-group"><label class="form-label">File Foto (JPG/PNG)</label>
                        <?php if($editData && $editData['foto']): ?>
                            <img src="../Img/<?= $editData['foto'] ?>" class="img-preview" alt="Preview">
                        <?php endif; ?>
                        <input type="file" name="foto" class="form-input" accept=".jpg,.jpeg,.png">
                    </div>
                    
                    <button type="submit" class="btn-submit"><?= $editData ? 'Simpan Perubahan' : 'Tambah ke Galeri' ?></button>
                    <?php if($editData): ?><a href="galeri.php" style="margin-left:10px;">Batal</a><?php endif; ?>
                </form>
            </div>

            <div class="card">
                <div class="section-header">
                    <h2>Daftar Foto Galeri</h2>
                </div>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Preview</th>
                                <th>Judul</th>
                                <th>Urutan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($list)): ?>
                            <tr>
                                <td>
                                    <img src="../Img/<?= htmlspecialchars($row['foto']) ?>" class="img-preview" alt="Gallery">
                                </td>
                                <td>
                                    <div class="td-name"><?= htmlspecialchars($row['judul']) ?></div>
                                </td>
                                <td><strong><?= $row['urutan'] ?></strong></td>
                                <td>
                                    <a href="galeri.php?edit=<?= $row['id'] ?>" class="btn-edit"><i class="fa-solid fa-pen"></i></a>
                                    <a href="galeri.php?hapus=<?= $row['id'] ?>" class="btn-hapus" onclick="return confirm('Hapus foto ini dari galeri?')"><i class="fa-solid fa-trash"></i></a>
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
