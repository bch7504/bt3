<?php
// BE/violations/list.php
header('Content-Type: application/json; charset=utf-8');

require_once '../config/database.php';
require_once '../auth/session.php';

// Yêu cầu quyền admin
check_admin_login();

// Bộ lọc
$filter_province = isset($_GET['province']) ? trim($_GET['province']) : '';
$filter_status = isset($_GET['status']) ? trim($_GET['status']) : '';
$filter_date = isset($_GET['date']) ? trim($_GET['date']) : '';
$search_plate = isset($_GET['search_plate']) ? trim($_GET['search_plate']) : '';

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

try {
    // 1. Lấy danh sách tỉnh duy nhất để phục vụ bộ lọc
    $provinces = $conn->query("SELECT DISTINCT province FROM violations WHERE province IS NOT NULL AND province != '' ORDER BY province ASC")->fetchAll(PDO::FETCH_COLUMN);

    // 2. Lấy danh sách trạng thái duy nhất để phục vụ bộ lọc
    $statuses = $conn->query("SELECT DISTINCT status FROM violations WHERE status IS NOT NULL AND status != '' ORDER BY status ASC")->fetchAll(PDO::FETCH_COLUMN);

    // 3. Lấy danh sách phương tiện để phục vụ Dropdown thêm mới / chỉnh sửa
    $vehicles_list = $conn->query("SELECT id, license_plate, owner_name FROM vehicles ORDER BY license_plate ASC")->fetchAll();

    // 4. Xây dựng câu truy vấn lọc
    $where_clauses = [];
    $params = [];

    if ($filter_province !== '') {
        $where_clauses[] = "v.province = :province";
        $params['province'] = $filter_province;
    }
    if ($filter_status !== '') {
        if ($filter_status === 'Đã nộp phạt' || $filter_status === 'paid' || $filter_status === 'Đã xử lý' || $filter_status === 'Đã nộp') {
            $where_clauses[] = "v.status IN ('paid', 'Đã nộp phạt', 'Đã xử lý', 'Đã nộp', 'da nop phat')";
        } else {
            $where_clauses[] = "v.status NOT IN ('paid', 'Đã nộp phạt', 'Đã xử lý', 'Đã nộp', 'da nop phat') OR v.status IS NULL";
        }
    }
    if ($filter_date !== '') {
        $where_clauses[] = "v.violated_at::date = :violated_at";
        $params['violated_at'] = $filter_date;
    }
    if ($search_plate !== '') {
        $where_clauses[] = "ve.license_plate ILIKE :search_plate";
        $params['search_plate'] = "%$search_plate%";
    }

    $where_sql = "";
    if (!empty($where_clauses)) {
        $where_sql = "WHERE " . implode(" AND ", $where_clauses);
    }

    // 5. Tính tổng số lượng để phân trang
    $count_sql = "
        SELECT COUNT(*) 
        FROM violations v 
        LEFT JOIN vehicles ve ON v.vehicle_id = ve.id 
        $where_sql
    ";
    $stmt_count = $conn->prepare($count_sql);
    foreach ($params as $key => $val) {
        $stmt_count->bindValue(":$key", $val, PDO::PARAM_STR);
    }
    $stmt_count->execute();
    $total_rows = $stmt_count->fetchColumn();
    $total_pages = ceil($total_rows / $limit);
    if ($total_pages < 1) $total_pages = 1;
    if ($page > $total_pages) $page = $total_pages;
    if ($page < 1) $page = 1;
    $offset = ($page - 1) * $limit;

    // 6. Lấy dữ liệu phân trang
    $sql = "
        SELECT v.*, ve.license_plate, ve.owner_name 
        FROM violations v 
        LEFT JOIN vehicles ve ON v.vehicle_id = ve.id 
        $where_sql 
        ORDER BY v.violated_at DESC 
        LIMIT :limit OFFSET :offset
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    foreach ($params as $key => $val) {
        $stmt->bindValue(":$key", $val, PDO::PARAM_STR);
    }
    $stmt->execute();
    $violations = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'violations' => $violations,
        'provinces' => $provinces,
        'statuses' => $statuses,
        'vehicles_list' => $vehicles_list,
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
