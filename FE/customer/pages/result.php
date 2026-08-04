<?php
// FE/customer/pages/result.php
if (!defined('APP_RUNNING')) {
    exit('Truy cập trực tiếp bị nghiêm cấm.');
}

$license_plate = isset($_GET['license_plate']) ? trim($_GET['license_plate']) : '';
?>

<!-- Premium Custom CSS Styles -->
<style>
    .result-container {
        animation: fadeInUp 0.6s ease-out;
    }
    
    .card-premium {
        border-radius: 20px !important;
        border: 1px solid rgba(0, 0, 0, 0.04) !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04) !important;
        background: #ffffff;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card-premium:hover {
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08) !important;
    }
    
    /* Biển số xe thiết kế 3D chân thực */
    .plate-emblem {
        display: inline-block;
        background: #ffffff;
        color: #1a2530;
        border: 2.5px solid #1a2530;
        border-radius: 8px;
        padding: 6px 20px;
        font-family: 'Outfit', 'Inter', 'Segoe UI', sans-serif;
        font-weight: 800;
        font-size: 1.35rem;
        letter-spacing: 2px;
        text-transform: uppercase;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1), inset 0 1px 3px rgba(0,0,0,0.08);
        position: relative;
        text-align: center;
        min-width: 170px;
    }
    .plate-emblem::before {
        content: '';
        position: absolute;
        top: 4px;
        left: 50%;
        transform: translateX(-50%);
        width: 6px;
        height: 6px;
        background: #6c757d;
        border-radius: 50%;
    }
    
    /* Khối thông tin chi tiết xe */
    .info-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    .info-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 15px;
        border-radius: 12px;
        background-color: #f8f9fa;
        border: 1px solid rgba(0,0,0,0.02);
    }
    .info-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #6c757d;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .info-value {
        font-size: 0.95rem;
        font-weight: 700;
        color: #212529;
    }
    
    /* Thẻ vi phạm kiểu mới (Violation Card) */
    .violation-card-item {
        border-radius: 16px;
        border: 1px solid rgba(0, 0, 0, 0.05);
        background: #ffffff;
        margin-bottom: 18px;
        transition: all 0.3s ease;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
    }
    .violation-card-item:hover {
        border-color: rgba(220, 53, 69, 0.2);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(220, 53, 69, 0.06);
    }
    .violation-card-item.paid:hover {
        border-color: rgba(40, 167, 69, 0.2);
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.06);
    }
    
    .violation-card-header {
        padding: 16px 20px;
        background-color: #fff8f8;
        border-bottom: 1px solid rgba(220, 53, 69, 0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    .violation-card-item.paid .violation-card-header {
        background-color: #f8fff9;
        border-bottom: 1px solid rgba(40, 167, 69, 0.05);
    }
    
    .violation-card-body {
        padding: 20px;
    }
    
    .violation-meta-item {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
        font-size: 0.9rem;
    }
    .violation-meta-label {
        font-weight: 600;
        color: #6c757d;
        min-width: 130px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .violation-meta-value {
        color: #212529;
        flex: 1;
    }
    
    /* Hiệu ứng Fade In */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="container my-5 result-container">
    <!-- Spinner trạng thái đang tải dữ liệu -->
    <div id="loading-state" class="text-center py-5 my-5">
        <div class="spinner-border text-primary" role="status" style="width: 3.5rem; height: 3.5rem; border-width: 0.25em;"></div>
        <div class="text-muted mt-3 fw-semibold">Đang truy vấn dữ liệu từ Cục Cảnh sát giao thông...</div>
    </div>

    <!-- Khung chứa thông báo lỗi / không tìm thấy phương tiện -->
    <div id="error-state" class="d-none animate-fade-in">
        <div class="card card-premium shadow border-0 p-4 text-center my-5">
            <div class="card-body">
                <div class="mb-4">
                    <span class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger rounded-circle p-4" style="width: 80px; height: 80px;">
                        <i class="bi bi-exclamation-triangle-fill fs-1" id="error-icon"></i>
                    </span>
                </div>
                <h4 class="fw-bold text-dark mb-2" id="error-title">Không tìm thấy phương tiện!</h4>
                <p class="text-secondary max-width-600 mx-auto mb-4" id="error-msg"></p>
                <a href="index.php" class="btn btn-dark px-4 rounded-pill">Quay lại tìm kiếm</a>
            </div>
        </div>
    </div>

    <!-- Khung chứa kết quả chính -->
    <div id="result-state" class="row g-4 d-none">
        <!-- Cột trái: Card thông tin phương tiện -->
        <div class="col-lg-4">
            <div class="card card-premium shadow-sm border-0 h-100">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <span class="text-muted small d-block mb-2 text-uppercase fw-semibold tracking-wider">Biển kiểm soát tra cứu</span>
                        <div class="plate-emblem" id="lbl-license-plate"></div>
                    </div>
                    
                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="bi bi-info-circle text-primary me-2"></i>Thông tin xe
                    </h5>
                    
                    <div class="info-list">
                        <div class="info-row">
                            <span class="info-label"><i class="bi bi-person text-secondary"></i> Chủ sở hữu</span>
                            <span class="info-value text-uppercase" id="lbl-owner-name"></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="bi bi-car-front text-secondary"></i> Loại xe</span>
                            <span class="info-value" id="lbl-vehicle-type"></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="bi bi-tag text-secondary"></i> Hãng sản xuất</span>
                            <span class="info-value" id="lbl-brand"></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="bi bi-gear text-secondary"></i> Số loại (Model)</span>
                            <span class="info-value" id="lbl-model"></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="bi bi-palette text-secondary"></i> Màu sơn</span>
                            <span class="info-value" id="lbl-color"></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="bi bi-calendar3 text-secondary"></i> Ngày đăng ký</span>
                            <span class="info-value" id="lbl-registration-date"></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="bi bi-shield-check text-secondary"></i> Hạn đăng kiểm</span>
                            <span class="info-value" id="lbl-inspection-expiry"></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="bi bi-activity text-secondary"></i> Trạng thái hành chính</span>
                            <span class="info-value" id="lbl-status"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột phải: Danh sách vi phạm -->
        <div class="col-lg-8">
            <div class="card card-premium shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">KẾT QUẢ PHẠT NGUỘI</h4>
                        <p class="text-muted small mb-0">Lịch sử vi phạm giao thông được lưu trữ trên cơ sở dữ liệu quốc gia</p>
                    </div>
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2 fw-bold fs-7 border border-danger border-opacity-25" id="lbl-violations-count">
                        0 lỗi vi phạm
                    </span>
                </div>
                
                <div class="card-body px-4 pb-4" id="violations-container">
                    <!-- Sẽ được render động bằng JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const licensePlate = "<?php echo addslashes($license_plate); ?>";
    
    if (!licensePlate) {
        showError("Yêu cầu không hợp lệ", "Vui lòng cung cấp biển kiểm soát xe để tra cứu.", "bi-exclamation-octagon-fill");
        return;
    }

    // Gọi API Backend tra cứu
    fetch(`../../BE/customer/lookup.php?license_plate=${encodeURIComponent(licensePlate)}`)
        .then(response => {
            if (!response.ok) {
                throw new Error("Lỗi kết nối máy chủ dịch vụ.");
            }
            return response.json();
        })
        .then(data => {
            document.getElementById("loading-state").classList.add("d-none");

            if (data.success === false) {
                // Không tìm thấy phương tiện
                showError("Không tìm thấy phương tiện!", data.error || `Không tìm thấy thông tin đăng kiểm của biển kiểm soát "${licensePlate}".`, "bi-exclamation-triangle-fill");
            } else {
                // Hiển thị kết quả
                renderResult(data.vehicle, data.violations);
            }
        })
        .catch(error => {
            document.getElementById("loading-state").classList.add("d-none");
            showError("Đã xảy ra lỗi!", error.message || "Không thể kết nối đến máy chủ. Vui lòng thử lại sau.", "bi-exclamation-octagon-fill");
        });

    function showError(title, message, iconClass) {
        const errorState = document.getElementById("error-state");
        const icon = document.getElementById("error-icon");
        const titleEl = document.getElementById("error-title");
        const msgEl = document.getElementById("error-msg");

        icon.className = `bi text-danger fs-1 ${iconClass}`;
        titleEl.textContent = title;
        msgEl.innerHTML = message;
        
        errorState.classList.remove("d-none");
    }

    function renderResult(vehicle, violations) {
        // 1. Điền thông tin phương tiện ở cột trái
        document.getElementById("lbl-license-plate").textContent = vehicle.license_plate;
        document.getElementById("lbl-owner-name").textContent = vehicle.owner_name || "Chưa rõ";
        document.getElementById("lbl-vehicle-type").textContent = vehicle.vehicle_type || "Chưa rõ";
        document.getElementById("lbl-brand").textContent = vehicle.brand || "Chưa rõ";
        document.getElementById("lbl-model").textContent = vehicle.model || "Chưa rõ";
        document.getElementById("lbl-color").textContent = vehicle.color || "Chưa rõ";
        
        // Định dạng ngày đăng ký
        document.getElementById("lbl-registration-date").textContent = formatDate(vehicle.registration_date);
        
        // Định dạng hạn đăng kiểm
        const inspectionEl = document.getElementById("lbl-inspection-expiry");
        if (vehicle.inspection_expiry) {
            const expTime = new Date(vehicle.inspection_expiry).getTime();
            const nowTime = new Date().getTime();
            const formattedExp = formatDate(vehicle.inspection_expiry);
            if (expTime < nowTime) {
                inspectionEl.innerHTML = `<span class='text-danger fw-bold'><i class='bi bi-calendar-x-fill me-1'></i> ${formattedExp} (Hết hạn)</span>`;
            } else {
                inspectionEl.innerHTML = `<span class='text-success fw-bold'><i class='bi bi-calendar-check-fill me-1'></i> ${formattedExp}</span>`;
            }
        } else {
            inspectionEl.textContent = "Chưa có";
        }

        // Định dạng trạng thái xe
        const statusEl = document.getElementById("lbl-status");
        const statusLower = (vehicle.status || "").toLowerCase();
        if (statusLower === 'bình thường' || statusLower === 'binh thuong' || statusLower === 'active') {
            statusEl.innerHTML = '<span class="badge bg-success rounded-pill px-2.5 py-1.5"><i class="bi bi-check-circle-fill me-1"></i> Bình thường</span>';
        } else {
            statusEl.innerHTML = `<span class="badge bg-danger rounded-pill px-2.5 py-1.5"><i class="bi bi-exclamation-triangle-fill me-1"></i> ${vehicle.status || 'Chưa rõ'}</span>`;
        }

        // 2. Điền danh sách vi phạm ở cột phải
        const container = document.getElementById("violations-container");
        
        // Cập nhật số lượng lỗi hiển thị ở badge
        const countBadge = document.getElementById("lbl-violations-count");
        if (violations.length === 0) {
            countBadge.className = "badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-bold fs-7 border border-success border-opacity-25";
            countBadge.textContent = "Không có lỗi vi phạm";
            
            container.innerHTML = `
                <div class="text-center py-5 my-3 animate-fade-in">
                    <div class="mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle p-4" style="width: 75px; height: 75px;">
                            <i class="bi bi-shield-check fs-1"></i>
                        </span>
                    </div>
                    <h5 class="fw-bold text-success">Không phát hiện lỗi vi phạm</h5>
                    <p class="text-muted max-width-500 mx-auto small mb-0">
                        Chúc mừng! Phương tiện mang biển kiểm soát <strong class="text-dark">${vehicle.license_plate}</strong> không có dữ liệu vi phạm chưa xử lý trong hệ thống cơ sở dữ liệu quốc gia.
                    </p>
                </div>
            `;
        } else {
            countBadge.className = "badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2 fw-bold fs-7 border border-danger border-opacity-25";
            countBadge.textContent = `Phát hiện ${violations.length} lỗi vi phạm`;
            
            let cardsHtml = "";
            violations.forEach((v, index) => {
                const statusLower = (v.status || "").toLowerCase();
                let statusBadge = "";
                let isPaid = false;

                if (statusLower === 'đã nộp phạt' || statusLower === 'da nop phat' || statusLower === 'paid' || statusLower === 'đã xử lý' || statusLower === 'đã nộp') {
                    statusBadge = '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1.5"><i class="bi bi-check-circle-fill me-1"></i> Đã chấp hành nộp phạt</span>';
                    isPaid = true;
                } else {
                    statusBadge = '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1.5"><i class="bi bi-x-circle-fill me-1"></i> Chưa nộp phạt</span>';
                }

                // Tính hạn nộp phạt
                let dueHtml = "Chưa rõ";
                if (v.due_date) {
                    const dueTime = new Date(v.due_date).getTime();
                    const nowTime = new Date().getTime();
                    const formattedDue = formatDate(v.due_date);
                    if (dueTime < nowTime && !isPaid) {
                        dueHtml = `<span class='text-danger fw-bold'><i class='bi bi-exclamation-circle-fill me-1'></i> Hạn chót: ${formattedDue} (Quá hạn nộp phạt)</span>`;
                    } else {
                        dueHtml = `<span class='text-secondary fw-semibold'>Hạn chót: ${formattedDue}</span>`;
                    }
                }

                // Định dạng thời gian
                const violatedDate = formatDate(v.violated_at);
                const violatedTime = formatTime(v.violated_at);

                cardsHtml += `
                    <div class="violation-card-item ${isPaid ? 'paid' : ''}">
                        <div class="violation-card-header">
                            <span class="fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="bi ${isPaid ? 'bi-shield-check text-success' : 'bi-shield-slash text-danger'} fs-5"></i>
                                Lỗi vi phạm #${index + 1}
                            </span>
                            ${statusBadge}
                        </div>
                        <div class="violation-card-body">
                            <h6 class="fw-bold text-danger text-uppercase mb-3">${v.description || 'Chưa rõ lỗi cụ thể'}</h6>
                            
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="violation-meta-item">
                                        <span class="violation-meta-label"><i class="bi bi-calendar2-event"></i> Ngày vi phạm:</span>
                                        <span class="violation-meta-value fw-semibold">${violatedDate} lúc ${violatedTime}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="violation-meta-item">
                                        <span class="violation-meta-label"><i class="bi bi-geo-alt"></i> Nơi xảy ra:</span>
                                        <span class="violation-meta-value fw-medium">${v.location || 'Chưa rõ'} (${v.province || 'Chưa rõ'})</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="violation-meta-item">
                                        <span class="violation-meta-label"><i class="bi bi-file-earmark-text"></i> Số quyết định:</span>
                                        <span class="violation-meta-value font-monospace text-uppercase fw-bold text-dark">${v.decision_no || 'Chưa có quyết định'}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="violation-meta-item">
                                        <span class="violation-meta-label"><i class="bi bi-clock-history"></i> Hạn chấp hành:</span>
                                        <span class="violation-meta-value">${dueHtml}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = cardsHtml;
        }

        document.getElementById("result-state").classList.remove("d-none");
    }

    // Hàm format ngày DD/MM/YYYY
    function formatDate(dateStr) {
        if (!dateStr) return "Chưa có";
        try {
            const d = new Date(dateStr);
            if (isNaN(d.getTime())) return dateStr;
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = d.getFullYear();
            return `${day}/${month}/${year}`;
        } catch(e) {
            return dateStr;
        }
    }

    // Hàm format giờ HH:MM
    function formatTime(dateStr) {
        if (!dateStr) return "";
        try {
            const d = new Date(dateStr);
            if (isNaN(d.getTime())) return "";
            const hours = String(d.getHours()).padStart(2, '0');
            const minutes = String(d.getMinutes()).padStart(2, '0');
            return `${hours}:${minutes}`;
        } catch(e) {
            return "";
        }
    }
});
</script>
