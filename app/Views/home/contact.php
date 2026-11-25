<?php /* Liên hệ, góp ý */ ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <h1 class="mb-2 text-center">Liên hệ và góp ý</h1>
            <p class="text-muted text-center mb-4">Bạn gặp lỗi hoặc có đề xuất tính năng mới? Hãy để lại tin nhắn cho chúng tôi.</p>
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="post" action="<?= url('lien-he') ?>" novalidate>
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label" for="name">Họ và tên</label>
                            <input class="form-control<?= has_error('name') ?>" id="name" name="name" value="<?= old('name') ?>" placeholder="VD: Nguyễn Văn A" required maxlength="100">
                            <?= error_for('name') ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="email">Địa chỉ email</label>
                            <input class="form-control<?= has_error('email') ?>" id="email" name="email" type="email" value="<?= old('email') ?>" placeholder="name@example.com" required>
                            <?= error_for('email') ?>
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="content">Nội dung góp ý</label>
                            <textarea class="form-control<?= has_error('content') ?>" id="content" name="content" rows="5" placeholder="Mô tả chi tiết ý kiến hoặc lỗi bạn gặp phải..." required><?= old('content') ?></textarea>
                            <?= error_for('content') ?>
                        </div>
                        <button class="btn btn-primary w-100" type="submit"><i class="ph ph-paper-plane-tilt" aria-hidden="true"></i> Gửi góp ý</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
