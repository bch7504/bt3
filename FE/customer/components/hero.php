<?php
// FE/customer/components/hero.php
if (!defined('APP_RUNNING')) {
    exit('Truy cập trực tiếp bị nghiêm cấm.');
}
?>
<section class="hero-section text-white py-5 position-relative overflow-hidden">
    <div class="container py-4 position-relative z-index-2">
        <div class="row align-items-center justify-content-center text-center">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3 tracking-wider shadow-sm animate-bounce">
                    <i class="bi bi-bell-fill me-1 text-danger"></i> Cập nhật dữ liệu phạt nguội liên tục 24/7
                </span>
                <h1 class="display-5 fw-extrabold text-uppercase mb-3 text-shadow">
                    Tra Cứu Phương Tiện <br><span class="text-gradient">Vi Phạm Giao Thông</span>
                </h1>
                <p class="lead text-white-75 mb-4 max-w-2xl mx-auto fs-6">
                    Hệ thống tra cứu thông tin quyết định xử phạt hành chính về trật tự an toàn giao thông đường bộ trên toàn quốc. Nhanh chóng, chính xác, bảo mật.
                </p>
                
                <!-- Form Tra Cứu -->
                <div class="search-container p-4 rounded-4 shadow-lg mx-auto bg-blur border border-white border-opacity-15" style="max-width: 600px;">
                    <form action="index.php" method="GET" id="searchForm" class="needs-validation" novalidate>
                        <div class="row g-2">
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-0 text-secondary"><i class="bi bi-car-front-fill fs-5"></i></span>
                                    <input type="text" name="license_plate" id="license_plate_input" class="form-control border-0 py-3 font-monospace text-uppercase fw-bold text-primary placeholder-muted" placeholder="Nhập biển số xe (Ví dụ: 30A-12345)" required autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-warning w-100 py-3 fw-bold rounded-3 shadow d-flex align-items-center justify-content-center gap-2 hover-scale">
                                    <i class="bi bi-search"></i> Tra cứu
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <p class="text-white-50 small mt-3">
                    <i class="bi bi-info-circle me-1"></i> Định dạng hỗ trợ cả biển số có khoảng trắng, dấu gạch ngang (Ví dụ: 30A 12345 hoặc 30A-123.45)
                </p>
            </div>
        </div>
    </div>
</section>
