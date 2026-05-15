// 1. Fungsi Menu Mobile (Drawer)
const menuToggle = document.getElementById('mobile-menu');
const mobileDrawer = document.getElementById('mobile-drawer');
const overlay = document.getElementById('mobile-overlay');
const drawerClose = document.getElementById('drawer-close');

const openDrawer = () => {
    mobileDrawer?.classList.add('open');
    overlay?.classList.add('active');
    document.body.style.overflow = 'hidden';
};

const closeDrawer = () => {
    mobileDrawer?.classList.remove('open');
    overlay?.classList.remove('active');
    document.body.style.overflow = '';
};

menuToggle?.addEventListener('click', openDrawer);
drawerClose?.addEventListener('click', closeDrawer);
overlay?.addEventListener('click', closeDrawer);

document.querySelectorAll('.drawer-links a').forEach(link => {
    link.addEventListener('click', closeDrawer);
});

// 2. Animasi Reveal (Muncul saat Scroll)
const reveals = document.querySelectorAll('.reveal');

const observerOptions = {
    threshold: 0.12
};

const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('active');
            revealObserver.unobserve(entry.target);
        }
    });
}, observerOptions);

reveals.forEach(el => revealObserver.observe(el));

// 3. Efek Sticky Navbar
const navbar = document.querySelector('.navbar');
window.addEventListener('scroll', () => {
    if (window.scrollY > 80) {
        if (navbar) {
            navbar.style.padding = '8px 20px';
            navbar.style.top = '10px';
            navbar.style.width = '95%';
        }
    } else {
        if (navbar) {
            navbar.style.padding = '10px 20px';
            navbar.style.top = '20px';
            navbar.style.width = '90%';
        }
    }
});