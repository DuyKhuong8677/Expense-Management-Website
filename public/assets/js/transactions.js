/*
 * Trang Giao dịch — TV3.
 * Thêm/sửa bằng hộp thoại: lấy dữ liệu form -> api() gửi POST -> máy chủ kiểm tra và lưu -> trả JSON.
 * Sửa: trước khi mở hộp thoại, gọi GET /giao-dich/{id} để lấy dữ liệu hiện tại.
 */
(function () {
    const modalEl = document.getElementById('txModal');
    if (!modalEl) return;

    const modal = new bootstrap.Modal(modalEl);
    const form = document.getElementById('txForm');
    const title = document.getElementById('txModalTitle');
    const submit = document.getElementById('txSubmit');
    const formError = document.getElementById('txFormError');
    const category = document.getElementById('txCategory');

    /** Chỉ hiện danh mục đúng loại đang chọn (Chi hoặc Thu) */
    function filterCategories() {
        const type = form.querySelector('input[name="type"]:checked').value;
        let firstMatch = null;
        Array.from(category.options).forEach(option => {
            const match = option.dataset.type === type;
            option.hidden = !match;
            option.disabled = !match;
            if (match && !firstMatch) firstMatch = option;
        });
        if (category.selectedOptions[0]?.disabled && firstMatch) {
            category.value = firstMatch.value;
        }
    }

    function clearErrors() {
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('[data-error-for]').forEach(el => { el.textContent = ''; });
        formError.classList.add('d-none');
    }

    function showErrors(error) {
        Object.entries(error.errors || {}).forEach(([field, message]) => {
            const input = form.elements[field];
            const holder = form.querySelector(`[data-error-for="${field}"]`);
            if (input && input.classList) input.classList.add('is-invalid');
            if (holder) holder.textContent = message;
        });
        formError.textContent = error.message;
        formError.classList.remove('d-none');
    }

    function openAdd() {
        form.reset();
        clearErrors();
        document.getElementById('txId').value = '';
        document.getElementById('txDate').value = todayISO();
        document.getElementById('txDate').max = todayISO();
        title.textContent = 'Thêm giao dịch';
        filterCategories();
        modal.show();
    }

    async function openEdit(id) {
        clearErrors();
        try {
            const { data } = await api(`giao-dich/${id}`); // GET
            form.reset();
            document.getElementById('txId').value = data.id;
            form.querySelector(`input[name="type"][value="${data.type}"]`).checked = true;
            filterCategories();
            category.value = data.category_id;
            document.getElementById('txWallet').value = data.wallet_id;
            document.getElementById('txAmount').value = new Intl.NumberFormat('vi-VN').format(data.amount);
            document.getElementById('txDate').value = data.trans_date;
            document.getElementById('txDate').max = todayISO();
            document.getElementById('txNote').value = data.note || '';
            title.textContent = 'Sửa giao dịch';
            modal.show();
        } catch (error) {
            toast(error.message, 'danger');
        }
    }

    form.addEventListener('change', event => {
        if (event.target.name === 'type') filterCategories();
    });

    form.addEventListener('submit', async event => {
        event.preventDefault();
        clearErrors();
        const id = document.getElementById('txId').value;
        const body = new FormData(form);
        body.delete('id');

        submit.disabled = true;
        try {
            await api(id ? `giao-dich/${id}/sua` : 'giao-dich', { method: 'POST', body });
            window.location.reload(); // máy chủ đã lưu thông báo thành công, hiện sau khi tải lại
        } catch (error) {
            showErrors(error);
            submit.disabled = false;
        }
    });

    document.addEventListener('click', event => {
        const edit = event.target.closest('.js-edit');
        if (edit) {
            openEdit(edit.dataset.id);
            return;
        }
        if (event.target.closest('.js-open-add, #btnAddTransaction')) {
            event.preventDefault();
            openAdd();
        }
    });

    // Bấm "Thêm giao dịch" từ trang khác sẽ tới /giao-dich?them=1 -> tự mở hộp thoại
    const params = new URLSearchParams(window.location.search);
    if (params.get('them') === '1') {
        history.replaceState(null, '', window.location.pathname);
        openAdd();
    }
})();
