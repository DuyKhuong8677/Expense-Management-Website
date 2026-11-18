<?php
/**
 * Trang chờ cho chức năng chưa làm. Biến: $title, $owner, $branch, $tasks.
 * Thành viên phụ trách xóa lời gọi placeholder() trong controller và tạo view thật.
 */
?>
<div class="card">
    <div class="card-body empty-state">
        <i class="ph ph-hammer" aria-hidden="true"></i>
        <h2 class="mt-3"><?= e($title) ?> đang được xây dựng</h2>
        <p>Phụ trách: <strong><?= e($owner) ?></strong> · nhánh <code><?= e($branch) ?></code></p>
    </div>
</div>

<div class="card mt-3">
    <div class="card-body">
        <h3>Việc cần làm</h3>
        <ul class="mb-0">
            <?php foreach ($tasks as $task): ?>
                <li><?= e($task) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
