<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <!-- Alert thông báo -->
            <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                <i class="fas fa-exclamation-triangle me-3 fa-2x"></i>
                <div>
                    <h5 class="alert-heading mb-1">Không thể gửi Email tự động</h5>
                    <p class="mb-0">
                        <?php if (!$passwordInfo['emailConfigured']): ?>
                            <strong>Lý do:</strong> Chưa cấu hình SMTP email trong hệ thống.
                        <?php else: ?>
                            <strong>Lỗi:</strong> <?= htmlspecialchars($passwordInfo['emailError']) ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            
            <!-- Card hiển thị thông tin -->
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient text-white py-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h4 class="mb-0">
                        <i class="fas fa-key me-2"></i>
                        Thông tin mật khẩu mới
                    </h4>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-success mb-4">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Yêu cầu đã được duyệt thành công!</strong> Mật khẩu đã được cập nhật.
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered mb-4">
                            <tbody>
                                <tr>
                                    <th class="bg-light" style="width: 35%;">
                                        <i class="fas fa-user me-2 text-primary"></i>Họ tên
                                    </th>
                                    <td><strong><?= htmlspecialchars($passwordInfo['hoTen']) ?></strong></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">
                                        <i class="fas fa-id-card me-2 text-primary"></i>Tên đăng nhập
                                    </th>
                                    <td><code class="fs-5"><?= htmlspecialchars($passwordInfo['tenDangNhap']) ?></code></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">
                                        <i class="fas fa-envelope me-2 text-primary"></i>Email
                                    </th>
                                    <td><?= htmlspecialchars($passwordInfo['email']) ?></td>
                                </tr>
                                <tr>
                                    <th class="bg-light">
                                        <i class="fas fa-user-tag me-2 text-primary"></i>Vai trò
                                    </th>
                                    <td>
                                        <?php
                                        $badgeClass = 'bg-secondary';
                                        if ($passwordInfo['vaiTro'] === 'Admin') $badgeClass = 'bg-danger';
                                        elseif ($passwordInfo['vaiTro'] === 'GiangVien') $badgeClass = 'bg-info';
                                        elseif ($passwordInfo['vaiTro'] === 'SinhVien') $badgeClass = 'bg-success';
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($passwordInfo['vaiTro']) ?></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Mật khẩu mới - nổi bật -->
                    <div class="card border-primary mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Mật khẩu mới</h5>
                        </div>
                        <div class="card-body text-center py-4">
                            <div class="input-group input-group-lg mb-3">
                                <input type="text" id="newPassword" class="form-control text-center fw-bold fs-3 font-monospace" 
                                       value="<?= htmlspecialchars($passwordInfo['newPassword']) ?>" readonly
                                       style="letter-spacing: 3px; background-color: #f8f9fa;">
                                <button class="btn btn-outline-primary" type="button" onclick="copyPassword()">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Click nút copy hoặc chọn và sao chép mật khẩu
                            </small>
                        </div>
                    </div>
                    
                    <!-- Hướng dẫn -->
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body">
                            <h6 class="text-secondary mb-3">
                                <i class="fas fa-tasks me-2"></i>Hướng dẫn tiếp theo:
                            </h6>
                            <ol class="mb-0">
                                <li class="mb-2">Sao chép mật khẩu trên</li>
                                <li class="mb-2">Liên hệ người dùng qua một trong các phương thức:
                                    <ul class="mt-1">
                                        <li><i class="fas fa-envelope me-1 text-info"></i>Email thủ công</li>
                                        <li><i class="fab fa-facebook-messenger me-1 text-primary"></i>Zalo / Messenger</li>
                                        <li><i class="fas fa-phone me-1 text-success"></i>Điện thoại</li>
                                    </ul>
                                </li>
                                <li>Thông báo tên đăng nhập và mật khẩu mới cho họ</li>
                                <li class="text-primary">
                                    <strong>Lưu ý:</strong> Người dùng sẽ được yêu cầu đổi mật khẩu khi đăng nhập lần đầu
                                </li>
                            </ol>
                        </div>
                    </div>
                    
                    <!-- Nút quay lại -->
                    <div class="text-center">
                        <a href="index.php?url=YeuCauDoiMatKhau/index" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách yêu cầu
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Thông báo cấu hình SMTP -->
            <?php if (!$passwordInfo['emailConfigured']): ?>
            <div class="card mt-4 border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Hướng dẫn cấu hình SMTP Email</h5>
                </div>
                <div class="card-body">
                    <p>Để hệ thống tự động gửi email, vui lòng cấu hình SMTP trong file:</p>
                    <pre class="bg-dark text-light p-3 rounded"><code>app/config/email.php</code></pre>
                    
                    <p class="mb-2"><strong>Với Gmail:</strong></p>
                    <ol>
                        <li>Đăng nhập Gmail → Bảo mật → Bật <strong>Xác minh 2 bước</strong></li>
                        <li>Vào <strong>Mật khẩu ứng dụng</strong> → Tạo mật khẩu mới</li>
                        <li>Sao chép mật khẩu 16 ký tự và dán vào cấu hình</li>
                    </ol>
                    
                    <div class="alert alert-secondary mt-3 mb-0">
                        <code>
                            'smtp_username' => 'your-email@gmail.com',<br>
                            'smtp_password' => 'xxxx-xxxx-xxxx-xxxx', // Mật khẩu ứng dụng
                        </code>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function copyPassword() {
    const passwordInput = document.getElementById('newPassword');
    passwordInput.select();
    passwordInput.setSelectionRange(0, 99999);
    
    navigator.clipboard.writeText(passwordInput.value).then(function() {
        // Hiển thị thông báo đã copy
        const btn = event.currentTarget;
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i>';
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-success');
        
        setTimeout(function() {
            btn.innerHTML = originalHTML;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-primary');
        }, 2000);
    });
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
