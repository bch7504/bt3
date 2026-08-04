<?php
// BE/auth/session.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Kiểm tra xem admin đã đăng nhập chưa. 
 * Nếu chưa, trả về JSON 401 Unauthorized và kết thúc thực thi.
 */
function check_admin_login() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(401);
        echo json_encode(['error' => 'Chưa đăng nhập hoặc phiên làm việc đã hết hạn.', 'code' => 401]);
        exit();
    }
}

/**
 * Ghi log hoạt động của admin vào bảng audit_logs
 */
function write_audit_log($conn, $admin_id, $action, $entity_type, $entity_id) {
    try {
        $stmt = $conn->prepare("
            INSERT INTO audit_logs (admin_id, action, entity_type, entity_id, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$admin_id, $action, $entity_type, $entity_id]);
    } catch (PDOException $e) {
        // Bỏ qua lỗi ghi log để tránh gián đoạn các API chính
    }
}
