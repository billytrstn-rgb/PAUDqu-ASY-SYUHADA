/**
 * PAUDqu ASY SYUHADA
 * Main Interactions & Animations
 */

document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Mobile Menu Drawer Logic
    const menuToggle = document.getElementById('mobile-menu');
    const mobileDrawer = document.getElementById('mobile-drawer');
    const overlay = document.getElementById('mobile-overlay');
    const drawerClose = document.getElementById('drawer-close');
    const drawerLinks = document.querySelectorAll('.drawer-links a');

    const toggleDrawer = (isOpen) => {
        mobileDrawer?.classList.toggle('open', isOpen);
        overlay?.classList.toggle('active', isOpen);
        document.body.style.overflow = isOpen ? 'hidden' : '';
    };

    menuToggle?.addEventListener('click', () => toggleDrawer(true));
    drawerClose?.addEventListener('click', () => toggleDrawer(false));
    overlay?.addEventListener('click', () => toggleDrawer(false));

    drawerLinks.forEach(link => {
        link.addEventListener('click', () => toggleDrawer(false));
    });

    // 2. Navbar Scroll Effect
    const navbar = document.querySelector('.navbar');
    const handleScroll = () => {
        if (window.scrollY > 50) {
            navbar?.classList.add('scrolled');
        } else {
            navbar?.classList.remove('scrolled');
        }
    };

    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll(); // Initial check

    // 3. Reveal on Scroll Animation (Intersection Observer)
    const reveals = document.querySelectorAll('.reveal');
    const revealOptions = {
        threshold: 0.15,
        rootMargin: '0px 0px -50px 0px'
    };

    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                revealObserver.unobserve(entry.target);
            }
        });
    }, revealOptions);

    reveals.forEach(el => revealObserver.observe(el));

    // 4. Smooth Scrolling for Anchor Links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            e.preventDefault();
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                const navHeight = 80;
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - navHeight;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // 5. Counter Animation for Stats (Optional - adds extra flair)
    const stats = document.querySelectorAll('.stat-box h2');
    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = entry.target;
                const endValue = parseInt(target.textContent);
                let startValue = 0;
                const duration = 2000;
                const increment = endValue / (duration / 16);
                
                const updateCounter = () => {
                    startValue += increment;
                    if (startValue < endValue) {
                        target.textContent = Math.round(startValue) + '+';
                        requestAnimationFrame(updateCounter);
                    } else {
                        target.textContent = endValue + '+';
                    }
                };
                
                updateCounter();
                statsObserver.unobserve(target);
            }
        });
    }, { threshold: 0.5 });

    stats.forEach(stat => statsObserver.observe(stat));

});