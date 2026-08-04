<?php
// BE/vehicles/view.php
header('Content-Type: application/json; charset=utf-8');

require_once '../config/database.php';
require_once '../auth/session.php';

// Yêu cầu quyền admin
check_admin_login();

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID phương tiện không hợp lệ.']);
    exit();
}

try {
    $stmt = $conn->prepare("SELECT * FROM vehicles WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $vehicle = $stmt->fetch();

    if (!$vehicle) {
        http_response_code(404);
        echo json_encode(['error' => 'Không tìm thấy phương tiện.']);
        exit();
    }

    // Lấy thêm các vi phạm liên quan để hỗ trợ xem đầy đủ thông tin
    $stmt_violation = $conn->prepare("SELECT * FROM violations WHERE vehicle_id = ? ORDER BY violated_at DESC");
    $stmt_violation->execute([$id]);
    $violations = $stmt_violation->fetchAll();

    echo json_encode([
        'success' => true,
        'vehicle' => $vehicle,
        'violations' => $violations
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()]);
}
