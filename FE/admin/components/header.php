<?php
// FE/admin/components/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Xác định trang hiện tại để active menu
$page = isset($page) ? $page : 'dashboard';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Hệ thống Quản trị - Tra cứu vi phạm'; ?></title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom Admin Style -->
    <style>
        :root {
            --sidebar-width: 260px;
            --topbar-height: 70px;
            --admin-blue: #0b132b;
            --admin-dark: #1c2541;
            --admin-hover: #3a86c8;
        }

        body {
            min-height: 100vh;
            background-color: #f8fafc;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: var(--admin-blue);
            color: #ffffff;
            z-index: 1000;
            transition: all 0.3s ease;
            border-right: 3px solid #f59e0b;
        }

        .sidebar-brand {
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-menu {
            padding: 1.5rem 0;
            list-style: none;
            margin: 0;
        }

        .sidebar-menu li {
            margin-bottom: 0.5rem;
        }

        .sidebar-menu a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 0.8rem 1.5rem;
            gap: 12px;
            font-weight: 500;
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
        }

        .sidebar-menu a:hover, 
        .sidebar-menu li.active a {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.05);
            border-left-color: #f59e0b;
        }

        /* Main Content & Topbar */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .topbar {
            height: var(--topbar-height);
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        .content-body {
            padding: 2rem;
            flex-grow: 1;
        }

        /* Cards and Elements Styling */
        .card-stat {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
            transition: transform 0.2s ease;
        }
        .card-stat:hover {
            transform: translateY(-3px);
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                left: calc(-1 * var(--sidebar-width));
            }
            .sidebar.active {
                left: 0;
            }
            .main-wrapper {
                margin-left: 0;
            }
            .main-wrapper.active {
                margin-left: var(--sidebar-width);
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar" id="adminSidebar">
        <div class="sidebar-brand">
            <a href="index.php?page=dashboard" class="text-white text-decoration-none d-flex align-items-center gap-2 fw-bold text-uppercase">
                <i class="bi bi-shield-shaded text-warning fs-4"></i>
                <span>CSGT Admin</span>
            </a>
        </div>
        <ul class="sidebar-menu">
            <li class="<?php echo ($page === 'dashboard') ? 'active' : ''; ?>">
                <a href="index.php?page=dashboard">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="<?php echo ($page === 'vehicles') ? 'active' : ''; ?>">
                <a href="index.php?page=vehicles">
                    <i class="bi bi-car-front-fill"></i> Quản lý phương tiện
                </a>
            </li>
            <li class="<?php echo ($page === 'violations') ? 'active' : ''; ?>">
                <a href="index.php?page=violations">
                    <i class="bi bi-exclamation-octagon-fill"></i> Quản lý vi phạm
                </a>
            </li>
            <li class="<?php echo ($page === 'notifications') ? 'active' : ''; ?>">
                <a href="index.php?page=notifications">
                    <i class="bi bi-chat-left-dots-fill"></i> Thông báo
                </a>
            </li>
            <li class="<?php echo ($page === 'audit_logs') ? 'active' : ''; ?>">
                <a href="index.php?page=audit_logs">
                    <i class="bi bi-journal-text"></i> Audit Logs
                </a>
            </li>
            <li class="mt-4">
                <a href="logout.php" class="text-warning">
                    <i class="bi bi-box-arrow-right"></i> Đăng xuất
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content Wrapper -->
    <div class="main-wrapper" id="mainWrapper">
        
        <!-- Topbar -->
        <header class="topbar">
            <button class="btn btn-link text-dark d-lg-none" id="sidebarToggle">
                <i class="bi bi-list fs-3"></i>
            </button>
            <div class="ms-auto d-flex align-items-center gap-3">
                <div class="d-none d-sm-block text-end">
                    <div class="fw-semibold text-dark"><?php echo isset($_SESSION['admin_username']) ? htmlspecialchars($_SESSION['admin_username']) : 'Quản trị viên'; ?></div>
                    <small class="text-muted">Administrator</small>
                </div>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 42px; height: 42px;">
                            <?php 
                                $username = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'A';
                                echo strtoupper(substr($username, 0, 1)); 
                            ?>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="profileDropdown">
                        <li><h6 class="dropdown-header">Tài khoản: <?php echo htmlspecialchars($username); ?></h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item d-flex align-items-center gap-2" href="logout.php"><i class="bi bi-box-arrow-right text-danger"></i> Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Body Content -->
        <div class="content-body">
