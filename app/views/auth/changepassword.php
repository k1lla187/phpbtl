<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đổi Mật Khẩu - UNISCORE</title>
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
        
        html { overflow-y: auto; overflow-x: hidden; }
        
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
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            animation: float 30s linear infinite;
            z-index: 0;
        }
        
        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(-50px, -50px) rotate(360deg); }
        }
        
        .change-password-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
            padding: 20px;
        }
        
        .change-password-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            padding: 40px 35px;
            text-align: center;
        }
        
        .logo-container {
            width: 80px; height: 80px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
            transform: rotate(-5deg);
            transition: transform 0.3s ease;
        }
        
        .logo-container:hover { transform: rotate(0deg) scale(1.05); }
        .logo-container i { font-size: 36px; color: white; }
        
        .change-password-card h2 {
            font-size: 26px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }
        
        .change-password-card .subtitle {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 25px;
            line-height: 1.5;
        }
        
        .welcome-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
            text-align: left;
        }
        
        .welcome-box h4 {
            color: #16a34a;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .welcome-box p {
            color: #166534;
            font-size: 13px;
            margin: 0;
        }
        
        .form-group {
            position: relative;
            margin-bottom: 18px;
            text-align: left;
        }
        
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-wrapper i.icon-left {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            transition: color 0.3s;
        }
        
        .input-wrapper input {
            width: 100%;
            padding: 12px 45px 12px 42px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f8fafc;
        }
        
        .input-wrapper input:focus {
            outline: none;
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }
        
        .input-wrapper:focus-within i.icon-left { color: var(--primary-color); }
        
        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            cursor: pointer;
            padding: 5px;
            transition: color 0.3s;
        }
        
        .toggle-password:hover { color: var(--primary-color); }
        
        .password-strength {
            margin-top: 8px;
            height: 4px;
            background: #e2e8f0;
            border-radius: 2px;
            overflow: hidden;
        }
        
        .password-strength .bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        
        .password-strength.weak .bar { width: 33%; background: #ef4444; }
        .password-strength.medium .bar { width: 66%; background: #f59e0b; }
        .password-strength.strong .bar { width: 100%; background: #10b981; }
        
        .password-hint {
            font-size: 11px;
            color: #64748b;
            margin-top: 5px;
        }
        
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.35);
        }
        
        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
        
        .btn-skip {
            width: 100%;
            padding: 12px;
            background: transparent;
            color: #64748b;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 12px;
        }
        
        .btn-skip:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
        }
        
        .alert {
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 20px;
            font-size: 13px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            text-align: left;
        }
        
        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }
        
        .alert-warning {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #d97706;
        }
        
        .info-box {
            background: #fef3c7;
            border: 1px solid #fde68a;
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 20px;
            text-align: left;
        }
        
        .info-box h4 {
            color: #d97706;
            font-size: 13px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .info-box p {
            color: #92400e;
            font-size: 12px;
            margin: 0;
            line-height: 1.5;
        }
        
        .shape {
            position: fixed;
            border-radius: 50%;
            opacity: 0.1;
            z-index: 0;
        }
        
        .shape-1 { width: 300px; height: 300px; background: white; top: -100px; right: -100px; }
        .shape-2 { width: 200px; height: 200px; background: white; bottom: -50px; left: -50px; }
        
        .spinner {
            display: none;
            width: 18px; height: 18px;
            border: 2px solid #ffffff;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin { to { transform: rotate(360deg); } }
        
        .user-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
            padding: 12px;
            background: #f8fafc;
            border-radius: 12px;
        }
        
        .user-avatar {
            width: 45px; height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 18px;
        }
        
        .user-details {
            text-align: left;
        }
        
        .user-details .name {
            font-weight: 600;
            color: #1e293b;
            font-size: 14px;
        }
        
        .user-details .role {
            font-size: 12px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    
    <div class="change-password-container">
        <div class="change-password-card">
            <div class="logo-container">
                <i class="fas fa-key"></i>
            </div>
            <h2>Đổi Mật Khẩu</h2>
            <p class="subtitle">Để bảo mật tài khoản, vui lòng tạo mật khẩu mới dễ nhớ hơn.</p>
            
            <!-- Thông tin người dùng -->
            <div class="user-info">
                <div class="user-avatar">
                    <?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="user-details">
                    <div class="name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Người dùng') ?></div>
                    <div class="role">
                        <?php
                        $role = $_SESSION['user_role'] ?? '';
                        echo match($role) {
                            'Admin' => 'Quản trị viên',
                            'GiangVien' => 'Giảng viên',
                            'SinhVien' => 'Sinh viên',
                            default => $role
                        };
                        ?>
                    </div>
                </div>
            </div>
            
            <?php if(isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
            <?php endif; ?>
            
            <div class="info-box">
                <h4><i class="fas fa-shield-alt"></i> Tại sao cần đổi mật khẩu?</h4>
                <p>Mật khẩu tạm thời đã được gửi qua email. Để bảo mật, bạn nên tạo mật khẩu mới chỉ có bạn biết.</p>
            </div>
            
            <form action="index.php?url=Auth/submitChangePassword" method="POST" id="changePasswordForm">
                <div class="form-group">
                    <label>Mật khẩu mới <span style="color: #dc2626;">*</span></label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock icon-left"></i>
                        <input type="password" name="new_password" id="new_password" placeholder="Nhập mật khẩu mới (ít nhất 6 ký tự)" required minlength="6">
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('new_password', this)"></i>
                    </div>
                    <div class="password-strength" id="passwordStrength">
                        <div class="bar"></div>
                    </div>
                    <p class="password-hint" id="strengthText">Độ mạnh: Chưa nhập</p>
                </div>
                
                <div class="form-group">
                    <label>Xác nhận mật khẩu <span style="color: #dc2626;">*</span></label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock icon-left"></i>
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Nhập lại mật khẩu mới" required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('confirm_password', this)"></i>
                    </div>
                    <p class="password-hint" id="matchText" style="display: none;"></p>
                </div>
                
                <button type="submit" class="btn-submit" id="submitBtn">
                    <span class="spinner" id="spinner"></span>
                    <i class="fas fa-check" id="btnIcon"></i>
                    <span id="btnText">Đổi mật khẩu</span>
                </button>
            </form>
            
            <a href="index.php?url=Auth/skipChangePassword" class="btn-skip">
                <i class="fas fa-clock me-1"></i>Để sau, giữ mật khẩu tạm thời
            </a>
            
            <p style="margin-top: 18px; color: #94a3b8; font-size: 12px;">
                © <?= date('Y') ?> <strong style="color: #2563eb;">UNISCORE</strong> - Quản lý điểm sinh viên
            </p>
        </div>
    </div>
    
    <script>
        // Toggle hiển thị mật khẩu
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
        
        // Kiểm tra độ mạnh mật khẩu
        document.getElementById('new_password').addEventListener('input', function() {
            const password = this.value;
            const strengthDiv = document.getElementById('passwordStrength');
            const strengthText = document.getElementById('strengthText');
            
            strengthDiv.className = 'password-strength';
            
            if (password.length === 0) {
                strengthText.textContent = 'Độ mạnh: Chưa nhập';
                return;
            }
            
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            if (strength <= 2) {
                strengthDiv.classList.add('weak');
                strengthText.textContent = 'Độ mạnh: Yếu';
                strengthText.style.color = '#ef4444';
            } else if (strength <= 3) {
                strengthDiv.classList.add('medium');
                strengthText.textContent = 'Độ mạnh: Trung bình';
                strengthText.style.color = '#f59e0b';
            } else {
                strengthDiv.classList.add('strong');
                strengthText.textContent = 'Độ mạnh: Mạnh';
                strengthText.style.color = '#10b981';
            }
            
            checkMatch();
        });
        
        // Kiểm tra khớp mật khẩu
        document.getElementById('confirm_password').addEventListener('input', checkMatch);
        
        function checkMatch() {
            const password = document.getElementById('new_password').value;
            const confirm = document.getElementById('confirm_password').value;
            const matchText = document.getElementById('matchText');
            
            if (confirm.length === 0) {
                matchText.style.display = 'none';
                return;
            }
            
            matchText.style.display = 'block';
            if (password === confirm) {
                matchText.textContent = '✓ Mật khẩu khớp';
                matchText.style.color = '#10b981';
            } else {
                matchText.textContent = '✗ Mật khẩu không khớp';
                matchText.style.color = '#ef4444';
            }
        }
        
        // Submit form
        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            const password = document.getElementById('new_password').value;
            const confirm = document.getElementById('confirm_password').value;
            
            if (password !== confirm) {
                e.preventDefault();
                alert('Mật khẩu xác nhận không khớp!');
                return;
            }
            
            const btn = document.getElementById('submitBtn');
            const spinner = document.getElementById('spinner');
            const icon = document.getElementById('btnIcon');
            const text = document.getElementById('btnText');
            
            btn.disabled = true;
            spinner.style.display = 'inline-block';
            icon.style.display = 'none';
            text.textContent = 'Đang xử lý...';
        });
    </script>
</body>
</html>
