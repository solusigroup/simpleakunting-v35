    </main> <!-- Penutup .p-4 -->
    
    <footer class="footer mt-auto py-3 text-muted text-center">
        <div class="container">
            <span>
                &copy; <?php echo date('Y'); ?> - SIMPLE AKUNTING created by <a href="https://solusiconsulting.simpleakunting.biz.id" target="_blank" class="fw-bold text-decoration-none">Kurniawan</a>
            </span>
        </div>
    </footer>

</div> <!-- Penutup .main-wrapper -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const themeToggle = document.getElementById('theme-toggle');
            const body = document.body;
            const themeIcon = document.getElementById('theme-icon');
            
            // Sidebar Toggle (Mobile & Desktop)
            const savedSidebar = localStorage.getItem('sidebar-collapsed') === 'true';
            if (savedSidebar && window.innerWidth >= 992) {
                body.classList.add('sidebar-collapsed');
            }

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    if (window.innerWidth < 992) {
                        body.classList.toggle('sidebar-open');
                    } else {
                        body.classList.toggle('sidebar-collapsed');
                        localStorage.setItem('sidebar-collapsed', body.classList.contains('sidebar-collapsed'));
                    }
                });
            }

            // Theme Toggle
            const savedTheme = localStorage.getItem('theme') || 'light';
            setTheme(savedTheme);

            if (themeToggle) {
                themeToggle.addEventListener('click', () => {
                    const currentTheme = body.getAttribute('data-theme');
                    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                    setTheme(newTheme);
                });
            }

            function setTheme(theme) {
                body.setAttribute('data-theme', theme);
                localStorage.setItem('theme', theme);
                if (themeIcon) {
                    themeIcon.className = theme === 'light' ? 'bi bi-moon-fill fs-5' : 'bi bi-sun-fill fs-5';
                }
            }
            
            // Auto-hide sidebar on small screens after clicking link
            const sidebarLinks = document.querySelectorAll('.sidebar a.nav-link:not([data-bs-toggle])');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 992) {
                        body.classList.remove('sidebar-open');
                    }
                });
            });
        });
    </script>
</body>
</html>

