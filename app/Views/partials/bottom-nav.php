<?php
/**
 * Lucian UX/UI Prompt #041 · Notched bottom tab bar
 * MIT License (c) 2026 Seokwoooo · https://lucianlabs.dev/uxui-prompt/#041
 *
 * Element: Notched bottom tab bar ("홈 파인 하단 탭 바")
 * ID: 041 · Category: Menus & Tabs · Tags: tab-bar, mobile, navigation, notch, indicator
 * What it does: A round button rises above the selected tab while the top edge of the bar dips
 * into a matching notch and slides along with it. The icon lifts up into the circle and the label appears below.
 */

$navTabs = [
    [
        'route' => 'tong-quan',
        'url'   => url('tong-quan'),
        'label' => 'Tổng quan',
        'svg'   => '<svg viewBox="0 0 24 24"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.5"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5"/></svg>',
    ],
    [
        'route' => 'giao-dich',
        'url'   => url('giao-dich'),
        'label' => 'Giao dịch',
        'svg'   => '<svg viewBox="0 0 24 24"><path d="M8 6h12M8 12h12M8 18h12M3.5 6h.01M3.5 12h.01M3.5 18h.01"/></svg>',
    ],
    [
        'route' => 'ngan-sach',
        'url'   => url('ngan-sach'),
        'label' => 'Ngân sách',
        'svg'   => '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="4.5"/><circle cx="12" cy="12" r="1"/></svg>',
    ],
    [
        'route' => 'bao-cao',
        'url'   => url('bao-cao'),
        'label' => 'Báo cáo',
        'svg'   => '<svg viewBox="0 0 24 24"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>',
    ],
    [
        'route' => 'vi',
        'url'   => url('vi'),
        'label' => 'Ví',
        'svg'   => '<svg viewBox="0 0 24 24"><path d="M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/><path d="M16 14h.01"/><path d="M4 7V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v2"/></svg>',
    ],
];

$hasActiveNav = false;
foreach ($navTabs as &$tabItem) {
    $tabItem['active'] = !empty(active($tabItem['route']));
    if ($tabItem['active']) {
        $hasActiveNav = true;
    }
}
unset($tabItem);
?>
<!-- Lucian UX/UI Prompt #041 · MIT License (c) 2026 Seokwoooo · https://lucianlabs.dev/uxui-prompt/#041 -->
<nav class="ux u041" id="u041BottomNav" aria-label="Menu chính trên thiết bị di động">
    <span class="u041-shadow" aria-hidden="true"></span>
    <div class="u041-bar" aria-hidden="true"></div>
    <span class="u041-bub<?= $hasActiveNav ? '' : ' is-hidden' ?>" aria-hidden="true"></span>
    <ul class="u041-tabs" role="tablist" aria-label="Điều hướng chính">
        <?php foreach ($navTabs as $idx => $t): ?>
            <li role="none">
                <a class="u041-tab" 
                   href="<?= $t['url'] ?>" 
                   role="tab" 
                   aria-selected="<?= $t['active'] ? 'true' : 'false' ?>" 
                   tabindex="<?= ($t['active'] || (!$hasActiveNav && $idx === 0)) ? '0' : '-1' ?>"
                   data-route="<?= $t['route'] ?>">
                    <span class="u041-ic" aria-hidden="true"><?= $t['svg'] ?></span>
                    <span class="u041-lab"><?= e($t['label']) ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<script>
(function () {
    var root = document.getElementById('u041BottomNav');
    if (!root) return;
    var bub = root.querySelector('.u041-bub');
    var tabs = Array.prototype.slice.call(root.querySelectorAll('.u041-tab'));
    if (!tabs.length) return;

    var rm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var x = 0, v = 0, target = 0, raf = 0;

    function center(t) {
        var rootRect = root.getBoundingClientRect();
        var tabRect = t.getBoundingClientRect();
        if (tabRect.width > 0 && rootRect.width > 0) {
            return (tabRect.left - rootRect.left) + tabRect.width / 2;
        }
        return t.parentNode.offsetLeft + t.offsetWidth / 2;
    }

    function step() {
        var a = (target - x) * 0.16 - v * 0.42;
        v += a;
        x += v;
        root.style.setProperty('--x', x.toFixed(2) + 'px');
        if (Math.abs(target - x) > 0.3 || Math.abs(v) > 0.3) {
            raf = requestAnimationFrame(step);
        } else {
            x = target;
            v = 0;
            root.style.setProperty('--x', x + 'px');
            raf = 0;
        }
    }

    function select(i, focus) {
        tabs.forEach(function (t, k) {
            var isSel = (k === i);
            t.setAttribute('aria-selected', String(isSel));
            t.tabIndex = isSel ? 0 : -1;
        });
        if (bub) {
            bub.classList.toggle('is-hidden', i < 0);
        }
        if (i < 0) {
            root.style.setProperty('--x', '-9999px');
            return;
        }
        if (focus && tabs[i]) {
            tabs[i].focus();
        }
        target = center(tabs[i]);
        if (rm) {
            x = target;
            root.style.setProperty('--x', x + 'px');
            return;
        }
        if (!raf) raf = requestAnimationFrame(step);
    }

    tabs.forEach(function (t, i) {
        t.addEventListener('click', function () {
            select(i);
        });
        t.addEventListener('keydown', function (e) {
            var d = e.key === 'ArrowRight' ? 1 : e.key === 'ArrowLeft' ? -1 : 0;
            if (d) {
                e.preventDefault();
                var nextIdx = (i + d + tabs.length) % tabs.length;
                select(nextIdx, true);
            } else if (e.key === ' ' || e.key === 'Spacebar') {
                e.preventDefault();
                t.click();
            }
        });
    });

    var activeIdx = -1;
    for (var k = 0; k < tabs.length; k++) {
        if (tabs[k].getAttribute('aria-selected') === 'true') {
            activeIdx = k;
            break;
        }
    }

    function syncPosition() {
        if (activeIdx >= 0 && tabs[activeIdx]) {
            target = center(tabs[activeIdx]);
            x = target;
            root.style.setProperty('--x', x.toFixed(2) + 'px');
            if (bub) bub.classList.remove('is-hidden');
        } else {
            root.style.setProperty('--x', '-9999px');
            if (bub) bub.classList.add('is-hidden');
        }
    }

    syncPosition();

    var resizeTimer = null;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            if (activeIdx >= 0 && tabs[activeIdx]) {
                target = center(tabs[activeIdx]);
                x = target;
                root.style.setProperty('--x', x.toFixed(2) + 'px');
            }
        }, 50);
    }, { passive: true });

    window.addEventListener('pageshow', function () {
        syncPosition();
    });
})();
</script>
