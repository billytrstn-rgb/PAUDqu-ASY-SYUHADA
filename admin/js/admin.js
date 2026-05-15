// ========== Sidebar Toggle ==========
const sidebar       = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebarToggle');
const overlay       = document.getElementById('sidebarOverlay');

const isMobile = () => window.innerWidth <= 992;

if (sidebarToggle && sidebar && overlay) {
    sidebarToggle.addEventListener('click', () => {
        if (isMobile()) {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        } else {
            sidebar.classList.toggle('collapsed');
        }
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
    });
}

window.addEventListener('resize', () => {
    if (!isMobile() && sidebar) {
        sidebar.classList.remove('active');
        if(overlay) overlay.classList.remove('active');
    }
});

// ========== Active Nav Link ==========
document.querySelectorAll('.sidebar-nav a').forEach(link => {
    link.addEventListener('click', function() {
        if (isMobile() && sidebar) {
            sidebar.classList.remove('active');
            if(overlay) overlay.classList.remove('active');
        }
    });
});

// ========== Count Up Animation ==========
function countUp(el) {
    const target = parseInt(el.dataset.target);
    const duration = 1200;
    const step = target / (duration / 16);
    let current = 0;
    const timer = setInterval(() => {
        current += step;
        if (current >= target) {
            el.textContent = target + (target >= 100 ? '+' : '');
            clearInterval(timer);
        } else {
            el.textContent = Math.floor(current) + (target >= 100 ? '+' : '');
        }
    }, 16);
}

const countEls = document.querySelectorAll('.count-up');
const countObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            countUp(entry.target);
            countObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.5 });
countEls.forEach(el => countObserver.observe(el));

// ========== Current Date ==========
const dateEl = document.getElementById('currentDate');
if (dateEl) {
    const now = new Date();
    const opts = { weekday:'long', day:'numeric', month:'long', year:'numeric' };
    dateEl.textContent = now.toLocaleDateString('id-ID', opts);
}

// ========== Mini Calendar ==========
let calYear = new Date().getFullYear();
let calMonth = new Date().getMonth();

const dayNames = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
const monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
const eventDays = [5, 10, 14, 20, 25]; // sample event days

function renderCalendar() {
    const grid = document.getElementById('calGrid');
    const label = document.getElementById('calMonth');
    if (!grid || !label) return;

    label.textContent = monthNames[calMonth] + ' ' + calYear;

    const firstDay = new Date(calYear, calMonth, 1).getDay();
    const daysInMonth = new Date(calYear, calMonth + 1, 0).getDate();
    const daysInPrev  = new Date(calYear, calMonth, 0).getDate();
    const today = new Date();

    let html = dayNames.map(d => `<div class="cal-day-header">${d}</div>`).join('');

    // Previous month tail
    for (let i = firstDay - 1; i >= 0; i--) {
        html += `<div class="cal-day other-month">${daysInPrev - i}</div>`;
    }

    // Current month
    for (let d = 1; d <= daysInMonth; d++) {
        const isToday = (d === today.getDate() && calMonth === today.getMonth() && calYear === today.getFullYear());
        const hasEvent = eventDays.includes(d);
        html += `<div class="cal-day${isToday ? ' today' : ''}${hasEvent ? ' has-event' : ''}">${d}</div>`;
    }

    // Next month fill
    const total = firstDay + daysInMonth;
    const remaining = total % 7 === 0 ? 0 : 7 - (total % 7);
    for (let d = 1; d <= remaining; d++) {
        html += `<div class="cal-day other-month">${d}</div>`;
    }

    grid.innerHTML = html;
}

document.getElementById('calPrev')?.addEventListener('click', () => {
    calMonth--;
    if (calMonth < 0) { calMonth = 11; calYear--; }
    renderCalendar();
});
document.getElementById('calNext')?.addEventListener('click', () => {
    calMonth++;
    if (calMonth > 11) { calMonth = 0; calYear++; }
    renderCalendar();
});

renderCalendar();

// ========== Toast Notification ==========
function showToast(msg) {
    const toast = document.getElementById('toast');
    if (!toast) return;
    toast.textContent = '🚀 ' + msg;
    toast.style.opacity = '1';
    toast.style.transform = 'translateY(0)';
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(20px)';
    }, 2500);
}

// Topbar buttons demo
document.getElementById('notifBtn')?.addEventListener('click', () => showToast('Tidak ada notifikasi baru'));
document.getElementById('msgBtn')?.addEventListener('click', () => showToast('Kotak pesan kosong'));
document.getElementById('adminProfile')?.addEventListener('click', () => showToast('Fitur profil segera hadir!'));

// ========== Progress Bar Animate ==========
const fills = document.querySelectorAll('.progress-fill');
const progObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const el = entry.target;
            const w = el.style.width;
            el.style.width = '0';
            setTimeout(() => { el.style.width = w; }, 100);
            progObserver.unobserve(el);
        }
    });
}, { threshold: 0.5 });
fills.forEach(f => { progObserver.observe(f); });
