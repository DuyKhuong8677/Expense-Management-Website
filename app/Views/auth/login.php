<div class="auth-wrap">
    <div class="card auth-card">
        <div class="card-body">
            <h1 class="h2 text-center mb-1">Đăng nhập</h1>
            <p class="text-center text-muted mb-4">Chào mừng bạn quay lại</p>

            <form method="post" action="<?= url('dang-nhap') ?>" novalidate>
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-control<?= has_error('email') ?>" id="email" name="email" type="email" value="<?= old('email') ?>" autocomplete="email" required autofocus>
                    <?= error_for('email') ?>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="password">Mật khẩu</label>
                    <input class="form-control<?= has_error('password') ?>" id="password" name="password" type="password" autocomplete="current-password" required>
                    <?= error_for('password') ?>
                </div>
                <button class="btn btn-primary w-100" type="submit">Đăng nhập</button>
            </form>

            <p class="text-center mt-4 mb-0">Chưa có tài khoản? <a href="<?= url('dang-ky') ?>">Đăng ký</a></p>
        </div>
    </div>
</div>
