            </div> <!-- Fermeture du container-fluid -->
        </div> <!-- Fermeture du layout Content -->
    </div> <!-- Fermeture du wrapper parent -->

    <!-- Scripts Javascript (Bootstrap, JQuery) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- GESTION SIDEBAR ---
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            const btn = document.getElementById('sidebarCollapse');

            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed && window.innerWidth > 768) {
                sidebar.classList.add('collapsed');
                if(content) content.classList.add('full-width');
            }

            if(btn) {
                btn.addEventListener('click', function() {
                    if (window.innerWidth > 768) {
                        sidebar.classList.toggle('collapsed');
                        if(content) content.classList.toggle('full-width');
                        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
                    } else {
                        sidebar.classList.toggle('active');
                    }
                });
            }

            // --- GESTION MODE NUIT ---
            const themeSwitch = document.getElementById('themeSwitch');
            const body = document.body;
            const themeIcon = themeSwitch ? themeSwitch.querySelector('i') : null;

            // Appliquer le thème sauvegardé
            const currentTheme = localStorage.getItem('theme') || 'light';
            if (currentTheme === 'dark') {
                body.classList.add('dark-theme');
                if(themeIcon) {
                    themeIcon.classList.remove('fa-moon');
                    themeIcon.classList.add('fa-sun');
                }
            }

            if(themeSwitch) {
                themeSwitch.addEventListener('click', function() {
                    body.classList.toggle('dark-theme');
                    const isDark = body.classList.contains('dark-theme');
                    localStorage.setItem('theme', isDark ? 'dark' : 'light');
                    
                    if(themeIcon) {
                        if(isDark) {
                            themeIcon.classList.remove('fa-moon');
                            themeIcon.classList.add('fa-sun');
                        } else {
                            themeIcon.classList.remove('fa-sun');
                            themeIcon.classList.add('fa-moon');
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
