<?php
// FE/admin/index.php
define('APP_RUNNING', true);

require_once 'components/auth.php';

// Kiểm tra đăng nhập, yêu cầu chuyển hướng về login.php nếu chưa đăng nhập
check_login('login.php');

// Lấy tham số trang con, mặc định là dashboard
$page = isset($_GET['page']) ? trim($_GET['page']) : 'dashboard';

// Bảo vệ tránh Directory Traversal
$allowed_pages = ['dashboard', 'vehicles', 'violations', 'notifications', 'audit_logs'];
if (!in_array($page, $allowed_pages)) {
    $page = 'dashboard';
}

// Cấu hình tiêu đề động
$page_title = 'CSGT Admin';
if ($page === 'dashboard') {
    $page_title = 'Bảng điều khiển - CSGT Admin';
} elseif ($page === 'vehicles') {
    $page_title = 'Quản lý phương tiện - CSGT Admin';
} elseif ($page === 'violations') {
    $page_title = 'Quản lý lỗi vi phạm - CSGT Admin';
} elseif ($page === 'notifications') {
    $page_title = 'Nhật ký thông báo - CSGT Admin';
} elseif ($page === 'audit_logs') {
    $page_title = 'Nhật ký hoạt động - CSGT Admin';
}

// Render layout
include 'components/header.php';
include "pages/{$page}.php";
include 'components/footer.php';
