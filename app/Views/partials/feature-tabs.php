<!-- Lucian UX/UI Prompt #213 · MIT License (c) 2026 Seokwoooo · https://lucianlabs.dev/uxui-prompt/#213 -->
<!--
Element: Auto-advancing feature tabs
ID: 213 · Category: Sections & Content · Tags: tabs, autoplay, features, progress, landing
What it does: A landing page's feature tabs move to the next tab every few seconds, and the bar under the current tab fills with the time left. When the tab changes, the product screen below swaps and plays its scene: calendar blocks drop into place, document lines write themselves, or chart bars grow. Press a tab to jump straight to it, and rest the pointer on the screen to pause the bar.
How to use it: Press a tab, or rest the pointer on the screen to pause the auto-advance
Footprint: fixed size, square (adapted to responsive Bento card)
Needs: tokens mapped to project's theme variables.
Live demo: https://lucianlabs.dev/uxui-prompt/#213
-->
<?php
use App\Core\Auth;
$userName = Auth::user()['full_name'] ?? 'Tôi';
$firstName = explode(' ', trim($userName));
$shortName = end($firstName) ?: 'Minh An';
?>
<style>
.ux.u213{
    --ux-accent: var(--c-primary);
    --ux-accent-soft: var(--c-primary-tint);
    --ux-surface: var(--c-surface);
    --ux-surface-2: var(--c-surface-hover);
    --ux-surface-3: var(--c-line);
    --ux-line: var(--c-line);
    --ux-ink: var(--c-text);
    --ux-ink-2: var(--c-text-muted);
    --ux-ink-3: var(--c-text-subtle);
    --ux-shadow-strong: var(--sh-md);

    position: relative;
    width: 100%;
    max-width: 460px;
    height: 350px;
    border-radius: var(--r-xl);
    overflow: hidden;
    background: radial-gradient(80% 50% at 50% 0%, var(--ux-accent-soft), transparent 70%), var(--ux-surface);
    box-shadow: 0 0 0 1px var(--ux-line), var(--sh-xs);
    color: var(--ux-ink);
    margin: 0 auto;
    transition: background var(--transition-theme), border-color var(--transition-theme);
}
.u213-hd{position:absolute;left:0;right:0;top:18px;text-align:center;padding:0 12px}
.u213-hd small{font-size:10px;font-weight:800;letter-spacing:.08em;color:var(--ux-accent);text-transform:uppercase}
.u213-hd h3{margin:3px 0 0;font-size:17.5px;font-weight:800;letter-spacing:-.025em;color:var(--ux-ink)}
.u213-tl{position:absolute;left:16px;right:16px;top:72px;display:grid;grid-template-columns:repeat(3,1fr);gap:6px}
.u213-tb{position:relative;display:flex;flex-direction:column;align-items:center;gap:4px;padding:7px 4px 10px;border:0;border-radius:12px;background:none;color:var(--ux-ink-3);font:inherit;font-size:12px;font-weight:700;cursor:pointer;transition:color .25s,background .25s}
.u213-tb svg{width:16px;height:16px}
.u213-tb.is-on{color:var(--ux-ink);background:var(--ux-surface-2)}
.u213-tb:focus-visible{outline:2px solid var(--ux-accent);outline-offset:1px}
.u213-tb>i{position:absolute;left:10px;right:10px;bottom:4px;height:3px;border-radius:2px;background:var(--ux-line);overflow:hidden}
.u213-tb>i b{display:block;height:100%;border-radius:2px;background:var(--ux-accent);transform:scaleX(0);transform-origin:0 50%}
.u213-pv{position:absolute;left:16px;right:16px;top:136px;bottom:16px;border-radius:16px;background:var(--ux-surface);box-shadow:0 0 0 1px var(--ux-line),var(--sh-xs);overflow:hidden}
.u213-pn{position:absolute;inset:0;padding:12px 14px;opacity:0;transform:translateY(10px) scale(.98);transition:opacity .35s,transform .45s cubic-bezier(.2,.8,.2,1);pointer-events:none}
.u213-pn.is-on{opacity:1;transform:none}
.u213-pn small{font-size:10px;font-weight:700;color:var(--ux-ink-3)}

.u213-wk{position:relative;display:grid;grid-template-columns:repeat(5,1fr);gap:4px;margin-top:6px;height:132px}
.u213-wk>span{display:block;border-radius:7px;background:var(--ux-surface-2);font-size:9px;font-weight:700;color:var(--ux-ink-3);text-align:center;padding-top:4px}
.u213-ev{position:absolute;border-radius:6px;padding:3px 4px;font-size:8.5px;font-weight:700;line-height:1.2;color:#fff;opacity:0;transform:translateY(-8px) scale(.9);transition:opacity .3s,transform .45s cubic-bezier(.3,1.5,.5,1);overflow:hidden;white-space:nowrap;text-overflow:ellipsis}
.u213-pn.is-play .u213-ev{opacity:1;transform:none}

.u213-dh{display:flex;align-items:center;gap:6px}
.u213-dh b{font-size:12.5px;font-weight:800;color:var(--ux-ink);margin-right:auto}
.u213-dh em{display:inline-flex;align-items:center;gap:4px;font-style:normal;font-size:9.5px;font-weight:700;color:var(--c-expense)}
.u213-dh em::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.u213-ln{height:7px;margin-top:9px;border-radius:4px;background:var(--ux-surface-3);transform:scaleX(0);transform-origin:0 50%;transition:transform .5s cubic-bezier(.3,.8,.3,1)}
.u213-ln.is-h{height:10px;background:var(--ux-ink-3);margin-top:14px}
.u213-pn.is-play .u213-ln{transform:scaleX(var(--w))}
.u213-cr{position:absolute;width:2px;height:12px;background:var(--c-expense);border-radius:1px;left:14px;top:40px;opacity:0;transition:transform .5s cubic-bezier(.3,.8,.3,1),opacity .2s}
.u213-cr::after{content:"<?= e($shortName) ?>";position:absolute;left:0;top:-13px;padding:1px 4px;border-radius:4px 4px 4px 0;background:var(--c-expense);color:#fff;font-size:8px;font-weight:800;white-space:nowrap}
.u213-pn.is-play .u213-cr{opacity:1}

.u213-kp{display:flex;align-items:baseline;gap:8px;margin-top:2px}
.u213-kp b{font-size:21px;font-weight:800;letter-spacing:-.03em;font-variant-numeric:tabular-nums;color:var(--ux-ink)}
.u213-kp span{font-size:10px;font-weight:700;color:var(--c-income)}
.u213-br{display:flex;align-items:flex-end;gap:7px;height:96px;margin-top:8px;padding-bottom:1px;border-bottom:1px solid var(--ux-line)}
.u213-br i{flex:1;border-radius:5px 5px 2px 2px;background:color-mix(in srgb,var(--ux-accent) 30%,transparent);height:var(--h);transform:scaleY(0);transform-origin:50% 100%;transition:transform .6s cubic-bezier(.3,1.3,.5,1)}
.u213-br i:last-child{background:var(--ux-accent)}
.u213-pn.is-play .u213-br i{transform:none}
@media (prefers-reduced-motion: reduce){.u213-pn,.u213-ev,.u213-ln,.u213-br i,.u213-cr{transition:none!important}}
</style>
<div class="card h-100 border-0 bg-transparent">
    <div class="card-body p-0 d-flex align-items-center justify-content-center">
        <div class="ux u213">
          <div class="u213-hd"><small>Tính năng thông minh</small><h3>Quản lý thu chi trực quan</h3></div>
          <div class="u213-tl" role="tablist" aria-label="Tính năng nổi bật">
            <button type="button" class="u213-tb" role="tab"><svg viewBox="0 0 16 16" aria-hidden="true"><rect x="2" y="3" width="12" height="11" rx="2.2" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M2 6.5h12M5.5 1.5v3M10.5 1.5v3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>Lịch chi<i><b></b></i></button>
            <button type="button" class="u213-tb" role="tab"><svg viewBox="0 0 16 16" aria-hidden="true"><path d="M4 1.8h5.5L13 5.3v8.4a.6.6 0 0 1-.6.6H4a.6.6 0 0 1-.6-.6V2.4a.6.6 0 0 1 .6-.6z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M6 8.5h4.5M6 11h3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>Ghi chép<i><b></b></i></button>
            <button type="button" class="u213-tb" role="tab"><svg viewBox="0 0 16 16" aria-hidden="true"><path d="M2.5 13.5h11M4.5 11V8M8 11V4.5M11.5 11V6.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>Báo cáo<i><b></b></i></button>
          </div>
          <div class="u213-pv">
            <div class="u213-pn" role="tabpanel"><small>Kế hoạch chi tiêu tuần này</small><div class="u213-wk"><span>T2</span><span>T3</span><span>T4</span><span>T5</span><span>T6</span></div></div>
            <div class="u213-pn" role="tabpanel"><div class="u213-dh"><b>Sổ nhật ký thu chi</b><em>đang nhập</em></div><div class="u213-ln is-h" style="--w:.55"></div><div class="u213-ln" style="--w:.95"></div><div class="u213-ln" style="--w:.82"></div><div class="u213-ln" style="--w:.9"></div><div class="u213-ln is-h" style="--w:.4"></div><div class="u213-ln" style="--w:.75"></div><div class="u213-ln" style="--w:.6"></div><span class="u213-cr" aria-hidden="true"></span></div>
            <div class="u213-pn" role="tabpanel"><small>Dòng tiền tích lũy tháng này</small><div class="u213-kp"><b>+0 ₫</b><span>+18% so với tháng trước</span></div><div class="u213-br"><i style="--h:38%"></i><i style="--h:52%"></i><i style="--h:44%"></i><i style="--h:63%"></i><i style="--h:58%"></i><i style="--h:76%"></i><i style="--h:94%"></i></div></div>
          </div>
        </div>
    </div>
</div>
<script>
(function () {
  var root = document.currentScript.previousElementSibling.querySelector('.u213');
  if (!root) return;
  var T = Array.prototype.slice.call(root.querySelectorAll('.u213-tb')), P = Array.prototype.slice.call(root.querySelectorAll('.u213-pn')), pv = root.querySelector('.u213-pv');
  var rm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var uid = 'u213' + Math.random().toString(36).slice(2, 7);
  T.forEach(function (t, i) { t.id = uid + 't' + i; P[i].id = uid + 'p' + i; t.setAttribute('aria-controls', P[i].id); P[i].setAttribute('aria-labelledby', t.id); });
  var EV = [[0, 8, 22, 'Tiền lương'], [1, 30, 20, 'Ăn uống'], [2, 6, 16, 'Tiền trọ'], [2, 52, 24, 'Mua sắm'], [3, 18, 30, 'Điện nước'], [4, 40, 18, 'Tiết kiệm']];
  var EC = ['#10B981', '#F59E0B', '#0EA5E9', '#EC4899', '#6366F1', '#8B5CF6'], wk = root.querySelector('.u213-wk');
  EV.forEach(function (e, i) { var d = document.createElement('div'); d.className = 'u213-ev'; d.textContent = e[3]; d.style.cssText = 'left:calc(' + e[0] * 20 + '% + 1px);width:calc(20% - 6px);top:calc(18px + ' + e[1] + '% * .8);height:calc(' + e[2] + '% * .8);background:' + EC[i] + ';transition-delay:' + (0.15 + i * 0.09) + 's'; wk.appendChild(d); });
  Array.prototype.forEach.call(root.querySelectorAll('.u213-ln'), function (l, i) { l.style.transitionDelay = (0.1 + i * 0.12) + 's'; });
  Array.prototype.forEach.call(root.querySelectorAll('.u213-br i'), function (b, i) { b.style.transitionDelay = (0.08 + i * 0.06) + 's'; });
  var cr = root.querySelector('.u213-cr'), kp = root.querySelector('.u213-kp b');
  var DUR = 4200, cur = -1, acc = 0, last = 0, hold = false, vis = true, raf = 0, sub = [];
  function clearSub() { sub.forEach(clearTimeout); sub = []; }
  function formatMoney(num) {
    return '+' + new Intl.NumberFormat('vi-VN').format(Math.round(num)) + ' ₫';
  }
  function scene(i) {
    var p = P[i]; p.classList.remove('is-play'); void p.offsetWidth; p.classList.add('is-play'); clearSub();
    if (i === 1) {
      var L = p.querySelectorAll('.u213-ln');
      cr.style.transform = 'translate(0,0)';
      [1, 2, 3, 5].forEach(function (n, k) { sub.push(setTimeout(function () { var l = L[n]; if (l) cr.style.transform = 'translate(' + (l.offsetWidth * parseFloat(l.style.getPropertyValue('--w'))).toFixed(0) + 'px,' + (l.offsetTop - 40 - 3) + 'px)'; }, rm ? 0 : 250 + k * 380)); });
    }
    if (i === 2) {
      var to = 48200000, t0 = performance.now();
      if (rm) { kp.textContent = formatMoney(to); return; }
      (function f(now) { var k = Math.min(1, (now - t0) / 1100), e = 1 - Math.pow(1 - k, 3); kp.textContent = formatMoney(to * e); if (k < 1 && cur === 2) requestAnimationFrame(f); })(t0);
    }
  }
  function go(i) {
    if (i === cur) return;
    cur = i; acc = 0;
    T.forEach(function (t, k) { var on = k === i; t.classList.toggle('is-on', on); t.setAttribute('aria-selected', on); t.tabIndex = on ? 0 : -1; t.querySelector('b').style.transform = 'scaleX(' + (on && rm ? 1 : 0) + ')'; });
    P.forEach(function (p, k) { p.classList.toggle('is-on', k === i); p.setAttribute('aria-hidden', k !== i); if (k !== i) p.classList.remove('is-play'); });
    scene(i);
  }
  function tick(now) {
    raf = 0;
    if (!root.isConnected || rm) return;
    if (!hold && vis) acc += Math.min(64, now - last);
    last = now;
    var p = Math.min(1, acc / DUR);
    T[cur].querySelector('b').style.transform = 'scaleX(' + p.toFixed(3) + ')';
    if (p >= 1) go((cur + 1) % T.length);
    if (vis) raf = requestAnimationFrame(tick);
  }
  function run() { if (!raf && !rm) { last = performance.now(); raf = requestAnimationFrame(tick); } }
  T.forEach(function (t, i) {
    t.addEventListener('click', function () { go(i); run(); });
    t.addEventListener('keydown', function (e) {
      var d = e.key === 'ArrowRight' ? 1 : e.key === 'ArrowLeft' ? -1 : 0;
      if (d) { e.preventDefault(); var n = (i + d + T.length) % T.length; go(n); T[n].focus(); }
    });
  });
  pv.addEventListener('pointerenter', function () { hold = true; });
  pv.addEventListener('pointerleave', function () { hold = false; });
  root.addEventListener('focusin', function () { hold = true; });
  root.addEventListener('focusout', function () { hold = false; });
  if ('IntersectionObserver' in window) new IntersectionObserver(function (es) { vis = es[0].isIntersecting; if (vis) run(); }).observe(root);
  go(0); run();
})();
</script>
