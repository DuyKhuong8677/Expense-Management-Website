<div class="container py-5">
    <div class="empty-state">
        <i class="ph ph-map-trifold" aria-hidden="true"></i>
        <h1 class="mt-3">Không tìm thấy trang</h1>
        <p>Đường dẫn có thể đã bị đổi hoặc gõ nhầm.</p>
        <a class="btn btn-primary" href="<?= url(App\Core\Auth::check() ? 'tong-quan' : '') ?>">Về trang chủ</a>
    </div>
</div>
