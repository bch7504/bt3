<?php
// FE/admin/login.php
define('APP_RUNNING', true);
// Khởi chạy session ở FE chỉ để giữ liên kết session cookie
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Hệ thống Quản trị - CSGT</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0b132b 0%, #1c2541 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
            background-color: #ffffff;
            overflow: hidden;
            width: 100%;
            max-width: 420px;
        }
        .login-header {
            background-color: #0b132b;
            color: #ffffff;
            padding: 2rem 1.5rem;
            text-align: center;
            border-bottom: 4px solid #f59e0b;
        }
        .btn-login {
            background-color: #0b132b;
            color: #ffffff;
            border: none;
            font-weight: 600;
            padding: 0.8rem;
            transition: all 0.2s ease;
        }
        .btn-login:hover {
            background-color: #3a86c8;
            color: #ffffff;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <i class="bi bi-shield-lock-fill fs-1 text-warning"></i>
            <h4 class="fw-bold mt-2 text-uppercase tracking-wider">Hệ thống Quản trị</h4>
            <span class="text-white-50 small">Tra cứu & Xử lý vi phạm giao thông</span>
        </div>
        <div class="card-body p-4">
            
            <!-- Box lỗi hiển thị động -->
            <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center gap-2 small mb-3 d-none" id="error-box" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div id="error-message"></div>
            </div>

            <form id="login-form" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="username" class="form-label fw-semibold text-muted">Tên đăng nhập</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-secondary">
                            <i class="bi bi-person-fill"></i>
                        </span>
                        <input type="text" name="username" id="username" class="form-control border-start-0 bg-light" placeholder="Nhập tên đăng nhập" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label fw-semibold text-muted">Mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-secondary">
                            <i class="bi bi-key-fill"></i>
                        </span>
                        <input type="password" name="password" id="password" class="form-control border-start-0 bg-light" placeholder="Nhập mật khẩu" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-login w-100 rounded-3 shadow mb-2" id="btn-submit">
                    ĐĂNG NHẬP
                </button>
            </form>

            <div class="mt-3 text-center">
                <a href="../customer/index.php" class="text-decoration-none text-secondary small hover-text-dark">
                    <i class="bi bi-arrow-left"></i> Quay lại Cổng tra cứu công cộng
                </a>
            </div>
        </div>
    </div>

    <!-- Script xử lý đăng nhập bằng Fetch API -->
    <script>
        document.getElementById('login-form').addEventListener('submit', function (event) {
            event.preventDefault();
            event.stopPropagation();
            
            const form = event.currentTarget;
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }

            form.classList.add('was-validated');

            const usernameInput = document.getElementById('username').value.trim();
            const passwordInput = document.getElementById('password').value.trim();
            const errorBox = document.getElementById('error-box');
            const errorMessage = document.getElementById('error-message');
            const btnSubmit = document.getElementById('btn-submit');

            // Disabled button để tránh spam click
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> ĐANG XỬ LÝ...';
            errorBox.classList.add('d-none');

            fetch('../../BE/auth/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    username: usernameInput,
                    password: passwordInput
                })
            })
            .then(response => {
                return response.json().then(data => {
                    if (!response.ok) {
                        throw new Error(data.error || "Lỗi đăng nhập không xác định.");
                    }
                    return data;
                });
            })
            .then(data => {
                if (data.success) {
                    window.location.href = 'index.php';
                } else {
                    throw new Error(data.error || "Tên đăng nhập hoặc mật khẩu không đúng.");
                }
            })
            .catch(error => {
                errorMessage.textContent = error.message;
                errorBox.classList.remove('d-none');
                btnSubmit.disabled = false;
                btnSubmit.textContent = 'ĐĂNG NHẬP';
            });
        });
    </script>
</body>
</html>
