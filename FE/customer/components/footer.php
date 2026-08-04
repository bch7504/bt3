<?php
// FE/customer/components/footer.php
if (!defined('APP_RUNNING')) {
    exit('Truy cập trực tiếp bị nghiêm cấm.');
}
?>
<footer class="footer bg-dark-blue text-white-50 py-4 mt-auto border-top border-white border-opacity-10">
    <div class="container">
        <div class="row align-items-center justify-content-between text-center text-md-start">
            <div class="col-md-6 mb-3 mb-md-0">
                <a class="d-flex align-items-center gap-2 fw-bold text-uppercase text-white text-decoration-none justify-content-center justify-content-md-start mb-2" href="index.php">
                    <i class="bi bi-shield-shaded text-warning fs-4"></i>
                    <span>Cổng tra cứu vi phạm</span>
                </a>
                <p class="small mb-0 text-white-50">Hệ thống đồng bộ dữ liệu phạt nguội trực tiếp từ Cục Cảnh sát giao thông đường bộ.</p>
            </div>
            <div class="col-md-6 text-center text-md-end small">
                <p class="mb-1">&copy; <?php echo date('Y'); ?> Cục Cảnh sát giao thông. Toàn quyền bảo lưu.</p>
                <p class="mb-0 text-white-30">Phát triển bởi Đội Kỹ thuật nghiệp vụ.</p>
            </div>
        </div>
    </div>
</footer>
