<div class="auth-wrap">
    <div class="card auth-card">
        <div class="card-body p-4 p-sm-5">
            <div class="text-center mb-4">
                <div class="d-inline-grid place-items-center mb-2" style="width: 48px; height: 48px; border-radius: var(--r-md); background: var(--c-primary-tint); color: var(--c-primary); font-size: 26px;">
                    <i class="ph ph-wallet" aria-hidden="true"></i>
                </div>
                <h1 class="h2 mb-1">Đăng nhập</h1>
                <p class="text-muted small">Chào mừng bạn quay lại với Quản lý chi tiêu</p>
            </div>

            <form method="post" action="<?= url('dang-nhap') ?>" novalidate>
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label" for="email">Địa chỉ email</label>
                    <input class="form-control<?= has_error('email') ?>" id="email" name="email" type="email" value="<?= old('email') ?>" placeholder="name@example.com" autocomplete="email" required autofocus>
                    <?= error_for('email') ?>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="password">Mật khẩu</label>
                    <input class="form-control<?= has_error('password') ?>" id="password" name="password" type="password" placeholder="••••••••" autocomplete="current-password" required>
                    <?= error_for('password') ?>
                </div>
                <button class="btn btn-primary w-100 py-2" type="submit">Đăng nhập</button>
            </form>

            <div class="text-center mt-4 pt-2 border-top">
                <span class="text-muted small">Chưa có tài khoản?</span>
                <a class="fw-semibold small ms-1" href="<?= url('dang-ky') ?>">Đăng ký ngay</a>
            </div>
        </div>
    </div>
</div>
