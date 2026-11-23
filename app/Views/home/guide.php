<?php /* Trang hướng dẫn */ ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-2">Hướng dẫn sử dụng</h1>
            <p class="text-muted mb-4">Các bước đơn giản để bắt đầu quản lý thu chi và lập ngân sách cá nhân.</p>

            <div class="card">
                <div class="card-body p-4">
                    <ol class="list-unstyled d-grid gap-4 mb-0">
                        <li class="d-flex gap-3 align-items-start">
                            <span class="badge bg-primary text-white rounded-circle p-0 d-inline-grid place-items-center flex-shrink-0" style="width: 32px; height: 32px; font-size: 15px;">1</span>
                            <div>
                                <h3 class="h5 mb-1">Tạo tài khoản</h3>
                                <p class="text-muted mb-0">Đăng ký bằng email của bạn. Ngay sau khi tạo tài khoản, hệ thống sẽ tự động thiết lập sẵn ví "Tiền mặt" và các danh mục thông dụng để bạn dùng ngay.</p>
                            </div>
                        </li>
                        <li class="d-flex gap-3 align-items-start">
                            <span class="badge bg-primary text-white rounded-circle p-0 d-inline-grid place-items-center flex-shrink-0" style="width: 32px; height: 32px; font-size: 15px;">2</span>
                            <div>
                                <h3 class="h5 mb-1">Thêm ví tiền</h3>
                                <p class="text-muted mb-0">Thêm ví cho tài khoản ngân hàng hoặc ví điện tử ở mục <a href="<?= url('vi') ?>">Ví tiền</a> để theo dõi chính xác số dư từng nguồn tiền.</p>
                            </div>
                        </li>
                        <li class="d-flex gap-3 align-items-start">
                            <span class="badge bg-primary text-white rounded-circle p-0 d-inline-grid place-items-center flex-shrink-0" style="width: 32px; height: 32px; font-size: 15px;">3</span>
                            <div>
                                <h3 class="h5 mb-1">Ghi giao dịch hàng ngày</h3>
                                <p class="text-muted mb-0">Bấm nút "Thêm giao dịch" ở góc trên màn hình: chọn Thu hoặc Chi, nhập số tiền, chọn danh mục và ví tương ứng.</p>
                            </div>
                        </li>
                        <li class="d-flex gap-3 align-items-start">
                            <span class="badge bg-primary text-white rounded-circle p-0 d-inline-grid place-items-center flex-shrink-0" style="width: 32px; height: 32px; font-size: 15px;">4</span>
                            <div>
                                <h3 class="h5 mb-1">Đặt ngân sách tháng</h3>
                                <p class="text-muted mb-0">Tại mục <a href="<?= url('ngan-sach') ?>">Ngân sách</a>, đặt hạn mức cho các danh mục bạn hay chi nhiều như Ăn uống hay Mua sắm để nhận cảnh báo khi chạm mốc 80%.</p>
                            </div>
                        </li>
                        <li class="d-flex gap-3 align-items-start">
                            <span class="badge bg-primary text-white rounded-circle p-0 d-inline-grid place-items-center flex-shrink-0" style="width: 32px; height: 32px; font-size: 15px;">5</span>
                            <div>
                                <h3 class="h5 mb-1">Xem báo cáo & xuất dữ liệu</h3>
                                <p class="text-muted mb-0">Xem mục <a href="<?= url('bao-cao') ?>">Báo cáo</a> để biết tỷ trọng chi tiêu, so sánh thu chi các tháng và xuất file CSV khi cần đối soát.</p>
                            </div>
                        </li>
                    </ol>
                </div>
            </div>

            <div class="text-center mt-4">
                <a class="btn btn-primary" href="<?= url('dang-ky') ?>">Bắt đầu ngay hôm nay</a>
            </div>
        </div>
    </div>
</div>
