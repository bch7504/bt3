<?php
// FE/admin/pages/vehicles.php
if (!defined('APP_RUNNING')) {
    exit();
}
?>

<!-- Alert thông báo động (Toast/Alert) -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1085;">
    <div id="liveToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toast-message"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark text-uppercase mb-1">Quản lý phương tiện</h3>
        <p class="text-muted small mb-0">Quản lý cơ sở dữ liệu ô tô và xe máy đăng ký lưu hành</p>
    </div>
    <button type="button" class="btn btn-primary d-flex align-items-center gap-2 px-3 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCreateVehicle">
        <i class="bi bi-plus-circle-fill"></i> Thêm phương tiện mới
    </button>
</div>

<!-- Bộ lọc & Tìm kiếm -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form id="search-form" class="row g-2">
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="search-input" class="form-control bg-light border-0" placeholder="Tìm theo biển kiểm soát, chủ xe, loại xe, hãng xe...">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100 rounded-3">Tìm kiếm</button>
            </div>
        </form>
    </div>
</div>

<!-- Danh sách phương tiện -->
<div class="card border-0 rounded-4 shadow-sm">
    <div class="card-body p-0" id="table-container">
        <!-- Spinner đang tải bảng -->
        <div class="text-center py-5 text-muted" id="table-loading">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mb-0 mt-2 small">Đang tải danh sách phương tiện...</p>
        </div>
        
        <div class="table-responsive d-none" id="table-wrapper">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="ps-4">Biển kiểm soát</th>
                        <th scope="col">Loại xe</th>
                        <th scope="col">Hãng</th>
                        <th scope="col">Số loại (Model)</th>
                        <th scope="col">Chủ phương tiện</th>
                        <th scope="col">Số điện thoại</th>
                        <th scope="col" class="text-center">Trạng thái</th>
                        <th scope="col" class="pe-4 text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="vehicle-rows">
                    <!-- Được render bằng JS -->
                </tbody>
            </table>
        </div>
        
        <!-- Khung phân trang -->
        <div class="card-footer bg-white py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2 d-none" id="pagination-wrapper">
            <div class="text-muted small" id="pagination-info">
                Hiển thị trang -- / -- (Tổng cộng -- dòng)
            </div>
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0" id="pagination-list">
                    <!-- Được render bằng JS -->
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- ==================================== MODAL XEM CHI TIẾT PHƯƠNG TIỆN ==================================== -->
<div class="modal fade" id="modalViewVehicle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-dark text-white rounded-top-4 py-3">
                <h5 class="modal-title fw-bold text-uppercase d-flex align-items-center gap-2">
                    <i class="bi bi-car-front-fill text-warning"></i> Chi tiết phương tiện
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="viewVehicleBody">
                <!-- Được render bằng JS -->
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- ==================================== MODAL THÊM PHƯƠNG TIỆN ==================================== -->
<div class="modal fade" id="modalCreateVehicle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-primary text-white rounded-top-4 py-3">
                <h5 class="modal-title fw-bold text-uppercase d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle-fill"></i> Thêm phương tiện mới
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createVehicleForm" class="needs-validation" novalidate>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="create_license_plate" class="form-label fw-semibold">Biển kiểm soát <span class="text-danger">*</span></label>
                            <input type="text" name="license_plate" id="create_license_plate" class="form-control text-uppercase font-monospace" placeholder="Ví dụ: 30A-12345" required>
                        </div>
                        <div class="col-md-6">
                            <label for="create_vehicle_type" class="form-label fw-semibold">Loại phương tiện</label>
                            <select name="vehicle_type" id="create_vehicle_type" class="form-select">
                                <option value="Ô tô">Ô tô</option>
                                <option value="Xe máy">Xe máy</option>
                                <option value="Xe tải">Xe tải</option>
                                <option value="Xe khách">Xe khách</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="create_brand" class="form-label fw-semibold">Hãng sản xuất</label>
                            <input type="text" name="brand" id="create_brand" class="form-control" placeholder="Ví dụ: Toyota">
                        </div>
                        <div class="col-md-4">
                            <label for="create_model" class="form-label fw-semibold">Số loại (Model)</label>
                            <input type="text" name="model" id="create_model" class="form-control" placeholder="Ví dụ: Camry">
                        </div>
                        <div class="col-md-4">
                            <label for="create_color" class="form-label fw-semibold">Màu sơn</label>
                            <input type="text" name="color" id="create_color" class="form-control" placeholder="Ví dụ: Đen">
                        </div>
                        <div class="col-md-6">
                            <label for="create_engine_number" class="form-label fw-semibold">Số máy</label>
                            <input type="text" name="engine_number" id="create_engine_number" class="form-control" placeholder="Nhập số máy">
                        </div>
                        <div class="col-md-6">
                            <label for="create_chassis_number" class="form-label fw-semibold">Số khung</label>
                            <input type="text" name="chassis_number" id="create_chassis_number" class="form-control" placeholder="Nhập số khung">
                        </div>
                        <div class="col-md-6">
                            <label for="create_registration_date" class="form-label fw-semibold">Ngày đăng ký</label>
                            <input type="date" name="registration_date" id="create_registration_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="create_inspection_expiry" class="form-label fw-semibold">Hạn đăng kiểm</label>
                            <input type="date" name="inspection_expiry" id="create_inspection_expiry" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="create_status" class="form-label fw-semibold">Trạng thái phương tiện</label>
                            <select name="status" id="create_status" class="form-select">
                                <option value="Bình thường">Bình thường</option>
                                <option value="Tạm giữ">Tạm giữ</option>
                                <option value="Mất cắp">Mất cắp</option>
                            </select>
                        </div>
                        <div class="col-12 mt-4 border-top pt-3">
                            <h6 class="fw-bold text-primary mb-3">Thông tin chủ sở hữu</h6>
                        </div>
                        <div class="col-md-6">
                            <label for="create_owner_name" class="form-label fw-semibold">Họ tên chủ xe <span class="text-danger">*</span></label>
                            <input type="text" name="owner_name" id="create_owner_name" class="form-control" placeholder="Nhập họ tên chủ xe" required>
                        </div>
                        <div class="col-md-6">
                            <label for="create_owner_id_no" class="form-label fw-semibold">Số CCCD/CMND</label>
                            <input type="text" name="owner_id_no" id="create_owner_id_no" class="form-control" placeholder="Nhập số CMND/CCCD">
                        </div>
                        <div class="col-md-6">
                            <label for="create_owner_phone" class="form-label fw-semibold">Số điện thoại</label>
                            <input type="text" name="owner_phone" id="create_owner_phone" class="form-control" placeholder="Nhập số điện thoại">
                        </div>
                        <div class="col-md-6">
                            <label for="create_owner_email" class="form-label fw-semibold">Email liên hệ</label>
                            <input type="email" name="owner_email" id="create_owner_email" class="form-control" placeholder="Nhập địa chỉ email">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-pill" id="btn-submit-create">Lưu lại</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==================================== MODAL SỬA PHƯƠNG TIỆN ==================================== -->
<div class="modal fade" id="modalEditVehicle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-warning text-dark rounded-top-4 py-3">
                <h5 class="modal-title fw-bold text-uppercase d-flex align-items-center gap-2">
                    <i class="bi bi-pencil-square"></i> Chỉnh sửa phương tiện
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editVehicleForm" class="needs-validation" novalidate>
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body p-4" id="editVehicleBody">
                    <!-- Sẽ được render điền động qua JS -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-warning px-4 rounded-pill" id="btn-submit-edit">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==================================== MODAL XÁC NHẬN XÓA PHƯƠNG TIỆN ==================================== -->
<div class="modal fade" id="modalDeleteVehicle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-danger text-white rounded-top-4 py-3">
                <h5 class="modal-title fw-bold text-uppercase d-flex align-items-center gap-2">
                    <i class="bi bi-trash-fill"></i> Xác nhận xóa phương tiện
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-1 mb-3 d-block"></i>
                <p class="mb-2">Bạn có chắc chắn muốn xóa phương tiện có biển kiểm soát:</p>
                <h4 class="fw-bold text-primary font-monospace text-uppercase" id="delete_license_plate_label"></h4>
                <p class="text-muted small mb-0 mt-3">Hành động này không thể hoàn tác. Không thể xóa nếu phương tiện đang có lịch sử vi phạm giao thông.</p>
            </div>
            <div class="modal-footer border-top-0 justify-content-center pb-4">
                <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Hủy bỏ</button>
                <button type="button" id="delete_vehicle_btn" class="btn btn-danger px-4 rounded-pill">Xác nhận xóa</button>
            </div>
        </div>
    </div>
</div>

<!-- ==================================== SCRIPTS LOGIC FRONTEND ==================================== -->
<script>
let currentPage = 1;
let currentSearch = "";

document.addEventListener("DOMContentLoaded", function () {
    // Tự động viết hoa biển số khi gõ
    const createPlateInput = document.getElementById('create_license_plate');
    if (createPlateInput) {
        createPlateInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }

    // Tải danh sách lần đầu
    loadVehicles(1, "");

    // Xử lý Tìm kiếm
    document.getElementById("search-form").addEventListener("submit", function (e) {
        e.preventDefault();
        currentSearch = document.getElementById("search-input").value.trim();
        loadVehicles(1, currentSearch);
    });

    // Xử lý Gửi Form Thêm mới
    document.getElementById("createVehicleForm").addEventListener("submit", function (e) {
        e.preventDefault();
        const form = e.currentTarget;
        if (!form.checkValidity()) {
            form.classList.add("was-validated");
            return;
        }

        const btn = document.getElementById("btn-submit-create");
        btn.disabled = true;

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        fetch("../../BE/vehicles/create.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(resData => {
            btn.disabled = false;
            if (resData.success) {
                showToast(resData.message || "Đã thêm phương tiện thành công", "bg-success");
                form.reset();
                form.classList.remove("was-validated");
                bootstrap.Modal.getInstance(document.getElementById("modalCreateVehicle")).hide();
                loadVehicles(currentPage, currentSearch);
            } else {
                showToast(resData.error || "Có lỗi xảy ra", "bg-danger");
            }
        })
        .catch(err => {
            btn.disabled = false;
            showToast("Không thể kết nối đến máy chủ API.", "bg-danger");
        });
    });

    // Xử lý Gửi Form Chỉnh sửa
    document.getElementById("editVehicleForm").addEventListener("submit", function (e) {
        e.preventDefault();
        const form = e.currentTarget;
        if (!form.checkValidity()) {
            form.classList.add("was-validated");
            return;
        }

        const btn = document.getElementById("btn-submit-edit");
        btn.disabled = true;

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        fetch("../../BE/vehicles/edit.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(resData => {
            btn.disabled = false;
            if (resData.success) {
                showToast(resData.message || "Cập nhật phương tiện thành công", "bg-success");
                form.reset();
                form.classList.remove("was-validated");
                bootstrap.Modal.getInstance(document.getElementById("modalEditVehicle")).hide();
                loadVehicles(currentPage, currentSearch);
            } else {
                showToast(resData.error || "Có lỗi xảy ra", "bg-danger");
            }
        })
        .catch(err => {
            btn.disabled = false;
            showToast("Không thể kết nối đến máy chủ API.", "bg-danger");
        });
    });
});

// Hàm Toast thông báo
function showToast(message, bgClass) {
    const toastEl = document.getElementById('liveToast');
    const messageEl = document.getElementById('toast-message');
    toastEl.className = `toast align-items-center text-white border-0 ${bgClass}`;
    messageEl.textContent = message;
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}

// Hàm format ngày DD/MM/YYYY
function formatDate(dateStr) {
    if (!dateStr) return "Chưa có";
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return dateStr;
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    return `${day}/${month}/${year}`;
}

// Tải danh sách phương tiện từ API Backend
function loadVehicles(page, search) {
    const loading = document.getElementById("table-loading");
    const wrapper = document.getElementById("table-wrapper");
    const tbody = document.getElementById("vehicle-rows");
    const pagWrapper = document.getElementById("pagination-wrapper");

    loading.classList.remove("d-none");
    wrapper.classList.add("d-none");
    pagWrapper.classList.add("d-none");

    currentPage = page;

    fetch(`../../BE/vehicles/list.php?page=${page}&search=${encodeURIComponent(search)}`)
        .then(res => {
            if (res.status === 401) {
                window.location.href = "login.php";
                return;
            }
            return res.json();
        })
        .then(data => {
            loading.classList.add("d-none");

            if (data.vehicles.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-car-front-fill fs-1 mb-2 text-secondary opacity-55 d-block"></i>
                            Không tìm thấy phương tiện nào phù hợp.
                        </td>
                    </tr>
                `;
                wrapper.classList.remove("d-none");
                return;
            }

            let html = "";
            data.vehicles.forEach(v => {
                const statusLower = (v.status || "").toLowerCase();
                let statusBadge = "";
                if (statusLower === 'bình thường' || statusLower === 'binh thuong' || statusLower === 'active') {
                    statusBadge = '<span class="badge bg-success rounded-pill px-2.5 py-1.5"><i class="bi bi-check-circle-fill me-1"></i> Bình thường</span>';
                } else {
                    statusBadge = `<span class="badge bg-danger rounded-pill px-2.5 py-1.5"><i class="bi bi-exclamation-triangle-fill me-1"></i> ${v.status || 'Chưa rõ'}</span>`;
                }

                html += `
                    <tr>
                        <td class="ps-4">
                            <span class="text-primary font-monospace bg-light px-2.5 py-1 rounded border fw-bold text-uppercase fs-7">
                                ${v.license_plate}
                            </span>
                        </td>
                        <td>${v.vehicle_type || 'Chưa rõ'}</td>
                        <td>${v.brand || 'Chưa rõ'}</td>
                        <td>${v.model || 'Chưa rõ'}</td>
                        <td class="fw-semibold">${v.owner_name || 'Chưa rõ'}</td>
                        <td>${v.owner_phone || 'Chưa có'}</td>
                        <td class="text-center">${statusBadge}</td>
                        <td class="pe-4 text-center">
                            <div class="d-inline-flex gap-1">
                                <button type="button" class="btn btn-outline-info btn-sm px-2 rounded-3 text-secondary border-secondary border-opacity-25" title="Chi tiết" onclick="viewVehicle(${v.id})">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                                <button type="button" class="btn btn-outline-warning btn-sm px-2 rounded-3 text-dark border-secondary border-opacity-25" title="Chỉnh sửa" onclick="editVehicle(${v.id})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm px-2 rounded-3 border-secondary border-opacity-25" title="Xóa" onclick="confirmDeleteVehicle(${v.id}, '${v.license_plate}')">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
            wrapper.classList.remove("d-none");

            // Phân trang
            renderPagination(data.pagination);
        })
        .catch(err => {
            loading.classList.add("d-none");
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-5 text-danger small">
                        <i class="bi bi-exclamation-circle-fill me-2 fs-3 d-block mb-2"></i>
                        Không thể kết nối đến máy chủ Backend.
                    </td>
                </tr>
            `;
            wrapper.classList.remove("d-none");
        });
}

// Render các nút phân trang
function renderPagination(meta) {
    const pagWrapper = document.getElementById("pagination-wrapper");
    const info = document.getElementById("pagination-info");
    const list = document.getElementById("pagination-list");

    if (meta.total_pages <= 1) {
        pagWrapper.classList.add("d-none");
        return;
    }

    info.textContent = `Hiển thị trang ${meta.page} / ${meta.total_pages} (Tổng cộng ${meta.total_rows} dòng)`;
    
    let html = "";
    
    // Nút Trước
    html += `
        <li class="page-item ${meta.page <= 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadVehicles(${meta.page - 1}, currentSearch); return false;">Trước</a>
        </li>
    `;

    // Các trang
    for (let i = 1; i <= meta.total_pages; i++) {
        html += `
            <li class="page-item ${i === meta.page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadVehicles(${i}, currentSearch); return false;">${i}</a>
            </li>
        `;
    }

    // Nút Tiếp
    html += `
        <li class="page-item ${meta.page >= meta.total_pages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadVehicles(${meta.page + 1}, currentSearch); return false;">Tiếp</a>
        </li>
    `;

    list.innerHTML = html;
    pagWrapper.classList.remove("d-none");
}

// Xem chi tiết xe
function viewVehicle(id) {
    const modal = new bootstrap.Modal(document.getElementById('modalViewVehicle'));
    modal.show();
    
    const body = document.getElementById('viewVehicleBody');
    body.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <div class="text-muted small mt-2">Đang tải thông tin...</div>
        </div>
    `;

    fetch(`../../BE/vehicles/view.php?id=${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                body.innerHTML = `<div class="alert alert-danger border-0 m-3 small">${data.error}</div>`;
                return;
            }

            const v = data.vehicle;
            
            // Xây dựng bảng vi phạm liên quan (nếu có)
            let violationsHtml = '<p class="text-success small fw-semibold"><i class="bi bi-shield-check me-1"></i> Phương tiện hiện không có lỗi vi phạm nào.</p>';
            if (data.violations && data.violations.length > 0) {
                let rows = "";
                data.violations.forEach((vi, idx) => {
                    const statusLower = (vi.status || "").toLowerCase();
                    let stBadge = "";
                    if (statusLower === 'đã nộp phạt' || statusLower === 'da nop phat' || statusLower === 'paid' || statusLower === 'đã xử lý' || statusLower === 'đã nộp') {
                        stBadge = '<span class="badge bg-success rounded-pill px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i> Đã nộp</span>';
                    } else if (statusLower === 'chưa nộp phạt' || statusLower === 'chua nop phat' || statusLower === 'unpaid' || statusLower === 'chưa xử lý' || statusLower === 'chưa nộp') {
                        stBadge = '<span class="badge bg-danger rounded-pill px-2 py-1"><i class="bi bi-x-circle-fill me-1"></i> Chưa nộp</span>';
                    } else {
                        stBadge = `<span class="badge bg-warning text-dark rounded-pill px-2 py-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> ${vi.status}</span>`;
                    }

                    rows += `
                        <tr>
                            <td class="small text-secondary">${idx+1}</td>
                            <td class="small text-dark fw-semibold">${formatDate(vi.violated_at)}</td>
                            <td class="small text-dark">${vi.location || 'Chưa rõ'}</td>
                            <td class="small text-muted">${vi.description || 'Chưa rõ'}</td>
                            <td class="small text-center">${stBadge}</td>
                        </tr>
                    `;
                });

                violationsHtml = `
                    <div class="table-responsive mt-2">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;">STT</th>
                                    <th>Ngày</th>
                                    <th>Địa điểm</th>
                                    <th>Lỗi vi phạm</th>
                                    <th class="text-center">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${rows}
                            </tbody>
                        </table>
                    </div>
                `;
            }

            body.innerHTML = `
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small d-block">Biển kiểm soát</label>
                            <strong class="font-monospace text-primary text-uppercase fs-5">${v.license_plate}</strong>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small d-block">Trạng thái xe</label>
                            <span class="badge ${v.status === 'Bình thường' ? 'bg-success' : 'bg-danger'} rounded-pill px-3 py-1.5">${v.status || 'Bình thường'}</span>
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="text-muted small d-block">Chủ phương tiện</label>
                            <span class="fw-semibold text-dark text-uppercase">${v.owner_name}</span>
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="text-muted small d-block">CCCD/CMND</label>
                            <span class="text-dark">${v.owner_id_no || 'Chưa có'}</span>
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="text-muted small d-block">Số điện thoại</label>
                            <span class="text-dark">${v.owner_phone || 'Chưa có'}</span>
                        </div>
                        <div class="col-md-12 mt-2">
                            <label class="text-muted small d-block">Email liên hệ</label>
                            <span class="text-dark">${v.owner_email || 'Chưa có'}</span>
                        </div>
                        <div class="col-12 mt-4 border-top pt-3">
                            <h6 class="fw-bold text-dark mb-2">Thông số kỹ thuật phương tiện</h6>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <span class="text-muted small d-block">Loại xe:</span>
                                    <span class="fw-medium text-dark">${v.vehicle_type || 'Chưa rõ'}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-muted small d-block">Hãng:</span>
                                    <span class="fw-medium text-dark">${v.brand || 'Chưa rõ'}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-muted small d-block">Số loại:</span>
                                    <span class="fw-medium text-dark">${v.model || 'Chưa rõ'}</span>
                                </div>
                                <div class="col-md-4 mt-2">
                                    <span class="text-muted small d-block">Màu sơn:</span>
                                    <span class="fw-medium text-dark">${v.color || 'Chưa rõ'}</span>
                                </div>
                                <div class="col-md-4 mt-2">
                                    <span class="text-muted small d-block">Số máy:</span>
                                    <span class="font-monospace text-dark">${v.engine_number || 'Chưa có'}</span>
                                </div>
                                <div class="col-md-4 mt-2">
                                    <span class="text-muted small d-block">Số khung:</span>
                                    <span class="font-monospace text-dark">${v.chassis_number || 'Chưa có'}</span>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <span class="text-muted small d-block">Ngày đăng ký:</span>
                                    <span class="fw-medium text-dark">${formatDate(v.registration_date)}</span>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <span class="text-muted small d-block">Hạn đăng kiểm:</span>
                                    <span class="fw-medium text-dark">${formatDate(v.inspection_expiry)}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mt-4 border-top pt-3">
                            <h6 class="fw-bold text-dark mb-2">Lịch sử vi phạm của phương tiện</h6>
                            ${violationsHtml}
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(err => {
            body.innerHTML = `<div class="alert alert-danger border-0 m-3 small">Không thể lấy thông tin chi tiết.</div>`;
        });
}

// Sửa phương tiện: Lấy dữ liệu điền Form
// Sửa phương tiện: Lấy dữ liệu điền Form
function editVehicle(id) {
    const modal = new bootstrap.Modal(document.getElementById('modalEditVehicle'));
    modal.show();
    
    const body = document.getElementById('editVehicleBody');
    body.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <div class="text-muted small mt-2">Đang tải thông tin chỉnh sửa...</div>
        </div>
    `;

    fetch(`../../BE/vehicles/view.php?id=${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                body.innerHTML = `<div class="alert alert-danger border-0 m-3 small">${data.error}</div>`;
                return;
            }

            const v = data.vehicle;
            document.getElementById('edit_id').value = v.id;
            
            body.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="edit_license_plate" class="form-label fw-semibold">Biển kiểm soát <span class="text-danger">*</span></label>
                        <input type="text" name="license_plate" id="edit_license_plate" class="form-control text-uppercase font-monospace fw-bold text-primary" value="${v.license_plate || ''}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="edit_vehicle_type" class="form-label fw-semibold">Loại phương tiện</label>
                        <select name="vehicle_type" id="edit_vehicle_type" class="form-select">
                            <option value="Ô tô" ${v.vehicle_type === 'Ô tô' ? 'selected' : ''}>Ô tô</option>
                            <option value="Xe máy" ${v.vehicle_type === 'Xe máy' ? 'selected' : ''}>Xe máy</option>
                            <option value="Xe tải" ${v.vehicle_type === 'Xe tải' ? 'selected' : ''}>Xe tải</option>
                            <option value="Xe khách" ${v.vehicle_type === 'Xe khách' ? 'selected' : ''}>Xe khách</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="edit_brand" class="form-label fw-semibold">Hãng sản xuất</label>
                        <input type="text" name="brand" id="edit_brand" class="form-control" value="${v.brand || ''}">
                    </div>
                    <div class="col-md-4">
                        <label for="edit_model" class="form-label fw-semibold">Số loại (Model)</label>
                        <input type="text" name="model" id="edit_model" class="form-control" value="${v.model || ''}">
                    </div>
                    <div class="col-md-4">
                        <label for="edit_color" class="form-label fw-semibold">Màu sơn</label>
                        <input type="text" name="color" id="edit_color" class="form-control" value="${v.color || ''}">
                    </div>
                    <div class="col-md-6">
                        <label for="edit_engine_number" class="form-label fw-semibold">Số máy</label>
                        <input type="text" name="engine_number" id="edit_engine_number" class="form-control" value="${v.engine_number || ''}">
                    </div>
                    <div class="col-md-6">
                        <label for="edit_chassis_number" class="form-label fw-semibold">Số khung</label>
                        <input type="text" name="chassis_number" id="edit_chassis_number" class="form-control" value="${v.chassis_number || ''}">
                    </div>
                    <div class="col-md-6">
                        <label for="edit_registration_date" class="form-label fw-semibold">Ngày đăng ký</label>
                        <input type="date" name="registration_date" id="edit_registration_date" class="form-control" value="${v.registration_date || ''}">
                    </div>
                    <div class="col-md-6">
                        <label for="edit_inspection_expiry" class="form-label fw-semibold">Hạn đăng kiểm</label>
                        <input type="date" name="inspection_expiry" id="edit_inspection_expiry" class="form-control" value="${v.inspection_expiry || ''}">
                    </div>
                    <div class="col-md-6">
                        <label for="edit_status" class="form-label fw-semibold">Trạng thái phương tiện</label>
                        <select name="status" id="edit_status" class="form-select">
                            <option value="Bình thường" ${v.status === 'Bình thường' ? 'selected' : ''}>Bình thường</option>
                            <option value="Tạm giữ" ${v.status === 'Tạm giữ' ? 'selected' : ''}>Tạm giữ</option>
                            <option value="Mất cắp" ${v.status === 'Mất cắp' ? 'selected' : ''}>Mất cắp</option>
                        </select>
                    </div>
                    <div class="col-12 mt-4 border-top pt-3">
                        <h6 class="fw-bold text-primary mb-3">Thông tin chủ sở hữu</h6>
                    </div>
                    <div class="col-md-6">
                        <label for="edit_owner_name" class="form-label fw-semibold">Họ tên chủ xe <span class="text-danger">*</span></label>
                        <input type="text" name="owner_name" id="edit_owner_name" class="form-control" value="${v.owner_name || ''}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="edit_owner_id_no" class="form-label fw-semibold">Số CCCD/CMND</label>
                        <input type="text" name="owner_id_no" id="edit_owner_id_no" class="form-control" value="${v.owner_id_no || ''}">
                    </div>
                    <div class="col-md-6">
                        <label for="edit_owner_phone" class="form-label fw-semibold">Số điện thoại</label>
                        <input type="text" name="owner_phone" id="edit_owner_phone" class="form-control" value="${v.owner_phone || ''}">
                    </div>
                    <div class="col-md-6">
                        <label for="edit_owner_email" class="form-label fw-semibold">Email liên hệ</label>
                        <input type="email" name="owner_email" id="edit_owner_email" class="form-control" value="${v.owner_email || ''}">
                    </div>
                </div>
            `;
            
            // Tự động viết hoa biển số sửa
            document.getElementById('edit_license_plate').addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        })
        .catch(err => {
            body.innerHTML = `<div class="alert alert-danger border-0 m-3 small">Không thể lấy dữ liệu chỉnh sửa.</div>`;
        });
}

// Gọi Modal xác nhận xóa
function confirmDeleteVehicle(id, plate) {
    document.getElementById('delete_license_plate_label').innerText = plate;
    const confirmBtn = document.getElementById('delete_vehicle_btn');
    
    // Gán hàm gọi API xóa khi click nút
    confirmBtn.onclick = function() {
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Đang xóa...';
        
        fetch(`../../BE/vehicles/delete.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Xác nhận xóa';
                bootstrap.Modal.getInstance(document.getElementById('modalDeleteVehicle')).hide();
                
                if (data.success) {
                    showToast(data.message || "Xóa xe thành công", "bg-success");
                    loadVehicles(currentPage, currentSearch);
                } else {
                    showToast(data.error || "Không thể xóa phương tiện", "bg-danger");
                }
            })
            .catch(err => {
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Xác nhận xóa';
                showToast("Lỗi kết nối máy chủ.", "bg-danger");
            });
    };

    const modal = new bootstrap.Modal(document.getElementById('modalDeleteVehicle'));
    modal.show();
}
</script>
