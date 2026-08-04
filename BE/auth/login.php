<?php
// BE/auth/login.php
header('Content-Type: application/json; charset=utf-8');

require_once '../config/database.php';
require_once 'session.php';

// Đọc và parse dữ liệu JSON gửi lên nếu có, nếu không thì lấy từ $_POST
$input = json_decode(file_get_contents('php://input'), true);
$username = isset($input['username']) ? trim($input['username']) : (isset($_POST['username']) ? trim($_POST['username']) : '');
$password = isset($input['password']) ? trim($input['password']) : (isset($_POST['password']) ? trim($_POST['password']) : '');

if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Vui lòng nhập tài khoản và mật khẩu.']);
    exit();
}

try {
    $admin = null;
    $auth_success = false;

    // 1. Kiểm tra tài khoản trong bảng admins trước
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $db_admin = $stmt->fetch();

    if ($db_admin) {
        // Tài khoản tồn tại trong bảng admins, kiểm tra mật khẩu qua password_hash
        if (password_verify($password, $db_admin['password_hash'])) {
            $admin = $db_admin;
            $auth_success = true;
        }
    }

    // 2. Dự phòng (Fallback): Kiểm tra thông tin kết nối DB trong database.php
    if (!$auth_success) {
        // Lưu giá trị request vào biến tạm tránh bị đè khi include file config
        $req_username = $username;
        $req_password = $password;
        
        // Đọc lại biến cấu hình từ file database.php
        include '../config/database.php';
        
        // So sánh thông tin đăng nhập với thông tin kết nối DB
        if ($req_username === $username && $req_password === $password) {
            $auth_success = true;

            // Thiết lập lại biến username cho đúng
            $username = $req_username;

            // Nếu khớp và tài khoản chưa tồn tại trong bảng admins, tự động tạo mới tài khoản
            if (!$db_admin) {
                $hashed_password = password_hash($req_password, PASSWORD_DEFAULT);
                $stmt_insert = $conn->prepare("
                    INSERT INTO admins (username, password_hash, created_at)
                    VALUES (?, ?, NOW())
                ");
                $stmt_insert->execute([$username, $hashed_password]);
                
                // Lấy lại thông tin admin vừa tạo
                $stmt_fetch = $conn->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
                $stmt_fetch->execute([$username]);
                $admin = $stmt_fetch->fetch();
            } else {
                $admin = $db_admin;
            }
        }
    }

    if ($auth_success && $admin) {
        // Lưu thông tin đăng nhập vào Session
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_fullname'] = $admin['username']; // Fallback vì bảng admins không có cột fullname

        // Ghi nhận nhật ký audit log LOGIN
        write_audit_log($conn, $admin['id'], 'LOGIN', 'Admin', $admin['id']);

        echo json_encode([
            'success' => true,
            'message' => 'Đăng nhập thành công.',
            'admin' => [
                'id' => $admin['id'],
                'username' => $admin['username'],
                'fullname' => $admin['username']
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Tên đăng nhập hoặc mật khẩu không chính xác.']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
