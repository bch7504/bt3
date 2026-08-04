<?php
// BE/vehicles/list.php
header('Content-Type: application/json; charset=utf-8');

require_once '../config/database.php';
require_once '../auth/session.php';

// Yêu cầu quyền admin
check_admin_login();

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

try {
    $where_clause = "";
    $params = [];
    if ($search !== '') {
        $where_clause = "WHERE license_plate ILIKE :search OR owner_name ILIKE :search OR vehicle_type ILIKE :search OR brand ILIKE :search OR model ILIKE :search";
        $params['search'] = "%$search%";
    }

    // Tính tổng số lượng
    $count_sql = "SELECT COUNT(*) FROM vehicles $where_clause";
    $stmt_count = $conn->prepare($count_sql);
    $stmt_count->execute($params);
    $total_rows = $stmt_count->fetchColumn();
    $total_pages = ceil($total_rows / $limit);
    if ($total_pages < 1) $total_pages = 1;
    if ($page > $total_pages) $page = $total_pages;
    if ($page < 1) $page = 1;
    $offset = ($page - 1) * $limit;

    // Lấy dữ liệu phân trang
    $sql = "SELECT * FROM vehicles $where_clause ORDER BY id DESC LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    if ($search !== '') {
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    }
    $stmt->execute();
    $vehicles = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'vehicles' => $vehicles,
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
