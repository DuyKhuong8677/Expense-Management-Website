<div class="container py-5">
    <div class="empty-state py-5">
        <i class="ph ph-lock-key text-primary" aria-hidden="true"></i>
        <h1 class="h2 mt-3 mb-2">Không có quyền truy cập</h1>
        <p class="text-muted mb-4">Khu vực này được bảo vệ và chỉ dành cho tài khoản quản trị viên.</p>
        <a class="btn btn-primary" href="<?= url(App\Core\Auth::check() ? 'tong-quan' : '') ?>">
            <i class="ph ph-house" aria-hidden="true"></i> <span>Về trang chủ</span>
        </a>
    </div>
</div>
