<?php
// pages/home.php
if (!defined('APP_RUNNING')) {
    exit('Truy cập trực tiếp bị nghiêm cấm.');
}
?>

<div class="row justify-content-center">
    <div class="col-lg-8 text-center py-4">
        <!-- Chào mừng & Thống kê nhanh hoặc Mẹo an toàn -->
        <div class="card card-premium shadow-sm border-0 p-4 mb-4">
            <div class="card-body">
                <div class="mb-3 text-warning">
                    <i class="bi bi-shield-check fs-1"></i>
                </div>
                <h4 class="fw-bold text-dark text-uppercase">Cổng Tra Cứu Phạt Nguội Quốc Gia</h4>
                <p class="text-secondary small mx-auto max-width-600">
                    Chào mừng bạn đến với Cổng thông tin điện tử hỗ trợ người dân tự tra cứu và kiểm tra lịch sử chấp hành luật an toàn giao thông của phương tiện đường bộ. Hệ thống kết nối cơ sở dữ liệu tập trung đảm bảo tính chính xác và minh bạch 100%.
                </p>
                <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
                    <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill"><i class="bi bi-shield-shaded me-1"></i> Chính xác 100%</span>
                    <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill"><i class="bi bi-lightning-charge-fill text-warning me-1"></i> Tra cứu tức thời</span>
                    <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill"><i class="bi bi-fingerprint me-1"></i> Tuyệt đối bảo mật</span>
                </div>
            </div>
        </div>
        
        <!-- Mẹo nhanh cho người lái xe -->
        <div class="alert alert-warning border-0 bg-warning bg-opacity-10 text-dark-blue small p-3 rounded-4 d-inline-flex align-items-center gap-2" role="alert">
            <i class="bi bi-info-circle-fill fs-5 text-warning"></i>
            <span class="text-start"><strong>Lưu ý:</strong> Vui lòng nhập đúng biển kiểm soát để nhận thông tin đăng kiểm chính xác nhất.</span>
        </div>
    </div>
</div>
