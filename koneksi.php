<?php
$host = "localhost";
$user = "paudquas";
$pass = "Paudqu001";
$db   = "paudquas_db";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Koneksi awal ke server MySQL (tanpa memilih database)
$conn = mysqli_connect($host, $user, $pass);

if (!$conn) {
    die("Koneksi ke server MySQL gagal: " . mysqli_connect_error());
}

// Buat database jika belum ada
if (!mysqli_select_db($conn, $db)) {
    mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    mysqli_select_db($conn, $db);
}

// --- INISIALISASI TABEL ---

// 1. Tabel admin
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `admin` (
  `id`       int(11)      NOT NULL AUTO_INCREMENT,
  `username` varchar(50)  NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$checkAdmin = mysqli_query($conn, "SELECT id FROM admin WHERE username = 'admin'");
if ($checkAdmin && mysqli_num_rows($checkAdmin) == 0) {
    $hashedPassword = password_hash('paud2026', PASSWORD_DEFAULT);
    mysqli_query($conn, "INSERT INTO `admin` (`username`, `password`) VALUES ('admin', '$hashedPassword')");
}

// 2. Tabel guru
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `guru` (
  `id`      int(11)      NOT NULL AUTO_INCREMENT,
  `nama`    varchar(100) NOT NULL,
  `jabatan` varchar(50)  NOT NULL,
  `foto`    varchar(255) DEFAULT NULL,
  `status`  enum('Aktif','Tidak Aktif') DEFAULT 'Aktif',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 3. Tabel siswa
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `siswa` (
  `id`             int(11)              NOT NULL AUTO_INCREMENT,
  `nama`           varchar(100)         NOT NULL,
  `nis`            varchar(20)          DEFAULT NULL,
  `tanggal_lahir`  date                 DEFAULT NULL,
  `jenis_kelamin`  enum('L','P')        DEFAULT 'L',
  `alamat`         text                 DEFAULT NULL,
  `nama_ortu`      varchar(100)         DEFAULT NULL,
  `telepon`        varchar(20)          DEFAULT NULL,
  `foto`           varchar(255)         DEFAULT NULL,
  `status`         enum('Aktif','Alumni') DEFAULT 'Aktif',
  `tahun_masuk`    year                 DEFAULT NULL,
  `tahun_lulus`    year                 DEFAULT NULL,
  `created_at`     timestamp            NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 4. Tabel berita
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `berita` (
  `id`         int(11)      NOT NULL AUTO_INCREMENT,
  `judul`      varchar(200) NOT NULL,
  `isi`        text         NOT NULL,
  `foto`       varchar(255) DEFAULT NULL,
  `tanggal`    date         NOT NULL,
  `status`     enum('Publikasi','Draft') DEFAULT 'Draft',
  `created_at` timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 5. Tabel galeri
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `galeri` (
  `id`          int(11)      NOT NULL AUTO_INCREMENT,
  `judul`       varchar(100) NOT NULL,
  `foto`        varchar(255) NOT NULL,
  `keterangan`  text         DEFAULT NULL,
  `urutan`      int(11)      DEFAULT 0,
  `created_at`  timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 6. Tabel kegiatan
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `kegiatan` (
  `id`        int(11)      NOT NULL AUTO_INCREMENT,
  `nama`      varchar(100) NOT NULL,
  `deskripsi` text         DEFAULT NULL,
  `ikon`      varchar(50)  DEFAULT 'fa-star',
  `warna`     varchar(20)  DEFAULT 'orange',
  `urutan`    int(11)      DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 7. Tabel fasilitas
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `fasilitas` (
  `id`        int(11)      NOT NULL AUTO_INCREMENT,
  `nama`      varchar(100) NOT NULL,
  `deskripsi` text         DEFAULT NULL,
  `ikon`      varchar(50)  DEFAULT 'fa-building',
  `warna`     varchar(20)  DEFAULT 'blue',
  `urutan`    int(11)      DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// 8. Tabel pengaturan
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `pengaturan` (
  `id`    int(11)     NOT NULL AUTO_INCREMENT,
  `kunci` varchar(50) NOT NULL,
  `nilai` text        DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kunci` (`kunci`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// --- SEED DATA AWAL JIKA KOSONG ---

$checkPengaturan = mysqli_query($conn, "SELECT id FROM pengaturan LIMIT 1");
if ($checkPengaturan && mysqli_num_rows($checkPengaturan) == 0) {
    
    // Seed Guru
    mysqli_query($conn, "INSERT IGNORE INTO `guru` (`nama`, `jabatan`, `foto`, `status`) VALUES
    ('Ibu Neneng, S.Pd',          'Kepala Sekolah', 'Neneng.jpeg',          'Aktif'),
    ('Ibu Enung Surtasih',        'Guru 1',         'Enung Surtasih.jpeg',  'Aktif'),
    ('Ibu Sumiati',               'Guru 2',         'Sumiati.jpeg',         'Aktif'),
    ('Pak Usep Saefullah, S.Pd, I','Operator',      'Usep Saefullah.jpeg',  'Aktif')");
    
    // Seed Siswa
    mysqli_query($conn, "INSERT IGNORE INTO `siswa` (`nama`, `nis`, `tanggal_lahir`, `jenis_kelamin`, `nama_ortu`, `telepon`, `status`, `tahun_masuk`) VALUES
    ('Ahmad Zaki',       '2024001', '2020-03-15', 'L', 'Bapak Zainal',   '08123456789', 'Aktif',  2024),
    ('Siti Aisyah',      '2024002', '2020-07-22', 'P', 'Ibu Rohimah',    '08234567890', 'Aktif',  2024),
    ('Muhammad Faris',   '2024003', '2019-11-05', 'L', 'Bapak Hasan',    '08345678901', 'Aktif',  2024),
    ('Nur Fadillah',     '2024004', '2020-01-30', 'P', 'Ibu Fatimah',    '08456789012', 'Aktif',  2024),
    ('Rizky Pratama',    '2023001', '2018-05-10', 'L', 'Bapak Rudi',     '08567890123', 'Alumni', 2023)");

    // Seed Berita
    mysqli_query($conn, "INSERT IGNORE INTO `berita` (`judul`, `isi`, `foto`, `tanggal`, `status`) VALUES
    ('Penerimaan Siswa Baru Tahun Ajaran 2026/2027', 'Mari bergabung bersama keluarga besar PAUDqu ASY SYUHADA. Kami membuka pendaftaran siswa baru untuk tahun ajaran 2026/2027. Kuota terbatas! Segera hubungi kami untuk informasi lebih lanjut mengenai persyaratan dan jadwal pendaftaran.', 'Foto-Foto murid.jpeg', '2026-05-10', 'Publikasi'),
    ('Serunya Kunjungan ke Taman Alam', 'Eksplorasi alam membantu anak mengenal lingkungan sekitar dengan cara yang menyenangkan. Anak-anak sangat antusias mengikuti kegiatan kunjungan ke taman alam minggu lalu. Mereka belajar mengenal berbagai jenis tanaman dan hewan secara langsung.', 'Usep Saefullah.jpeg', '2026-05-05', 'Publikasi')");

    // Seed Galeri
    mysqli_query($conn, "INSERT IGNORE INTO `galeri` (`judul`, `foto`, `keterangan`, `urutan`) VALUES
    ('Keceriaan Bersama', 'Foto-Foto murid.jpeg', 'Momen berharga bersama para murid', 1),
    ('Tenaga Pendidik',   'Neneng.jpeg',          'Kepala Sekolah kami yang berdedikasi', 2),
    ('Guru Penuh Kasih',  'Sumiati.jpeg',          'Ibu Guru yang menyayangi anak didik', 3),
    ('Belajar Ceria',     'Enung Surtasih.jpeg',   'Suasana belajar yang menyenangkan', 4)");

    // Seed Kegiatan
    mysqli_query($conn, "INSERT IGNORE INTO `kegiatan` (`nama`, `deskripsi`, `ikon`, `warna`, `urutan`) VALUES
    ('Karakter Mulia',   'Membangun etika dan rasa empati melalui cerita dan praktik harian.',         'fa-heart',          'orange', 1),
    ('Eksplorasi Seni',  'Mewarnai dan membuat prakarya tangan untuk mengasah kreativitas anak.',      'fa-palette',        'green',  2),
    ('Kemandirian',      'Melatih tanggung jawab kecil untuk membentuk kepercayaan diri anak.',        'fa-person-walking', 'blue',   3)");

    // Seed Fasilitas
    mysqli_query($conn, "INSERT IGNORE INTO `fasilitas` (`nama`, `deskripsi`, `ikon`, `warna`, `urutan`) VALUES
    ('Ruang Kelas Nyaman', 'Ruang belajar yang bersih, cerah, dan dirancang ramah anak untuk kenyamanan belajar.', 'fa-building',       'blue',   1),
    ('Area Bermain',       'Taman bermain yang aman dengan peralatan edukatif untuk mengembangkan motorik anak.',   'fa-child-reaching', 'green',  2),
    ('Toilet Bersih',      'Fasilitas sanitasi yang terjaga kebersihannya dan aman untuk digunakan anak-anak.',     'fa-toilet-paper',   'orange', 3)");

    // Seed Pengaturan
    mysqli_query($conn, "INSERT IGNORE INTO `pengaturan` (`kunci`, `nilai`) VALUES
    ('nama_sekolah',  'PAUDqu ASY SYUHADA'),
    ('tagline',       'Bermain & Belajar Lebih Seru!'),
    ('deskripsi_hero','Selamat datang di PAUDqu ASY SYUHADA, tempat eksplorasi tanpa batas untuk buah hati Anda.'),
    ('jumlah_siswa',  '80'),
    ('jumlah_alumni', '200'),
    ('foto_hero',     'Foto-Foto murid.jpeg'),
    ('visi',          'Menjadi taman bermain dan belajar yang membentuk anak Indonesia yang sehat, berakhlak mulia, cerdas, dan mandiri.'),
    ('tentang',       'PAUDqu ASY SYUHADA hadir sebagai mitra orang tua dalam memberikan pondasi karakter terbaik sejak usia dini. Kami percaya bahwa pendidikan anak bukan hanya soal angka, tapi tentang kebahagiaan dalam menemukan potensi diri.'),
    ('misi_1_judul',  'Pembiasaan Positif'),
    ('misi_1_isi',    'Menanamkan nilai kejujuran dan etika melalui teladan nyata setiap hari.'),
    ('misi_1_ikon',   'fa-star'),
    ('misi_1_warna',  'orange'),
    ('misi_2_judul',  'Kreativitas Tanpa Batas'),
    ('misi_2_isi',    'Mengasah daya imajinasi anak melalui eksplorasi seni, musik, dan gerak.'),
    ('misi_2_ikon',   'fa-palette'),
    ('misi_2_warna',  'green'),
    ('misi_3_judul',  'Kasih Sayang'),
    ('misi_3_isi',    'Menyediakan lingkungan asuhan yang hangat, aman, dan tanpa diskriminasi.'),
    ('misi_3_ikon',   'fa-heart'),
    ('misi_3_warna',  'blue'),
    ('whatsapp',      '6285771751759'),
    ('alamat',        'Jl. Raya Ciomas Cibinong No.34, Sukamakmur, Kec. Ciomas, Kabupaten Bogor, Jawa Barat 16610'),
    ('maps_embed',    'https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3963.292319!2d106.7500809!3d-6.6033336!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zNsKwMzYnMTEuOSJTIDEwNsKwNDUnMDAuMyJF!5e0!3m2!1sid!2sid!4v1714737000000!5m2!1sid!2sid'),
    ('maps_link',     'https://maps.app.goo.gl/8FVMbuEbUNRMKHqU8'),
    ('instagram',     '#'),
    ('facebook',      '#'),
    ('youtube',       '#')");
}
?>