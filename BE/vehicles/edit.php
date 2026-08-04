<?php
// BE/vehicles/edit.php
header('Content-Type: application/json; charset=utf-8');

require_once '../config/database.php';
require_once '../auth/session.php';

// Yêu cầu quyền admin
check_admin_login();

// Nhận và parse dữ liệu JSON hoặc POST
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$id = isset($input['id']) && is_numeric($input['id']) ? (int)$input['id'] : 0;
$license_plate = isset($input['license_plate']) ? trim(strtoupper($input['license_plate'])) : '';
$vehicle_type = isset($input['vehicle_type']) ? trim($input['vehicle_type']) : '';
$brand = isset($input['brand']) ? trim($input['brand']) : '';
$model = isset($input['model']) ? trim($input['model']) : '';
$color = isset($input['color']) ? trim($input['color']) : '';
$engine_number = isset($input['engine_number']) ? trim($input['engine_number']) : '';
$chassis_number = isset($input['chassis_number']) ? trim($input['chassis_number']) : '';
$registration_date = !empty($input['registration_date']) ? $input['registration_date'] : null;
$inspection_expiry = !empty($input['inspection_expiry']) ? $input['inspection_expiry'] : null;
$status = isset($input['status']) ? trim($input['status']) : 'Bình thường';
$owner_name = isset($input['owner_name']) ? trim($input['owner_name']) : '';
$owner_id_no = isset($input['owner_id_no']) ? trim($input['owner_id_no']) : '';
$owner_phone = isset($input['owner_phone']) ? trim($input['owner_phone']) : '';
$owner_email = isset($input['owner_email']) ? trim($input['owner_email']) : '';

if ($id <= 0 || empty($license_plate) || empty($owner_name)) {
    http_response_code(400);
    echo json_encode(['error' => 'Thông tin chỉnh sửa phương tiện không hợp lệ hoặc thiếu.']);
    exit();
}

try {
    // Kiểm tra xem biển số mới có trùng với phương tiện khác không
    $stmt_check = $conn->prepare("
        SELECT COUNT(*) 
        FROM vehicles 
        WHERE REPLACE(REPLACE(UPPER(license_plate), '-', ''), ' ', '') = REPLACE(REPLACE(UPPER(?), '-', ''), ' ', '') 
          AND id != ?
    ");
    $stmt_check->execute([$license_plate, $id]);
    $exists = $stmt_check->fetchColumn();

    if ($exists > 0) {
        http_response_code(400);
        echo json_encode(['error' => "Biển kiểm soát '$license_plate' đã được sử dụng bởi phương tiện khác."]);
        exit();
    }

    // Thực hiện Cập nhật
    $sql = "UPDATE vehicles SET 
                license_plate = ?, vehicle_type = ?, brand = ?, model = ?, color = ?, 
                engine_number = ?, chassis_number = ?, registration_date = ?, inspection_expiry = ?, 
                status = ?, owner_name = ?, owner_id_no = ?, owner_phone = ?, owner_email = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $license_plate, $vehicle_type, $brand, $model, $color,
        $engine_number, $chassis_number, $registration_date, $inspection_expiry,
        $status, $owner_name, $owner_id_no, $owner_phone, $owner_email, $id
    ]);

    // Ghi log hoạt động UPDATE
    write_audit_log($conn, $_SESSION['admin_id'], 'UPDATE', 'Vehicle', $id);

    echo json_encode([
        'success' => true,
        'message' => "Đã cập nhật thông tin phương tiện '$license_plate' thành công."
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()]);
}
