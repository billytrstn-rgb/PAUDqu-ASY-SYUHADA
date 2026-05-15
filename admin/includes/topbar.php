<header class="topbar">
    <div class="topbar-left">
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="page-title">
            <h1><?= $page_title ?? 'Dashboard' ?></h1>
            <p id="currentDate"></p>
        </div>
    </div>
    <div class="topbar-right">
        <div class="admin-profile" id="adminProfile">
            <div class="admin-avatar"><?= strtoupper(substr($_SESSION['admin_user'] ?? 'AD', 0, 2)) ?></div>
            <div class="admin-info">
                <span class="name"><?= htmlspecialchars($_SESSION['admin_user'] ?? 'Admin') ?></span>
                <span class="role">Administrator</span>
            </div>
        </div>
    </div>
</header>
