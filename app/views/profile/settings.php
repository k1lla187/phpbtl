<?php
$baseUrl = defined('URLROOT') ? rtrim(URLROOT, '/') : '';
$loginType = $_SESSION['login_type'] ?? 'admin';
$userName = $_SESSION['user_name'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài đặt - Giao diện</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; padding: 40px; max-width: 500px; margin: 0 auto; }
        .theme-btn { padding: 12px 24px; border-radius: 8px; cursor: pointer; border: 2px solid #ddd; margin: 8px; }
        .theme-btn.active { border-color: #2563eb; background: #eff6ff; }
        .theme-btn:hover { background: #f1f5f9; }
    </style>
</head>
<body>
<h4 class="mb-4"><i class="fas fa-cog me-2"></i>Cài đặt</h4>
<div class="card mb-4">
    <div class="card-header">Giao diện</div>
    <div class="card-body">
        <p class="text-muted mb-3">Chọn theme hiển thị:</p>
        <div>
            <button type="button" class="theme-btn" id="themeLight" data-theme="light">
                <i class="fas fa-sun me-2"></i>Giao diện sáng
            </button>
            <button type="button" class="theme-btn" id="themeDark" data-theme="dark">
                <i class="fas fa-moon me-2"></i>Giao diện tối
            </button>
        </div>
    </div>
</div>
<a href="javascript:history.back()" class="btn btn-outline-secondary">Quay lại</a>

<script>
(function() {
    var theme = localStorage.getItem('app-theme') || 'light';
    function applyTheme(t) {
        document.documentElement.classList.remove('dark-theme');
        if (t === 'dark') document.documentElement.classList.add('dark-theme');
        localStorage.setItem('app-theme', t);
        document.getElementById('themeLight').classList.toggle('active', t === 'light');
        document.getElementById('themeDark').classList.toggle('active', t === 'dark');
    }
    applyTheme(theme);
    document.getElementById('themeLight').onclick = function() { applyTheme('light'); };
    document.getElementById('themeDark').onclick = function() { applyTheme('dark'); };
})();
</script>
</body>
</html>
