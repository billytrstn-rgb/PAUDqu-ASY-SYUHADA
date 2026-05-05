<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAUDqu ASY SYUHADA - Cerdas & Ceria</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- ===== NAVBAR ===== -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <div class="logo-icon"><i class="fa-solid fa-child-reaching"></i></div>
                <span>ASY SYUHADA</span>
            </div>

            <!-- Hamburger (mobile only) -->
            <div class="menu-toggle" id="mobile-menu" aria-label="Buka menu">
                <i class="fa-solid fa-bars"></i>
            </div>

            <!-- Desktop nav -->
            <ul class="nav-links">
                <li><a href="#beranda">BERANDA</a></li>
                <li><a href="#profil">PROFIL</a></li>
                <li><a href="#kegiatan">KEGIATAN</a></li>
                <li><a href="#guru-kami">GURU KAMI</a></li>
                 <li><a href="#lokasi">LOKASI</a></li>
                <li><a href="#fasilitas">FASILITAS</a></li>
            </ul>
            <a href="#cta" class="btn btn-primary nav-btn">AYO BERGABUNG!</a>
        </div>
    </nav>

    <!-- ===== MOBILE DRAWER ===== -->
    <div class="mobile-overlay" id="mobile-overlay"></div>

    <div class="mobile-drawer" id="mobile-drawer" role="dialog" aria-modal="true" aria-label="Menu navigasi">
        <div class="drawer-header">
            <div class="drawer-logo">
                <div class="logo-icon"><i class="fa-solid fa-child-reaching"></i></div>
                <span>ASY SYUHADA</span>
            </div>
            <div class="drawer-close" id="drawer-close" aria-label="Tutup menu">
                <i class="fa-solid fa-xmark"></i>
            </div>
        </div>

        <ul class="drawer-links">
            <li>
                <a href="#beranda">
                    <span class="drawer-icon di-orange"><i class="fa-solid fa-house"></i></span>
                    Beranda
                </a>
            </li>
            <li>
                <a href="#profil">
                    <span class="drawer-icon di-blue"><i class="fa-solid fa-circle-info"></i></span>
                    Profil Sekolah
                </a>
            </li>
            <li>
                <a href="#kegiatan">
                    <span class="drawer-icon di-green"><i class="fa-solid fa-star"></i></span>
                    Kegiatan
                </a>
            </li>
            <li>
                <a href="#guru-kami">
                    <span class="drawer-icon di-pink"><i class="fa-solid fa-chalkboard-user"></i></span>
                    Guru Kami
                </a>
            </li>
            <li>
        <a href="#lokasi">
            <span class="drawer-icon di-orange"><i class="fa-solid fa-map-location-dot"></i></span>
            Lokasi
        </a>
    </li>
            <li>
                <a href="#fasilitas">
                    <span class="drawer-icon di-purple"><i class="fa-solid fa-building"></i></span>
                    Fasilitas
                </a>
            </li>
        </ul>

        <div class="drawer-footer">
            <a href="#cta" class="btn btn-primary">AYO BERGABUNG!</a>
        </div>
    </div>

    <!-- ===== HERO ===== -->
    <section id="beranda" class="hero container">
        <div class="hero-content">
            <h1>Bermain &<br>Belajar Lebih<br><span class="highlight-orange">Seru!</span></h1>
            <p>Selamat datang di PAUDqu ASY SYUHADA, tempat eksplorasi tanpa batas untuk buah hati Anda.</p>
            
            <div class="stats-container">
                <div class="stat-box">
                    <h2 class="text-blue">80+</h2>
                    <p>SISWA AKTIF</p>
                </div>
                <div class="stat-box">
                    <h2 class="text-pink">200+</h2>
                    <p>ALUMNI</p>
                </div>
            </div>
            <a href="#cta" class="btn btn-success">Daftar Sekarang</a>
        </div>
        <div class="hero-image">
            <img src="Img/Foto-Foto murid.jpeg" alt="Foto Siswa">
        </div>
    </section>

    <!-- ===== PROFIL ===== -->
    <section id="profil" class="about container reveal">
        <div class="about-grid">
            <div class="about-blue-card">
                <span class="badge">SEJARAH & IDENTITAS</span>
                <h2>Mengenal PAUDqu</h2>
                <p>PAUDqu ASY SYUHADA hadir sebagai mitra orang tua dalam memberikan pondasi karakter terbaik sejak usia dini. Kami percaya bahwa pendidikan anak bukan hanya soal angka, tapi tentang kebahagiaan dalam menemukan potensi diri.</p>
                
                <div class="visi-box">
                    <h3><i class="fa-solid fa-eye"></i> Visi Kami</h3>
                    <p>"Menjadi taman bermain dan belajar yang membentuk anak Indonesia yang sehat, berakhlak mulia, cerdas, dan mandiri."</p>
                </div>
            </div>
            <div class="about-white-card">
                <h2>Misi Kami</h2>
                <div class="misi-list">
                    <div class="misi-item">
                        <div class="misi-icon icon-orange"><i class="fa-solid fa-star"></i></div>
                        <div>
                            <h4>Pembiasaan Positif</h4>
                            <p>Menanamkan nilai kejujuran dan etika melalui teladan nyata setiap hari.</p>
                        </div>
                    </div>
                    <div class="misi-item">
                        <div class="misi-icon icon-green"><i class="fa-solid fa-palette"></i></div>
                        <div>
                            <h4>Kreativitas Tanpa Batas</h4>
                            <p>Mengasah daya imajinasi anak melalui eksplorasi seni, musik, dan gerak.</p>
                        </div>
                    </div>
                    <div class="misi-item">
                        <div class="misi-icon icon-blue"><i class="fa-solid fa-heart"></i></div>
                        <div>
                            <h4>Kasih Sayang</h4>
                            <p>Menyediakan lingkungan asuhan yang hangat, aman, dan tanpa diskriminasi.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== KEGIATAN ===== -->
    <section id="kegiatan" class="programs container section-padding reveal">
        <h2 class="section-title">Program Unggulan</h2>
        <div class="programs-grid">
            <div class="card program-card border-bottom-orange">
                <i class="fa-solid fa-heart icon-large text-orange"></i>
                <h3>Karakter Mulia</h3>
                <p>Membangun etika dan rasa empati melalui cerita dan praktik harian.</p>
            </div>
            <div class="card program-card border-bottom-green">
                <i class="fa-solid fa-palette icon-large text-green"></i>
                <h3>Eksplorasi Seni</h3>
                <p>Mewarnai dan membuat prakarya tangan untuk mengasah kreativitas.</p>
            </div>
            <div class="card program-card border-bottom-blue">
                <i class="fa-solid fa-person-walking icon-large text-blue"></i>
                <h3>Kemandirian</h3>
                <p>Melatih tanggung jawab kecil untuk membentuk kepercayaan diri.</p>
            </div>
        </div>
    </section>

    <!-- ===== GURU KAMI ===== -->
    <section id="guru-kami" class="teachers container section-padding reveal">
        <h2 class="section-title">Tim Pendidik Kami</h2>
        <p class="section-subtitle">Dibimbing oleh guru-guru yang sabar & penuh kasih sayang</p>
        <div class="teachers-grid">
            
            <div class="card teacher-card">
                <div class="teacher-img ring-orange">
                    <img src="img/Usep Saefullah.jpeg" alt="Pak Usep Saefullah, S.Pd">
                </div>
                <h3>Pak Usep Saefullah, S.Pd, I</h3>
                <p class="role role-orange">KEPALA SEKOLAH</p>
            </div>
            
            <div class="card teacher-card">
                <div class="teacher-img ring-blue">
                    <img src="img/Enung Surtasih.jpeg" alt="Ibu Enung Surtasih, A.Ma">
                </div>
                <h3>Ibu Enung Surtasih, S, Pd</h3>
            </div>
            
            <div class="card teacher-card">
                <div class="teacher-img ring-pink">
                    <img src="img/Sumiati.jpeg" alt="Ibu Sumiati, S.Pd">
                </div>
                <h3>Ibu Sumiati, S.Pd</h3>
            </div>
            
            <div class="card teacher-card">
                <div class="teacher-img ring-green">
                    <img src="img/Neneng.jpeg" alt="Ibu Neneng">
                </div>
                <h3>Ibu Neneng, S.Pd</h3>
            </div>
            
        </div>
    </section>

  <!-- ===== LOKASI ===== -->
 <section id="lokasi" class="location container section-padding reveal">
    <h2 class="section-title">Lokasi Kami</h2>
    <div class="location-grid">
        <!-- Kartu Alamat -->
        <div class="location-card">
            <div class="card-content">
                <i class="fa-solid fa-location-dot icon-large text-orange"></i>
                <h3>PAUDqu ASY SYUHADA</h3>
                <p>Jl. Raya Ciomas Cibinong No.34, Sukamakmur, Kec. Ciomas, Kabupaten Bogor, Jawa Barat 16610</p>
                <a href="https://maps.app.goo.gl/8FVMbuEbUNRMKHqU8" target="_blank" class="btn btn-primary">Lihat di Google Maps</a>
            </div>
        </div>

        <!-- Wadah Peta -->
        <div class="map-container">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3963.292319!2d106.7500809!3d-6.6033336!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zNsKwMzYnMTEuOSJTIDEwNsKwNDUnMDAuMyJF!5e0!3m2!1sid!2sid!4v1714737000000!5m2!1sid!2sid" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>
        </div>
    </div>
</section>

    <!-- ===== FASILITAS ===== -->
    <section id="fasilitas" class="facilities container section-padding reveal">
        <h2 class="section-title">Fasilitas Sekolah</h2>
        <div class="facilities-grid">
            <div class="facility-card bg-light-blue">
                <i class="fa-solid fa-wind text-blue"></i>
                <h4>Udara Segar Alami</h4>
            </div>
            <div class="facility-card bg-light-green">
                <i class="fa-solid fa-users text-green"></i>
                <h4>Taman Bermain</h4>
            </div>
            <div class="facility-card bg-light-orange">
                <i class="fa-solid fa-book-open text-orange"></i>
                <h4>Pojok Baca</h4>
            </div>
            <div class="facility-card bg-light-pink">
                <i class="fa-solid fa-shield-halved text-pink"></i>
                <h4>Lingkungan Aman</h4>
            </div>
        </div>
    </section>

    <!-- ===== CTA ===== -->
    <section id="cta" class="cta-section container section-padding">
        <div class="cta-banner">
            <h2>AYO BERGABUNG!</h2>
            <p>Mari menjadi bagian dari keceriaan di PAUDqu ASY SYUHADA.</p>
            <a href="https://api.whatsapp.com/send?phone=6285771751759" target="_blank" class="btn btn-white">HUBUNGI WA KAMI</a>
        </div>
    </section>

    <footer>
        <p>&copy; 2026 PAUDQU ASY SYUHADA. BERBUAT DENGAN KASIH SAYANG.</p>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
