document.addEventListener('DOMContentLoaded', () => {
    // Theme Toggle Logic
    const themeToggles = document.querySelectorAll('.theme-toggle, #theme-toggle');
    const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
    
    // Check for saved theme preference or use system preference
    const currentTheme = localStorage.getItem('theme') || (prefersDarkScheme.matches ? 'dark' : 'light');
    
    if (currentTheme === 'light') {
        document.documentElement.setAttribute('data-theme', 'light');
        updateToggleIcons('light');
    } else {
        document.documentElement.setAttribute('data-theme', 'dark');
        updateToggleIcons('dark');
    }
    
    themeToggles.forEach(toggle => {
        toggle.addEventListener('click', () => {
            let theme = document.documentElement.getAttribute('data-theme');
            let newTheme = theme === 'light' ? 'dark' : 'light';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateToggleIcons(newTheme);
        });
    });
    
    function updateToggleIcons(theme) {
        themeToggles.forEach(toggle => {
            const icon = toggle.querySelector('i');
            if (icon) {
                if (theme === 'light') {
                    icon.classList.remove('fa-sun');
                    icon.classList.add('fa-moon');
                } else {
                    icon.classList.remove('fa-moon');
                    icon.classList.add('fa-sun');
                }
            }
        });
    }

    // Sidebar Toggle Logic (Mobile)
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const sidebar = document.querySelector('.dashboard-sidebar');
    const navLinks = document.querySelector('.nav-links');
    
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', () => {
            if (sidebar) {
                sidebar.classList.toggle('active');
            }
            if (navLinks) {
                navLinks.classList.toggle('active');
            }
        });
    }

    // SPA View Switching Logic
    const viewLinks = document.querySelectorAll('.sidebar-menu a[data-target]');
    const views = document.querySelectorAll('.view-section');

    if (viewLinks.length > 0 && views.length > 0) {
        viewLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = link.getAttribute('data-target');
                
                // Handle active class on sidebar
                document.querySelectorAll('.sidebar-menu li').forEach(li => li.classList.remove('active'));
                link.closest('li').classList.add('active');

                // Hide all views
                views.forEach(view => {
                    view.style.display = 'none';
                    view.classList.remove('active');
                });

                // Show target view
                const targetView = document.getElementById('view-' + targetId);
                if (targetView) {
                    targetView.style.display = 'block';
                    // Small delay to allow display:block to apply before adding opacity class
                    setTimeout(() => {
                        targetView.classList.add('active');
                    }, 10);
                }
            });
        });
    }

    // ── FAQ Accordion ──────────────────────────────────────────────────────
    document.querySelectorAll('.faq-question').forEach(btn => {
        btn.addEventListener('click', () => {
            const item = btn.closest('.faq-item');
            const answer = item.querySelector('.faq-answer');
            const isOpen = item.classList.contains('open');

            // Close all open items
            document.querySelectorAll('.faq-item.open').forEach(openItem => {
                openItem.classList.remove('open');
                openItem.querySelector('.faq-answer').classList.remove('open');
                openItem.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
            });

            // Toggle clicked item
            if (!isOpen) {
                item.classList.add('open');
                answer.classList.add('open');
                btn.setAttribute('aria-expanded', 'true');
            }
        });
    });

    // ── Pricing Tab Switching ──────────────────────────────────────────────
    const tabBtns = document.querySelectorAll('.pricing-tabs .tab-btn');
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            tabBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        });
    });

    // ── Smooth scroll for anchor links ────────────────────────────────────
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', e => {
            const target = document.querySelector(anchor.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // ── Navbar scroll shadow ───────────────────────────────────────────────
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                navbar.style.boxShadow = '0 4px 30px rgba(0,0,0,0.2)';
                navbar.style.backdropFilter = 'blur(20px)';
            } else {
                navbar.style.boxShadow = '';
            }
        });
    }
});
