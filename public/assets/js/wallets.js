/*
 * Trang Ví tiền — TV4. Mở hộp thoại sửa và điền sẵn dữ liệu của ví được chọn.
 * Form trong hộp thoại gửi POST thường tới /vi/{id}/sua (không dùng fetch).
 */
(function () {
    const modalEl = document.getElementById('walletModal');
    if (!modalEl) return;
    const modal = new bootstrap.Modal(modalEl);
    const form = document.getElementById('walletForm');

    function open(data) {
        form.action = `${form.dataset.base}/${data.id}/sua`;
        document.getElementById('wEditName').value = data.name;
        document.getElementById('wEditType').value = data.type;
        document.getElementById('wEditInitial').value = data.initial;
        modal.show();
    }

    document.addEventListener('click', event => {
        const button = event.target.closest('.js-edit-wallet');
        if (button) open(button.dataset);
    });

    // Sửa bị lỗi: server gửi lại dữ liệu vừa nhập để mở lại hộp thoại
    const old = document.getElementById('walletEditOld');
    if (old) open(JSON.parse(old.textContent));
})();
