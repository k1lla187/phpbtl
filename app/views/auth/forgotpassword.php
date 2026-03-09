<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quên Mật Khẩu - UNISCORE</title>
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
        
        .forgot-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
            padding: 20px;
        }
        
        .forgot-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            padding: 40px 35px;
            text-align: center;
        }
        
        .logo-container {
            width: 80px; height: 80px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 30px rgba(245, 158, 11, 0.3);
            transform: rotate(-5deg);
            transition: transform 0.3s ease;
        }
        
        .logo-container:hover { transform: rotate(0deg) scale(1.05); }
        .logo-container i { font-size: 36px; color: white; }
        
        .forgot-card h2 {
            font-size: 26px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }
        
        .forgot-card .subtitle {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 25px;
            line-height: 1.5;
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
        
        .input-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            transition: color 0.3s;
        }
        
        .input-wrapper input, .input-wrapper textarea {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f8fafc;
        }
        
        .input-wrapper textarea {
            padding-left: 14px;
            resize: vertical;
            min-height: 80px;
        }
        
        .input-wrapper input:focus, .input-wrapper textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }
        
        .input-wrapper:focus-within i { color: var(--primary-color); }
        
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
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
            box-shadow: 0 10px 25px rgba(245, 158, 11, 0.35);
        }
        
        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
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
        
        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #16a34a;
        }
        
        .alert-warning {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #d97706;
        }
        
        .info-box {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 20px;
            text-align: left;
        }
        
        .info-box h4 {
            color: #0369a1;
            font-size: 13px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .info-box ul {
            margin: 0;
            padding-left: 18px;
            color: #475569;
            font-size: 12px;
        }
        
        .info-box li { margin-bottom: 4px; }
        
        .back-link {
            margin-top: 18px;
            color: #64748b;
            font-size: 13px;
        }
        
        .back-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link a:hover { text-decoration: underline; }
        
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
        
        .success-icon {
            width: 80px; height: 80px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .success-icon i { font-size: 40px; color: white; }
        
        .timeline {
            text-align: left;
            padding: 15px 0;
        }
        
        .timeline-item {
            display: flex;
            gap: 12px;
            padding-bottom: 15px;
            position: relative;
        }
        
        .timeline-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: 11px;
            top: 28px;
            width: 2px;
            height: calc(100% - 20px);
            background: #e2e8f0;
        }
        
        .timeline-dot {
            width: 24px; height: 24px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .timeline-dot.active { background: #10b981; }
        .timeline-dot.pending { background: #f59e0b; }
        .timeline-dot i { font-size: 10px; color: white; }
        
        .timeline-content h5 {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 2px;
        }
        
        .timeline-content p {
            font-size: 12px;
            color: #64748b;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    
    <div class="forgot-container">
        <div class="forgot-card">
            <?php if(isset($success)): ?>
                <!-- Hiển thị khi gửi yêu cầu thành công -->
                <div class="success-icon">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <h2>Yêu cầu đã được gửi!</h2>
                <p class="subtitle">Yêu cầu khôi phục mật khẩu của bạn đã được gửi đến Quản trị viên.</p>
                
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?= htmlspecialchars($success) ?></span>
                </div>
                
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-dot active"><i class="fas fa-check"></i></div>
                        <div class="timeline-content">
                            <h5>Gửi yêu cầu</h5>
                            <p>Bạn đã gửi yêu cầu thành công</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot pending"><i class="fas fa-clock"></i></div>
                        <div class="timeline-content">
                            <h5>Chờ duyệt</h5>
                            <p>Admin sẽ xem xét và duyệt yêu cầu</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot"><i class="fas fa-envelope"></i></div>
                        <div class="timeline-content">
                            <h5>Nhận mật khẩu mới</h5>
                            <p>Mật khẩu mới sẽ được gửi qua email</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot"><i class="fas fa-key"></i></div>
                        <div class="timeline-content">
                            <h5>Đăng nhập và đổi mật khẩu</h5>
                            <p>Đăng nhập và tạo mật khẩu mới dễ nhớ</p>
                        </div>
                    </div>
                </div>
                
                <p class="back-link">
                    <a href="index.php?url=Auth/index"><i class="fas fa-arrow-left"></i> Quay lại đăng nhập</a>
                </p>
            <?php else: ?>
                <!-- Form gửi yêu cầu -->
                <div class="logo-container">
                    <i class="fas fa-key"></i>
                </div>
                <h2>Quên mật khẩu?</h2>
                <p class="subtitle">Nhập email đã đăng ký để gửi yêu cầu khôi phục mật khẩu.<br>Admin sẽ xem xét và gửi mật khẩu mới qua email.</p>
                
                <?php if(isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
                <?php endif; ?>
                
                <div class="info-box">
                    <h4><i class="fas fa-info-circle"></i> Quy trình khôi phục</h4>
                    <ul>
                        <li>Bạn gửi yêu cầu với email đã đăng ký</li>
                        <li>Admin xem xét và duyệt yêu cầu</li>
                        <li>Mật khẩu mới được gửi đến email của bạn</li>
                        <li>Đăng nhập và đổi sang mật khẩu dễ nhớ hơn</li>
                    </ul>
                </div>
                
                <form action="index.php?url=Auth/submitForgotPassword" method="POST" id="forgotForm">
                    <div class="form-group">
                        <label>Địa chỉ Email <span style="color: #dc2626;">*</span></label>
                        <div class="input-wrapper">
                            <input type="email" name="email" id="email" placeholder="Nhập email đã đăng ký trong hệ thống" required>
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Lý do (không bắt buộc)</label>
                        <div class="input-wrapper">
                            <textarea name="lydo" placeholder="Mô tả ngắn gọn lý do quên mật khẩu (ví dụ: quên mật khẩu, đổi thiết bị...)" maxlength="500"></textarea>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <span class="spinner" id="spinner"></span>
                        <i class="fas fa-paper-plane" id="btnIcon"></i>
                        <span id="btnText">Gửi yêu cầu đến Admin</span>
                    </button>
                </form>
                
                <p class="back-link">
                    <a href="index.php?url=Auth/index"><i class="fas fa-arrow-left"></i> Quay lại đăng nhập</a>
                </p>
            <?php endif; ?>
            
            <p style="margin-top: 18px; color: #94a3b8; font-size: 12px;">
                © <?= date('Y') ?> <strong style="color: #2563eb;">UNISCORE</strong> - Quản lý điểm sinh viên
            </p>
        </div>
    </div>
    
    <script>
        document.getElementById('forgotForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            const spinner = document.getElementById('spinner');
            const icon = document.getElementById('btnIcon');
            const text = document.getElementById('btnText');
            
            btn.disabled = true;
            spinner.style.display = 'inline-block';
            icon.style.display = 'none';
            text.textContent = 'Đang gửi yêu cầu...';
        });
    </script>
</body>
</html>
