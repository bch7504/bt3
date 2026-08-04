<?php
// BE/violations/delete.php
header('Content-Type: application/json; charset=utf-8');

require_once '../config/database.php';
require_once '../auth/session.php';

// Yêu cầu quyền admin
check_admin_login();

// Nhận tham số id từ GET hoặc POST/JSON
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($input['id']) ? (int)$input['id'] : 0);

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID lỗi vi phạm không hợp lệ.']);
    exit();
}

try {
    // 1. Xóa các thông báo liên quan trước để tránh lỗi ràng buộc khóa ngoại (foreign key constraint)
    $stmt_noti = $conn->prepare("DELETE FROM notifications WHERE violation_id = ?");
    $stmt_noti->execute([$id]);

    // 2. Thực hiện Xóa vi phạm
    $stmt_delete = $conn->prepare("DELETE FROM violations WHERE id = ?");
    $stmt_delete->execute([$id]);

    // Ghi log hoạt động DELETE
    write_audit_log($conn, $_SESSION['admin_id'], 'DELETE', 'Violation', $id);

    echo json_encode([
        'success' => true,
        'message' => "Đã xóa bản ghi lỗi vi phạm thành công."
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Lỗi hệ thống khi xóa vi phạm: ' . $e->getMessage()]);
}
