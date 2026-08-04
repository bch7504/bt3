<?php
// BE/auth/check.php
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    echo json_encode([
        'logged_in' => true,
        'username' => $_SESSION['admin_username'],
        'fullname' => $_SESSION['admin_fullname'],
        'admin_id' => $_SESSION['admin_id']
    ]);
} else {
    echo json_encode(['logged_in' => false]);
}
