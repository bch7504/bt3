<?php
// BE/audit_logs/list.php
header('Content-Type: application/json; charset=utf-8');

require_once '../config/database.php';
require_once '../auth/session.php';

// Yêu cầu quyền admin
check_admin_login();

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 15;
$offset = ($page - 1) * $limit;

try {
    // Tính tổng số lượng dòng log
    $total_rows = $conn->query("SELECT COUNT(*) FROM audit_logs")->fetchColumn();
    $total_pages = ceil($total_rows / $limit);
    if ($total_pages < 1) $total_pages = 1;
    if ($page > $total_pages) $page = $total_pages;
    if ($page < 1) $page = 1;
    $offset = ($page - 1) * $limit;

    // Lấy dữ liệu log join với bảng admins để lấy username
    $sql = "
        SELECT al.*, ad.username 
        FROM audit_logs al 
        LEFT JOIN admins ad ON al.admin_id = ad.id 
        ORDER BY al.created_at DESC 
        LIMIT :limit OFFSET :offset
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $logs = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'logs' => $logs,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total_rows' => (int)$total_rows,
            'total_pages' => $total_pages
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()]);
}
