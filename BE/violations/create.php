<?php
// BE/violations/create.php
header('Content-Type: application/json; charset=utf-8');

require_once '../config/database.php';
require_once '../auth/session.php';

// Yêu cầu quyền admin
check_admin_login();

// Nhận và parse dữ liệu JSON hoặc POST
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$vehicle_id = isset($input['vehicle_id']) && is_numeric($input['vehicle_id']) ? (int)$input['vehicle_id'] : 0;
$violated_at = isset($input['violated_at']) ? trim($input['violated_at']) : '';
$province = isset($input['province']) ? trim($input['province']) : '';
$location = isset($input['location']) ? trim($input['location']) : '';
$description = isset($input['description']) ? trim($input['description']) : '';
$decision_no = isset($input['decision_no']) ? trim(strtoupper($input['decision_no'])) : '';
$due_date = !empty($input['due_date']) ? $input['due_date'] : null;
$status = isset($input['status']) ? trim($input['status']) : 'Chưa nộp phạt';

if ($vehicle_id <= 0 || empty($violated_at) || empty($province) || empty($location) || empty($description)) {
    http_response_code(400);
    echo json_encode(['error' => 'Vui lòng điền đầy đủ các thông tin bắt buộc.']);
    exit();
}

try {
    // Lấy thông tin phương tiện để phục vụ tạo thông báo và ghi log
    $stmt_vehicle = $conn->prepare("SELECT license_plate, owner_name, owner_phone, owner_email FROM vehicles WHERE id = ? LIMIT 1");
    $stmt_vehicle->execute([$vehicle_id]);
    $vehicle = $stmt_vehicle->fetch();

    if (!$vehicle) {
        http_response_code(404);
        echo json_encode(['error' => 'Không tìm thấy phương tiện được chọn trong hệ thống.']);
        exit();
    }

    // 1. Thực hiện Insert lỗi vi phạm
    $sql = "INSERT INTO violations (
                vehicle_id, description, violated_at, status, 
                decision_no, due_date, province, location
            ) VALUES (
                ?, ?, ?, ?, 
                ?, ?, ?, ?
            )";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $vehicle_id, $description, $violated_at, $status,
        $decision_no, $due_date, $province, $location
    ]);

    $violation_id = $conn->lastInsertId();

    // 2. Ghi log hoạt động INSERT
    write_audit_log($conn, $_SESSION['admin_id'], 'INSERT', 'Violation', $violation_id);

    // 3. TỰ ĐỘNG tạo thông báo (Notification) gửi tới chủ xe
    $recipient = '';
    $channel = 'Hệ thống';
    
    if (!empty($vehicle['owner_phone'])) {
        $recipient = $vehicle['owner_phone'];
        $channel = 'SMS';
    } elseif (!empty($vehicle['owner_email'])) {
        $recipient = $vehicle['owner_email'];
        $channel = 'Email';
    } else {
        $recipient = $vehicle['owner_name'];
        $channel = 'Portal';
    }

    $formatted_time = date('d/m/Y H:i', strtotime($violated_at));
    $notification_message = "Thong bao tu Cuc CSGT: Phuong tien bien so " . $vehicle['license_plate'] . " cua ong/ba " . $vehicle['owner_name'] . " bi ghi nhan vi pham: '" . $description . "' vao luc " . $formatted_time . " tai " . $location . ". Quyet dinh xu phat: " . ($decision_no ?: 'Dang cap nhat') . ". Vui long kiem tra va nop phat dung han.";

    $stmt_noti = $conn->prepare("
        INSERT INTO notifications (violation_id, channel, recipient, message, sent_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt_noti->execute([$violation_id, $channel, $recipient, $notification_message]);

    echo json_encode([
        'success' => true,
        'message' => "Đã ghi nhận vi phạm của xe '" . $vehicle['license_plate'] . "' thành công và tự động gửi thông báo.",
        'id' => $violation_id
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()]);
}
