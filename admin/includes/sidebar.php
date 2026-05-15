<?php
$current_page = basename($_SERVER['PHP_SELF']);
function is_active($page, $current) {
    return $page == $current ? 'active' : '';
}
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <img src="../Img/logo%20Paudqu%20asy%20syuhada%20_page-0001.jpg" alt="Logo" style="height: 35px; width: auto; border-radius: 4px; margin-right: 10px;">
        <div class="sidebar-logo-text">
            ASY SYUHADA
            <span>Admin Panel</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <p class="nav-section-label">UTAMA</p>
        <ul>
            <li>
                <a href="index.php" class="<?= is_active('index.php', $current_page) ?>">
                    <span class="nav-icon"><i class="fa-solid fa-chart-pie"></i></span>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="guru.php" class="<?= is_active('guru.php', $current_page) ?>">
                    <span class="nav-icon"><i class="fa-solid fa-chalkboard-user"></i></span>
                    <span class="nav-text">Data Guru</span>
                </a>
            </li>
        </ul>

        <p class="nav-section-label">KONTEN</p>
        <ul>
            <li>
                <a href="kegiatan.php" class="<?= is_active('kegiatan.php', $current_page) ?>">
                    <span class="nav-icon"><i class="fa-solid fa-shapes"></i></span>
                    <span class="nav-text">Kegiatan Sekolah</span>
                </a>
            </li>
            <li>
                <a href="fasilitas.php" class="<?= is_active('fasilitas.php', $current_page) ?>">
                    <span class="nav-icon"><i class="fa-solid fa-building-circle-check"></i></span>
                    <span class="nav-text">Fasilitas</span>
                </a>
            </li>
            <li>
                <a href="berita.php" class="<?= is_active('berita.php', $current_page) ?>">
                    <span class="nav-icon"><i class="fa-solid fa-newspaper"></i></span>
                    <span class="nav-text">Berita</span>
                </a>
            </li>
            <li>
                <a href="galeri.php" class="<?= is_active('galeri.php', $current_page) ?>">
                    <span class="nav-icon"><i class="fa-solid fa-images"></i></span>
                    <span class="nav-text">Galeri</span>
                </a>
            </li>
            <li>
                <a href="misi.php" class="<?= is_active('misi.php', $current_page) ?>">
                    <span class="nav-icon"><i class="fa-solid fa-bullseye"></i></span>
                    <span class="nav-text">Misi Sekolah</span>
                </a>
            </li>
        </ul>

        <p class="nav-section-label">SISTEM</p>
        <ul>
            <li>
                <a href="pengaturan.php" class="<?= is_active('pengaturan.php', $current_page) ?>">
                    <span class="nav-icon"><i class="fa-solid fa-cog"></i></span>
                    <span class="nav-text">Pengaturan Web</span>
                </a>
            </li>
            <li>
                <a href="ganti_password.php" class="<?= is_active('ganti_password.php', $current_page) ?>">
                    <span class="nav-icon"><i class="fa-solid fa-user-shield"></i></span>
                    <span class="nav-text">Pengaturan Akun</span>
                </a>
            </li>
            <li>
                <a href="logout.php" style="color:var(--clr-accent-pink);">
                    <span class="nav-icon"><i class="fa-solid fa-right-from-bracket"></i></span>
                    <span class="nav-text">Keluar / Logout</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <a href="../index.php" target="_blank">
            <span class="nav-icon"><i class="fa-solid fa-globe"></i></span>
            <span>Lihat Website</span>
        </a>
    </div>
</aside>
