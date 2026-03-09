<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng Nhập - UNISCORE</title>
    <link rel="icon" type="image/svg+xml" href="<?php echo (defined('URLROOT') ? rtrim(URLROOT, '/') : '') . '/favicon.svg'; ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --gradient-start: #667eea;
            --gradient-end: #764ba2;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        html {
            overflow-y: auto;
            overflow-x: hidden;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
            position: relative;
            padding: 20px 0;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            animation: float 30s linear infinite;
            z-index: 0;
        }
        
        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(-50px, -50px) rotate(360deg); }
        }
        
        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            padding: 50px 40px;
            text-align: center;
        }
        
        .logo-container {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.3);
            transform: rotate(-5deg);
            transition: transform 0.3s ease;
        }
        
        .logo-container:hover {
            transform: rotate(0deg) scale(1.05);
        }
        
        .logo-container i {
            font-size: 40px;
            color: white;
        }
        
        .login-card h2 {
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }
        
        .login-card .subtitle {
            color: #64748b;
            font-size: 15px;
            margin-bottom: 35px;
        }
        
        .role-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
        }
        
        .role-btn {
            flex: 1;
            padding: 15px 10px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .role-btn:hover {
            border-color: var(--primary-color);
            background: #f8fafc;
        }
        
        .role-btn.active {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.1), rgba(37, 99, 235, 0.05));
        }
        
        .role-btn i {
            font-size: 24px;
            color: var(--primary-color);
            display: block;
            margin-bottom: 8px;
        }
        
        .role-btn span {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
        }
        
        .form-group {
            position: relative;
            margin-bottom: 20px;
            text-align: left;
        }
        
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            transition: color 0.3s;
        }
        
        .input-wrapper input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f8fafc;
        }
        
        .input-wrapper input:focus {
            outline: none;
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }
        
        .input-wrapper input:focus + i,
        .input-wrapper:focus-within i {
            color: var(--primary-color);
        }
        
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 14px;
        }
        
        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-check input {
            width: 18px;
            height: 18px;
            accent-color: var(--primary-color);
        }
        
        .form-check label {
            color: #64748b;
            cursor: pointer;
        }
        
        .forgot-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .forgot-link:hover {
            text-decoration: underline;
        }
        
        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.35);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .alert {
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 25px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }
        
        .footer-text {
            margin-top: 30px;
            color: #94a3b8;
            font-size: 13px;
        }
        
        .footer-text a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        /* Floating shapes decoration */
        .shape {
            position: fixed;
            border-radius: 50%;
            opacity: 0.1;
            z-index: 0;
        }
        
        .shape-1 {
            width: 300px;
            height: 300px;
            background: white;
            top: -100px;
            right: -100px;
        }
        
        .shape-2 {
            width: 200px;
            height: 200px;
            background: white;
            bottom: -50px;
            left: -50px;
        }
        
        /* Responsive - Cho phép scroll trên mobile */
        @media (max-height: 800px) {
            body {
                align-items: flex-start;
                padding: 40px 0;
            }
            
            .login-container {
                margin: auto;
            }
        }
        
        @media (max-height: 600px) {
            .logo-container {
                width: 70px;
                height: 70px;
                margin-bottom: 15px;
            }
            
            .logo-container i {
                font-size: 32px;
            }
            
            .login-card h2 {
                font-size: 24px;
            }
            
            .login-card .subtitle {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    
    <div class="login-container">
        <div class="login-card">
            <div class="logo-container">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h2>Chào mừng đến UNISCORE!</h2>
            <p class="subtitle">Hệ thống quản lý điểm sinh viên chuyên nghiệp</p>
            
            <?php 
            $displayError = $data['error'] ?? '';
            if (isset($_GET['error']) && $_GET['error'] === 'account_disabled') {
                $displayError = 'Tài khoản không hoạt động. Vui lòng liên hệ admin hoặc cố vấn học tập để được hỗ trợ!';
            }
            if (!empty($displayError)): 
            ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($displayError) ?>
            </div>
            <?php endif; ?>
            
            <!-- Role Selector -->
            <div class="role-selector">
                <div class="role-btn active" data-role="admin" onclick="selectRole(this)">
                    <i class="fas fa-user-shield"></i>
                    <span>Quản trị</span>
                </div>
                <div class="role-btn" data-role="teacher" onclick="selectRole(this)">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Giảng viên</span>
                </div>
                <div class="role-btn" data-role="student" onclick="selectRole(this)">
                    <i class="fas fa-user-graduate"></i>
                    <span>Sinh viên</span>
                </div>
            </div>
            
            <form action="index.php?url=Auth/login" method="POST" id="loginForm">
                <input type="hidden" name="role" id="selectedRole" value="admin">
                
                <div class="form-group">
                    <label>Tên đăng nhập</label>
                    <div class="input-wrapper">
                        <input type="text" name="username" id="username" placeholder="Nhập tên đăng nhập" required autocomplete="username">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Mật khẩu</label>
                    <div class="input-wrapper">
                        <input type="password" name="password" id="password" placeholder="Nhập mật khẩu" required autocomplete="current-password">
                        <i class="fas fa-lock"></i>
                    </div>
                </div>
                
                <div class="form-options">
                    <div class="form-check">
                        <input type="checkbox" id="remember" name="remember" checked>
                        <label for="remember">Ghi nhớ đăng nhập</label>
                    </div>
                    <a href="index.php?url=Auth/forgotPassword" class="forgot-link">Quên mật khẩu?</a>
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i>
                    <span id="loginText">Đăng nhập</span>
                </button>
            </form>
            
            <p class="footer-text">
                © <?= date('Y') ?> <strong style="color: #2563eb;">UNISCORE</strong> - Quản lý điểm sinh viên
            </p>
        </div>
    </div>
    
    
    <script>
        function selectRole(element) {
            document.querySelectorAll('.role-btn').forEach(btn => btn.classList.remove('active'));
            element.classList.add('active');
            document.getElementById('selectedRole').value = element.dataset.role;
            
            // Kiểm tra xem có token "ghi nhớ" cho vai trò này không
            checkRememberToken(element.dataset.role);
        }
        
        // Kiểm tra token khi chọn vai trò
        function checkRememberToken(role) {
            const hasToken = document.cookie.includes('remember_token=');
            const loginBtn = document.querySelector('.btn-login');
            const loginText = document.getElementById('loginText');
            
            if (hasToken) {
                // Có token, hiển thị "Tự động đăng nhập..."
                loginText.textContent = 'Tự động đăng nhập...';
                loginBtn.disabled = true;
                
                // Tự động submit form để kiểm tra token
                setTimeout(() => {
                    const form = document.getElementById('loginForm');
                    const usernameInput = document.getElementById('username');
                    const passwordInput = document.getElementById('password');
                    
                    // Điền giá trị giả để pass validation
                    if (!usernameInput.value) usernameInput.value = 'auto_login_check';
                    if (!passwordInput.value) passwordInput.value = 'auto_login_check';
                    
                    form.submit();
                }, 500);
            } else {
                // Không có token
                loginText.textContent = 'Đăng nhập';
                loginBtn.disabled = false;
            }
        }
        
        // Kiểm tra token khi trang load (cho vai trò mặc định)
        window.addEventListener('load', function() {
            checkRememberToken('admin');
        });
    </script>
</body>
</html>

