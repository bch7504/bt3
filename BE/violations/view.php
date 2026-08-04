<?php
// BE/violations/view.php
header('Content-Type: application/json; charset=utf-8');

require_once '../config/database.php';
require_once '../auth/session.php';

// Yêu cầu quyền admin
check_admin_login();

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID lỗi vi phạm không hợp lệ.']);
    exit();
}

try {
    // Lấy thông tin vi phạm kèm theo phương tiện liên quan
    $stmt = $conn->prepare("
        SELECT v.*, ve.license_plate, ve.owner_name, ve.vehicle_type, ve.brand, ve.model 
        FROM violations v 
        LEFT JOIN vehicles ve ON v.vehicle_id = ve.id 
        WHERE v.id = :id 
        LIMIT 1
    ");
    $stmt->execute(['id' => $id]);
    $violation = $stmt->fetch();

    if (!$violation) {
        http_response_code(404);
        echo json_encode(['error' => 'Không tìm thấy thông tin vi phạm.']);
        exit();
    }

    echo json_encode([
        'success' => true,
        'violation' => $violation
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Lỗi kết nối cơ sở dữ liệu: ' . $e->getMessage()]);
}
