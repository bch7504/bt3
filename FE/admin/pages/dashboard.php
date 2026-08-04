<?php
// FE/admin/pages/dashboard.php
if (!defined('APP_RUNNING')) {
    exit();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark text-uppercase mb-1">Bảng điều khiển</h3>
        <p class="text-muted small mb-0">Hệ thống tổng hợp và giám sát vi phạm giao thông đường bộ</p>
    </div>
    <div class="text-muted small">
        <i class="bi bi-clock-fill text-warning me-1"></i>
        Hôm nay: <span id="current-date">--/--/----</span>
    </div>
</div>

<!-- Thống kê dạng Card -->
<div class="row g-3 mb-4">
    <!-- Card 1: Tổng phương tiện -->
    <div class="col-md-6 col-lg-3">
        <div class="card card-stat bg-white border-start border-primary border-4 p-3 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase fw-semibold small">Tổng phương tiện</span>
                    <h3 class="fw-bold text-dark mt-2 mb-0" id="stat-vehicles">...</h3>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                    <i class="bi bi-car-front-fill fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Tổng vi phạm -->
    <div class="col-md-6 col-lg-3">
        <div class="card card-stat bg-white border-start border-danger border-4 p-3 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase fw-semibold small">Tổng vi phạm</span>
                    <h3 class="fw-bold text-dark mt-2 mb-0" id="stat-violations">...</h3>
                </div>
                <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle">
                    <i class="bi bi-exclamation-octagon-fill fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Vi phạm chưa xử lý -->
    <div class="col-md-6 col-lg-3">
        <div class="card card-stat bg-white border-start border-warning border-4 p-3 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase fw-semibold small">Chưa nộp phạt</span>
                    <h3 class="fw-bold text-dark mt-2 mb-0" id="stat-unpaid">...</h3>
                </div>
                <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                    <i class="bi bi-credit-card-2-back-fill fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Tổng thông báo -->
    <div class="col-md-6 col-lg-3">
        <div class="card card-stat bg-white border-start border-success border-4 p-3 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase fw-semibold small">Tổng thông báo</span>
                    <h3 class="fw-bold text-dark mt-2 mb-0" id="stat-notifications">...</h3>
                </div>
                <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                    <i class="bi bi-chat-left-dots-fill fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Danh sách 10 vi phạm mới nhất -->
<div class="card border-0 rounded-4 shadow-sm">
    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
        <h5 class="card-title fw-bold text-dark mb-0 text-uppercase d-flex align-items-center gap-2">
            <i class="bi bi-clock-history text-danger"></i> 10 vi phạm mới nhất
        </h5>
        <a href="index.php?page=violations" class="btn btn-outline-primary btn-sm px-3 rounded-pill">Xem tất cả</a>
    </div>
    <div class="card-body p-0" id="recent-violations-container">
        <!-- Spinner đang tải bảng -->
        <div class="text-center py-5 text-muted" id="recent-loading">
            <div class="spinner-border text-danger" role="status"></div>
            <p class="mb-0 mt-2 small">Đang tải danh sách vi phạm gần đây...</p>
        </div>
        
        <div class="table-responsive d-none" id="recent-table-wrapper">
            <table class="table table-hover align-middle mb-0" style="border-collapse: collapse;">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="ps-4">Biển số</th>
                        <th scope="col">Ngày vi phạm</th>
                        <th scope="col">Địa điểm</th>
                        <th scope="col">Tỉnh/Thành</th>
                        <th scope="col">Mô tả hành vi</th>
                        <th scope="col">Số quyết định</th>
                        <th scope="col" class="pe-4 text-center">Trạng thái</th>
                    </tr>
                </thead>
                <tbody id="recent-violations-list">
                    <!-- Sẽ được thêm bằng JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tải dữ liệu dashboard bằng Fetch API -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Hiển thị ngày hôm nay ở client
    const today = new Date();
    const day = String(today.getDate()).padStart(2, '0');
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const year = today.getFullYear();
    document.getElementById("current-date").textContent = `${day}/${month}/${year}`;

    // Gọi API Dashboard
    fetch('../../BE/dashboard.php')
        .then(response => {
            if (response.status === 401) {
                // Hết hạn đăng nhập
                window.location.href = 'login.php';
                return;
            }
            if (!response.ok) {
                throw new Error("Không thể kết nối đến API Dashboard.");
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Điền thống kê
                document.getElementById("stat-vehicles").textContent = new Intl.NumberFormat().format(data.stats.total_vehicles);
                document.getElementById("stat-violations").textContent = new Intl.NumberFormat().format(data.stats.total_violations);
                document.getElementById("stat-unpaid").textContent = new Intl.NumberFormat().format(data.stats.unpaid_violations);
                document.getElementById("stat-notifications").textContent = new Intl.NumberFormat().format(data.stats.total_notifications);

                // Render bảng vi phạm
                renderRecentViolations(data.recent_violations);
            }
        })
        .catch(error => {
            console.error("Dashboard error:", error);
            // Hiện thông báo lỗi
            document.getElementById("recent-loading").innerHTML = `
                <div class="p-4 text-danger small">
                    <i class="bi bi-exclamation-circle-fill me-2"></i> Lỗi tải dữ liệu thống kê. Vui lòng thử lại.
                </div>
            `;
        });

    function renderRecentViolations(violations) {
        const loading = document.getElementById("recent-loading");
        const wrapper = document.getElementById("recent-table-wrapper");
        const tbody = document.getElementById("recent-violations-list");

        loading.classList.add("d-none");

        if (violations.length === 0) {
            document.getElementById("recent-violations-container").innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-check-circle-fill text-success fs-1 mb-2"></i>
                    <p class="mb-0">Hiện chưa ghi nhận hành vi vi phạm nào.</p>
                </div>
            `;
            return;
        }

        let html = "";
        violations.forEach(v => {
            const statusLower = (v.status || "").toLowerCase();
            let statusBadge = "";

            if (statusLower === 'đã nộp phạt' || statusLower === 'da nop phat' || statusLower === 'paid' || statusLower === 'đã xử lý' || statusLower === 'đã nộp') {
                statusBadge = '<span class="badge bg-success rounded-pill px-2.5 py-1.5"><i class="bi bi-check-circle-fill me-1"></i> Đã nộp</span>';
            } else if (statusLower === 'chưa nộp phạt' || statusLower === 'chua nop phat' || statusLower === 'unpaid' || statusLower === 'chưa xử lý' || statusLower === 'chưa nộp') {
                statusBadge = '<span class="badge bg-danger rounded-pill px-2.5 py-1.5"><i class="bi bi-x-circle-fill me-1"></i> Chưa nộp</span>';
            } else {
                statusBadge = `<span class="badge bg-warning text-dark rounded-pill px-2.5 py-1.5"><i class="bi bi-exclamation-triangle-fill me-1"></i> ${v.status || 'Chưa rõ'}</span>`;
            }

            const vDate = formatDate(v.violated_at);
            const vTime = formatTime(v.violated_at);

            html += `
                <tr>
                    <td class="ps-4">
                        <span class="text-primary font-monospace bg-light px-2.5 py-1 rounded border fw-bold text-uppercase fs-7">
                            ${v.license_plate || 'Chưa rõ'}
                        </span>
                    </td>
                    <td>
                        <div class="fw-semibold">${vDate}</div>
                        <div class="text-muted small">${vTime}</div>
                    </td>
                    <td class="small text-secondary">${v.location || 'Chưa rõ'}</td>
                    <td class="fw-semibold text-dark">${v.province || 'Chưa rõ'}</td>
                    <td class="small text-dark fw-medium">${v.description || 'Chưa rõ'}</td>
                    <td class="font-monospace text-uppercase small text-dark fw-bold">${v.decision_no || 'Chưa có'}</td>
                    <td class="pe-4 text-center">${statusBadge}</td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
        wrapper.classList.remove("d-none");
    }

    function formatDate(dateStr) {
        if (!dateStr) return "Chưa rõ";
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        const day = String(d.getDate()).padStart(2, '0');
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const year = d.getFullYear();
        return `${day}/${month}/${year}`;
    }

    function formatTime(dateStr) {
        if (!dateStr) return "";
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return "";
        const hours = String(d.getHours()).padStart(2, '0');
        const minutes = String(d.getMinutes()).padStart(2, '0');
        return `${hours}:${minutes}`;
    }
});
</script>
