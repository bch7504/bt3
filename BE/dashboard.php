<?php
// BE/dashboard.php
header('Content-Type: application/json; charset=utf-8');

require_once 'config/database.php';
require_once 'auth/session.php';

// Yêu cầu quyền admin
check_admin_login();

try {
    // 1. Thống kê phương tiện
    $total_vehicles = $conn->query("SELECT COUNT(*) FROM vehicles")->fetchColumn();

    // 2. Thống kê vi phạm
    $total_violations = $conn->query("SELECT COUNT(*) FROM violations")->fetchColumn();

    // 3. Thống kê vi phạm chưa xử lý
    $unpaid_violations = $conn->query("
        SELECT COUNT(*) 
        FROM violations 
        WHERE LOWER(status) IN ('chưa xử lý', 'chưa nộp phạt', 'unpaid', 'chưa nộp', 'chua nop phat', 'chua xu ly')
    ")->fetchColumn();

    // 4. Thống kê thông báo
    $total_notifications = $conn->query("SELECT COUNT(*) FROM notifications")->fetchColumn();

    // 5. Lấy danh sách 10 vi phạm mới nhất
    $stmt_recent = $conn->query("
        SELECT v.*, ve.license_plate 
        FROM violations v 
        LEFT JOIN vehicles ve ON v.vehicle_id = ve.id 
        ORDER BY v.violated_at DESC 
        LIMIT 10
    ");
    $recent_violations = $stmt_recent->fetchAll();

    echo json_encode([
        'success' => true,
        'stats' => [
            'total_vehicles' => (int)$total_vehicles,
            'total_violations' => (int)$total_violations,
            'unpaid_violations' => (int)$unpaid_violations,
            'total_notifications' => (int)$total_notifications
        ],
        'recent_violations' => $recent_violations
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()]);
}
