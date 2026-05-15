<?php
require_once 'koneksi.php';

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

// Fetch guru
$guruList = mysqli_query($conn, "SELECT * FROM guru WHERE status='Aktif' ORDER BY urutan ASC, id ASC");

// Fetch berita
$beritaList = mysqli_query($conn, "SELECT * FROM berita WHERE status='Publikasi' ORDER BY tanggal DESC LIMIT 3");

// Fetch galeri
$galeriList = mysqli_query($conn, "SELECT * FROM galeri ORDER BY urutan ASC");

// Fetch kegiatan
$kegiatanList = mysqli_query($conn, "SELECT * FROM kegiatan ORDER BY urutan ASC");

// Fetch fasilitas
$fasilitasList = mysqli_query($conn, "SELECT * FROM fasilitas ORDER BY urutan ASC");

// Fetch misi
$misiList = mysqli_query($conn, "SELECT * FROM misi ORDER BY urutan ASC");

// Real Statistics for Homepage (Siswa & Alumni dari Pengaturan, Guru dari Database)
$countAktif = (int)s_raw('jumlah_siswa');
$countAlumni = (int)s_raw('jumlah_alumni');
$countGuru = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM guru WHERE status='Aktif'"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= s('nama_sekolah') ?> - Cerdas & Ceria</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- ===== NAVBAR ===== -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <img src="Img/logo%20Paudqu%20asy%20syuhada%20_page-0001.jpg" alt="Logo PAUDqu" style="height: 45px; width: auto; border-radius: 8px;">
                <span><?= strtoupper(s('nama_sekolah')) ?></span>
            </div>

            <!-- Hamburger (mobile only) -->
            <div class="menu-toggle" id="mobile-menu" aria-label="Buka menu">
                <i class="fa-solid fa-bars"></i>
            </div>

            <!-- Desktop nav -->
            <ul class="nav-links">
                <li><a href="#beranda">Beranda</a></li>
                <li><a href="#profil">Profil</a></li>
                <li><a href="#kegiatan">Kegiatan</a></li>
                <li><a href="#guru-kami">Guru Kami</a></li>
                <li><a href="#galeri">Galeri</a></li>
                <li><a href="#berita">Berita</a></li>
                <li><a href="#lokasi">Lokasi</a></li>
            </ul>
            <a href="javascript:void(0)" onclick="openJoinModal()" class="btn btn-primary nav-btn">Hubungi Kami</a>
        </div>
    </nav>

    <!-- ===== MOBILE DRAWER ===== -->
    <div class="mobile-overlay" id="mobile-overlay"></div>

    <div class="mobile-drawer" id="mobile-drawer" role="dialog" aria-modal="true" aria-label="Menu navigasi">
        <div class="drawer-header">
            <div class="drawer-logo">
                <img src="Img/logo%20Paudqu%20asy%20syuhada%20_page-0001.jpg" alt="Logo" style="height: 40px; width: auto; border-radius: 6px;">
                <span><?= strtoupper(s('nama_sekolah')) ?></span>
            </div>
            <div class="drawer-close" id="drawer-close" aria-label="Tutup menu">
                <i class="fa-solid fa-xmark"></i>
            </div>
        </div>

        <ul class="drawer-links">
            <li><a href="#beranda"><span class="drawer-icon di-orange"><i class="fa-solid fa-house"></i></span> Beranda</a></li>
            <li><a href="#profil"><span class="drawer-icon di-blue"><i class="fa-solid fa-circle-info"></i></span> Profil</a></li>
            <li><a href="#kegiatan"><span class="drawer-icon di-green"><i class="fa-solid fa-shapes"></i></span> Kegiatan</a></li>
            <li><a href="#guru-kami"><span class="drawer-icon di-pink"><i class="fa-solid fa-chalkboard-user"></i></span> Guru Kami</a></li>
            <li><a href="#galeri"><span class="drawer-icon di-purple"><i class="fa-solid fa-images"></i></span> Galeri</a></li>
            <li><a href="#berita"><span class="drawer-icon di-green"><i class="fa-solid fa-newspaper"></i></span> Berita</a></li>
            <li><a href="#lokasi"><span class="drawer-icon di-orange"><i class="fa-solid fa-map-location-dot"></i></span> Lokasi</a></li>
        </ul>

        <div class="drawer-footer">
            <a href="javascript:void(0)" onclick="openJoinModal()" class="btn btn-primary" style="width:100%;justify-content:center;">Hubungi Kami</a>
        </div>
    </div>

    <!-- ===== HERO ===== -->
    <section id="beranda" class="hero container">
        <div class="hero-content">
            <h1><?= nl2br(s('tagline')) ?></h1>
            <p><?= s('deskripsi_hero') ?></p>
            
            <div class="stats-container">
                <div class="stat-box">
                    <h2 class="text-blue"><?= $countAktif ?></h2>
                    <p>Siswa Aktif</p>
                </div>
                <div class="stat-box">
                    <h2 class="text-pink"><?= $countAlumni ?></h2>
                    <p>Total Alumni</p>
                </div>
                <div class="stat-box">
                    <h2 class="text-orange"><?= $countGuru ?></h2>
                    <p>Guru & Staf</p>
                </div>
            </div>
            <a href="#cta" class="btn btn-success">Daftar Sekarang</a>
        </div>
        <div class="hero-image">
            <img src="Img/<?= s('foto_hero') ?>" alt="Hero Image">
        </div>
    </section>

    <!-- ===== PROFIL ===== -->
    <section id="profil" class="about container reveal">
        <div class="about-grid">
            <div class="about-blue-card">
                <h2>Mengenal <?= s('nama_sekolah') ?></h2>
                <p><?= s('tentang') ?></p>
                
                <div class="visi-box">
                    <h3><i class="fa-solid fa-eye"></i> Visi Kami</h3>
                    <p>"<?= s('visi') ?>"</p>
                </div>
            </div>
            <div class="about-white-card">
                <h2>Misi Kami</h2>
                <div class="misi-list">
                    <?php if($misiList && mysqli_num_rows($misiList) > 0): while($row = mysqli_fetch_assoc($misiList)): ?>
                    <div class="misi-item">
                        <div class="misi-icon icon-<?= htmlspecialchars($row['warna']) ?>"><i class="fa-solid <?= htmlspecialchars($row['ikon']) ?>"></i></div>
                        <div>
                            <h4><?= htmlspecialchars($row['judul']) ?></h4>
                            <p><?= htmlspecialchars($row['deskripsi']) ?></p>
                        </div>
                    </div>
                    <?php endwhile; endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== KEGIATAN ===== -->
    <section id="kegiatan" class="programs container section-padding reveal">
        <h2 class="section-title">Program Unggulan</h2>
        <div class="programs-grid">
            <?php if($kegiatanList && mysqli_num_rows($kegiatanList) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($kegiatanList)): ?>
                <div class="card program-card border-bottom-<?= htmlspecialchars($row['warna']) ?>">
                    <?php if($row['foto']): ?>
                        <div class="card-img-top">
                            <img src="Img/<?= htmlspecialchars($row['foto']) ?>" alt="<?= htmlspecialchars($row['nama']) ?>">
                        </div>
                    <?php endif; ?>
                    <div class="card-body-content">
                        <?php if(!$row['foto']): ?>
                            <i class="fa-solid <?= htmlspecialchars($row['ikon']) ?> icon-large text-<?= htmlspecialchars($row['warna']) ?>" style="margin-bottom: 1.5rem; display: block;"></i>
                        <?php endif; ?>
                        <h3><?= htmlspecialchars($row['nama']) ?></h3>
                        <p><?= htmlspecialchars($row['deskripsi']) ?></p>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- ===== GURU KAMI ===== -->
    <section id="guru-kami" class="teachers container section-padding reveal">
        <h2 class="section-title">Tim Pendidik Kami</h2>
        <p class="section-subtitle">Dibimbing oleh guru-guru yang sabar & penuh kasih sayang</p>
        <div class="teachers-grid">
            <?php 
            if($guruList && mysqli_num_rows($guruList) > 0): 
                $colors = ['green', 'blue', 'pink', 'orange'];
                $i = 0;
                while($row = mysqli_fetch_assoc($guruList)): 
                    $color = $colors[$i % 4];
                    $i++;
            ?>
            <div class="card teacher-card">
                <div class="teacher-img-container">
                    <div class="teacher-img ring-<?= $color ?>">
                        <?php if($row['foto']): ?>
                        <img src="Img/<?= htmlspecialchars($row['foto']) ?>" alt="<?= htmlspecialchars($row['nama']) ?>">
                        <?php else: ?>
                        <div style="width:100%;height:100%;background:#eee;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:2rem;color:#ccc;">
                            <?= strtoupper(substr($row['nama'], 0, 1)) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="teacher-info">
                    <h3><?= htmlspecialchars($row['nama']) ?></h3>
                    <span class="teacher-badge"><?= htmlspecialchars($row['jabatan']) ?></span>
                </div>
            </div>
            <?php endwhile; endif; ?>
        </div>
    </section>

    <!-- ===== LOKASI ===== -->
    <section id="lokasi" class="location container section-padding reveal">
        <h2 class="section-title">Lokasi Kami</h2>
        <div class="location-grid">
            <div class="location-card card">
                <div class="card-body-content">
                    <i class="fa-solid fa-location-dot icon-large text-orange"></i>
                    <h3><?= s('nama_sekolah') ?></h3>
                    <p><?= s('alamat') ?></p>
                    <a href="<?= s('maps_link') ?>" target="_blank" class="btn btn-primary">Lihat di Google Maps</a>
                </div>
            </div>
            <div class="map-container">
                <iframe src="<?= s('maps_embed') ?>" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </section>

    <!-- ===== GALERI ===== -->
    <section id="galeri" class="gallery container section-padding reveal">
        <h2 class="section-title">Galeri Keceriaan</h2>
        <p class="section-subtitle">Momen berharga aktivitas belajar dan bermain anak-anak</p>
        <div class="gallery-grid">
            <?php if($galeriList && mysqli_num_rows($galeriList) > 0): while($row = mysqli_fetch_assoc($galeriList)): ?>
            <div class="gallery-item">
                <img src="Img/<?= htmlspecialchars($row['foto']) ?>" alt="<?= htmlspecialchars($row['judul']) ?>">
                <div class="gallery-overlay"><span><?= htmlspecialchars($row['judul']) ?></span></div>
            </div>
            <?php endwhile; endif; ?>
        </div>
    </section>

    <!-- ===== FASILITAS ===== -->
    <section id="fasilitas" class="programs container section-padding reveal">
        <h2 class="section-title">Fasilitas Kami</h2>
        <div class="programs-grid">
            <?php if($fasilitasList && mysqli_num_rows($fasilitasList) > 0): while($row = mysqli_fetch_assoc($fasilitasList)): ?>
            <div class="card program-card border-bottom-<?= htmlspecialchars($row['warna']) ?>">
                <?php if($row['foto']): ?>
                    <div class="card-img-top">
                        <img src="Img/<?= htmlspecialchars($row['foto']) ?>" alt="<?= htmlspecialchars($row['nama']) ?>">
                    </div>
                <?php else: ?>
                    <div class="facility-icon-wrapper">
                        <div class="facility-icon-circle text-<?= htmlspecialchars($row['warna']) ?>">
                            <i class="fa-solid <?= htmlspecialchars($row['ikon']) ?>"></i>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="card-body-content">
                    <h3><?= htmlspecialchars($row['nama']) ?></h3>
                    <p><?= htmlspecialchars($row['deskripsi']) ?></p>
                </div>
            </div>
            <?php endwhile; endif; ?>
        </div>
    </section>

    <!-- ===== BERITA & ARTIKEL ===== -->
    <section id="berita" class="news container section-padding reveal">
        <h2 class="section-title">Berita Terkini</h2>
        <div class="news-grid">
            <?php if($beritaList && mysqli_num_rows($beritaList) > 0): while($row = mysqli_fetch_assoc($beritaList)): ?>
            <div class="card news-card">
                <div class="news-img">
                    <img src="Img/<?= htmlspecialchars($row['foto'] ?? 'default.jpg') ?>" alt="<?= htmlspecialchars($row['judul']) ?>">
                </div>
                <div class="news-content">
                    <span class="news-date"><i class="fa-solid fa-calendar-days"></i> <?= date('d M Y', strtotime($row['tanggal'])) ?></span>
                    <h3><?= htmlspecialchars($row['judul']) ?></h3>
                    <p><?= htmlspecialchars(substr($row['isi'], 0, 100)) ?>...</p>
                    <a href="javascript:void(0)" 
                       onclick="openNewsModal('<?= htmlspecialchars(addslashes($row['judul'])) ?>', '<?= htmlspecialchars(addslashes($row['isi'])) ?>', 'Img/<?= htmlspecialchars($row['foto'] ?? 'default.jpg') ?>', '<?= date('d M Y', strtotime($row['tanggal'])) ?>')" 
                       class="btn-text">Baca Selengkapnya →</a>
                </div>
            </div>
            <?php endwhile; endif; ?>
        </div>
    </section>

    <!-- ===== CTA ===== -->
    <section id="cta" class="cta-section container section-padding">
        <div class="cta-banner">
            <h2>AYO BERGABUNG!</h2>
            <p>Mari menjadi bagian dari keceriaan di <?= s('nama_sekolah') ?>.</p>
            <a href="javascript:void(0)" onclick="openJoinModal()" class="btn btn-white">HUBUNGI WA KAMI</a>
        </div>
    </section>

    <footer style="padding: 3rem 1rem; background: #f8fafc; border-top: 1px solid var(--clr-border); text-align: center;">
        <div style="max-width: 1200px; margin: 0 auto;">
            <p style="color: #64748b; font-size: 0.9rem; font-weight: 600; letter-spacing: 0.5px;">
                &copy; <?= date('Y') ?> <?= strtoupper(s('nama_sekolah')) ?>. BERBUAT DENGAN KASIH SAYANG.
            </p>
            <div style="margin-top: 1rem;">
                <a href="admin/login.php" style="color: #cbd5e1; font-size: 1.2rem; transition: color 0.3s;" title="Portal Admin">
                    <i class="fa-solid fa-circle-user"></i>
                </a>
            </div>
        </div>
    </footer>

    <!-- ===== NEWS MODAL ===== -->
    <div id="newsModal" class="news-modal">
        <div class="news-modal-content card reveal">
            <span class="close-modal">&times;</span>
            <div id="modalImageContainer" class="modal-image">
                <img id="modalImg" src="" alt="">
            </div>
            <div class="modal-body">
                <span id="modalDate" class="news-date"></span>
                <h2 id="modalTitle"></h2>
                <div id="modalText" class="modal-text"></div>
            </div>
        </div>
    </div>

    <!-- ===== JOIN MODAL (WhatsApp Form) ===== -->
    <div id="joinModal" class="news-modal">
        <div class="news-modal-content card" style="max-width: 500px;">
            <span class="close-modal" onclick="closeJoinModal()">&times;</span>
            <div class="modal-body">
                <div style="text-align: center; margin-bottom: 2rem;">
                    <i class="fa-solid fa-comments-dollar" style="font-size: 3rem; color: var(--clr-primary); margin-bottom: 1rem;"></i>
                    <h2 style="font-size: 1.5rem;">Formulir Pendaftaran</h2>
                    <p style="color: var(--clr-text-muted);">Silakan isi data singkat untuk memulai obrolan di WhatsApp</p>
                </div>
                
                <form id="waForm" onsubmit="sendToWA(event)">
                    <div class="form-group" style="margin-bottom: 1.2rem;">
                        <label class="form-label" style="text-align: left; display: block;">Nama Orang Tua / Wali</label>
                        <input type="text" id="wa_parent" class="form-input" required style="width: 100%; padding: 12px; border: 1px solid var(--clr-border); border-radius: 8px;">
                    </div>
                    <div class="form-group" style="margin-bottom: 1.2rem;">
                        <label class="form-label" style="text-align: left; display: block;">Nomor WhatsApp Orang Tua</label>
                        <input type="tel" id="wa_number_parent" class="form-input" placeholder="Cth: 08123456xxx" required style="width: 100%; padding: 12px; border: 1px solid var(--clr-border); border-radius: 8px;">
                    </div>
                    <div class="form-group" style="margin-bottom: 1.2rem;">
                        <label class="form-label" style="text-align: left; display: block;">Nama Lengkap Anak</label>
                        <input type="text" id="wa_child" class="form-input" required style="width: 100%; padding: 12px; border: 1px solid var(--clr-border); border-radius: 8px;">
                    </div>
                    <div class="form-group" style="margin-bottom: 2rem;">
                        <label class="form-label" style="text-align: left; display: block;">Pesan Tambahan (Opsional)</label>
                        <textarea id="wa_msg" class="form-input" style="width: 100%; padding: 12px; border: 1px solid var(--clr-border); border-radius: 8px; min-height: 80px;"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; height: 50px; font-size: 1rem;">
                        <i class="fa-brands fa-whatsapp"></i> Lanjut Ke WhatsApp
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
    <script>
        function openNewsModal(title, text, imgSrc, date) {
            const modal = document.getElementById('newsModal');
            document.getElementById('modalTitle').innerText = title;
            document.getElementById('modalText').innerText = text;
            document.getElementById('modalImg').src = imgSrc;
            document.getElementById('modalDate').innerHTML = '<i class="fa-solid fa-calendar-days"></i> ' + date;
            
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden'; 
        }

        function openJoinModal() {
            document.getElementById('joinModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeJoinModal() {
            document.getElementById('joinModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function sendToWA(e) {
            e.preventDefault();
            const parent = document.getElementById('wa_parent').value;
            const parentWA = document.getElementById('wa_number_parent').value;
            const child = document.getElementById('wa_child').value;
            const msg = document.getElementById('wa_msg').value;
            const schoolName = "<?= s('nama_sekolah') ?>";
            const waNumber = "<?= s('whatsapp') ?>";

            let text = `Halo Admin *${schoolName}*,\n\nSaya ingin bertanya mengenai pendaftaran siswa baru.\n\n*Data Calon Siswa:*\n- Nama Orang Tua: ${parent}\n- WhatsApp: ${parentWA}\n- Nama Anak: ${child}\n- Pesan: ${msg || '-'}\n\nTerima kasih.`;
            
            const encodedText = encodeURIComponent(text);
            window.open(`https://api.whatsapp.com/send?phone=${waNumber}&text=${encodedText}`, '_blank');
            closeJoinModal();
        }

        const modalNews = document.getElementById('newsModal');
        const modalJoin = document.getElementById('joinModal');
        const spanNews = document.getElementsByClassName('close-modal')[0];

        spanNews.onclick = function() {
            modalNews.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        window.onclick = function(event) {
            if (event.target == modalNews) {
                modalNews.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
            if (event.target == modalJoin) {
                modalJoin.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }
    </script>
</body>
</html>
