<?php
require_once 'auth.php';
require_once '../koneksi.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $id        = intval($_POST['id'] ?? 0);
    $judul     = trim($_POST['judul'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $ikon      = trim($_POST['ikon'] ?? 'fa-check');
    $warna     = trim($_POST['warna'] ?? 'blue');
    $urutan    = intval($_POST['urutan'] ?? 0);
    
    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE misi SET judul=?, deskripsi=?, ikon=?, warna=?, urutan=? WHERE id=?");
        $stmt->bind_param("ssssii", $judul, $deskripsi, $ikon, $warna, $urutan, $id);
        $stmt->execute();
        $message = 'Misi berhasil diperbarui!';
    } else {
        $stmt = $conn->prepare("INSERT INTO misi (judul, deskripsi, ikon, warna, urutan) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $judul, $deskripsi, $ikon, $warna, $urutan);
        $stmt->execute();
        $message = 'Misi baru berhasil ditambahkan!';
    }
}

if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM misi WHERE id = $id");
    header('Location: misi.php?msg=deleted');
    exit;
}
if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $message = 'Misi berhasil dihapus.';
}

$editData = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM misi WHERE id = $id");
    if ($res) $editData = $res->fetch_assoc();
}

$list = mysqli_query($conn, "SELECT * FROM misi ORDER BY urutan ASC, id ASC");
?>
<?php
$page_title = 'Kelola Misi Sekolah';
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="main-content" id="mainContent">
    <?php include 'includes/topbar.php'; ?>
    <main class="dashboard-body">
            <?php if($message): ?><div class="alert alert-success"><?= $message ?></div><?php endif; ?>

            <div class="form-card">
                <div class="section-header">
                    <h2><?= $editData ? 'Edit Misi' : 'Tambah Misi Baru' ?></h2>
                </div>
                <form method="POST" action="misi.php" style="margin-top:15px;">
                    <?= csrf_field() ?>
                    <?php if($editData): ?><input type="hidden" name="id" value="<?= $editData['id'] ?>"><?php endif; ?>
                    
                    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:15px;">
                        <div class="form-group"><label class="form-label">Judul Misi</label>
                            <input type="text" name="judul" class="form-input" required value="<?= htmlspecialchars($editData['judul']??'') ?>">
                        </div>
                        <div class="form-group"><label class="form-label">Urutan</label>
                            <input type="number" name="urutan" class="form-input" value="<?= $editData['urutan']??0 ?>">
                        </div>
                    </div>
                    
                    <div class="form-group"><label class="form-label">Deskripsi Misi</label>
                        <textarea name="deskripsi" rows="2" class="form-textarea" required><?= htmlspecialchars($editData['deskripsi']??'') ?></textarea>
                    </div>
                    
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                        <div class="form-group"><label class="form-label">Ikon (FontAwesome)</label>
                            <input type="text" name="ikon" class="form-input" value="<?= htmlspecialchars($editData['ikon']??'fa-check') ?>">
                        </div>
                        <div class="form-group"><label class="form-label">Warna Ikon</label>
                            <select name="warna" class="form-select">
                                <option value="blue" <?= ($editData['warna']??'')=='blue'?'selected':'' ?>>Blue</option>
                                <option value="green" <?= ($editData['warna']??'')=='green'?'selected':'' ?>>Green</option>
                                <option value="orange" <?= ($editData['warna']??'')=='orange'?'selected':'' ?>>Orange</option>
                                <option value="pink" <?= ($editData['warna']??'')=='pink'?'selected':'' ?>>Pink</option>
                                <option value="purple" <?= ($editData['warna']??'')=='purple'?'selected':'' ?>>Purple</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="margin-top:20px;">
                        <button type="submit" class="btn-submit"><?= $editData ? 'Simpan Perubahan' : 'Tambah Misi' ?></button>
                        <?php if($editData): ?><a href="misi.php" class="btn-cancel" style="margin-left:10px;">Batal</a><?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="card">
                <div class="section-header">
                    <h2>Daftar Misi Sekolah</h2>
                </div>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Ikon</th>
                                <th>Judul / Deskripsi</th>
                                <th>Urutan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($list)): ?>
                            <tr>
                                <td>
                                    <div class="avatar-circle" style="background:var(--clr-primary-soft); color:var(--clr-primary);">
                                        <i class="fa-solid <?= htmlspecialchars($row['ikon']) ?>"></i>
                                    </div>
                                </td>
                                <td>
                                    <div class="td-name"><?= htmlspecialchars($row['judul']) ?></div>
                                    <small style="color:var(--clr-text-muted)"><?= htmlspecialchars($row['deskripsi']) ?></small>
                                </td>
                                <td><strong><?= $row['urutan'] ?></strong></td>
                                <td>
                                    <a href="misi.php?edit=<?= $row['id'] ?>" class="btn-edit"><i class="fa-solid fa-pen"></i></a>
                                    <a href="misi.php?hapus=<?= $row['id'] ?>" class="btn-hapus" onclick="return confirm('Hapus misi ini?')"><i class="fa-solid fa-trash"></i></a>
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
