<?php
// FE/admin/components/auth.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Kiểm tra xem Admin đã đăng nhập chưa. Nếu chưa, chuyển hướng về trang login.
 */
function check_login($login_path = 'login.php') {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: " . $login_path);
        exit();
    }
}
