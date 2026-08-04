<?php
// BE/violations/edit.php
header('Content-Type: application/json; charset=utf-8');

require_once '../config/database.php';
require_once '../auth/session.php';

// Yêu cầu quyền admin
check_admin_login();

// Nhận và parse dữ liệu JSON hoặc POST
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$id = isset($input['id']) && is_numeric($input['id']) ? (int)$input['id'] : 0;
$vehicle_id = isset($input['vehicle_id']) && is_numeric($input['vehicle_id']) ? (int)$input['vehicle_id'] : 0;
$violated_at = isset($input['violated_at']) ? trim($input['violated_at']) : '';
$province = isset($input['province']) ? trim($input['province']) : '';
$location = isset($input['location']) ? trim($input['location']) : '';
$description = isset($input['description']) ? trim($input['description']) : '';
$decision_no = isset($input['decision_no']) ? trim(strtoupper($input['decision_no'])) : '';
$due_date = !empty($input['due_date']) ? $input['due_date'] : null;
$status = isset($input['status']) ? trim($input['status']) : 'Chưa nộp phạt';

if ($id <= 0 || $vehicle_id <= 0 || empty($violated_at) || empty($province) || empty($location) || empty($description)) {
    http_response_code(400);
    echo json_encode(['error' => 'Thông tin chỉnh sửa vi phạm không hợp lệ hoặc thiếu.']);
    exit();
}

try {
    // Thực hiện cập nhật
    $sql = "UPDATE violations SET 
                vehicle_id = ?, description = ?, violated_at = ?, status = ?, 
                decision_no = ?, due_date = ?, province = ?, location = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $vehicle_id, $description, $violated_at, $status,
        $decision_no, $due_date, $province, $location, $id
    ]);

    // Ghi log hoạt động UPDATE
    write_audit_log($conn, $_SESSION['admin_id'], 'UPDATE', 'Violation', $id);

    echo json_encode([
        'success' => true,
        'message' => 'Đã cập nhật lỗi vi phạm thành công.'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()]);
}
