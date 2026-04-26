// ========== Mobile Drawer ==========
const menuToggle   = document.getElementById('mobile-menu');
const mobileDrawer = document.getElementById('mobile-drawer');
const overlay      = document.getElementById('mobile-overlay');
const drawerClose  = document.getElementById('drawer-close');

function openDrawer() {
    mobileDrawer.classList.add('open');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeDrawer() {
    mobileDrawer.classList.remove('open');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
}

menuToggle?.addEventListener('click', openDrawer);
drawerClose?.addEventListener('click', closeDrawer);
overlay?.addEventListener('click', closeDrawer);

// Close drawer when a nav link is tapped
document.querySelectorAll('.drawer-links a').forEach(link => {
    link.addEventListener('click', closeDrawer);
});

// ========== Reveal on Scroll ==========
const reveals = document.querySelectorAll('.reveal');

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('active');
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.12 });

reveals.forEach(el => observer.observe(el));
