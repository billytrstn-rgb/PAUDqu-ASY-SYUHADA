<?php
$page_title = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

// Fetch all pengaturan
$set = [];
$q_set = mysqli_query($conn, "SELECT kunci, nilai FROM pengaturan");
if ($q_set) {
    while($r = mysqli_fetch_assoc($q_set)){
        $set[$r['kunci']] = $r['nilai'];
    }
}
function s($kunci) { global $set; return htmlspecialchars($set[$kunci] ?? ''); }
function s_raw($kunci) { global $set; return $set[$kunci] ?? ''; }

// Fetch Guru Teratas (Urutan berdasarkan prioritas)
$guruList = mysqli_query($conn, "SELECT * FROM guru ORDER BY urutan ASC, id ASC LIMIT 4");

// Fetch Berita Teratas
$beritaList = mysqli_query($conn, "SELECT * FROM berita ORDER BY tanggal DESC LIMIT 4");

// Fetch Kegiatan
$kegiatanAdminList = mysqli_query($conn, "SELECT * FROM kegiatan ORDER BY urutan ASC LIMIT 3");

// Real Statistics (Guru tetap otomatis dari database, Siswa & Alumni dari pengaturan)
$countAktif = (int)s_raw('jumlah_siswa');
$countAlumni = (int)s_raw('jumlah_alumni');
$countGuru = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM guru WHERE status='Aktif'"))['total'];
?>
<div class="main-content" id="mainContent">
    <?php include 'includes/topbar.php'; ?>
    <main class="dashboard-body">

            <!-- STAT CARDS (Siswa Aktif, Guru, Alumni) -->
            <div class="stats-row" style="grid-template-columns: repeat(3, 1fr);">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fa-solid fa-children"></i></div>
                    <div class="stat-info">
                        <h3 class="count-up" data-target="<?= $countAktif ?>">0</h3>
                        <p>TOTAL SISWA AKTIF</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fa-solid fa-chalkboard-user"></i></div>
                    <div class="stat-info">
                        <h3 class="count-up" data-target="<?= $countGuru ?>">0</h3>
                        <p>TOTAL GURU & STAF</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon pink"><i class="fa-solid fa-graduation-cap"></i></div>
                    <div class="stat-info">
                        <h3 class="count-up" data-target="<?= $countAlumni ?>">0</h3>
                        <p>TOTAL ALUMNI</p>
                    </div>
                </div>
            </div>

            <div class="grid-2">
                <!-- TABEL GURU -->
                <div class="card">
                    <div class="section-header">
                        <h2>Tim Pendidik</h2>
                        <a href="guru.php" class="btn btn-outline btn-sm">Kelola</a>
                    </div>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Jabatan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($guruList): while($g = mysqli_fetch_assoc($guruList)): 
                                    $inisial = strtoupper(substr($g['nama'], 0, 1));
                                ?>
                                <tr>
                                    <td>
                                        <div class="td-avatar">
                                            <div class="avatar-circle av-blue"><?= $inisial ?></div>
                                            <div>
                                                <div class="td-name"><?= htmlspecialchars($g['nama']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-blue"><?= htmlspecialchars($g['jabatan']) ?></span></td>
                                    <td><span class="badge badge-<?= $g['status'] == 'Aktif' ? 'green' : 'pink' ?>"><?= htmlspecialchars($g['status']) ?></span></td>
                                </tr>
                                <?php endwhile; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- BERITA TERBARU -->
                <div class="card">
                    <div class="section-header">
                        <h2>Berita Terbaru</h2>
                        <a href="berita.php" class="btn btn-outline btn-sm">Kelola</a>
                    </div>
                    <div class="activity-list">
                        <?php if($beritaList): while($b = mysqli_fetch_assoc($beritaList)): ?>
                        <div class="activity-item">
                            <div class="activity-dot" style="background:var(--clr-primary-soft); color:var(--clr-primary);"><i class="fa-solid fa-newspaper"></i></div>
                            <div class="activity-content">
                                <p><strong><?= htmlspecialchars($b['judul']) ?></strong></p>
                                <span><i class="fa-regular fa-clock"></i> <?= date('d M Y', strtotime($b['tanggal'])) ?></span>
                            </div>
                        </div>
                        <?php endwhile; endif; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="section-header">
                        <h2>Program Kegiatan</h2>
                        <a href="kegiatan.php" class="btn btn-outline btn-sm">Kelola</a>
                    </div>
                    <div class="activity-list">
                        <?php if($kegiatanAdminList): while($k = mysqli_fetch_assoc($kegiatanAdminList)): ?>
                        <div class="activity-item">
                            <div class="activity-dot" style="background:var(--clr-accent-orange-soft); color:var(--clr-accent-orange);"><i class="fa-solid <?= htmlspecialchars($k['ikon']) ?>"></i></div>
                            <div class="activity-content">
                                <p><strong><?= htmlspecialchars($k['nama']) ?></strong></p>
                                <span><?= htmlspecialchars(substr($k['deskripsi'], 0, 40)) ?>...</span>
                            </div>
                        </div>
                        <?php endwhile; endif; ?>
                    </div>
                </div>
            </div>
        </main>
<?php include 'includes/footer.php'; ?>
