<div class="auth-wrap">
    <div class="card auth-card">
        <div class="card-body">
            <h1 class="h2 text-center mb-1">Tạo tài khoản</h1>
            <p class="text-center text-muted mb-4">Miễn phí, chỉ cần email</p>

            <form method="post" action="<?= url('dang-ky') ?>" novalidate>
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label" for="full_name">Họ tên</label>
                    <input class="form-control<?= has_error('full_name') ?>" id="full_name" name="full_name" value="<?= old('full_name') ?>" autocomplete="name" maxlength="100" required autofocus>
                    <?= error_for('full_name') ?>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-control<?= has_error('email') ?>" id="email" name="email" type="email" value="<?= old('email') ?>" autocomplete="email" required>
                    <?= error_for('email') ?>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Mật khẩu</label>
                    <input class="form-control<?= has_error('password') ?>" id="password" name="password" type="password" autocomplete="new-password" minlength="8" required aria-describedby="passwordHelp">
                    <div id="passwordHelp" class="form-text">Ít nhất 8 ký tự, có cả chữ và số.</div>
                    <?= error_for('password') ?>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="password_confirmation">Nhập lại mật khẩu</label>
                    <input class="form-control<?= has_error('password_confirmation') ?>" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
                    <?= error_for('password_confirmation') ?>
                </div>
                <button class="btn btn-primary w-100" type="submit">Đăng ký</button>
            </form>

            <p class="text-center mt-4 mb-0">Đã có tài khoản? <a href="<?= url('dang-nhap') ?>">Đăng nhập</a></p>
        </div>
    </div>
</div>
