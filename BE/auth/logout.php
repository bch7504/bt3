<?php
// BE/auth/logout.php
header('Content-Type: application/json; charset=utf-8');

require_once '../config/database.php';
require_once 'session.php';

// Ghi log hoạt động LOGOUT nếu tài khoản đang đăng nhập
if (isset($_SESSION['admin_id'])) {
    write_audit_log($conn, $_SESSION['admin_id'], 'LOGOUT', 'Admin', $_SESSION['admin_id']);
}

// Xóa session
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

echo json_encode(['success' => true, 'message' => 'Đã đăng xuất thành công.']);
