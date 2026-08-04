<?php
// BE/config/database.php

// Hàm đọc file .env thủ công (để tránh cài thêm thư viện ngoài hoặc composer)
if (!function_exists('loadEnv')) {
    function loadEnv($path) {
        if (!file_exists($path)) {
            return;
        }
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            // Bỏ qua dòng comment hoặc dòng rỗng
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }
            // Tách khóa và giá trị bằng dấu bằng đầu tiên
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                // Xóa dấu nháy kép hoặc nháy đơn bao quanh giá trị nếu có
                $value = trim($value, "\"'");
                
                // Thiết lập vào môi trường
                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

// Load file .env ở thư mục gốc dự án
loadEnv(__DIR__ . '/../../.env');

// Lấy thông tin cấu hình từ môi trường (với các giá trị mặc định dự phòng)
$host     = getenv('DB_HOST') ?: 'localhost';
$port     = getenv('DB_PORT') ?: '5432';
$dbname   = getenv('DB_NAME') ?: 'bt3';
$username = getenv('DB_USER') ?: 'postgres';
$password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '123456';

try {
    $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
    // Thiết lập chế độ báo lỗi PDO thành ngoại lệ
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Thiết lập chế độ trả về dữ liệu mặc định là mảng kết hợp
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode(['error' => 'Lỗi kết nối cơ sở dữ liệu: ' . $e->getMessage()]);
    exit();
}

