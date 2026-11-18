/*
 * Hàm JavaScript dùng chung. Nạp ở mọi trang (layout), dùng được ngay trong file JS của module.
 *
 *   const res = await api('giao-dich', { method: 'POST', body: { amount: 50000 } });
 *   list.innerHTML = rows.map(r => `<td>${escapeHtml(r.note)}</td>`).join('');
 *   toast('Đã lưu giao dịch');
 */

/** Mã hóa chuỗi trước khi chèn vào innerHTML (chống XSS). BẮT BUỘC với dữ liệu người dùng. */
function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

/** Tạo URL trong website, giống hàm url() bên PHP */
function url(path = '') {
    return (window.APP.basePath || '') + '/' + String(path).replace(/^\/+/, '');
}

/**
 * Gọi API của website. Tự gửi CSRF token và nhận JSON.
 * Trả về object JSON; nếu server báo lỗi thì ném Error với message tiếng Việt.
 */
async function api(path, { method = 'GET', body = null } = {}) {
    const options = {
        method,
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-Token': window.APP.csrf,
        },
    };
    if (body instanceof FormData) {
        options.body = body;
    } else if (body !== null) {
        const form = new FormData();
        Object.entries(body).forEach(([k, v]) => form.append(k, v ?? ''));
        options.body = form;
    }
    const response = await fetch(url(path), options);
    let data = {};
    try {
        data = await response.json();
    } catch (e) {
        throw new Error('Máy chủ trả về dữ liệu không hợp lệ.');
    }
    if (response.status === 401) {
        window.location.href = url('dang-nhap');
    }
    if (!response.ok || data.success === false) {
        const error = new Error(data.message || 'Có lỗi xảy ra, vui lòng thử lại.');
        error.errors = data.errors || {};
        throw error;
    }
    return data;
}

/** 1250000 -> "1.250.000 ₫" */
function formatMoney(amount) {
    return new Intl.NumberFormat('vi-VN').format(Math.round(Number(amount) || 0)) + ' ₫';
}

/**
 * Ngày hôm nay dạng YYYY-MM-DD theo giờ máy người dùng.
 * KHÔNG dùng new Date().toISOString() vì nó đổi sang giờ UTC, ở Việt Nam sẽ lùi 1 ngày trước 7h sáng.
 */
function todayISO(date = new Date()) {
    const pad = n => String(n).padStart(2, '0');
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
}

/** Thông báo nhỏ góc màn hình, tự ẩn sau 3 giây. type: success | danger | warning */
function toast(message, type = 'success') {
    let stack = document.querySelector('.toast-stack');
    if (!stack) {
        stack = document.createElement('div');
        stack.className = 'toast-stack';
        stack.setAttribute('aria-live', 'polite');
        document.body.appendChild(stack);
    }
    const item = document.createElement('div');
    item.className = `alert alert-${type} shadow-sm mb-0`;
    item.textContent = message;
    stack.appendChild(item);
    setTimeout(() => item.remove(), 3000);
}

/** Ô nhập số tiền: tự thêm dấu chấm khi gõ (class="js-money") */
document.addEventListener('input', event => {
    const input = event.target;
    if (!input.classList || !input.classList.contains('js-money')) return;
    const digits = input.value.replace(/\D/g, '').replace(/^0+(?=\d)/, '');
    input.value = digits ? new Intl.NumberFormat('vi-VN').format(Number(digits)) : '';
});

/** Hộp xác nhận trước khi xóa: <form data-confirm="Xóa giao dịch này?"> */
document.addEventListener('submit', event => {
    const message = event.target.dataset && event.target.dataset.confirm;
    if (message && !window.confirm(message)) {
        event.preventDefault();
    }
});
