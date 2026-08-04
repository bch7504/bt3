<?php
// FE/customer/index.php
// Định nghĩa hằng số bảo vệ ngăn chặn việc truy cập trực tiếp vào các file component và page con
define('APP_RUNNING', true);

// Bắt đầu session phục vụ lưu trữ trạng thái nếu cần
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Quyết định trang hiển thị dựa trên tham số gửi lên
$page = 'home';
if (isset($_GET['page'])) {
    $page = trim($_GET['page']);
}
if (isset($_GET['license_plate']) && trim($_GET['license_plate']) !== '') {
    $page = 'result';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tra cứu phương tiện vi phạm giao thông - Cổng thông tin phạt nguội toàn quốc</title>
    
    <!-- Meta SEO -->
    <meta name="description" content="Cổng thông tin tra cứu phạt nguội vi phạm giao thông đường bộ trên toàn quốc. Cập nhật dữ liệu chính xác trực tiếp từ Cục Cảnh sát giao thông.">
    <meta name="keywords" content="phat nguoi, tra cuu phat nguoi, vi pham giao thong, csgt, bien so xe">
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

    <!-- Header / Navbar -->
    <?php include 'components/navbar.php'; ?>

    <!-- Hero Section / Search Box -->
    <?php 
    if ($page !== 'guide') {
        include 'components/hero.php'; 
    }
    ?>

    <!-- Main Content Area -->
    <main class="container py-5 flex-grow-1">
        <?php 
        if ($page === 'result') {
            include 'pages/result.php';
        } elseif ($page === 'guide') {
            include 'pages/guide.php';
        } else {
            include 'pages/home.php';
        }
        ?>
    </main>

    <!-- Footer -->
    <?php include 'components/footer.php'; ?>

    <!-- Bootstrap 5.3 Bundle JS with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
</body>
</html>
