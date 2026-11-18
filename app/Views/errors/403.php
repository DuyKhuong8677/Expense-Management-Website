<div class="container py-5">
    <div class="empty-state">
        <i class="ph ph-lock-key" aria-hidden="true"></i>
        <h1 class="mt-3">Bạn không có quyền truy cập trang này</h1>
        <p>Trang này chỉ dành cho quản trị viên.</p>
        <a class="btn btn-primary" href="<?= url(App\Core\Auth::check() ? 'tong-quan' : '') ?>">Về trang chủ</a>
    </div>
</div>
