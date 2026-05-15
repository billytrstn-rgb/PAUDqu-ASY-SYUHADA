<?php
require_once 'auth.php';
require_once '../koneksi.php';

// Buat tabel guru jika belum ada
$createGuruTable = "CREATE TABLE IF NOT EXISTS `guru` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `jabatan` varchar(50) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('Aktif','Tidak Aktif') DEFAULT 'Aktif',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
mysqli_query($conn, $createGuruTable);

// Isi data awal jika tabel kosong
$checkGuru = mysqli_query($conn, "SELECT id FROM guru");
if (mysqli_num_rows($checkGuru) == 0) {
    mysqli_query($conn, "INSERT INTO `guru` (`nama`, `jabatan`, `foto`, `status`) VALUES
        ('Ibu Neneng, S.Pd', 'Kepala Sekolah', 'Neneng.jpeg', 'Aktif'),
        ('Ibu Enung Surtasih', 'Guru 1', 'Enung Surtasih.jpeg', 'Aktif'),
        ('Ibu Sumiati', 'Guru 2', 'Sumiati.jpeg', 'Aktif'),
        ('Pak Usep Saefullah, S.Pd, I', 'Operator', 'Usep Saefullah.jpeg', 'Aktif')
    ");
}

$message = '';
$messageType = '';

// Handle Tambah / Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $id     = intval($_POST['id'] ?? 0);
    $nama   = trim($_POST['nama'] ?? '');
    $jabatan = trim($_POST['jabatan'] ?? '');
    $status  = $_POST['status'] ?? 'Aktif';
    $urutan  = intval($_POST['urutan'] ?? 0);
    $foto    = '';

    // Upload foto jika ada
    if (!empty($_FILES['foto']['name'])) {
        $ext   = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];
        if (in_array($ext, $allowed)) {
            $newName = uniqid('guru_') . '.' . $ext;
            $dest    = '../Img/' . $newName;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $dest)) {
                $foto = $newName;
            }
        } else {
            $message = 'Format foto tidak didukung. Gunakan JPG, PNG, atau WEBP.';
            $messageType = 'error';
        }
    }

    if (!$message) {
        if ($id > 0) {
            // Edit
            if ($foto) {
                $stmt = $conn->prepare("UPDATE guru SET nama=?, jabatan=?, foto=?, status=?, urutan=? WHERE id=?");
                $stmt->bind_param("ssssii", $nama, $jabatan, $foto, $status, $urutan, $id);
            } else {
                $stmt = $conn->prepare("UPDATE guru SET nama=?, jabatan=?, status=?, urutan=? WHERE id=?");
                $stmt->bind_param("sssii", $nama, $jabatan, $status, $urutan, $id);
            }
            $stmt->execute();
            $stmt->close();
            $message = 'Data guru berhasil diperbarui!';
            $messageType = 'success';
        } else {
            // Tambah
            $stmt = $conn->prepare("INSERT INTO guru (nama, jabatan, foto, status, urutan) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $nama, $jabatan, $foto, $status, $urutan);
            $stmt->execute();
            $stmt->close();
            $message = 'Guru baru berhasil ditambahkan!';
            $messageType = 'success';
        }
    }
}

// Handle Hapus
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM guru WHERE id = $id");
    header('Location: guru.php?msg=deleted');
    exit;
}

if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $message = 'Data guru berhasil dihapus.';
    $messageType = 'success';
}

// Ambil data untuk form edit
$editData = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM guru WHERE id = $id");
    if ($res) $editData = $res->fetch_assoc();
}

// Ambil semua data guru
$guruList = mysqli_query($conn, "SELECT * FROM guru ORDER BY id ASC");
?>
<?php
$page_title = 'Data Guru';
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

            <!-- FORM TAMBAH / EDIT -->
            <div class="guru-form">
                <div class="section-header" style="margin-bottom:20px;">
                    <h2><?= $editData ? 'Edit Data Guru' : 'Tambah Guru Baru' ?></h2>
                </div>
                <form method="POST" action="guru.php" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <?php if ($editData): ?>
                    <input type="hidden" name="id" value="<?= $editData['id'] ?>">
                    <?php endif; ?>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-input" placeholder="Cth: Ibu Neneng, S.Pd" required value="<?= htmlspecialchars($editData['nama'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="jabatan" class="form-input" placeholder="Cth: Kepala Sekolah" required value="<?= htmlspecialchars($editData['jabatan'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Foto (JPG/PNG, opsional)</label>
                            <input type="file" name="foto" class="form-input" accept=".jpg,.jpeg,.png,.webp">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="Aktif" <?= (!$editData || $editData['status'] === 'Aktif') ? 'selected' : '' ?>>Aktif</option>
                                <option value="Tidak Aktif" <?= ($editData && $editData['status'] === 'Tidak Aktif') ? 'selected' : '' ?>>Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Urutan Tampil</label>
                            <input type="number" name="urutan" class="form-input" placeholder="Cth: 1" value="<?= $editData['urutan'] ?? 0 ?>">
                        </div>
                    </div>
                    <div style="margin-top:20px;">
                        <button type="submit" class="btn-submit">
                            <i class="fa-solid <?= $editData ? 'fa-floppy-disk' : 'fa-plus' ?>"></i>
                            <?= $editData ? 'Simpan Perubahan' : 'Tambah Guru' ?>
                        </button>
                        <?php if ($editData): ?>
                        <a href="guru.php" class="btn-cancel">Batal</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- TABEL GURU -->
            <div class="card">
                <div class="section-header">
                    <h2>Daftar Tim Pendidik</h2>
                </div>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Urutan</th>
                                <th>Foto</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $res = mysqli_query($conn, "SELECT * FROM guru ORDER BY urutan ASC, id DESC");
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($res)):
                            $inisial = strtoupper(substr($row['nama'], 0, 1));
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><span class="badge badge-info"><?= $row['urutan'] ?></span></td>
                            <td>
                                <?php if ($row['foto']): ?>
                                <img src="../Img/<?= htmlspecialchars($row['foto']) ?>" class="img-preview" alt="Guru">
                                <?php else: ?>
                                <div class="avatar-circle av-blue"><?= htmlspecialchars($inisial) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><div class="td-name"><?= htmlspecialchars($row['nama']) ?></div></td>
                            <td><span class="badge badge-blue"><?= htmlspecialchars($row['jabatan']) ?></span></td>
                            <td>
                                <span class="badge <?= $row['status'] === 'Aktif' ? 'badge-green' : 'badge-pink' ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="guru.php?edit=<?= $row['id'] ?>" class="btn-edit"><i class="fa-solid fa-pen"></i></a>
                                <a href="guru.php?hapus=<?= $row['id'] ?>" class="btn-hapus"
                                   onclick="return confirm('Yakin ingin menghapus <?= htmlspecialchars(addslashes($row['nama'])) ?>?')">
                                   <i class="fa-solid fa-trash"></i>
                                </a>
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
