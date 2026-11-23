<div class="auth-wrap">
    <div class="card auth-card">
        <div class="card-body p-4 p-sm-5">
            <div class="text-center mb-4">
                <div class="d-inline-grid place-items-center mb-2" style="width: 48px; height: 48px; border-radius: var(--r-md); background: var(--c-primary-tint); color: var(--c-primary); font-size: 26px;">
                    <i class="ph ph-user-plus" aria-hidden="true"></i>
                </div>
                <h1 class="h2 mb-1">Tạo tài khoản</h1>
                <p class="text-muted small">Miễn phí và bắt đầu quản lý thu chi ngay hôm nay</p>
            </div>

            <form method="post" action="<?= url('dang-ky') ?>" novalidate>
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label" for="full_name">Họ và tên</label>
                    <input class="form-control<?= has_error('full_name') ?>" id="full_name" name="full_name" value="<?= old('full_name') ?>" placeholder="VD: Nguyễn Văn A" autocomplete="name" maxlength="100" required autofocus>
                    <?= error_for('full_name') ?>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="email">Địa chỉ email</label>
                    <input class="form-control<?= has_error('email') ?>" id="email" name="email" type="email" value="<?= old('email') ?>" placeholder="name@example.com" autocomplete="email" required>
                    <?= error_for('email') ?>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Mật khẩu</label>
                    <input class="form-control<?= has_error('password') ?>" id="password" name="password" type="password" placeholder="••••••••" autocomplete="new-password" minlength="8" required aria-describedby="passwordHelp">
                    <div id="passwordHelp" class="form-text">Ít nhất 8 ký tự, bao gồm cả chữ và số.</div>
                    <?= error_for('password') ?>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="password_confirmation">Xác nhận lại mật khẩu</label>
                    <input class="form-control<?= has_error('password_confirmation') ?>" id="password_confirmation" name="password_confirmation" type="password" placeholder="••••••••" autocomplete="new-password" required>
                    <?= error_for('password_confirmation') ?>
                </div>
                <button class="btn btn-primary w-100 py-2" type="submit">Đăng ký tài khoản</button>
            </form>

            <div class="text-center mt-4 pt-2 border-top">
                <span class="text-muted small">Đã có tài khoản?</span>
                <a class="fw-semibold small ms-1" href="<?= url('dang-nhap') ?>">Đăng nhập</a>
            </div>
        </div>
    </div>
</div>
