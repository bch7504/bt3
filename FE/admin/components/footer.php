<?php
// FE/admin/components/footer.php
?>
        </div> <!-- End of content-body -->

        <!-- Simple Footer -->
        <footer class="footer bg-white border-top py-3 text-center mt-auto">
            <span class="text-muted small">&copy; <?php echo date('Y'); ?> Hệ thống Quản trị Cục Cảnh sát giao thông. Toàn quyền bảo lưu.</span>
        </footer>

    </div> <!-- End of main-wrapper -->

    <!-- Bootstrap 5.3 Bundle JS with Popper from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Sidebar Toggle Script for Mobile -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('adminSidebar');
            const wrapper = document.getElementById('mainWrapper');

            if (toggleBtn && sidebar && wrapper) {
                toggleBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    sidebar.classList.toggle('active');
                    wrapper.classList.toggle('active');
                });

                // Close sidebar when clicking outside on mobile
                document.addEventListener('click', function (e) {
                    if (window.innerWidth < 992) {
                        if (!sidebar.contains(e.target) && e.target !== toggleBtn && !toggleBtn.contains(e.target)) {
                            sidebar.classList.remove('active');
                            wrapper.classList.remove('active');
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
