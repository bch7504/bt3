<?php
// FE/customer/components/navbar.php
if (!defined('APP_RUNNING')) {
    exit('Truy cập trực tiếp bị nghiêm cấm.');
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark-blue sticky-top shadow-sm py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-uppercase" href="index.php">
            <i class="bi bi-shield-shaded text-warning fs-3 animate-pulse"></i>
            <span>Tra cứu vi phạm <span class="text-warning">Giao thông</span></span>
        </a>
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center gap-2">
                <li class="nav-item">
                    <a class="nav-link active px-3 rounded-pill transition-all" href="index.php">
                        <i class="bi bi-house-door-fill me-1"></i> Trang chủ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 rounded-pill text-white-50 hover-text-warning transition-all" href="index.php?page=guide">
                        <i class="bi bi-info-circle-fill me-1"></i> Hướng dẫn
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 rounded-pill text-white-50 hover-text-warning transition-all" href="../admin/login.php">
                        <i class="bi bi-person-lock me-1"></i> Quản trị
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
