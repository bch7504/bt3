<?php
// FE/admin/logout.php
define('APP_RUNNING', true);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đang đăng xuất...</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">
    <div class="text-center">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
        <p class="mt-3 fw-medium text-secondary">Đang thực hiện đăng xuất an toàn...</p>
    </div>

    <script>
        fetch('../../BE/auth/logout.php')
            .then(() => {
                window.location.href = 'login.php';
            })
            .catch(() => {
                window.location.href = 'login.php';
            });
    </script>
</body>
</html>
