<?php
// BE/customer/lookup.php
header('Content-Type: application/json; charset=utf-8');

require_once '../config/database.php';

$license_plate = isset($_GET['license_plate']) ? trim($_GET['license_plate']) : '';

if (empty($license_plate)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Vui lòng nhập biển số xe để tra cứu.']);
    exit();
}

try {
    // Chuẩn hóa biển số xe để so sánh (bỏ khoảng trắng và dấu gạch ngang, viết hoa)
    $normalized_input = str_replace([' ', '-'], '', strtoupper($license_plate));

    // 1. Tìm thông tin xe
    $stmt_vehicle = $conn->prepare("
        SELECT * 
        FROM vehicles 
        WHERE REPLACE(REPLACE(UPPER(license_plate), '-', ''), ' ', '') = ? 
        LIMIT 1
    ");
    $stmt_vehicle->execute([$normalized_input]);
    $vehicle = $stmt_vehicle->fetch();

    if (!$vehicle) {
        // Trả về thành công nhưng thông báo không tìm thấy phương tiện
        echo json_encode([
            'success' => false,
            'error' => "Không tìm thấy phương tiện biển số '" . htmlspecialchars($license_plate) . "' trong hệ thống."
        ]);
        exit();
    }

    // 2. Tìm danh sách lỗi vi phạm của phương tiện
    $stmt_violations = $conn->prepare("
        SELECT * 
        FROM violations 
        WHERE vehicle_id = ? 
        ORDER BY violated_at DESC
    ");
    $stmt_violations->execute([$vehicle['id']]);
    $violations = $stmt_violations->fetchAll();

    echo json_encode([
        'success' => true,
        'vehicle' => $vehicle,
        'violations' => $violations
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Lỗi kết nối cơ sở dữ liệu: ' . $e->getMessage()]);
}
