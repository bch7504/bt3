<?php
// BE/vehicles/delete.php
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
    echo json_encode(['error' => 'ID phương tiện không hợp lệ.']);
    exit();
}

try {
    // 1. Lấy thông tin biển số xe để ghi log trước khi xóa
    $stmt_plate = $conn->prepare("SELECT license_plate FROM vehicles WHERE id = ? LIMIT 1");
    $stmt_plate->execute([$id]);
    $license_plate = $stmt_plate->fetchColumn();

    if (!$license_plate) {
        http_response_code(404);
        echo json_encode(['error' => 'Không tìm thấy phương tiện để xóa.']);
        exit();
    }

    // 2. Kiểm tra xem phương tiện có lỗi vi phạm nào chưa xử lý/đã xử lý không
    // Quy tắc: Không xóa phương tiện nếu còn bản ghi trong bảng violations
    $stmt_check = $conn->prepare("SELECT COUNT(*) FROM violations WHERE vehicle_id = ?");
    $stmt_check->execute([$id]);
    $violation_count = $stmt_check->fetchColumn();

    if ($violation_count > 0) {
        http_response_code(400);
        echo json_encode(['error' => "Không thể xóa! Phương tiện biển số '$license_plate' đang có lịch sử vi phạm giao thông ($violation_count lỗi)."]);
        exit();
    }

    // 3. Thực hiện Xóa
    $stmt_delete = $conn->prepare("DELETE FROM vehicles WHERE id = ?");
    $stmt_delete->execute([$id]);

    // Ghi log hoạt động DELETE
    write_audit_log($conn, $_SESSION['admin_id'], 'DELETE', 'Vehicle', $id);

    echo json_encode([
        'success' => true,
        'message' => "Đã xóa phương tiện biển kiểm soát '$license_plate' ra khỏi hệ thống thành công."
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Lỗi hệ thống khi xóa phương tiện: ' . $e->getMessage()]);
}
