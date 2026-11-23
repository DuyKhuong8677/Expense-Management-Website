<!-- Lucian UX/UI Prompt #021 · MIT License (c) 2026 Seokwoooo · https://lucianlabs.dev/uxui-prompt/#021 -->
<!--
Element: Day–night sky switch
ID: 021 · Category: Toggles & Pickers · Tags: toggle, switch, day-night, theme, sky
What it does: The sun knob rolls across and turns into a cratered moon. The sky deepens to night, the clouds sink away and the stars start to twinkle.
How to use it: Press the switch to flip between day and night
Footprint: responsive sky switch with sliding status label
Needs: tokens mapped to project theme variables.
Live demo: https://lucianlabs.dev/uxui-prompt/#021
-->
<div class="ux u021" id="u021SkySwitchWrap">
  <button class="u021-sw" id="sidebarThemeSwitchBtn" type="button" role="switch" aria-checked="false" aria-label="Chuyển chế độ Ngày / Đêm" data-tooltip="Đổi giao diện Ngày / Đêm">
    <span class="u021-halo" aria-hidden="true"><i></i><i></i><i></i></span>
    <span class="u021-stars" aria-hidden="true">
      <i style="left:26px;top:16px"></i>
      <i style="left:52px;top:44px;animation-delay:-.6s;width:3px;height:3px"></i>
      <i style="left:76px;top:20px;animation-delay:-1.2s"></i>
      <i style="left:38px;top:54px;animation-delay:-1.8s;width:3px;height:3px"></i>
      <i style="left:96px;top:46px;animation-delay:-.9s;width:2px;height:2px"></i>
      <i style="left:16px;top:36px;animation-delay:-1.5s;width:2px;height:2px"></i>
    </span>
    <span class="u021-clouds" aria-hidden="true"><i></i><i></i></span>
    <span class="u021-knob" aria-hidden="true">
      <span class="u021-moon"><i></i><i></i><i></i></span>
    </span>
  </button>
  <div class="u021-lab" aria-hidden="true">
    <div class="u021-val u021-val-day">
      <span class="u021-val-title">Ban ngày</span>
      <span class="u021-val-sub">Giao diện sáng</span>
    </div>
    <div class="u021-val u021-val-night">
      <span class="u021-val-title">Ban đêm</span>
      <span class="u021-val-sub">Giao diện tối</span>
    </div>
  </div>
</div>
