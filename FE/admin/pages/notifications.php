<?php
// FE/admin/pages/notifications.php
if (!defined('APP_RUNNING')) {
    exit();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark text-uppercase mb-1">Nhật ký thông báo</h3>
        <p class="text-muted small mb-0">Theo dõi lịch sử gửi thông tin phạt nguội tới các chủ phương tiện</p>
    </div>
</div>

<!-- Bộ lọc & Tìm kiếm -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form id="search-form" class="row g-2">
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="search-input" class="form-control bg-light border-0" placeholder="Tìm theo người nhận, email, số điện thoại hoặc nội dung...">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100 rounded-3">Tìm kiếm</button>
            </div>
        </form>
    </div>
</div>

<!-- Danh sách thông báo -->
<div class="card border-0 rounded-4 shadow-sm">
    <div class="card-body p-0" id="table-container">
        <!-- Spinner đang tải -->
        <div class="text-center py-5 text-muted" id="table-loading">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mb-0 mt-2 small">Đang tải lịch sử thông báo...</p>
        </div>

        <div class="table-responsive d-none" id="table-wrapper">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="ps-4" style="width: 80px;">ID</th>
                        <th scope="col" style="width: 140px;">Kênh gửi</th>
                        <th scope="col" style="width: 200px;">Người nhận</th>
                        <th scope="col">Nội dung thông báo</th>
                        <th scope="col" class="pe-4" style="width: 180px;">Ngày gửi</th>
                    </tr>
                </thead>
                <tbody id="notification-rows">
                    <!-- JS render -->
                </tbody>
            </table>
        </div>

        <!-- Phân trang -->
        <div class="card-footer bg-white py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2 d-none" id="pagination-wrapper">
            <div class="text-muted small" id="pagination-info">
                Hiển thị trang -- / -- (Tổng cộng -- thông báo)
            </div>
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0" id="pagination-list">
                    <!-- JS render -->
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- ==================================== SCRIPTS LOGIC FRONTEND ==================================== -->
<script>
let currentPage = 1;
let currentSearch = "";

document.addEventListener("DOMContentLoaded", function () {
    // Tải danh sách lần đầu
    loadNotifications(1, "");

    // Xử lý Tìm kiếm
    document.getElementById("search-form").addEventListener("submit", function (e) {
        e.preventDefault();
        currentSearch = document.getElementById("search-input").value.trim();
        loadNotifications(1, currentSearch);
    });
});

function loadNotifications(page, search) {
    const loading = document.getElementById("table-loading");
    const wrapper = document.getElementById("table-wrapper");
    const tbody = document.getElementById("notification-rows");
    const pagWrapper = document.getElementById("pagination-wrapper");

    loading.classList.remove("d-none");
    wrapper.classList.add("d-none");
    pagWrapper.classList.add("d-none");

    currentPage = page;

    fetch(`../../BE/notifications/list.php?page=${page}&search=${encodeURIComponent(search)}`)
        .then(res => {
            if (res.status === 401) {
                window.location.href = "login.php";
                return;
            }
            return res.json();
        })
        .then(data => {
            loading.classList.add("d-none");

            if (data.notifications.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-chat-left-dots-fill fs-1 mb-2 text-secondary opacity-50 d-block"></i>
                            Không có bản ghi thông báo nào phù hợp.
                        </td>
                    </tr>
                `;
                wrapper.classList.remove("d-none");
                return;
            }

            let html = "";
            data.notifications.forEach(n => {
                const channel = (n.channel || 'PORTAL').toUpperCase();
                let channelBadge = "";
                if (channel === 'SMS') {
                    channelBadge = '<span class="badge bg-primary bg-opacity-10 text-primary px-2.5 py-1.5"><i class="bi bi-phone-vibrate me-1"></i> SMS</span>';
                } else if (channel === 'EMAIL') {
                    channelBadge = '<span class="badge bg-success bg-opacity-10 text-success px-2.5 py-1.5"><i class="bi bi-envelope-at-fill me-1"></i> Email</span>';
                } else {
                    channelBadge = `<span class="badge bg-secondary bg-opacity-10 text-secondary px-2.5 py-1.5"><i class="bi bi-broadcast me-1"></i> ${channel}</span>`;
                }

                const sentDate = formatDateTime(n.sent_at);

                html += `
                    <tr>
                        <td class="ps-4 fw-bold text-secondary">#${n.id}</td>
                        <td>${channelBadge}</td>
                        <td class="fw-semibold text-dark">${n.recipient || 'Không rõ'}</td>
                        <td class="small text-secondary fw-medium">${n.message || 'Không có nội dung'}</td>
                        <td class="pe-4 text-muted small">${sentDate}</td>
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
                    <td colspan="5" class="text-center py-5 text-danger small">
                        <i class="bi bi-exclamation-circle-fill me-2 fs-3 d-block mb-2"></i>
                        Không thể kết nối đến máy chủ Backend.
                    </td>
                </tr>
            `;
            wrapper.classList.remove("d-none");
        });
}

function renderPagination(meta) {
    const pagWrapper = document.getElementById("pagination-wrapper");
    const info = document.getElementById("pagination-info");
    const list = document.getElementById("pagination-list");

    if (meta.total_pages <= 1) {
        pagWrapper.classList.add("d-none");
        return;
    }

    info.textContent = `Hiển thị trang ${meta.page} / ${meta.total_pages} (Tổng cộng ${meta.total_rows} thông báo)`;
    
    let html = "";
    
    // Nút Trước
    html += `
        <li class="page-item ${meta.page <= 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadNotifications(${meta.page - 1}, currentSearch); return false;">Trước</a>
        </li>
    `;

    // Các trang
    for (let i = 1; i <= meta.total_pages; i++) {
        html += `
            <li class="page-item ${i === meta.page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadNotifications(${i}, currentSearch); return false;">${i}</a>
            </li>
        `;
    }

    // Nút Tiếp
    html += `
        <li class="page-item ${meta.page >= meta.total_pages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadNotifications(${meta.page + 1}, currentSearch); return false;">Tiếp</a>
        </li>
    `;

    list.innerHTML = html;
    pagWrapper.classList.remove("d-none");
}

function formatDateTime(dateStr) {
    if (!dateStr) return "Chưa rõ";
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return dateStr;
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    const seconds = String(d.getSeconds()).padStart(2, '0');
    return `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
}
</script>
