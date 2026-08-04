<?php
// FE/admin/pages/violations.php
if (!defined('APP_RUNNING')) {
    exit();
}
?>

<!-- Alert thông báo động -->
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
        <h3 class="fw-bold text-dark text-uppercase mb-1">Quản lý vi phạm</h3>
        <p class="text-muted small mb-0">Quản lý và ghi nhận các lỗi phạt nguội vi phạm trật tự an toàn giao thông</p>
    </div>
    <button type="button" class="btn btn-danger d-flex align-items-center gap-2 px-3 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCreateViolation">
        <i class="bi bi-plus-circle-fill"></i> Ghi nhận vi phạm mới
    </button>
</div>

<!-- Bộ lọc & Tìm kiếm -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form id="filter-form" class="row g-3">
            <!-- Tìm theo biển số -->
            <div class="col-md-3">
                <label class="form-label fw-semibold small text-muted">Biển kiểm soát</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="filter-search-plate" class="form-control bg-light border-0 text-uppercase" placeholder="Nhập biển số xe...">
                </div>
            </div>
            
            <!-- Lọc theo Tỉnh -->
            <div class="col-md-3">
                <label class="form-label fw-semibold small text-muted">Tỉnh/Thành phố</label>
                <select id="filter-province" class="form-select bg-light border-0">
                    <option value="">-- Tất cả các tỉnh --</option>
                    <!-- Được thêm động -->
                </select>
            </div>

            <!-- Lọc theo Trạng thái -->
            <div class="col-md-3">
                <label class="form-label fw-semibold small text-muted">Trạng thái nộp phạt</label>
                <select id="filter-status" class="form-select bg-light border-0">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="Chưa nộp phạt">Chưa nộp phạt</option>
                    <option value="Đã nộp phạt">Đã nộp phạt</option>
                </select>
            </div>

            <!-- Lọc theo Ngày -->
            <div class="col-md-2">
                <label class="form-label fw-semibold small text-muted">Ngày vi phạm</label>
                <input type="date" id="filter-date" class="form-control bg-light border-0">
            </div>

            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-dark w-100 rounded-3 py-2"><i class="bi bi-funnel-fill"></i></button>
            </div>
        </form>
    </div>
</div>

<!-- Danh sách vi phạm -->
<div class="card border-0 rounded-4 shadow-sm">
    <div class="card-body p-0" id="table-container">
        <!-- Spinner đang tải -->
        <div class="text-center py-5 text-muted" id="table-loading">
            <div class="spinner-border text-danger" role="status"></div>
            <p class="mb-0 mt-2 small">Đang tải danh sách lỗi vi phạm...</p>
        </div>

        <div class="table-responsive d-none" id="table-wrapper">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="ps-4">Biển số</th>
                        <th scope="col">Ngày vi phạm</th>
                        <th scope="col">Địa điểm</th>
                        <th scope="col">Tỉnh/Thành</th>
                        <th scope="col">Hành vi vi phạm</th>
                        <th scope="col">Số quyết định</th>
                        <th scope="col">Hạn nộp</th>
                        <th scope="col" class="text-center">Trạng thái</th>
                        <th scope="col" class="pe-4 text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="violation-rows">
                    <!-- JS render -->
                </tbody>
            </table>
        </div>

        <!-- Phân trang -->
        <div class="card-footer bg-white py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2 d-none" id="pagination-wrapper">
            <div class="text-muted small" id="pagination-info">
                Hiển thị trang -- / -- (Tổng cộng -- dòng)
            </div>
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0" id="pagination-list">
                    <!-- JS render -->
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- ==================================== MODAL XEM CHI TIẾT VI PHẠM ==================================== -->
<div class="modal fade" id="modalViewViolation" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-dark text-white rounded-top-4 py-3">
                <h5 class="modal-title fw-bold text-uppercase d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle-fill text-warning"></i> Chi tiết biên bản vi phạm
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="viewViolationBody">
                <!-- JS render -->
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- ==================================== MODAL GHI NHẬN VI PHẠM ==================================== -->
<div class="modal fade" id="modalCreateViolation" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-danger text-white rounded-top-4 py-3">
                <h5 class="modal-title fw-bold text-uppercase d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle-fill"></i> Ghi nhận vi phạm mới
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createViolationForm" class="needs-validation" novalidate>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="create_vehicle_id" class="form-label fw-semibold">Phương tiện vi phạm <span class="text-danger">*</span></label>
                            <select name="vehicle_id" id="create_vehicle_id" class="form-select select2-enable" required>
                                <option value="">-- Chọn phương tiện (Biển số - Chủ xe) --</option>
                                <!-- JS render options -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="create_violated_at" class="form-label fw-semibold">Ngày giờ vi phạm <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="violated_at" id="create_violated_at" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="create_province" class="form-label fw-semibold">Tỉnh/Thành phố xảy ra <span class="text-danger">*</span></label>
                            <input type="text" name="province" id="create_province" class="form-control" placeholder="Ví dụ: Hà Nội" required>
                        </div>
                        <div class="col-md-12">
                            <label for="create_location" class="form-label fw-semibold">Địa điểm cụ thể <span class="text-danger">*</span></label>
                            <input type="text" name="location" id="create_location" class="form-control" placeholder="Ví dụ: Km 12+300 Cao tốc Hà Nội - Hải Phòng" required>
                        </div>
                        <div class="col-md-12">
                            <label for="create_description" class="form-label fw-semibold">Mô tả hành vi vi phạm <span class="text-danger">*</span></label>
                            <textarea name="description" id="create_description" rows="3" class="form-control" placeholder="Ví dụ: Điều khiển xe chạy quá tốc độ quy định từ 10 km/h đến 20 km/h" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="create_decision_no" class="form-label fw-semibold">Số quyết định xử phạt</label>
                            <input type="text" name="decision_no" id="create_decision_no" class="form-control text-uppercase font-monospace" placeholder="Ví dụ: 12345/QĐ-XPHC">
                        </div>
                        <div class="col-md-6">
                            <label for="create_due_date" class="form-label fw-semibold">Hạn nộp phạt</label>
                            <input type="date" name="due_date" id="create_due_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="create_status" class="form-label fw-semibold">Trạng thái xử lý</label>
                            <select name="status" id="create_status" class="form-select">
                                <option value="Chưa nộp phạt">Chưa nộp phạt</option>
                                <option value="Đã nộp phạt">Đã nộp phạt</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-danger px-4 rounded-pill" id="btn-submit-create">Ghi nhận</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==================================== MODAL SỬA VI PHẠM ==================================== -->
<div class="modal fade" id="modalEditViolation" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-warning text-dark rounded-top-4 py-3">
                <h5 class="modal-title fw-bold text-uppercase d-flex align-items-center gap-2">
                    <i class="bi bi-pencil-square"></i> Chỉnh sửa biên bản vi phạm
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editViolationForm" class="needs-validation" novalidate>
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body p-4" id="editViolationBody">
                    <!-- JS render form -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-warning px-4 rounded-pill" id="btn-submit-edit">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==================================== MODAL XÁC NHẬN XÓA VI PHẠM ==================================== -->
<div class="modal fade" id="modalDeleteViolation" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-danger text-white rounded-top-4 py-3">
                <h5 class="modal-title fw-bold text-uppercase d-flex align-items-center gap-2">
                    <i class="bi bi-trash-fill"></i> Xác nhận xóa vi phạm
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-1 mb-3 d-block"></i>
                <p class="mb-2">Bạn có chắc chắn muốn xóa bản ghi vi phạm này?</p>
                <p class="text-muted small">Lưu ý: Mọi thông báo liên quan đến vi phạm này cũng sẽ bị xóa khỏi hệ thống.</p>
            </div>
            <div class="modal-footer border-top-0 justify-content-center pb-4">
                <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Hủy bỏ</button>
                <button type="button" id="delete_violation_btn" class="btn btn-danger px-4 rounded-pill">Xác nhận xóa</button>
            </div>
        </div>
    </div>
</div>

<!-- ==================================== SCRIPTS LOGIC FRONTEND ==================================== -->
<script>
let currentPage = 1;
let filterSearchPlate = "";
let filterProvince = "";
let filterStatus = "";
let filterDate = "";

let globalVehiclesList = []; // Danh sách xe dùng chung

document.addEventListener("DOMContentLoaded", function () {
    // Tải danh sách lần đầu
    loadViolations(1);

    // Xử lý bộ lọc tìm kiếm
    document.getElementById("filter-form").addEventListener("submit", function (e) {
        e.preventDefault();
        filterSearchPlate = document.getElementById("filter-search-plate").value.trim();
        filterProvince = document.getElementById("filter-province").value;
        filterStatus = document.getElementById("filter-status").value;
        filterDate = document.getElementById("filter-date").value;
        loadViolations(1);
    });

    // Thêm vi phạm mới submit
    document.getElementById("createViolationForm").addEventListener("submit", function (e) {
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

        fetch("../../BE/violations/create.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(resData => {
            btn.disabled = false;
            if (resData.success) {
                showToast(resData.message || "Ghi nhận vi phạm thành công", "bg-success");
                form.reset();
                form.classList.remove("was-validated");
                bootstrap.Modal.getInstance(document.getElementById("modalCreateViolation")).hide();
                loadViolations(currentPage);
            } else {
                showToast(resData.error || "Có lỗi xảy ra", "bg-danger");
            }
        })
        .catch(err => {
            btn.disabled = false;
            showToast("Không thể kết nối đến máy chủ API.", "bg-danger");
        });
    });

    // Cập nhật vi phạm submit
    document.getElementById("editViolationForm").addEventListener("submit", function (e) {
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

        fetch("../../BE/violations/edit.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(resData => {
            btn.disabled = false;
            if (resData.success) {
                showToast(resData.message || "Cập nhật lỗi vi phạm thành công", "bg-success");
                form.reset();
                form.classList.remove("was-validated");
                bootstrap.Modal.getInstance(document.getElementById("modalEditViolation")).hide();
                loadViolations(currentPage);
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

// Toast thông báo
function showToast(message, bgClass) {
    const toastEl = document.getElementById('liveToast');
    const messageEl = document.getElementById('toast-message');
    toastEl.className = `toast align-items-center text-white border-0 ${bgClass}`;
    messageEl.textContent = message;
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}

// Format ngày DD/MM/YYYY
function formatDate(dateStr) {
    if (!dateStr) return "Chưa có";
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return dateStr;
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    return `${day}/${month}/${year}`;
}

// Format giờ HH:MM
function formatTime(dateStr) {
    if (!dateStr) return "";
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return "";
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    return `${hours}:${minutes}`;
}

// Load danh sách vi phạm từ API
function loadViolations(page) {
    const loading = document.getElementById("table-loading");
    const wrapper = document.getElementById("table-wrapper");
    const tbody = document.getElementById("violation-rows");
    const pagWrapper = document.getElementById("pagination-wrapper");

    loading.classList.remove("d-none");
    wrapper.classList.add("d-none");
    pagWrapper.classList.add("d-none");

    currentPage = page;

    let url = `../../BE/violations/list.php?page=${page}`;
    if (filterSearchPlate) url += `&search_plate=${encodeURIComponent(filterSearchPlate)}`;
    if (filterProvince) url += `&province=${encodeURIComponent(filterProvince)}`;
    if (filterStatus) url += `&status=${encodeURIComponent(filterStatus)}`;
    if (filterDate) url += `&date=${encodeURIComponent(filterDate)}`;

    fetch(url)
        .then(res => {
            if (res.status === 401) {
                window.location.href = "login.php";
                return;
            }
            return res.json();
        })
        .then(data => {
            loading.classList.add("d-none");

            // Lưu danh sách xe và danh sách bộ lọc lần đầu
            globalVehiclesList = data.vehicles_list || [];
            updateFilterDropdowns(data.provinces);
            populateVehiclesDropdown(data.vehicles_list);

            if (data.violations.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-exclamation-octagon fs-1 mb-2 text-secondary opacity-55 d-block"></i>
                            Không tìm thấy vi phạm nào phù hợp.
                        </td>
                    </tr>
                `;
                wrapper.classList.remove("d-none");
                return;
            }

            let html = "";
            data.violations.forEach((v, index) => {
                const statusLower = (v.status || "").toLowerCase();
                let statusBadge = "";
                if (statusLower === 'đã nộp phạt' || statusLower === 'da nop phat' || statusLower === 'paid' || statusLower === 'đã xử lý' || statusLower === 'đã nộp') {
                    statusBadge = '<span class="badge bg-success rounded-pill px-2.5 py-1.5"><i class="bi bi-check-circle-fill me-1"></i> Đã nộp</span>';
                } else {
                    statusBadge = `<span class="badge bg-danger rounded-pill px-2.5 py-1.5"><i class="bi bi-x-circle-fill me-1"></i> Chưa nộp</span>`;
                }

                // Tính hạn nộp phạt
                let dueHtml = "Chưa rõ";
                if (v.due_date) {
                    const dueTime = new Date(v.due_date).getTime();
                    const nowTime = new Date().getTime();
                    const formattedDue = formatDate(v.due_date);
                    if (dueTime < nowTime && (statusLower === 'chưa nộp phạt' || statusLower === 'chua nop phat' || statusLower === 'unpaid' || statusLower === 'chưa xử lý')) {
                        dueHtml = `<span class='text-danger fw-bold'><i class='bi bi-calendar-x-fill me-1'></i> ${formattedDue} (Quá hạn)</span>`;
                    } else {
                        dueHtml = `<span class='text-secondary'>${formattedDue}</span>`;
                    }
                }

                html += `
                    <tr>
                        <td class="ps-4">
                            <span class="text-primary font-monospace bg-light px-2.5 py-1 rounded border fw-bold text-uppercase fs-7">
                                ${v.license_plate}
                            </span>
                        </td>
                        <td>
                            <div class="fw-semibold">${formatDate(v.violated_at)}</div>
                            <div class="text-muted small">${formatTime(v.violated_at)}</div>
                        </td>
                        <td class="small text-secondary">${v.location || 'Chưa rõ'}</td>
                        <td class="fw-semibold text-dark">${v.province || 'Chưa rõ'}</td>
                        <td class="small text-dark fw-medium">${v.description || 'Chưa rõ'}</td>
                        <td class="font-monospace text-uppercase small text-dark fw-bold">${v.decision_no || 'Chưa có'}</td>
                        <td class="small">${dueHtml}</td>
                        <td class="text-center">${statusBadge}</td>
                        <td class="pe-4 text-center">
                            <div class="d-inline-flex gap-1">
                                <button type="button" class="btn btn-outline-info btn-sm px-2 rounded-3 text-secondary border-secondary border-opacity-25" title="Chi tiết" onclick="viewViolation(${v.id})">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                                <button type="button" class="btn btn-outline-warning btn-sm px-2 rounded-3 text-dark border-secondary border-opacity-25" title="Chỉnh sửa" onclick="editViolation(${v.id})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm px-2 rounded-3 border-secondary border-opacity-25" title="Xóa" onclick="confirmDeleteViolation(${v.id})">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
            wrapper.classList.remove("d-none");

            renderPagination(data.pagination);
        })
        .catch(err => {
            loading.classList.add("d-none");
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-5 text-danger small">
                        <i class="bi bi-exclamation-circle-fill me-2 fs-3 d-block mb-2"></i>
                        Không thể kết nối đến máy chủ Backend.
                    </td>
                </tr>
            `;
            wrapper.classList.remove("d-none");
        });
}

function updateFilterDropdowns(provinces) {
    const provSelect = document.getElementById("filter-province");
    // Chỉ cập nhật dropdown nếu chưa có các option tỉnh thành (tránh reset lựa chọn của user)
    if (provSelect.children.length <= 1) {
        provinces.forEach(p => {
            const opt = document.createElement("option");
            opt.value = p;
            opt.textContent = p;
            provSelect.appendChild(opt);
        });
    }
}

function populateVehiclesDropdown(vehicles) {
    const createSelect = document.getElementById("create_vehicle_id");
    if (createSelect.children.length <= 1) {
        vehicles.forEach(v => {
            const opt = document.createElement("option");
            opt.value = v.id;
            opt.textContent = `${v.license_plate} - Chủ xe: ${v.owner_name}`;
            createSelect.appendChild(opt);
        });
    }
}

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
            <a class="page-link" href="#" onclick="loadViolations(${meta.page - 1}); return false;">Trước</a>
        </li>
    `;

    // Các trang
    for (let i = 1; i <= meta.total_pages; i++) {
        html += `
            <li class="page-item ${i === meta.page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadViolations(${i}); return false;">${i}</a>
            </li>
        `;
    }

    // Nút Tiếp
    html += `
        <li class="page-item ${meta.page >= meta.total_pages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadViolations(${meta.page + 1}); return false;">Tiếp</a>
        </li>
    `;

    list.innerHTML = html;
    pagWrapper.classList.remove("d-none");
}

// Xem chi tiết vi phạm
function viewViolation(id) {
    const modal = new bootstrap.Modal(document.getElementById('modalViewViolation'));
    modal.show();
    
    const body = document.getElementById('viewViolationBody');
    body.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <div class="text-muted small mt-2">Đang tải thông tin...</div>
        </div>
    `;

    fetch(`../../BE/violations/view.php?id=${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                body.innerHTML = `<div class="alert alert-danger border-0 m-3 small">${data.error}</div>`;
                return;
            }

            const v = data.violation;
            const statusBadge = (v.status || "").toLowerCase().includes("chưa")
                ? '<span class="badge bg-danger rounded-pill px-3 py-1.5">Chưa nộp phạt</span>'
                : '<span class="badge bg-success rounded-pill px-3 py-1.5">Đã nộp phạt</span>';

            body.innerHTML = `
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small d-block">Biển kiểm soát</label>
                            <strong class="font-monospace text-primary text-uppercase fs-5">${v.license_plate}</strong>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small d-block">Trạng thái phạt</label>
                            ${statusBadge}
                        </div>
                        <div class="col-md-6 mt-3">
                            <label class="text-muted small d-block">Chủ phương tiện</label>
                            <span class="fw-semibold text-dark text-uppercase">${v.owner_name}</span>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label class="text-muted small d-block">Loại phương tiện</label>
                            <span class="text-dark">${v.vehicle_type} (${v.brand} ${v.model})</span>
                        </div>
                        <div class="col-12 mt-4 border-top pt-3">
                            <h6 class="fw-bold text-dark mb-2">Thông tin vi phạm chi tiết</h6>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <span class="text-muted small d-block">Thời gian xảy ra:</span>
                                    <span class="fw-medium text-dark">${formatDate(v.violated_at)} lúc ${formatTime(v.violated_at)}</span>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted small d-block">Tỉnh thành:</span>
                                    <span class="fw-medium text-dark">${v.province || 'Chưa rõ'}</span>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <span class="text-muted small d-block">Địa điểm cụ thể:</span>
                                    <span class="fw-medium text-dark">${v.location || 'Chưa rõ'}</span>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <span class="text-muted small d-block">Hành vi vi phạm:</span>
                                    <span class="fw-semibold text-danger">${v.description || 'Chưa rõ'}</span>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <span class="text-muted small d-block">Số quyết định xử lý:</span>
                                    <span class="font-monospace fw-bold text-dark text-uppercase">${v.decision_no || 'Chưa có'}</span>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <span class="text-muted small d-block">Hạn nộp phạt cuối:</span>
                                    <span class="fw-medium text-dark">${formatDate(v.due_date)}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(err => {
            body.innerHTML = `<div class="alert alert-danger border-0 m-3 small">Không thể lấy thông tin biên bản.</div>`;
        });
}

// Chỉnh sửa vi phạm Form
function editViolation(id) {
    const modal = new bootstrap.Modal(document.getElementById('modalEditViolation'));
    modal.show();

    const body = document.getElementById('editViolationBody');
    body.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <div class="text-muted small mt-2">Đang tải thông tin chỉnh sửa...</div>
        </div>
    `;

    fetch(`../../BE/violations/view.php?id=${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                body.innerHTML = `<div class="alert alert-danger border-0 m-3 small">${data.error}</div>`;
                return;
            }

            const v = data.violation;
            document.getElementById("edit_id").value = v.id;

            // Xây dựng dropdown các phương tiện
            let vehicleOptions = "";
            globalVehiclesList.forEach(veh => {
                vehicleOptions += `
                    <option value="${veh.id}" ${veh.id === v.vehicle_id ? 'selected' : ''}>
                        ${veh.license_plate} - Chủ xe: ${veh.owner_name}
                    </option>
                `;
            });

            // Định dạng ngày giờ vi phạm để điền vào input datetime-local (YYYY-MM-DDTHH:MM)
            let formattedDatetime = "";
            if (v.violated_at) {
                const dateObj = new Date(v.violated_at);
                const year = dateObj.getFullYear();
                const month = String(dateObj.getMonth() + 1).padStart(2, '0');
                const day = String(dateObj.getDate()).padStart(2, '0');
                const hour = String(dateObj.getHours()).padStart(2, '0');
                const minute = String(dateObj.getMinutes()).padStart(2, '0');
                formattedDatetime = `${year}-${month}-${day}T${hour}:${minute}`;
            }

            // Định dạng do_date YYYY-MM-DD
            let formattedDueDate = "";
            if (v.due_date) {
                const dateObj = new Date(v.due_date);
                const year = dateObj.getFullYear();
                const month = String(dateObj.getMonth() + 1).padStart(2, '0');
                const day = String(dateObj.getDate()).padStart(2, '0');
                formattedDueDate = `${year}-${month}-${day}`;
            }

            // Xác định trạng thái nộp phạt đã thực hiện hay chưa (xử lý cả 'paid', 'unprocessed', 'notified'...)
            const statusLower = (v.status || '').toLowerCase();
            const isPaidStatus = statusLower === 'paid' || statusLower === 'đã nộp phạt' || statusLower === 'đã xử lý' || statusLower === 'đã nộp' || statusLower === 'da nop phat';

            body.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-12">
                        <label for="edit_vehicle_id" class="form-label fw-semibold">Phương tiện vi phạm <span class="text-danger">*</span></label>
                        <select name="vehicle_id" id="edit_vehicle_id" class="form-select" required>
                            ${vehicleOptions}
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="edit_violated_at" class="form-label fw-semibold">Ngày giờ vi phạm <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="violated_at" id="edit_violated_at" class="form-control" value="${formattedDatetime}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="edit_province" class="form-label fw-semibold">Tỉnh/Thành phố xảy ra <span class="text-danger">*</span></label>
                        <input type="text" name="province" id="edit_province" class="form-control" value="${v.province || ''}" required>
                    </div>
                    <div class="col-md-12">
                        <label for="edit_location" class="form-label fw-semibold">Địa điểm cụ thể <span class="text-danger">*</span></label>
                        <input type="text" name="location" id="edit_location" class="form-control" value="${v.location || ''}" required>
                    </div>
                    <div class="col-md-12">
                        <label for="edit_description" class="form-label fw-semibold">Mô tả hành vi vi phạm <span class="text-danger">*</span></label>
                        <textarea name="description" id="edit_description" rows="3" class="form-control" required>${v.description || ''}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="edit_decision_no" class="form-label fw-semibold">Số quyết định xử phạt</label>
                        <input type="text" name="decision_no" id="edit_decision_no" class="form-control text-uppercase font-monospace" value="${v.decision_no || ''}" placeholder="Ví dụ: 12345/QĐ-XPHC">
                    </div>
                    <div class="col-md-6">
                        <label for="edit_due_date" class="form-label fw-semibold">Hạn nộp phạt</label>
                        <input type="date" name="due_date" id="edit_due_date" class="form-control" value="${formattedDueDate}">
                    </div>
                    <div class="col-md-6">
                        <label for="edit_status" class="form-label fw-semibold">Trạng thái xử lý</label>
                        <select name="status" id="edit_status" class="form-select">
                            <option value="Chưa nộp phạt" ${!isPaidStatus ? 'selected' : ''}>Chưa nộp phạt</option>
                            <option value="Đã nộp phạt" ${isPaidStatus ? 'selected' : ''}>Đã nộp phạt</option>
                        </select>
                    </div>
                </div>
            `;
        })
        .catch(err => {
            body.innerHTML = `<div class="alert alert-danger border-0 m-3 small">Không thể lấy dữ liệu chỉnh sửa.</div>`;
        });
}

// Gọi Modal xác nhận xóa
function confirmDeleteViolation(id) {
    const confirmBtn = document.getElementById('delete_violation_btn');
    
    confirmBtn.onclick = function() {
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Đang xóa...';
        
        fetch(`../../BE/violations/delete.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Xác nhận xóa';
                bootstrap.Modal.getInstance(document.getElementById('modalDeleteViolation')).hide();
                
                if (data.success) {
                    showToast(data.message || "Xóa vi phạm thành công", "bg-success");
                    loadViolations(currentPage);
                } else {
                    showToast(data.error || "Không thể xóa vi phạm", "bg-danger");
                }
            })
            .catch(err => {
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Xác nhận xóa';
                showToast("Lỗi kết nối máy chủ.", "bg-danger");
            });
    };

    const modal = new bootstrap.Modal(document.getElementById('modalDeleteViolation'));
    modal.show();
}
</script>
