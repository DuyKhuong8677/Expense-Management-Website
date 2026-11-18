<?php /* Liên hệ, góp ý — TV6 (FR-30): lưu vào bảng feedbacks trong HomeController::sendContact() */ ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <h1 class="mb-2">Liên hệ và góp ý</h1>
            <p class="text-muted mb-4">Bạn gặp lỗi hoặc muốn có thêm tính năng? Hãy cho nhóm biết.</p>
            <div class="card">
                <div class="card-body">
                    <form method="post" action="<?= url('lien-he') ?>" novalidate>
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label" for="name">Họ tên</label>
                            <input class="form-control<?= has_error('name') ?>" id="name" name="name" value="<?= old('name') ?>" required maxlength="100">
                            <?= error_for('name') ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="email">Email</label>
                            <input class="form-control<?= has_error('email') ?>" id="email" name="email" type="email" value="<?= old('email') ?>" required>
                            <?= error_for('email') ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="content">Nội dung</label>
                            <textarea class="form-control<?= has_error('content') ?>" id="content" name="content" rows="5" required><?= old('content') ?></textarea>
                            <?= error_for('content') ?>
                        </div>
                        <button class="btn btn-primary" type="submit">Gửi góp ý</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
