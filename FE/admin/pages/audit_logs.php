<?php
// FE/admin/pages/audit_logs.php
if (!defined('APP_RUNNING')) {
    exit();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark text-uppercase mb-1">Nhật ký hoạt động (Audit Logs)</h3>
        <p class="text-muted small mb-0">Theo dõi toàn bộ lịch sử thao tác của các tài khoản quản trị viên</p>
    </div>
</div>

<!-- Bảng hiển thị Audit Log -->
<div class="card border-0 rounded-4 shadow-sm">
    <div class="card-body p-0" id="table-container">
        <!-- Spinner đang tải -->
        <div class="text-center py-5 text-muted" id="table-loading">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mb-0 mt-2 small">Đang tải nhật ký hoạt động...</p>
        </div>

        <div class="table-responsive d-none" id="table-wrapper">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="ps-4" style="width: 80px;">STT</th>
                        <th scope="col" style="width: 180px;">Tài khoản Admin</th>
                        <th scope="col" style="width: 150px;">Thao tác (Action)</th>
                        <th scope="col" style="width: 180px;">Đối tượng (Entity)</th>
                        <th scope="col" style="width: 140px;">Mã đối tượng (ID)</th>
                        <th scope="col" class="pe-4">Thời gian thực hiện</th>
                    </tr>
                </thead>
                <tbody id="log-rows">
                    <!-- JS render -->
                </tbody>
            </table>
        </div>

        <!-- Phân trang -->
        <div class="card-footer bg-white py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2 d-none" id="pagination-wrapper">
            <div class="text-muted small" id="pagination-info">
                Hiển thị trang -- / -- (Tổng cộng -- bản ghi log)
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

document.addEventListener("DOMContentLoaded", function () {
    // Tải danh sách lần đầu
    loadLogs(1);
});

function loadLogs(page) {
    const loading = document.getElementById("table-loading");
    const wrapper = document.getElementById("table-wrapper");
    const tbody = document.getElementById("log-rows");
    const pagWrapper = document.getElementById("pagination-wrapper");

    loading.classList.remove("d-none");
    wrapper.classList.add("d-none");
    pagWrapper.classList.add("d-none");

    currentPage = page;

    fetch(`../../BE/audit_logs/list.php?page=${page}`)
        .then(res => {
            if (res.status === 401) {
                window.location.href = "login.php";
                return;
            }
            return res.json();
        })
        .then(data => {
            loading.classList.add("d-none");

            if (data.logs.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-journal-text fs-1 mb-2 text-secondary opacity-50 d-block"></i>
                            Hiện chưa có ghi nhận hoạt động nào.
                        </td>
                    </tr>
                `;
                wrapper.classList.remove("d-none");
                return;
            }

            // Tính toán STT bắt đầu
            const offset = (data.pagination.page - 1) * data.pagination.limit;
            let html = "";
            data.logs.forEach((l, idx) => {
                const action = (l.action || "").toUpperCase();
                let actionBadge = "";
                if (action === 'LOGIN') {
                    actionBadge = '<span class="badge bg-primary px-2.5 py-1.5"><i class="bi bi-box-arrow-in-right me-1"></i> LOGIN</span>';
                } else if (action === 'LOGOUT') {
                    actionBadge = '<span class="badge bg-secondary px-2.5 py-1.5"><i class="bi bi-box-arrow-left me-1"></i> LOGOUT</span>';
                } else if (action === 'INSERT') {
                    actionBadge = '<span class="badge bg-success px-2.5 py-1.5"><i class="bi bi-plus-lg me-1"></i> INSERT</span>';
                } else if (action === 'UPDATE') {
                    actionBadge = '<span class="badge bg-warning text-dark px-2.5 py-1.5"><i class="bi bi-pencil-square me-1"></i> UPDATE</span>';
                } else if (action === 'DELETE') {
                    actionBadge = '<span class="badge bg-danger px-2.5 py-1.5"><i class="bi bi-trash3-fill me-1"></i> DELETE</span>';
                } else {
                    actionBadge = `<span class="badge bg-dark px-2.5 py-1.5">${action}</span>`;
                }

                const createdDate = formatDateTime(l.created_at);

                html += `
                    <tr>
                        <td class="ps-4 fw-bold text-secondary">${offset + idx + 1}</td>
                        <td class="fw-semibold text-primary">
                            <i class="bi bi-person-circle me-1"></i>
                            ${l.username || 'Hệ thống'}
                        </td>
                        <td>${actionBadge}</td>
                        <td>
                            <span class="fw-medium text-dark">
                                ${l.entity_type || 'Chưa rõ'}
                            </span>
                        </td>
                        <td>
                            <span class="font-monospace fw-bold text-secondary bg-light px-2 py-0.5 rounded border small">
                                #${l.entity_id || 'N/A'}
                            </span>
                        </td>
                        <td class="pe-4 text-muted small">${createdDate}</td>
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
                    <td colspan="6" class="text-center py-5 text-danger small">
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

    info.textContent = `Hiển thị trang ${meta.page} / ${meta.total_pages} (Tổng cộng ${meta.total_rows} bản ghi log)`;
    
    let html = "";
    
    // Nút Trước
    html += `
        <li class="page-item ${meta.page <= 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadLogs(${meta.page - 1}); return false;">Trước</a>
        </li>
    `;

    // Các trang
    for (let i = 1; i <= meta.total_pages; i++) {
        html += `
            <li class="page-item ${i === meta.page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadLogs(${i}); return false;">${i}</a>
            </li>
        `;
    }

    // Nút Tiếp
    html += `
        <li class="page-item ${meta.page >= meta.total_pages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadLogs(${meta.page + 1}); return false;">Tiếp</a>
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
