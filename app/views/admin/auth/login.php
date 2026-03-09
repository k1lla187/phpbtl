<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng Nhập - Hệ Thống Quản Lý Đào Tạo</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo (defined('URLROOT') ? rtrim(URLROOT, '/') : '') . '/css/admin.css'; ?>" rel="stylesheet">
</head>
<body class="login-page">
    <div class="login-card">
        <div class="logo">QL</div>
        <h2>Đăng Nhập</h2>
        <p>Hệ thống quản lý đào tạo và điểm số</p>
        
        <?php if(isset($data['error'])): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i><?= $data['error'] ?>
        </div>
        <?php endif; ?>
        
        <form action="index.php?url=Auth/login" method="POST">
            <div class="mb-3">
                <label class="form-label">Tên đăng nhập</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Nhập tên đăng nhập" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
                </div>
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 py-2">
                <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
            </button>
        </form>
        
        <div class="text-center mt-4">
            <a href="#" class="text-muted">Quên mật khẩu?</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>