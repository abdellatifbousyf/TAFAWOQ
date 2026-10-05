// ============================================
// Tafawoq - Main JavaScript
// ============================================

document.addEventListener('DOMContentLoaded', function () {

    // === Mobile Menu Toggle ===
    const menuToggle = document.getElementById('menuToggle');
    const navLinks = document.getElementById('navLinks');

    if (menuToggle) {
        menuToggle.addEventListener('click', function () {
            navLinks.classList.toggle('open');
            this.classList.toggle('active');
        });
    }

    // Close menu on link click
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', () => {
            navLinks.classList.remove('open');
            menuToggle.classList.remove('active');
        });
    });

    // === Counter Animation ===
    const counters = document.querySelectorAll('.stat-number[data-count]');

    function animateCounters() {
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-count'));
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;

            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                counter.textContent = Math.floor(current).toLocaleString('ar-MA');
            }, 16);
        });
    }

    // Intersection Observer for counters
    if (counters.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(c => observer.observe(c));
    }

    // === Smooth Scroll ===
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // === Search Form Validation ===
    const searchForms = document.querySelectorAll('.hero-search form, .search-box form');
    searchForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            const input = this.querySelector('input[type="text"]');
            if (input && input.value.trim().length < 2) {
                e.preventDefault();
                input.style.borderColor = '#db4437';
                input.setAttribute('placeholder', 'الرجاء إدخال كلمتين على الأقل...');
                setTimeout(() => {
                    input.style.borderColor = '';
                }, 2000);
            }
        });
    });

    // === Confirm Delete ===
    document.querySelectorAll('.btn-delete, [data-confirm]').forEach(btn => {
        btn.addEventListener('click', function (e) {
            const msg = this.getAttribute('data-confirm') || 'هل أنت متأكد من الحذف؟';
            if (!confirm(msg)) {
                e.preventDefault();
            }
        });
    });

    // === File Upload Preview ===
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function () {
            const label = this.parentElement.querySelector('.file-name');
            if (label && this.files.length > 0) {
                label.textContent = this.files[0].name;
            }
        });
    });

    // === Auto-hide Alerts ===
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            alert.style.transition = 'all 0.5s ease';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });

    // === Filter Form Auto-Submit ===
    const filterSelects = document.querySelectorAll('.filter-select');
    filterSelects.forEach(select => {
        select.addEventListener('change', function () {
            const form = this.closest('form');
            if (form) form.submit();
        });
    });

    // === Scroll to Top Button ===
    const scrollBtn = document.createElement('button');
    scrollBtn.innerHTML = '⬆';
    scrollBtn.className = 'scroll-top-btn';
    scrollBtn.style.cssText = `
        position: fixed;
        bottom: 30px;
        left: 30px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--primary, #1a73e8);
        color: white;
        border: none;
        font-size: 1.3rem;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        display: none;
        z-index: 999;
        transition: all 0.3s ease;
    `;
    document.body.appendChild(scrollBtn);

    window.addEventListener('scroll', () => {
        scrollBtn.style.display = window.scrollY > 400 ? 'block' : 'none';
    });

    scrollBtn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

});