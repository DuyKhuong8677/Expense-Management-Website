<!-- Lucian UX/UI Prompt #214 · MIT License (c) 2026 Seokwoooo · https://lucianlabs.dev/uxui-prompt/#214 -->
<!--
Element: Living bento feature grid
ID: 214 · Category: Sections & Content · Tags: bento, features, grid, hover, landing
What it does: Features are introduced in a bento grid of interlocking tiles of different sizes. Hover a tile and it lifts slightly with a brighter border while its own little scene plays: the collaboration tile gathers avatars and types text, the search tile enters a query, the stats tile grows bars, the security tile pulses its shield, and the integrations tile scrolls a row of icons.
How to use it: Hover each tile to see its own motion
Footprint: fixed size, square (adapted to responsive Bento card)
Needs: tokens mapped to project's theme variables.
Live demo: https://lucianlabs.dev/uxui-prompt/#214
-->
<style>
.ux.u214 {
    --ux-accent: var(--c-primary);
    --ux-accent-soft: var(--c-primary-tint);
    --ux-surface: var(--c-surface);
    --ux-surface-2: var(--c-surface-hover);
    --ux-line: var(--c-line);
    --ux-ink: var(--c-text);
    --ux-ink-2: var(--c-text-muted);
    --ux-ink-3: var(--c-text-subtle);
    --ux-shadow-strong: var(--sh-md);

    position: relative;
    width: 100%;
    max-width: 480px;
    height: 350px;
    border-radius: var(--r-xl);
    overflow: hidden;
    background: radial-gradient(80% 50% at 50% 0%, var(--ux-accent-soft), transparent 70%), var(--ux-surface);
    box-shadow: 0 0 0 1px var(--ux-line), var(--sh-xs);
    color: var(--ux-ink);
    margin: 0 auto;
    transition: background var(--transition-theme), border-color var(--transition-theme);
}

.u214-hd {
    position: absolute;
    left: 18px;
    top: 16px;
    right: 18px;
    pointer-events: none;
}
.u214-hd small {
    display: block;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .08em;
    color: var(--ux-accent);
    text-transform: uppercase;
}
.u214-hd h3 {
    margin: 2px 0 0;
    font-size: 17px;
    font-weight: 800;
    letter-spacing: -.025em;
    color: var(--ux-ink);
}

.u214-gd {
    position: absolute;
    left: 14px;
    right: 14px;
    top: 68px;
    bottom: 14px;
    display: grid;
    grid-template-columns: 1fr 1fr .9fr;
    grid-template-rows: 1fr 1fr .78fr;
    grid-template-areas: "a a b" "c d b" "e e e";
    gap: 8px;
}

.u214-t {
    position: relative;
    overflow: hidden;
    border-radius: 14px;
    background: var(--ux-surface-2);
    box-shadow: inset 0 0 0 1px var(--ux-line);
    padding: 9px 11px;
    cursor: default;
    outline: none;
    transition: transform .35s cubic-bezier(.3, 1.4, .5, 1), box-shadow .3s, border-color .3s;
}

.u214-t:hover,
.u214-t.g-hov,
.u214-t:focus-visible {
    transform: translateY(-3px);
    box-shadow: inset 0 0 0 1.5px color-mix(in srgb, var(--ux-accent) 55%, transparent), 0 12px 24px -14px var(--ux-accent);
}

.u214-t:focus-visible {
    outline: 2px solid var(--ux-accent);
    outline-offset: 1px;
}

.u214-t b {
    position: relative;
    z-index: 1;
    display: block;
    font-size: 11.5px;
    font-weight: 800;
    color: var(--ux-ink);
}

.u214-t small {
    position: relative;
    z-index: 1;
    display: block;
    font-size: 9.5px;
    font-weight: 600;
    color: var(--ux-ink-3);
    margin-top: 1px;
}

.u214-a { grid-area: a; }
.u214-b { grid-area: b; }
.u214-c { grid-area: c; }
.u214-d { grid-area: d; }
.u214-e { grid-area: e; }

/* Tile A: Avatars + Typing */
.u214-av {
    position: absolute;
    right: 10px;
    top: 9px;
    display: flex;
}
.u214-av i {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 2px solid var(--ux-surface-2);
    margin-left: -6px;
    transition: transform .45s cubic-bezier(.3, 1.5, .5, 1);
}
.u214-av i:nth-child(1) { background: #F472B6; transform: translateX(16px); }
.u214-av i:nth-child(2) { background: #60A5FA; transform: translateX(8px); }
.u214-av i:nth-child(3) { background: #34D399; }
.u214-a.is-on .u214-av i { transform: none; }

.u214-ty {
    position: absolute;
    left: 10px;
    right: 10px;
    bottom: 9px;
    height: 22px;
    padding: 0 8px;
    border-radius: 7px;
    background: var(--ux-surface);
    box-shadow: inset 0 0 0 1px var(--ux-line);
    font-size: 10px;
    font-weight: 600;
    line-height: 22px;
    color: var(--ux-ink-2);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.u214-ty::after {
    content: "";
    display: inline-block;
    width: 1.5px;
    height: 11px;
    margin-left: 2px;
    vertical-align: -1px;
    background: #F472B6;
}

/* Tile B: Search + Result bars */
.u214-sb {
    margin-top: 8px;
    height: 24px;
    padding: 0 7px;
    border-radius: 7px;
    background: var(--ux-surface);
    box-shadow: inset 0 0 0 1px var(--ux-line);
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 9.5px;
    font-weight: 600;
    color: var(--ux-ink-2);
    white-space: nowrap;
    overflow: hidden;
}
.u214-sb svg {
    flex: none;
    width: 11px;
    height: 11px;
    color: var(--ux-ink-3);
}
.u214-rs {
    margin-top: 6px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.u214-rs i {
    height: 7px;
    border-radius: 4px;
    background: var(--ux-line);
    transform-origin: 0 50%;
    transform: scaleX(.3);
    opacity: .4;
    transition: transform .45s cubic-bezier(.3, 1.3, .5, 1), opacity .3s;
}
.u214-rs i:nth-child(2) { transition-delay: .06s; }
.u214-rs i:nth-child(3) { transition-delay: .12s; }
.u214-rs i:nth-child(4) { transition-delay: .18s; }
.u214-b.is-on .u214-rs i { transform: scaleX(var(--w, 1)); opacity: 1; }
.u214-rs i:first-child { background: color-mix(in srgb, var(--ux-accent) 45%, transparent); }

/* Tile C: Stat Bars */
.u214-br {
    position: absolute;
    left: 10px;
    right: 10px;
    bottom: 9px;
    height: 32px;
    display: flex;
    align-items: flex-end;
    gap: 4px;
}
.u214-br i {
    flex: 1;
    height: 100%;
    border-radius: 3px 3px 0 0;
    background: color-mix(in srgb, var(--c-primary) 65%, transparent);
    transform-origin: 50% 100%;
    transform: scaleY(.25);
    transition: transform .5s cubic-bezier(.3, 1.5, .5, 1);
    transition-delay: calc(var(--i) * 45ms);
}
.u214-c.is-on .u214-br i { transform: scaleY(var(--h)); }

/* Tile D: Pulsing Shield */
.u214-sh {
    position: absolute;
    right: 10px;
    bottom: 9px;
    width: 28px;
    height: 32px;
    color: #6366F1;
}
.u214-sh::before,
.u214-sh::after {
    content: "";
    position: absolute;
    left: 50%;
    top: 50%;
    width: 28px;
    height: 28px;
    margin: -14px 0 0 -14px;
    border-radius: 50%;
    border: 1.5px solid currentColor;
    opacity: 0;
}
.u214-d.is-on .u214-sh::before { animation: u214-p 1.4s ease-out infinite; }
.u214-d.is-on .u214-sh::after { animation: u214-p 1.4s ease-out .7s infinite; }
@keyframes u214-p {
    0% { transform: scale(.6); opacity: .7; }
    100% { transform: scale(1.7); opacity: 0; }
}
.u214-sh svg {
    position: relative;
    width: 100%;
    height: 100%;
}

/* Tile E: Integrations Logo Marquee */
.u214-lg {
    position: absolute;
    right: 0;
    top: 0;
    bottom: 0;
    width: 56%;
    display: flex;
    align-items: center;
    -webkit-mask-image: linear-gradient(90deg, transparent, #000 18%, #000 82%, transparent);
    mask-image: linear-gradient(90deg, transparent, #000 18%, #000 82%, transparent);
    overflow: hidden;
}
.u214-lt {
    display: flex;
    gap: 7px;
    animation: u214-m 10s linear infinite;
    animation-play-state: paused;
}
.u214-e.is-on .u214-lt {
    animation-play-state: running;
    animation-duration: 3.8s;
}
@keyframes u214-m {
    to { transform: translateX(-50%); }
}
.u214-lt i {
    flex: none;
    height: 28px;
    padding: 0 10px;
    border-radius: 8px;
    white-space: nowrap;
    background: var(--ux-surface);
    box-shadow: 0 0 0 1px var(--ux-line);
    display: grid;
    place-items: center;
    font-style: normal;
    font-size: 10.5px;
    font-weight: 800;
}

@media (prefers-reduced-motion: reduce) {
    .u214-t,
    .u214-av i,
    .u214-rs i,
    .u214-br i {
        transition: none !important;
    }
    .u214-lt,
    .u214-d.is-on .u214-sh::before,
    .u214-d.is-on .u214-sh::after {
        animation: none !important;
    }
}
</style>

<div class="ux u214">
    <div class="u214-hd">
        <small>TÍNH NĂNG CHÍNH</small>
        <h3>Quản lý thu chi ở một nơi</h3>
    </div>
    <div class="u214-gd">
        <!-- Tile A: Ngân sách tháng -->
        <div class="u214-t u214-a" tabindex="0" role="region" aria-label="Ngân sách tháng">
            <b>Ngân sách tháng</b>
            <small>Cảnh báo khi dùng tới 80%</small>
            <span class="u214-av" aria-hidden="true"><i></i><i></i><i></i></span>
            <div class="u214-ty" aria-hidden="true">Ăn uống: 255.000 / 400.000 ₫</div>
        </div>

        <!-- Tile B: Tìm kiếm nhanh -->
        <div class="u214-t u214-b" tabindex="0" role="region" aria-label="Tìm kiếm nhanh">
            <b>Tìm kiếm</b>
            <div class="u214-sb" aria-hidden="true">
                <svg viewBox="0 0 12 12"><circle cx="5" cy="5" r="3.4" fill="none" stroke="currentColor" stroke-width="1.5"/><path d="M7.6 7.6 10.5 10.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                <span>Tìm ghi chú...</span>
            </div>
            <div class="u214-rs" aria-hidden="true">
                <i style="--w:.92"></i>
                <i style="--w:.7"></i>
                <i style="--w:.82"></i>
                <i style="--w:.55"></i>
            </div>
        </div>

        <!-- Tile C: Thống kê thu chi -->
        <div class="u214-t u214-c" tabindex="0" role="region" aria-label="Thống kê thu chi">
            <b>Thu chi</b>
            <div class="u214-br" aria-hidden="true"></div>
        </div>

        <!-- Tile D: Bảo mật tài chính -->
        <div class="u214-t u214-d" tabindex="0" role="region" aria-label="Bảo mật tài chính">
            <b>Bảo mật</b>
            <small>Mật khẩu mã hóa bcrypt</small>
            <span class="u214-sh" aria-hidden="true">
                <svg viewBox="0 0 30 34">
                    <path d="M15 2l11 4.5v8.5c0 8-5 13.5-11 16.5C9 28.5 4 23 4 15V6.5z" fill="currentColor" opacity=".18"/>
                    <path d="M15 2l11 4.5v8.5c0 8-5 13.5-11 16.5C9 28.5 4 23 4 15V6.5z" fill="none" stroke="currentColor" stroke-width="2"/>
                    <path d="M10.5 16.5l3 3 6-6.5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
        </div>

        <!-- Tile E: Nhiều ví -->
        <div class="u214-t u214-e" tabindex="0" role="region" aria-label="Quản lý nhiều ví">
            <b>Nhiều ví tiền</b>
            <small>Chuyển tiền giữa các ví</small>
            <div class="u214-lg" aria-hidden="true">
                <div class="u214-lt"></div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var root = document.currentScript.previousElementSibling;
    if (!root) return;
    var T = Array.prototype.slice.call(root.querySelectorAll('.u214-t'));
    var ty = root.querySelector('.u214-ty');
    var sb = root.querySelector('.u214-sb span');
    var br = root.querySelector('.u214-br');
    var lt = root.querySelector('.u214-lt');
    var rm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Khởi tạo 6 cột biểu đồ động
    if (br && !br.children.length) {
        [0.45, 0.7, 0.55, 0.9, 0.75, 1].forEach(function (h, i) {
            var b = document.createElement('i');
            b.style.setProperty('--h', h);
            b.style.setProperty('--i', i);
            br.appendChild(b);
        });
    }

    // Các loại ví người dùng tự tạo (không liên kết ngân hàng thật)
    if (lt && !lt.children.length) {
        var LOGOS = [
            ['Tiền mặt', 'var(--c-primary)'],
            ['Ngân hàng', 'var(--c-primary)'],
            ['Ví điện tử', 'var(--c-primary)'],
            ['Thẻ', 'var(--c-primary)']
        ];
        LOGOS.concat(LOGOS).forEach(function (l) {
            var i = document.createElement('i');
            i.textContent = l[0];
            i.style.color = l[1];
            lt.appendChild(i);
        });
    }

    var timers = {};
    function type(el, text, key) {
        if (!el) return;
        clearTimeout(timers[key]);
        var s = Array.from(text), k = 0;
        if (rm) {
            el.textContent = text;
            return;
        }
        (function t() {
            el.textContent = s.slice(0, ++k).join('');
            if (k < s.length) {
                timers[key] = setTimeout(t, 40);
            }
        })();
    }

    var defaultTy = 'Ăn uống: 255.000 / 400.000 ₫';
    var defaultSb = 'Tìm ghi chú...';
    if (ty) ty.textContent = defaultTy;
    if (sb) sb.textContent = defaultSb;

    T.forEach(function (t, i) {
        function on() {
            t.classList.add('is-on');
            if (i === 0 && ty) {
                type(ty, 'Mua sắm vượt hạn mức 90.000 ₫', 'a');
            }
            if (i === 1 && sb) {
                type(sb, 'cơm trưa', 'b');
            }
        }
        function off() {
            t.classList.remove('is-on');
            if (i === 0 && ty) {
                clearTimeout(timers['a']);
                ty.textContent = defaultTy;
            }
            if (i === 1 && sb) {
                clearTimeout(timers['b']);
                sb.textContent = defaultSb;
            }
        }

        t.addEventListener('pointerenter', on);
        t.addEventListener('pointerleave', off);
        t.addEventListener('focus', on);
        t.addEventListener('blur', off);
    });
})();
</script>
