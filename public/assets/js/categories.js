/*
 * Trang Danh mục — TV4. Mở hộp thoại sửa và điền sẵn dữ liệu của danh mục được chọn.
 * Form trong hộp thoại gửi POST thường tới /danh-muc/{id}/sua (không dùng fetch).
 */
(function () {
    const modalEl = document.getElementById('catModal');
    if (!modalEl) return;

    // Đưa modal ra trực tiếp dưới document.body để thoát khỏi stacking context của .app-content
    if (modalEl.parentElement && modalEl.parentElement !== document.body) {
        document.body.appendChild(modalEl);
    }

    const modal = new bootstrap.Modal(modalEl);
    const form = document.getElementById('catForm');

    modalEl.addEventListener('shown.bs.modal', () => {
        const nameInput = document.getElementById('editName');
        if (nameInput) {
            nameInput.focus();
            nameInput.select();
        }
    });

    function open(data) {
        form.action = `${form.dataset.base}/${data.id}/sua`;
        document.getElementById('editName').value = data.name;
        const used = Number(data.used) > 0;
        form.querySelectorAll('input[name="type"]').forEach(radio => {
            radio.checked = radio.value === data.type;
            radio.disabled = used && radio.value !== data.type; // đã có giao dịch thì khóa loại
        });
        document.getElementById('editTypeLocked').classList.toggle('d-none', !used);
        const icon = form.querySelector(`input[name="icon"][value="${CSS.escape(data.icon)}"]`);
        if (icon) icon.checked = true;
        modal.show();
    }

    document.addEventListener('click', event => {
        const button = event.target.closest('.js-edit-cat');
        if (button) open(button.dataset);
    });

    const old = document.getElementById('catEditOld');
    if (old) open(JSON.parse(old.textContent));
})();
