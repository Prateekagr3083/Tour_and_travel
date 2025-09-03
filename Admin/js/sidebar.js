// Admin Sidebar JavaScript
(function() {
    'use strict';

    // Initialize sidebar functionality
    function initSidebar() {
        // Add smooth scrolling for sidebar links
        setupSidebarNavigation();

        // Add responsive behavior
        setupResponsiveSidebar();

        // Add keyboard navigation
        setupKeyboardNavigation();
    }

    // Setup sidebar navigation
    function setupSidebarNavigation() {
        const sidebarLinks = document.querySelectorAll('.sidebar .nav-link');

        sidebarLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Remove active class from all items
                document.querySelectorAll('.sidebar .nav-item').forEach(item => {
                    item.classList.remove('active');
                });

                // Add active class to clicked item
                this.closest('.nav-item').classList.add('active');

                // Store active page in session storage
                const pageName = this.getAttribute('href');
                if (pageName && pageName !== '#') {
                    sessionStorage.setItem('activeAdminPage', pageName);
                }
            });
        });
    }

    // Setup responsive sidebar behavior
    function setupResponsiveSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const adminContainer = document.querySelector('.admin-container');

        if (!sidebar || !adminContainer) return;

        // Toggle sidebar on mobile
        function toggleSidebar() {
            const isMobile = window.innerWidth <= 768;
            if (isMobile) {
                sidebar.classList.toggle('sidebar-collapsed');
            }
        }

        // Add toggle button for mobile (if not exists)
        if (window.innerWidth <= 768 && !document.querySelector('.sidebar-toggle')) {
            const toggleBtn = document.createElement('button');
            toggleBtn.className = 'sidebar-toggle';
            toggleBtn.innerHTML = '☰';
            toggleBtn.style.cssText = `
                position: fixed;
                top: 10px;
                left: 10px;
                z-index: 1000;
                background: #667eea;
                color: white;
                border: none;
                padding: 10px;
                border-radius: 5px;
                cursor: pointer;
                display: none;
            `;

            document.body.appendChild(toggleBtn);
            toggleBtn.addEventListener('click', toggleSidebar);
            toggleBtn.style.display = 'block';
        }

        // Handle window resize
        window.addEventListener('resize', function() {
            const toggleBtn = document.querySelector('.sidebar-toggle');
            if (window.innerWidth <= 768) {
                if (toggleBtn) toggleBtn.style.display = 'block';
                sidebar.classList.add('sidebar-collapsed');
            } else {
                if (toggleBtn) toggleBtn.style.display = 'none';
                sidebar.classList.remove('sidebar-collapsed');
            }
        });
    }

    // Setup keyboard navigation
    function setupKeyboardNavigation() {
        const sidebarItems = document.querySelectorAll('.sidebar .nav-item');

        sidebarItems.forEach((item, index) => {
            item.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    const link = item.querySelector('.nav-link');
                    if (link) link.click();
                }

                // Arrow key navigation
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    const nextItem = sidebarItems[index + 1] || sidebarItems[0];
                    nextItem.focus();
                }

                if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    const prevItem = sidebarItems[index - 1] || sidebarItems[sidebarItems.length - 1];
                    prevItem.focus();
                }
            });
        });
    }

    // Restore active page from session storage
    function restoreActivePage() {
        const activePage = sessionStorage.getItem('activeAdminPage');
        if (activePage) {
            const currentPage = window.location.pathname.split('/').pop();
            if (activePage.includes(currentPage)) {
                const activeLink = document.querySelector(`.sidebar .nav-link[href="${activePage}"]`);
                if (activeLink) {
                    activeLink.closest('.nav-item').classList.add('active');
                }
            }
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initSidebar();
            restoreActivePage();
        });
    } else {
        initSidebar();
        restoreActivePage();
    }

    // Expose functions globally if needed
    window.SidebarManager = {
        toggle: function() {
            const sidebar = document.querySelector('.sidebar');
            if (sidebar) sidebar.classList.toggle('sidebar-collapsed');
        },
        collapse: function() {
            const sidebar = document.querySelector('.sidebar');
            if (sidebar) sidebar.classList.add('sidebar-collapsed');
        },
        expand: function() {
            const sidebar = document.querySelector('.sidebar');
            if (sidebar) sidebar.classList.remove('sidebar-collapsed');
        }
    };

})();
