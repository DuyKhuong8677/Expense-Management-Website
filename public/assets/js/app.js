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

/**
 * Quản lý Theme: Sáng / Tối với Circular Reveal (Lucian UX/UI Prompt #219)
 * MIT License (c) 2026 Seokwoooo · https://lucianlabs.dev/uxui-prompt/#219
 */
function getTheme() {
    return document.documentElement.getAttribute('data-bs-theme') || localStorage.getItem('theme') || 'light';
}

function applyThemeDom(theme) {
    const isDark = theme === 'dark';
    document.documentElement.setAttribute('data-bs-theme', theme);
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('theme', theme);

    // Đồng bộ trạng thái cho toàn bộ công tắc Day-Night Sky Switch (Lucian Prompt #021)
    document.querySelectorAll('.u021').forEach(el => {
        el.classList.toggle('is-night', isDark);
    });
    document.querySelectorAll('#sidebarThemeSwitchBtn, .u021-sw, #themeToggleBtn, .u021-header-sw, .theme-toggle-btn').forEach(btn => {
        btn.setAttribute('aria-checked', String(isDark));
        const modeLabel = isDark ? 'Chế độ Ban đêm (Tối)' : 'Chế độ Ban ngày (Sáng)';
        btn.setAttribute('aria-label', modeLabel);
        if (btn.hasAttribute('data-tooltip')) {
            btn.setAttribute('data-tooltip', modeLabel);
        }
        if (btn.hasAttribute('title')) {
            btn.setAttribute('title', modeLabel);
        }
    });

    // Cập nhật thẻ meta theme-color cho thanh địa chỉ trình duyệt
    const metaTheme = document.querySelector('meta[name="theme-color"]');
    if (metaTheme) {
        metaTheme.setAttribute('content', isDark ? '#0F1115' : '#F6F8FA');
    }

    window.dispatchEvent(new CustomEvent('themechanged', { detail: { theme } }));
}

function setTheme(theme) {
    applyThemeDom(theme);
}

// Khóa chống click spam nhiều lần liên tiếp gây xung đột View Transition
let isThemeToggling = false;

function toggleThemeWithReveal(originX, originY) {
    if (isThemeToggling) return;

    const current = getTheme();
    const next = current === 'dark' ? 'light' : 'dark';
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Trình duyệt không hỗ trợ startViewTransition hoặc người dùng bật giảm chuyển động -> Đổi ngay không chớp màu
    if (!document.startViewTransition || prefersReducedMotion) {
        document.documentElement.classList.add('theme-instant-switch');
        applyThemeDom(next);
        void document.documentElement.offsetHeight; // Kích hoạt reflow tức thì
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                document.documentElement.classList.remove('theme-instant-switch');
            });
        });
        return;
    }

    isThemeToggling = true;

    const x = Number.isFinite(originX) ? originX : (window.innerWidth / 2);
    const y = Number.isFinite(originY) ? originY : (window.innerHeight / 2);
    // Bán kính phủ kín màn hình (+4px buffer tránh viền sub-pixel)
    const endRadius = Math.ceil(Math.hypot(
        Math.max(x, window.innerWidth - x),
        Math.max(y, window.innerHeight - y)
    )) + 4;

    // Tắt transition đổi màu nền/chữ trong lúc chạy để tránh tạo màu xám trung gian làm chớp tối
    document.documentElement.classList.add('theme-revealing');

    try {
        const transition = document.startViewTransition(() => {
            applyThemeDom(next);
        });

        transition.ready.then(() => {
            document.documentElement.animate(
                {
                    clipPath: [
                        `circle(0px at ${x}px ${y}px)`,
                        `circle(${endRadius}px at ${x}px ${y}px)`
                    ]
                },
                {
                    duration: 520,
                    easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
                    pseudoElement: '::view-transition-new(root)'
                }
            );
        }).catch(() => {
            document.documentElement.classList.remove('theme-revealing');
            isThemeToggling = false;
        });

        transition.finished.finally(() => {
            document.documentElement.classList.remove('theme-revealing');
            isThemeToggling = false;
        });
    } catch (e) {
        document.documentElement.classList.remove('theme-revealing');
        applyThemeDom(next);
        isThemeToggling = false;
    }
}

document.addEventListener('click', event => {
    const btn = event.target.closest('#themeToggleBtn, .theme-toggle-btn, #sidebarThemeSwitchBtn, .u021-sw, .u021-header-sw');
    if (btn) {
        event.preventDefault();
        const rect = btn.getBoundingClientRect();
        // Lấy tọa độ click con trỏ chuột; nếu kích hoạt qua bàn phím (Enter/Space) thì lấy tâm nút bấm
        const hasCoords = Number.isFinite(event.clientX) && Number.isFinite(event.clientY) && (event.clientX > 0 || event.clientY > 0);
        const x = hasCoords ? event.clientX : (rect.left + rect.width / 2);
        const y = hasCoords ? event.clientY : (rect.top + rect.height / 2);
        toggleThemeWithReveal(x, y);
    }
});

// Đánh dấu theme đã sẵn sàng sau lần paint đầu tiên để tránh giật transition lúc mở trang
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        requestAnimationFrame(() => {
            document.documentElement.classList.add('theme-ready');
        });
    });
} else {
    requestAnimationFrame(() => {
        document.documentElement.classList.add('theme-ready');
    });
}

/** Khởi tạo & Điều khiển Glassy SideBar V5 (Lucian UX/UI + Animate UI) */
function initGlassySidebar() {
    const sidebar = document.getElementById('appSidebar');
    if (!sidebar) return;

    const toggleBtn = document.getElementById('sidebarToggleBtn');
    const mobileToggleBtn = document.getElementById('sidebarMobileToggle');
    const backdrop = document.getElementById('sidebarBackdrop');
    const searchInput = document.getElementById('sidebarSearchInput');
    const searchClear = document.getElementById('sidebarSearchClear');
    const searchCollapsedBtn = document.getElementById('sidebarSearchCollapsedBtn');
    const navBody = document.getElementById('sidebarNavBody');
    const emptyTip = navBody ? navBody.querySelector('.sidebar-search-empty') : null;

    function setSidebarCollapsed(collapsed) {
        if (collapsed) {
            document.documentElement.classList.add('sidebar-is-collapsed');
            localStorage.setItem('sidebar_collapsed', 'true');
            if (toggleBtn) {
                toggleBtn.setAttribute('aria-expanded', 'false');
                toggleBtn.setAttribute('aria-label', 'Mở rộng menu');
                toggleBtn.setAttribute('title', 'Mở rộng menu (Ctrl + B)');
            }
        } else {
            document.documentElement.classList.remove('sidebar-is-collapsed');
            localStorage.setItem('sidebar_collapsed', 'false');
            hideTooltip(true);
            if (toggleBtn) {
                toggleBtn.setAttribute('aria-expanded', 'true');
                toggleBtn.setAttribute('aria-label', 'Thu gọn menu');
                toggleBtn.setAttribute('title', 'Thu gọn menu (Ctrl + B)');
            }
        }
    }

    // Khởi tạo trạng thái đã lưu trên màn hình lớn
    const savedCollapsed = localStorage.getItem('sidebar_collapsed') === 'true';
    if (window.innerWidth >= 992 && savedCollapsed) {
        setSidebarCollapsed(true);
    }

    // Nút co / giãn menu
    if (toggleBtn) {
        toggleBtn.addEventListener('click', e => {
            e.preventDefault();
            const isCollapsed = document.documentElement.classList.contains('sidebar-is-collapsed');
            setSidebarCollapsed(!isCollapsed);
        });
    }

    // Phím tắt Ctrl + B / Cmd + B
    document.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && (e.key === 'b' || e.key === 'B')) {
            if (e.target && ['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName)) return;
            e.preventDefault();
            const isCollapsed = document.documentElement.classList.contains('sidebar-is-collapsed');
            setSidebarCollapsed(!isCollapsed);
        }
    });

    // Bấm nút tìm kiếm thu gọn -> mở rộng menu & focus ô tìm kiếm
    if (searchCollapsedBtn) {
        searchCollapsedBtn.addEventListener('click', e => {
            e.preventDefault();
            setSidebarCollapsed(false);
            setTimeout(() => {
                if (searchInput) searchInput.focus();
            }, 180);
        });
    }

    // Điều khiển ngăn kéo trên điện thoại
    function closeMobileSidebar() {
        document.body.classList.remove('sidebar-mobile-open');
        if (backdrop) backdrop.classList.remove('show');
    }

    function openMobileSidebar() {
        document.body.classList.add('sidebar-mobile-open');
        if (backdrop) backdrop.classList.add('show');
    }

    if (mobileToggleBtn) {
        mobileToggleBtn.addEventListener('click', e => {
            e.preventDefault();
            if (document.body.classList.contains('sidebar-mobile-open')) {
                closeMobileSidebar();
            } else {
                openMobileSidebar();
            }
        });
    }

    if (backdrop) {
        backdrop.addEventListener('click', closeMobileSidebar);
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            closeMobileSidebar();
        }
    });

    // Lọc nhanh menu không dấu tiếng Việt
    if (searchInput && navBody) {
        function removeTones(str) {
            return String(str || '')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/đ/g, 'd')
                .replace(/Đ/g, 'D')
                .toLowerCase()
                .trim();
        }

        searchInput.addEventListener('input', () => {
            const query = removeTones(searchInput.value);
            if (searchClear) {
                searchClear.classList.toggle('d-none', !query);
            }

            const links = navBody.querySelectorAll('a.nav-link');
            let matchCount = 0;

            links.forEach(link => {
                const text = removeTones(link.textContent);
                const isMatch = !query || text.includes(query);
                link.style.display = isMatch ? '' : 'none';
                if (isMatch) matchCount++;
            });

            // Ẩn tiêu đề nhóm nếu mọi liên kết trong nhóm đều ẩn
            navBody.querySelectorAll('.nav-section').forEach(sec => {
                const visibleLinks = sec.querySelectorAll('a.nav-link:not([style*="display: none"])');
                sec.style.display = visibleLinks.length > 0 ? '' : 'none';
            });

            if (emptyTip) {
                emptyTip.classList.toggle('d-none', matchCount > 0 || !query);
            }
        });

        if (searchClear) {
            searchClear.addEventListener('click', () => {
                searchInput.value = '';
                searchInput.dispatchEvent(new Event('input'));
                searchInput.focus();
            });
        }

        searchInput.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                if (searchInput.value) {
                    searchInput.value = '';
                    searchInput.dispatchEvent(new Event('input'));
                }
                searchInput.blur();
            }
        });
    }

    // Điều khiển Nhãn nổi thông minh khi thu gọn menu (Floating Tooltip - Lucian UX/UI #076 & Animate UI)
    const floatingTooltip = document.getElementById('sidebarFloatingTooltip');
    const tooltipLabel = floatingTooltip ? floatingTooltip.querySelector('.tooltip-label') : null;
    let tooltipShowTimer = null;
    let tooltipHideTimer = null;
    let isTooltipWarm = false;
    let tooltipCoolTimer = null;
    let currentTooltipTarget = null;

    function isCollapsedActive() {
        return document.documentElement.classList.contains('sidebar-is-collapsed') && window.innerWidth >= 992;
    }

    function positionTooltip(target, isGliding) {
        if (!floatingTooltip || !tooltipLabel) return;
        let text = target.getAttribute('data-tooltip') || target.getAttribute('title') || '';
        // Ưu tiên nhãn đã được dịch bên trong .nav-label
        const navLabel = target.querySelector('.nav-label');
        if (navLabel && navLabel.textContent.trim()) {
            text = navLabel.textContent.trim();
        }
        if (!text) return;

        tooltipLabel.textContent = text;
        const rect = target.getBoundingClientRect();
        const x = rect.right + 12;
        const y = rect.top + (rect.height / 2);

        floatingTooltip.classList.toggle('gliding', isGliding);
        floatingTooltip.style.left = `${Math.round(x)}px`;
        floatingTooltip.style.top = `${Math.round(y)}px`;
    }

    function showTooltip(target) {
        if (!isCollapsedActive()) return;
        clearTimeout(tooltipShowTimer);
        clearTimeout(tooltipHideTimer);
        clearTimeout(tooltipCoolTimer);

        const activate = () => {
            if (!isCollapsedActive()) return;
            const wasOpen = floatingTooltip && floatingTooltip.classList.contains('show');
            positionTooltip(target, wasOpen);
            if (floatingTooltip) {
                floatingTooltip.classList.add('show');
                floatingTooltip.setAttribute('aria-hidden', 'false');
            }
            isTooltipWarm = true;
            currentTooltipTarget = target;
        };

        if (isTooltipWarm) {
            activate();
        } else {
            tooltipShowTimer = setTimeout(activate, 150);
        }
    }

    function hideTooltip(immediate = false) {
        clearTimeout(tooltipShowTimer);
        const deactivate = () => {
            if (floatingTooltip) {
                floatingTooltip.classList.remove('show', 'gliding');
                floatingTooltip.setAttribute('aria-hidden', 'true');
            }
            currentTooltipTarget = null;
            tooltipCoolTimer = setTimeout(() => {
                isTooltipWarm = false;
            }, 250);
        };

        if (immediate) {
            deactivate();
        } else {
            tooltipHideTimer = setTimeout(deactivate, 60);
        }
    }

    sidebar.querySelectorAll('[data-tooltip]').forEach(el => {
        el.addEventListener('pointerenter', e => {
            if (e.pointerType === 'mouse') {
                showTooltip(el);
            }
        });

        el.addEventListener('pointerleave', e => {
            if (e.pointerType === 'mouse') {
                hideTooltip();
            }
        });

        el.addEventListener('focus', () => {
            showTooltip(el);
        });

        el.addEventListener('blur', () => {
            hideTooltip(true);
        });

        el.addEventListener('click', () => {
            hideTooltip(true);
        });
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            hideTooltip(true);
        }
    });

    window.addEventListener('scroll', () => {
        if (currentTooltipTarget) {
            hideTooltip(true);
        }
    }, { passive: true });
}

/**
 * Chuyển trang mượt mà & An toàn (Smooth Page Transitions - Animate UI & Lucian UX/UI)
 * - Tự động phát hiện liên kết chuyển trang nội bộ để tạo hiệu ứng mờ dần (120ms exit fade)
 * - Tự động bỏ qua các liên kết đặc biệt: tải file, mở tab mới, phím bổ trợ, mở modal
 * - Có timeout dự phòng và lắng nghe sự kiện pageshow (bfcache) để không bao giờ bị kẹt trắng
 */
function initPageTransitions() {
    const content = document.querySelector('.app-content');
    if (!content) return;

    // Phục hồi lại trạng thái hiển thị nếu người dùng bấm nút Back/Forward của trình duyệt (bfcache)
    window.addEventListener('pageshow', () => {
        content.classList.remove('page-exiting');
    });

    document.addEventListener('click', event => {
        // Nếu sự kiện đã bị chặn trước đó (ví dụ mở modal hoặc hành động JS khác) -> bỏ qua
        if (event.defaultPrevented) return;

        // Bỏ qua nếu có phím bổ trợ (Ctrl, Cmd, Shift, Alt) để người dùng mở tab mới bình thường
        if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;

        // Bỏ qua nếu không phải chuột trái (button = 0)
        if (event.button && event.button !== 0) return;

        const link = event.target.closest('a[href]');
        if (!link) return;

        // Bỏ qua các phần tử mở modal hoặc thao tác tại chỗ (tránh bẫy kẹt page-exiting)
        if (link.closest('#btnAddTransaction, .js-open-add, .js-edit, .js-edit-cat, [data-bs-toggle], [data-bs-dismiss], a[href*="them=1"]')) {
            return;
        }

        // Bỏ qua target="_blank", thuộc tính download, hoặc cờ data-no-transition
        if (link.target === '_blank' || link.hasAttribute('download') || link.dataset.noTransition !== undefined) {
            return;
        }

        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;

        try {
            const targetUrl = new URL(link.href, window.location.origin);

            // Chỉ xử lý các liên kết nội bộ cùng domain
            if (targetUrl.origin !== window.location.origin) return;

            // Bỏ qua nếu bấm vào chính trang hiện tại
            if (targetUrl.pathname === window.location.pathname && targetUrl.search === window.location.search) {
                return;
            }

            // Bỏ qua các đường dẫn tải file (ví dụ xuất CSV)
            if (targetUrl.pathname.includes('/xuat-csv')) return;

            // Tôn trọng cài đặt giảm chuyển động
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                return;
            }

            // Kích hoạt hiệu ứng thoát êm ái
            event.preventDefault();
            content.classList.add('page-exiting');

            let navigated = false;
            const navigate = () => {
                if (navigated) return;
                navigated = true;
                window.location.href = targetUrl.href;
            };

            // Thời gian chờ trượt thoát 120ms (Animate UI token)
            setTimeout(navigate, 120);

            // Dự phòng an toàn tuyệt đối: huỷ lớp page-exiting sau 450ms nếu trình duyệt chậm
            setTimeout(() => {
                content.classList.remove('page-exiting');
            }, 450);
        } catch (e) {
            // Trường hợp URL không hợp lệ, giữ nguyên hành vi mặc định
        }
    });
}

/**
 * Quản lý Modal & Tránh bẫy Stacking Context / Backdrop che phủ
 * Tự động đưa mọi .modal ra trực tiếp dưới document.body để nằm trên backdrop hoàn toàn
 */
function initModalLayering() {
    function moveModalsToBody() {
        document.querySelectorAll('.modal').forEach(modal => {
            if (modal.parentElement && modal.parentElement !== document.body) {
                document.body.appendChild(modal);
            }
        });
    }

    // Di chuyển ngay khi tải xong DOM
    moveModalsToBody();

    // Di chuyển khi có modal chuẩn bị mở
    document.addEventListener('show.bs.modal', event => {
        const modal = event.target;
        if (modal && modal.parentElement && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });

    // Dọn dẹp an toàn khi modal đóng: đảm bảo không còn backdrop mồ côi làm đơ màn hình
    document.addEventListener('hidden.bs.modal', () => {
        if (!document.querySelector('.modal.show')) {
            document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        }
    });
}

// Khởi chạy các module giao diện khi DOM sẵn sàng
function startApp() {
    applyThemeDom(getTheme());
    initGlassySidebar();
    initPageTransitions();
    initModalLayering();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', startApp);
} else {
    startApp();
}
