<?php
// Generate Fluent Forms nonce and expose to React
$ite_ff_nonce = wp_create_nonce('fluentFormNonce');
echo "<script>window.ITE_FF_NONCE = '" . esc_js($ite_ff_nonce) . "';</script>";
?>
<?php ob_start(); ?>
<!-- CDN: React 18, Babel, Lucide, Flatpickr — must load before text/babel script -->
<script src="https://unpkg.com/react@18.3.1/umd/react.production.min.js" crossorigin="anonymous"></script>
<script src="https://unpkg.com/react-dom@18.3.1/umd/react-dom.production.min.js" crossorigin="anonymous"></script>
<script src="https://unpkg.com/@babel/standalone@7.29.0/babel.min.js" crossorigin="anonymous"></script>
<script src="https://unpkg.com/lucide@0.469.0/dist/umd/lucide.min.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css" />
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<style>
    /* Fonts loaded globally by Oxygen — Jost as fallback via CDN */
    @import url('https://fonts.googleapis.com/css2?family=Jost:wght@400;500;700&display=swap');

    /* ═══════════════════════════════════════════════════════════
       DESIGN TOKENS
       Dark:  #080809  ·  Navy:  #1c1f2e  ·  Green: #C3FF8A
       Card:  #111116  ·  Grey:  #8a8d99
    ═══════════════════════════════════════════════════════════ */
    :root {
      --dark:    #080809;
      --navy:    #1c1f2e;
      --green:   #C3FF8A;
      --grey:    #8a8d99;
      --card-bg: #111116;
      --font:    'Century Gothic', 'Jost', Arial, sans-serif;
      --venues-grid-cols: 3;
      --green-faint:  rgba(195,255,138,0.15);
      --green-mid:    rgba(195,255,138,0.30);
      --green-strong: rgba(195,255,138,0.50);
      --ease-out: cubic-bezier(0.22, 1, 0.36, 1);
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    html, body {
      background: var(--dark);
      color: #fff;
      font-family: var(--font);
      font-size: 17px;
      line-height: 1.7;
      -webkit-font-smoothing: antialiased;
      text-rendering: optimizeLegibility;
    }

    a { color: var(--green); text-decoration: none; transition: color 120ms var(--ease-out); }
    a:hover { color: #fff; }

    .container { max-width: 1200px; margin: 0 auto; padding: 0 40px; }

    /* ── Scroll reveal ── */
    #root.js-reveal-ready .reveal {
      opacity: 0;
      transform: translateY(56px);
    }
    .reveal.in-view {
      opacity: 1 !important;
      transform: translateY(0) !important;
      transition: opacity 0.75s cubic-bezier(0.16, 1, 0.3, 1),
                  transform 0.75s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* ── Glass fade-in — plays with the upward bump on scroll-in ── */
    @keyframes cardGlassIn {
      from {
        backdrop-filter: blur(0px); -webkit-backdrop-filter: blur(0px);
        background: rgba(255,255,255,0);
      }
      to {
        backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
        background: rgba(255,255,255,0.08);
      }
    }
    .venue-card.in-view .info {
      animation: cardGlassIn 0.75s cubic-bezier(0.16, 1, 0.3, 1) 0.1s both;
    }

    /* ═══════════════════════════════════════════════════════════
       NAV
    ═══════════════════════════════════════════════════════════ */
    .nav {
      position: fixed; top: 0; left: 0; right: 0; z-index: 40;
      padding: 0 32px;
      height: 64px;
      display: flex; align-items: center; justify-content: space-between;
      background: rgba(8,8,9,0.94);
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
      border-bottom: 1px solid rgba(195,255,138,0.12);
      transition: background 0.3s;
    }
    .nav .brand { display: flex; align-items: center; gap: 12px; cursor: pointer; }
    .nav .brand img { height: 26px; display: block; }
    .nav .links { display: flex; gap: 28px; }
    .nav .links a {
      font-family: var(--font);
      font-weight: 500; font-size: 11px;
      letter-spacing: 0.22em; text-transform: uppercase;
      color: rgba(255,255,255,0.72); cursor: pointer;
      transition: color 160ms;
    }
    .nav .links a:hover,
    .nav .links a.active { color: var(--green); }

    /* ═══════════════════════════════════════════════════════════
       BUTTONS — ITE spec: 10px radius, 2px green border, 13px
    ═══════════════════════════════════════════════════════════ */
    .btn {
      display: inline-flex; align-items: center; gap: 8px;
      font-family: var(--font);
      font-weight: 500; font-size: 13px;
      letter-spacing: 0.14em; text-transform: uppercase;
      padding: 10px 24px;
      border-radius: 10px; border: 2px solid var(--green);
      cursor: pointer; text-decoration: none;
      transition: background 0.2s, color 0.2s;
      white-space: nowrap;
    }
    .btn.filled  { background: var(--green); color: var(--dark); }
    .btn.filled:hover  { background: transparent; color: var(--green); }
    .btn.outline { background: transparent; color: var(--green); }
    .btn.outline:hover { background: var(--green); color: var(--dark); }
    .btn[disabled] { opacity: 0.55; cursor: not-allowed; pointer-events: none; }

    /* ═══════════════════════════════════════════════════════════
       PAGE HEADER
    ═══════════════════════════════════════════════════════════ */
    .page-header {
      background: var(--dark);
      color: #fff;
      padding: 120px 0 96px;
      position: relative;
      overflow: hidden;
    }
    .page-header::before {
      content: '';
      position: absolute; inset: 0;
      background-image:
        linear-gradient(180deg, rgba(8,8,9,0.55) 0%, rgba(8,8,9,0.92) 100%),
        url('https://intheevent.com/wp-content/uploads/2026/06/gala-green.jpg');
      background-size: cover; background-position: center;
      opacity: 0.72; z-index: 0;
    }
    .page-header .spotlight-overlay {
      position: absolute; inset: 0; pointer-events: none;
      transition: background 0.1s; z-index: 1;
    }
    .page-header .container { position: relative; z-index: 2; }
    .page-header .eyebrow {
      display: block;
      font-family: var(--font); font-weight: 500; font-size: 13px;
      letter-spacing: 0.32em; text-transform: uppercase;
      color: var(--green); margin-bottom: 20px;
    }
    .page-header h1 {
      font-family: var(--font); font-weight: 500;
      font-size: clamp(3rem, 7vw, 84px);
      letter-spacing: 0.06em; text-transform: uppercase;
      line-height: 1.0; color: #fff; max-width: 14ch;
    }
    .page-header h1 .accent { color: var(--green); }
    .page-header .lead {
      font-family: var(--font);
      font-size: clamp(1.1rem, 1.8vw, 1.4rem); line-height: 1.55;
      color: rgba(255,255,255,0.85); max-width: 58ch; margin-top: 28px;
    }
    .page-header .meta {
      margin-top: 48px; display: flex; gap: 64px;
      padding-top: 32px; border-top: 1px solid rgba(255,255,255,0.18);
    }
    .page-header .meta .item .l {
      font-family: var(--font); font-weight: 500; font-size: 10px;
      letter-spacing: 0.22em; text-transform: uppercase;
      color: var(--grey); margin-bottom: 6px;
    }
    .page-header .meta .item .v {
      font-family: var(--font); font-weight: 500; font-size: 15px;
      letter-spacing: 0.10em; text-transform: uppercase; color: #fff;
    }

    /* ═══════════════════════════════════════════════════════════
       FILTER BAR
    ═══════════════════════════════════════════════════════════ */
    .filter-bar {
      position: sticky; top: 64px; z-index: 30;
      background: var(--navy);
      border-bottom: 1px solid rgba(195,255,138,0.15);
      padding: 14px 0;
      transition: background 500ms var(--ease-out);
    }
    .filter-bar.scrolled { background: var(--dark); }
    .filter-bar .row {
      display: flex; align-items: center;
      justify-content: space-between; gap: 32px;
    }
    .filter-bar .chips { display: flex; gap: 8px; flex-wrap: wrap; }
    .chip {
      font-family: var(--font); font-weight: 500; font-size: 10px;
      letter-spacing: 0.22em; text-transform: uppercase;
      padding: 7px 16px;
      border: 1px solid rgba(195,255,138,0.35);
      border-radius: 999px; color: var(--green);
      background: transparent; cursor: pointer;
      transition: all 160ms var(--ease-out);
      white-space: nowrap;
    }
    .chip:hover { background: rgba(195,255,138,0.08); border-color: rgba(195,255,138,0.60); }
    .chip.active { background: var(--green); color: var(--dark); border-color: var(--green); }
    .chip:focus-visible { outline: 2px solid var(--green); outline-offset: 2px; }
    .chip .ct { margin-left: 6px; font-size: 9px; opacity: 0.65; }
    .chip.active .ct { opacity: 0.55; }
    .filter-bar .count {
      font-family: var(--font); font-weight: 500; font-size: 11px;
      letter-spacing: 0.18em; text-transform: uppercase;
      color: var(--grey); white-space: nowrap; flex-shrink: 0;
    }
    .filter-bar .count strong { color: var(--green); font-weight: 500; }

    /* ═══════════════════════════════════════════════════════════
       FEATURED CARD
    ═══════════════════════════════════════════════════════════ */
    .featured-section { padding: 80px 0 40px; background: var(--dark); }
    .featured-card {
      display: grid; grid-template-columns: 1.4fr 1fr;
      background: var(--navy); color: #fff;
      border-radius: 8px; overflow: hidden;
      border: 1px solid rgba(195,255,138,0.22);
      box-shadow: 0 24px 80px -32px rgba(0,0,0,0.6);
    }
    @keyframes photoBgFade { from { opacity: 0; } to { opacity: 1; } }
    .featured-card .photo-bg {
      position: absolute; inset: 0;
      background-size: cover; background-position: center;
      filter: brightness(0.62);
      transition: filter 480ms cubic-bezier(0.22,1,0.36,1);
      animation: photoBgFade 1.2s var(--ease-out) both;
      z-index: 0;
    }
    .featured-card:hover .photo-bg { filter: brightness(0.84); }
    .featured-card .photo {
      position: relative; min-height: 560px;
      z-index: 1;
    }
    .featured-card .photo .overlay {
      position: absolute; inset: 0;
      background: linear-gradient(135deg, transparent 40%, rgba(8,8,9,0.42) 100%);
    }
    .featured-card .badge {
      position: absolute; top: 24px; left: 24px;
      display: inline-flex; align-items: center; gap: 7px;
      padding: 8px 14px;
      background: var(--green); color: var(--dark);
      font-family: var(--font); font-weight: 500; font-size: 10px;
      letter-spacing: 0.24em; text-transform: uppercase;
      border-radius: 4px; z-index: 2;
    }
    .featured-card .badge svg { width: 12px; height: 12px; }
    .featured-card .thumbs {
      position: absolute; bottom: 24px; left: 24px;
      display: flex; gap: 8px; z-index: 2;
    }
    .featured-card .thumb {
      width: 60px; height: 60px;
      background-size: cover; background-position: center;
      border-radius: 4px;
      border: 1px solid rgba(195,255,138,0.35);
      cursor: pointer; opacity: 0.55;
      transition: opacity 160ms, border-color 160ms;
    }
    .featured-card .thumb:hover,
    .featured-card .thumb.active { opacity: 1; border-color: var(--green); }
    /* ── Featured card body panel — Featured glass ── */
    .featured-card .body {
      padding: 56px 52px 48px;
      display: flex; flex-direction: column; gap: 22px;
      background: rgba(255,255,255,0.10);
      backdrop-filter: blur(22px); -webkit-backdrop-filter: blur(22px);
      position: relative;
      transition:
        background 480ms cubic-bezier(0.22,1,0.36,1),
        backdrop-filter 480ms cubic-bezier(0.22,1,0.36,1),
        -webkit-backdrop-filter 480ms cubic-bezier(0.22,1,0.36,1);
    }
    .featured-card .body::before {
      content: ''; position: absolute; inset: 0;
      border: 1px solid transparent;
      background: conic-gradient(
        from 0deg,
        rgba(195,255,138,0.28) 0deg,   rgba(160,200,255,0.28) 60deg,
        rgba(255,200,160,0.28) 120deg, rgba(195,255,138,0.28) 180deg,
        rgba(180,160,255,0.28) 240deg, rgba(255,230,140,0.28) 300deg,
        rgba(195,255,138,0.28) 360deg
      ) border-box;
      -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
      -webkit-mask-composite: destination-out; mask-composite: exclude;
      pointer-events: none;
    }
    .featured-card .body::after {
      content: ''; position: absolute; inset: 0;
      border: 1px solid transparent;
      background: linear-gradient(
        135deg,
        rgba(255,255,255,0.62) 0%, rgba(255,255,255,0.10) 30%,
        transparent 55%, rgba(0,0,0,0.12) 100%
      ) border-box;
      -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
      -webkit-mask-composite: destination-out; mask-composite: exclude;
      pointer-events: none;
    }
    .featured-card:hover .body {
      backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px);
      background: rgba(255,255,255,0.13);
    }
    .featured-card .body > * { position: relative; z-index: 1; }

    @keyframes featuredGlassIn {
      from { backdrop-filter: blur(0px); -webkit-backdrop-filter: blur(0px); transform: translateX(16px); }
      to   { backdrop-filter: blur(22px); -webkit-backdrop-filter: blur(22px); transform: translateX(0); }
    }
    .featured-card.in-view .body {
      animation: featuredGlassIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.3s both;
    }
    .featured-card .eyebrow {
      font-family: var(--font); font-weight: 500; font-size: 13px;
      letter-spacing: 0.32em; text-transform: uppercase; color: var(--green);
    }
    .heading-hairline { height: 1px; background: rgba(195,255,138,0.28); width: 56px; }
    .featured-card h2 {
      font-family: var(--font); font-weight: 500;
      font-size: clamp(1.5rem, 2.2vw, 2rem);
      letter-spacing: 0.10em; text-transform: uppercase;
      line-height: 1.2; color: #fff;
    }
    .featured-card .desc {
      font-size: 15px; line-height: 1.72; color: rgba(255,255,255,0.78);
    }
    .featured-card .specs {
      display: grid; grid-template-columns: 1fr 1fr; gap: 18px 28px;
      padding: 18px 0;
      border-top: 1px solid rgba(255,255,255,0.10);
      border-bottom: 1px solid rgba(255,255,255,0.10);
    }
    .featured-card .spec { display: flex; flex-direction: column; }
    .featured-card .spec .l {
      font-family: var(--font); font-weight: 500; font-size: 10px;
      letter-spacing: 0.22em; text-transform: uppercase;
      color: var(--grey); margin-bottom: 6px;
      display: flex; align-items: center; gap: 5px;
    }
    .featured-card .spec .l svg { width: 11px; height: 11px; color: var(--green); }
    .featured-card .spec .v { font-size: 14px; color: #fff; }
    .featured-card .actions { display: flex; gap: 12px; flex-wrap: wrap; margin-top: auto; }

    /* ═══════════════════════════════════════════════════════════
       VENUES SECTION + GRID
    ═══════════════════════════════════════════════════════════ */
    .venues-section { padding: 40px 0 80px; background: var(--navy); }
    .venues-section .section-head {
      display: flex; justify-content: space-between; align-items: flex-end;
      margin-bottom: 40px; gap: 32px;
    }
    .venues-section .section-head .eyebrow {
      display: block;
      font-family: var(--font); font-weight: 500; font-size: 13px;
      letter-spacing: 0.32em; text-transform: uppercase;
      color: var(--green); margin-bottom: 10px;
    }
    .venues-section .section-head h2 {
      font-family: var(--font); font-weight: 500;
      font-size: clamp(1.5rem, 2.2vw, 2rem);
      letter-spacing: 0.10em; text-transform: uppercase;
      color: var(--green); line-height: 1.2;
    }
    .venues-section .section-head p {
      font-size: 14px; color: var(--grey); line-height: 1.65; max-width: 36ch;
    }
    .venues-grid {
      display: grid;
      grid-template-columns: repeat(var(--venues-grid-cols, 3), 1fr);
      gap: 24px;
    }

    /* ── Venue card ── */
    .venue-card {
      cursor: pointer;
      display: flex; flex-direction: column;
      border: 1px solid rgba(195,255,138,0.22);
      border-radius: 8px; overflow: hidden;
      background: var(--card-bg);
      position: relative;
      transition:
        border-color 480ms cubic-bezier(0.22,1,0.36,1),
        box-shadow 480ms cubic-bezier(0.22,1,0.36,1),
        transform 480ms cubic-bezier(0.22,1,0.36,1);
    }
    .venue-card:hover {
      border-color: rgba(195,255,138,0.50);
      box-shadow: 0 16px 48px rgba(0,0,0,0.55);
      transform: translateY(-7px) !important;
    }
    .venue-card:focus-visible { outline: 2px solid var(--green); outline-offset: 2px; }
    /* Full-card photo bg — extends behind glass info panel so backdrop-filter has content to blur */
    .venue-card .photo-bg {
      position: absolute; inset: 0;
      background-size: cover; background-position: center;
      filter: brightness(0.62);
      transition: filter 480ms cubic-bezier(0.22,1,0.36,1);
      z-index: 0;
    }
    .venue-card:hover .photo-bg { filter: brightness(0.84); }
    .venue-card .media {
      position: relative; aspect-ratio: 4 / 3;
      overflow: visible; z-index: 1;
    }
    .venue-card .city-tag {
      position: absolute; top: 12px; left: 12px;
      font-family: var(--font); font-weight: 500; font-size: 9px;
      letter-spacing: 0.24em; text-transform: uppercase;
      color: #fff; padding: 5px 10px;
      background: rgba(0,0,0,0.5); backdrop-filter: blur(6px);
      border-radius: 999px; border: 1px solid rgba(195,255,138,0.35); z-index: 2;
    }
    .venue-card .cap-tag {
      position: absolute; bottom: 12px; right: 12px;
      font-family: var(--font); font-weight: 500; font-size: 9px;
      letter-spacing: 0.20em; text-transform: uppercase;
      color: #fff; padding: 5px 10px;
      background: rgba(0,0,0,0.5); backdrop-filter: blur(6px);
      border-radius: 999px; border: 1px solid rgba(195,255,138,0.35);
      display: flex; align-items: center; gap: 5px;
      z-index: 2; opacity: 0; transform: translateY(4px);
      transition: opacity 220ms, transform 220ms;
    }
    .venue-card:hover .cap-tag { opacity: 1; transform: translateY(0); }
    .venue-card .cap-tag svg { width: 10px; height: 10px; color: var(--green); }
    /* ── Venue card info panel — Standard glass ── */
    .venue-card .info {
      padding: 14px 16px;
      display: flex; flex-direction: column; gap: 8px;
      flex: 1;
      background: rgba(255,255,255,0.08);
      backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
      position: relative;
      transition:
        background 480ms cubic-bezier(0.22,1,0.36,1),
        backdrop-filter 480ms cubic-bezier(0.22,1,0.36,1),
        -webkit-backdrop-filter 480ms cubic-bezier(0.22,1,0.36,1);
    }
    .venue-card .info::before {
      content: ''; position: absolute; inset: 0;
      border-radius: 0 0 8px 8px; border: 1px solid transparent;
      background: conic-gradient(
        from 0deg,
        rgba(195,255,138,0.18) 0deg,   rgba(160,200,255,0.18) 60deg,
        rgba(255,200,160,0.18) 120deg, rgba(195,255,138,0.18) 180deg,
        rgba(180,160,255,0.18) 240deg, rgba(255,230,140,0.18) 300deg,
        rgba(195,255,138,0.18) 360deg
      ) border-box;
      -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
      -webkit-mask-composite: destination-out; mask-composite: exclude;
      pointer-events: none;
    }
    .venue-card .info::after {
      content: ''; position: absolute; inset: 0;
      border-radius: 0 0 8px 8px; border: 1px solid transparent;
      background: linear-gradient(
        135deg,
        rgba(255,255,255,0.55) 0%, rgba(255,255,255,0.08) 30%,
        transparent 55%, rgba(0,0,0,0.12) 100%
      ) border-box;
      -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
      -webkit-mask-composite: destination-out; mask-composite: exclude;
      pointer-events: none;
    }
    .venue-card:hover .info {
      background: rgba(255,255,255,0.13);
      backdrop-filter: blur(7px); -webkit-backdrop-filter: blur(7px);
    }
    .venue-card .info > * { position: relative; z-index: 1; }
    .venue-card .name {
      font-family: var(--font); font-weight: 500; font-size: 13px;
      letter-spacing: 0.12em; text-transform: uppercase;
      color: #fff; line-height: 1.25;
    }
    .venue-card .address {
      font-size: 12px; color: var(--grey); line-height: 1.5;
      display: flex; align-items: flex-start; gap: 5px;
      transition: color 480ms cubic-bezier(0.22,1,0.36,1);
    }
    .venue-card:hover .address { color: rgba(255,255,255,0.88); }
    .venue-card .address svg { width: 12px; height: 12px; color: var(--green); flex-shrink: 0; margin-top: 2px; }
    .venue-card .row-bottom {
      display: flex; justify-content: space-between; align-items: center;
      margin-top: 4px; flex-wrap: wrap; gap: 6px;
    }
    .venue-card .style-pill {
      display: inline-flex; align-items: center;
      border: 1px solid rgba(195,255,138,0.45); border-radius: 999px;
      color: var(--green);
      font-family: var(--font); font-size: 10px;
      letter-spacing: 0.10em; text-transform: uppercase;
      padding: 3px 10px;
    }
    .venue-card .view {
      font-family: var(--font); font-weight: 500; font-size: 10px;
      letter-spacing: 0.22em; text-transform: uppercase;
      color: var(--green);
      display: inline-flex; align-items: center; gap: 4px;
      transition: gap 180ms;
    }
    .venue-card:hover .view { gap: 8px; }
    .venue-card .view svg { width: 11px; height: 11px; }
    /* Mobile overlay style */
    .venue-card .city-tag-mobile { display: none; }

    /* ═══════════════════════════════════════════════════════════
       CTA BAND
    ═══════════════════════════════════════════════════════════ */
    .cta-band {
      background: var(--navy); color: #fff;
      padding: 80px 0; position: relative; overflow: hidden;
    }
    .cta-band .spotlight-overlay {
      position: absolute; inset: 0; pointer-events: none; z-index: 1;
    }
    .cta-band .inner {
      position: relative; z-index: 2;
      display: grid; grid-template-columns: 1.2fr 1fr;
      gap: 64px; align-items: start;
    }
    .cta-band .eyebrow {
      display: block;
      font-family: var(--font); font-weight: 500; font-size: 13px;
      letter-spacing: 0.32em; text-transform: uppercase;
      color: var(--green); margin-bottom: 16px;
    }
    .cta-band h2 {
      font-family: var(--font); font-weight: 500;
      font-size: clamp(1.5rem, 2.2vw, 2rem);
      letter-spacing: 0.10em; text-transform: uppercase;
      line-height: 1.25; color: var(--green);
    }
    .cta-band p {
      color: rgba(255,255,255,0.78); font-size: 16px; line-height: 1.7;
      margin: 20px 0 0; max-width: 52ch;
    }
    .cta-band .actions { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 28px; }
    .cta-band .sister {
      margin-top: 16px;
      font-family: var(--font); font-weight: 500; font-size: 11px;
      letter-spacing: 0.22em; text-transform: uppercase; color: var(--grey);
    }
    .cta-band .sister a { color: var(--green); cursor: pointer; }
    .cta-inquiry-card {
      padding: 32px;
      border: 1px solid rgba(195,255,138,0.22); border-radius: 8px;
      background: rgba(195,255,138,0.03);
    }
    .cta-inquiry-card .eyebrow {
      font-family: var(--font); font-weight: 500; font-size: 12px;
      letter-spacing: 0.32em; text-transform: uppercase; color: var(--green);
    }
    .cta-inquiry-card h3 {
      font-family: var(--font); font-weight: 500; font-size: 18px;
      letter-spacing: 0.10em; text-transform: uppercase;
      color: #fff; margin: 16px 0 20px;
    }
    .cta-inquiry-card .fields { display: flex; flex-direction: column; gap: 12px; }
    .cta-inquiry-card .form-field {
      display: flex; align-items: center; gap: 12px;
      padding: 12px 14px; background: var(--dark);
      border: 1px solid rgba(195,255,138,0.18); border-radius: 6px;
      color: rgba(255,255,255,0.55);
      font-family: var(--font); font-size: 14px;
      cursor: text; transition: border-color 160ms;
    }
    .cta-inquiry-card .form-field:hover { border-color: rgba(195,255,138,0.35); }
    .cta-inquiry-card .form-field svg { width: 15px; height: 15px; color: var(--green); flex-shrink: 0; }
    .cta-input {
      flex: 1; background: none; border: none; outline: none;
      font-family: var(--font); font-size: 14px;
      color: #fff; caret-color: var(--green);
    }
    .cta-input::placeholder { color: rgba(255,255,255,0.40); }
    .cta-error { font-size: 12px; color: #ff7070; line-height: 1.5; }
    .cta-success {
      display: flex; flex-direction: column; align-items: center; justify-content: center;
      gap: 10px; padding: 32px 16px; text-align: center;
    }
    .cta-success svg { width: 36px; height: 36px; color: var(--green); }
    .cta-success p {
      font-family: var(--font); font-size: 18px; font-weight: 500;
      letter-spacing: 0.10em; text-transform: uppercase; color: #fff;
    }
    .cta-success span {
      font-family: var(--font); font-size: 13px; color: rgba(255,255,255,0.6); line-height: 1.6;
    }

    /* ═══════════════════════════════════════════════════════════
       FOOTER
    ═══════════════════════════════════════════════════════════ */
    .footer {
      background: var(--dark); color: #fff;
      padding: 80px 0 40px;
      border-top: 1px solid rgba(195,255,138,0.12);
    }
    .footer .cols { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 48px; }
    .footer h4 {
      font-family: var(--font); font-weight: 500; font-size: 11px;
      letter-spacing: 0.22em; text-transform: uppercase;
      color: var(--green); margin-bottom: 18px;
    }
    .footer ul { list-style: none; display: flex; flex-direction: column; gap: 10px; }
    .footer a { color: rgba(255,255,255,0.72); font-size: 14px; }
    .footer a:hover { color: var(--green); }
    .footer .brand-block img { height: 48px; margin-bottom: 16px; }
    .footer .lead { color: rgba(255,255,255,0.7); font-size: 14px; line-height: 1.7; max-width: 36ch; }
    .footer .fine {
      margin-top: 56px; padding-top: 24px;
      border-top: 1px solid rgba(255,255,255,0.12);
      display: flex; justify-content: space-between;
      font-size: 12px; color: rgba(255,255,255,0.45);
      font-family: var(--font);
    }

    /* ═══════════════════════════════════════════════════════════
       MODAL
    ═══════════════════════════════════════════════════════════ */
    .modal-backdrop {
      position: fixed; inset: 0;
      background: rgba(8,8,9,0.95); backdrop-filter: blur(8px);
      z-index: 100; display: flex; align-items: center; justify-content: center;
      padding: 32px; opacity: 0; pointer-events: none; transition: opacity 220ms;
    }
    .modal-backdrop.open { opacity: 1; pointer-events: auto; }
    .modal-wrap { position: relative; max-width: 880px; width: 100%; }
    .modal {
      background: var(--navy);
      border: 1px solid rgba(195,255,138,0.22); border-radius: 8px;
      max-height: 90vh; overflow: auto;
      display: grid; grid-template-columns: 1fr 1fr;
      transform: translateY(12px);
      transition: transform 280ms var(--ease-out);
    }
    .modal-backdrop.open .modal { transform: translateY(0); }
    .modal-gallery { position: relative; display: flex; flex-direction: column; }
    .modal-photo {
      flex: 1; min-height: 320px;
      background-size: cover; background-position: center;
      filter: brightness(0.75);
    }
    .gal-prev, .gal-next {
      position: absolute; top: 50%; transform: translateY(-50%);
      width: 36px; height: 36px; border-radius: 50%;
      background: rgba(8,8,9,0.72); border: 1px solid rgba(195,255,138,0.35);
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; color: var(--green); transition: background 150ms;
    }
    .gal-prev:hover, .gal-next:hover { background: rgba(195,255,138,0.15); }
    .gal-prev { left: 12px; } .gal-next { right: 12px; }
    .gal-prev svg, .gal-next svg { width: 15px; height: 15px; }
    .gal-counter {
      position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%);
      font-family: var(--font); font-size: 11px; letter-spacing: 0.12em;
      color: rgba(255,255,255,0.7);
      background: rgba(8,8,9,0.6); padding: 3px 10px; border-radius: 20px;
    }
    .modal .body { padding: 40px 40px 32px; display: flex; flex-direction: column; gap: 16px; }
    .modal .eyebrow {
      font-family: var(--font); font-size: 12px; font-weight: 500;
      letter-spacing: 0.32em; text-transform: uppercase; color: var(--green);
    }
    .modal h3 {
      font-family: var(--font); font-weight: 500; font-size: 20px;
      letter-spacing: 0.10em; text-transform: uppercase;
      line-height: 1.25; color: #fff;
    }
    .modal .addr {
      font-size: 13px; color: var(--grey);
      display: flex; gap: 6px; align-items: flex-start;
    }
    .modal .addr svg { width: 13px; height: 13px; color: var(--green); margin-top: 2px; }
    .modal .desc { font-size: 14px; line-height: 1.72; color: rgba(255,255,255,0.78); }
    .modal .specs { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .modal .spec {
      padding: 14px; border: 1px solid rgba(195,255,138,0.18);
      border-radius: 6px; background: rgba(8,8,9,0.55);
      display: flex; flex-direction: column; align-items: center; text-align: center;
    }
    .modal .spec .l {
      font-family: var(--font); font-weight: 500; font-size: 10px;
      letter-spacing: 0.22em; text-transform: uppercase; color: var(--grey);
      display: flex; align-items: center; justify-content: center; gap: 5px;
    }
    .modal .spec .l svg { width: 11px; height: 11px; color: var(--green); }
    .modal .spec .v { font-size: 13px; font-weight: 500; color: #fff; margin-top: 6px; }
    .modal .actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 4px; }
    .modal .close {
      position: absolute; top: 14px; right: 14px;
      background: transparent; border: none;
      width: 32px; height: 32px; border-radius: 999px;
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; z-index: 10; color: var(--green);
      transition: background 160ms;
    }
    .modal .close:hover { background: rgba(195,255,138,0.12); }

    /* ═══════════════════════════════════════════════════════════
       FLATPICKR — ITE dark overrides
    ═══════════════════════════════════════════════════════════ */
    .flatpickr-calendar { background: var(--navy) !important; border: 1px solid rgba(195,255,138,0.25) !important; border-radius: 8px !important; box-shadow: 0 16px 48px rgba(0,0,0,0.7) !important; }
    .flatpickr-calendar.arrowTop:before { border-bottom-color: rgba(195,255,138,0.25) !important; }
    .flatpickr-calendar.arrowTop:after  { border-bottom-color: var(--navy) !important; }
    .flatpickr-months { border-bottom: 1px solid rgba(255,255,255,0.08) !important; }
    .flatpickr-month, .flatpickr-current-month, .cur-month, .cur-year { background: transparent !important; color: #fff !important; fill: #fff !important; }
    .flatpickr-monthDropdown-months { border: none !important; background: transparent !important; color: #fff !important; }
    .flatpickr-prev-month svg, .flatpickr-next-month svg { fill: var(--green) !important; }
    .flatpickr-prev-month:hover, .flatpickr-next-month:hover { background: rgba(195,255,138,0.1) !important; }
    .flatpickr-weekday { color: rgba(195,255,138,0.65) !important; background: transparent !important; }
    .flatpickr-days { border-color: rgba(255,255,255,0.06) !important; }
    .flatpickr-day { color: rgba(255,255,255,0.75) !important; border-radius: 6px !important; border-color: transparent !important; }
    .flatpickr-day:hover { background: rgba(195,255,138,0.12) !important; color: #fff !important; }
    .flatpickr-day.today { border-color: rgba(195,255,138,0.5) !important; }
    .flatpickr-day.selected, .flatpickr-day.selected:hover { background: var(--green) !important; border-color: var(--green) !important; color: var(--dark) !important; font-weight: 600 !important; }
    .flatpickr-day.flatpickr-disabled { color: rgba(255,255,255,0.2) !important; }

    /* ═══════════════════════════════════════════════════════════
       RESPONSIVE
    ═══════════════════════════════════════════════════════════ */
    @media (max-width: 1024px) {
      .featured-card { grid-template-columns: 1fr; }
      .featured-card .photo { min-height: 360px; }
      .venues-grid { grid-template-columns: repeat(2, 1fr) !important; }
      .cta-band .inner { grid-template-columns: 1fr; gap: 40px; }
      .modal { grid-template-columns: 1fr; }
      .modal-photo { min-height: 260px; }
      .footer .cols { grid-template-columns: 1fr 1fr; gap: 32px; }
    }
    @media (max-width: 640px) {
      .container { padding: 0 20px !important; }
      .page-header { padding: 80px 0 64px; }
      .page-header .meta { gap: 28px; flex-wrap: wrap; }
      .featured-card .body { padding: 28px 20px; }
      .venues-grid { grid-template-columns: 1fr !important; }
      .cta-band h2 { font-size: 1.4rem; }
      .modal-backdrop { padding: 16px; }
      .modal .body { padding: 24px 20px 20px; }
      .footer .cols { grid-template-columns: 1fr; }
      /* Mobile card: full-bleed overlay */
      .venue-card { position: relative; }
      .venue-card .media { flex: none; width: 100%; aspect-ratio: 4/3; filter: brightness(0.48); }
      .venue-card .info {
        position: absolute; inset: 0; background: none; border-top: none;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        padding: 20px; gap: 0;
      }
      .venue-card .name { text-align: center; font-size: 14px; }
      .venue-card .address,
      .venue-card .row-bottom,
      .venue-card .cap-tag,
      .venue-card .city-tag { display: none; }
      .venue-card .city-tag-mobile {
        display: inline-flex; margin-top: 6px;
        font-family: var(--font); font-size: 8px; font-weight: 500;
        letter-spacing: 0.18em; text-transform: uppercase;
        color: #fff; padding: 3px 8px;
        background: rgba(0,0,0,0.45); backdrop-filter: blur(6px);
        border-radius: 999px; border: 1px solid rgba(195,255,138,0.35);
      }
    }
  </style>
  <div id="root"></div>

  <script>
    // ── Venues data — 49 venues, WordPress photo URLs ──────────
    window.VENUES = [
      // FEATURED
      {
        id: 'grand-america',
        name: 'The Grand America Hotel',
        city: 'Salt Lake City, UT', region: 'slc',
        address: '555 S Main St, Salt Lake City, UT 84111',
        photo: 'https://intheevent.com/wp-content/uploads/2026/05/GrandAmerica1.png',
        style: 'Luxury Ballroom', capacity: '1,200', sqft: '24,000 sq ft', ceiling: "21' clear",
        desc: 'Five-star European-styled property anchoring downtown Salt Lake. The Grand Ballroom seats 1,200 for plated dinners and pairs with seven satellite ballrooms for breakouts, gala receptions, and multi-day conferences. We have rigged, staged, and floraled this room more than fifty times.',
        gallery: [
          'https://intheevent.com/wp-content/uploads/2026/05/GrandAmerica1.png',
          'https://intheevent.com/wp-content/uploads/2026/05/HyattRegency2.jpg',
          'https://intheevent.com/wp-content/uploads/2026/05/UCCU_Arena.png',
          'https://intheevent.com/wp-content/uploads/2026/05/MonsonCenter.jpg',
        ],
        featured: true,
      },
      // SLC — CONVENTION
      { id: 'salt-palace', name: 'Salt Palace Convention Center', city: 'Salt Lake City, UT', region: 'slc', address: '90 SW Temple, Salt Lake City, UT 84101', photo: 'https://intheevent.com/wp-content/uploads/2026/05/SaltPalaceConventionCenter2-scaled.jpg', style: 'Convention Center', capacity: '5,000+', sqft: '515,000 sq ft', ceiling: "30' clear", desc: "Utah's flagship convention space. We program the Salt Palace dozens of times a year — main stage, breakouts, exhibit halls, and outdoor activations on the plaza." },
      // SLC — HOTELS
      { id: 'hyatt-regency', name: 'Hyatt Regency Salt Lake City', city: 'Salt Lake City, UT', region: 'slc', address: '170 S West Temple St, Salt Lake City, UT 84101', photo: 'https://intheevent.com/wp-content/uploads/2026/05/HyattRegency2.jpg', style: 'Convention Hotel', capacity: '2,875', sqft: '60,000 sq ft', ceiling: "22' Regency Ballroom", desc: "Downtown SLC's newest convention hotel, connected to the Salt Palace. The Regency Ballroom spans 23,000 sq ft and pairs with 30 breakout rooms for multi-day conferences and signature galas." },
      { id: 'little-america', name: 'Little America Hotel', city: 'Salt Lake City, UT', region: 'slc', address: '500 South Main St, Salt Lake City, UT 84101', photo: 'https://intheevent.com/wp-content/uploads/2026/05/LittleAmerica1.jpg', style: 'Full-Service Hotel', capacity: '1,200', sqft: '28,000 sq ft', ceiling: "16' ballroom", desc: 'Beloved SLC landmark with a grand ballroom, multiple salons, and garden courtyards. A reliable production partner for corporate dinners, award shows, and holiday galas.' },
      { id: 'hilton-city-center', name: 'Hilton Salt Lake City Center', city: 'Salt Lake City, UT', region: 'slc', address: '255 S West Temple, Salt Lake City, UT 84101', photo: 'https://intheevent.com/wp-content/uploads/2026/05/SLC_Hilton1.jpg', style: 'Convention Hotel', capacity: '1,000', sqft: '25,000 sq ft', ceiling: "20' main hall", desc: 'High-capacity downtown property two blocks from the Salt Palace. Grand Ballroom and multi-room pre-function space ideal for breakout-heavy conferences and awards evenings.' },
      { id: 'le-meridien', name: 'Le Méridien Salt Lake City', city: 'Salt Lake City, UT', region: 'slc', address: '131 S 300 W, Salt Lake City, UT 84101', photo: 'https://intheevent.com/wp-content/uploads/2026/05/LeMeridien1.jpeg', style: 'Boutique Hotel', capacity: '350', sqft: '12,000 sq ft', ceiling: "14' ballroom", desc: "Modern European-inspired hotel in downtown's creative core. Intimate ballroom, art-forward lobby, and rooftop terrace make it a favorite for brand activations and executive dinners." },
      { id: 'marriott-downtown', name: 'Salt Lake Marriott Downtown', city: 'Salt Lake City, UT', region: 'slc', address: '75 South West Temple, Salt Lake City, UT 84101', photo: 'https://intheevent.com/wp-content/uploads/2026/05/MarriottDowntown1.jpeg', style: 'Full-Service Hotel', capacity: '800', sqft: '21,000 sq ft', ceiling: "18' ballroom", desc: 'Downtown anchor with a versatile ballroom and rooftop social spaces. Strong AV infrastructure and proximity to the convention district make it a proven conference hotel.' },
      { id: 'hotel-monaco', name: 'Hotel Monaco Salt Lake City', city: 'Salt Lake City, UT', region: 'slc', address: '15 W 200 S, Salt Lake City, UT 84101', photo: 'https://intheevent.com/wp-content/uploads/2026/05/HotelMonaco2.jpg', style: 'Boutique Hotel', capacity: '250', sqft: '6,000 sq ft', ceiling: "12' ballroom", desc: 'Eclectic Kimpton boutique hotel in a restored 1924 Continental Bank building. Intimate ballrooms and a dramatic lobby set the stage for receptions, brand dinners, and small conferences.' },
      { id: 'radisson-slc', name: 'Radisson Hotel Salt Lake City', city: 'Salt Lake City, UT', region: 'slc', address: '215 S Temple, Salt Lake City, UT 84101', photo: 'https://intheevent.com/wp-content/uploads/2026/05/RadissonHotel.jpeg', style: 'Full-Service Hotel', capacity: '600', sqft: '14,000 sq ft', ceiling: "16' ballroom", desc: 'Full-service hotel steps from Temple Square. Reliable ballroom footprint and central location make it a workhorse venue for corporate meetings, award luncheons, and receptions.' },
      { id: 'asher-adams', name: 'Asher Adams Hotel', city: 'Salt Lake City, UT', region: 'slc', address: '2 400 W, Salt Lake City, UT 84101', photo: 'https://intheevent.com/wp-content/uploads/2026/05/AsherAdams.jpeg', style: 'Autograph Collection Hotel', capacity: '400', sqft: '10,000 sq ft', ceiling: 'Restored train station atrium', desc: 'Historic Union Pacific rail station reborn as a Marriott Autograph Collection hotel. Barrel-vaulted ceilings, original terrazzo floors, and contemporary event spaces create a cinematic backdrop near the airport corridor.' },
      // SLC — ARTS & CULTURE
      { id: 'kingsbury-hall', name: 'Kingsbury Hall', city: 'Salt Lake City, UT', region: 'slc', address: '1395 Presidents Cir, Salt Lake City, UT 84112', photo: 'https://intheevent.com/wp-content/uploads/2026/05/KingsburyHall1.jpg', style: 'Theater', capacity: '1,900', sqft: 'Proscenium house', ceiling: 'Fly tower', desc: "University of Utah's historic proscenium theater. House lighting, fly tower, and orchestra pit make this a natural fit for keynotes, performances, and award shows." },
      { id: 'capitol-theatre', name: 'Capitol Theatre', city: 'Salt Lake City, UT', region: 'slc', address: '50 W 200 South, Salt Lake City, UT 84101', photo: 'https://intheevent.com/wp-content/uploads/2026/05/CapitolTheatre.jpeg', style: 'Historic Theater', capacity: '1,800', sqft: 'Proscenium house', ceiling: 'Fly tower', desc: 'Beautifully restored 1913 theater in the heart of downtown SLC. Grand lobby, ornate proscenium, and full fly tower — ideal for keynotes, award ceremonies, and theatrical galas.' },
      { id: 'rose-wagner', name: 'Rose Wagner Performing Arts Center', city: 'Salt Lake City, UT', region: 'slc', address: '138 West 300 South, Salt Lake City, UT 84101', photo: 'https://intheevent.com/wp-content/uploads/2026/05/RoseWagner.jpg', style: 'Performing Arts Center', capacity: '500', sqft: 'Multi-stage complex', ceiling: 'Black-box grid', desc: 'Three intimate performance spaces in one building — black box, proscenium, and studio. Flexible for intimate brand events, film screenings, and panel-format conferences.' },
      { id: 'hale-centre', name: 'Hale Centre Theatre', city: 'West Valley City, UT', region: 'slc', address: '3333 South Decker Lake Drive, West Valley City, UT 84119', photo: 'https://intheevent.com/wp-content/uploads/2026/05/HaleCenter.jpg', style: 'Theater', capacity: '900', sqft: 'Theater in the round', ceiling: 'Theatrical grid', desc: 'Award-winning theater-in-the-round with state-of-the-art staging. The intimate configuration and superior sightlines make it uniquely suited for immersive brand experiences and staged corporate productions.' },
      { id: 'monson-center', name: 'The Thomas S. Monson Center', city: 'Salt Lake City, UT', region: 'slc', address: '411 E South Temple, Salt Lake City, UT 84111', photo: 'https://intheevent.com/wp-content/uploads/2026/05/MonsonCenter.jpg', style: 'Historic Mansion', capacity: '180', sqft: '6,400 sq ft', ceiling: "14' parlor", desc: 'Restored 1898 mansion with intimate parlors, a grand staircase, and a tented south lawn. Ideal for board dinners, private receptions, and editorial brand activations.' },
      { id: 'natural-history-museum', name: 'Natural History Museum of Utah', city: 'Salt Lake City, UT', region: 'slc', address: '301 Wakara Way, Salt Lake City, UT 84108', photo: 'https://intheevent.com/wp-content/uploads/2026/05/NaturalHistoryMuseum.jpeg', style: 'Museum', capacity: '1,200', sqft: '15,000 sq ft rentable', ceiling: 'Atrium + galleries', desc: 'Award-winning copper-clad museum perched above the valley with panoramic mountain views. Dinosaur halls, canyon galleries, and a dramatic atrium create unforgettable event backdrops.' },
      { id: 'red-butte-garden', name: 'Red Butte Garden', city: 'Salt Lake City, UT', region: 'slc', address: '300 Wakara Way, Salt Lake City, UT 84108', photo: 'https://intheevent.com/wp-content/uploads/2026/05/RedButteGarden.jpg', style: 'Botanical Garden', capacity: '5,000', sqft: '100-acre estate', ceiling: 'Open sky', desc: 'The Wasatch foothills as your backdrop. Terraced gardens, stone pavilion, and an outdoor amphitheater host summer receptions, charity galas, and brand activations under the stars.' },
      // SLC — EVENT VENUES
      { id: 'gallivan-center', name: 'Gallivan Center', city: 'Salt Lake City, UT', region: 'slc', address: '50 E 200 S, Salt Lake City, UT 84111', photo: 'https://intheevent.com/wp-content/uploads/2026/05/GallivanCenter.jpg', style: 'Urban Plaza', capacity: '3,000', sqft: 'Open-air plaza + amphitheater', ceiling: 'Open sky', desc: "Downtown SLC's premier outdoor event plaza, steps from the TRAX stop. Open-air stage, grass lawn, and surrounding hardscape handle concerts, festivals, and corporate outdoor activations." },
      { id: 'the-complex', name: 'The Complex', city: 'Salt Lake City, UT', region: 'slc', address: '536 West 100 South, Salt Lake City, UT 84101', photo: 'https://intheevent.com/wp-content/uploads/2026/05/TheComplex.jpg', style: 'Concert & Event Venue', capacity: '1,500', sqft: '18,000 sq ft', ceiling: "26' clear", desc: "SLC's most flexible large-format event space — a former industrial complex with two main rooms, rooftop bar, and production-ready rigging and power infrastructure." },
      { id: 'union-event-center', name: 'The Union Event Center', city: 'Salt Lake City, UT', region: 'slc', address: '235 North 500 West, Salt Lake City, UT 84116', photo: 'https://intheevent.com/wp-content/uploads/2026/05/UnionEventCenter-scaled.jpg', style: 'Ballroom', capacity: '1,800', sqft: '22,000 sq ft', ceiling: "20' ballroom", desc: 'Elegant ballroom venue north of downtown with a sweeping domed ceiling and hardwood floors. One of the most beautiful dedicated ballroom spaces in the region for galas and corporate dinners.' },
      { id: 'falls-event-center', name: 'The Falls Event Center', city: 'Salt Lake City, UT', region: 'slc', address: '580 South 600 East, Salt Lake City, UT 84102', photo: 'https://intheevent.com/wp-content/uploads/2026/05/FallsEventCenter.jpg', style: 'Ballroom', capacity: '500', sqft: '10,000 sq ft', ceiling: "14' ballroom", desc: 'Turn-key ballroom venue with an elegant décor package, in-house catering coordination, and flexible layout for receptions, galas, and corporate events.' },
      { id: 'viridian-event-center', name: 'Viridian Event Center', city: 'West Jordan, UT', region: 'slc', address: '8030 South 1825 West, West Jordan, UT 84088', photo: 'https://intheevent.com/wp-content/uploads/2026/05/ViridianEventCenter.jpg', style: 'Event Center', capacity: '1,800', sqft: '35,000 sq ft', ceiling: "28' main hall", desc: "South Valley's leading event center with a clear-span main hall, divisible ballroom, and extensive pre-function space. Strong rigging points and power make it a technical production favorite." },
      // UTAH COUNTY
      { id: 'uccu-center', name: 'UCCU Center', city: 'Orem, UT', region: 'utah-county', address: '800 W University Pkwy, Orem, UT 84058', photo: 'https://intheevent.com/wp-content/uploads/2026/05/UCCU_Arena.png', style: 'Arena', capacity: '8,500', sqft: 'Arena floor + concourse', ceiling: "55' grid", desc: "UVU's 8,500-seat arena. Black-out capable for full theatrical galas — we transformed the room for the President's Scholarship Ball with crystal rigging and a 360° runway." },
      { id: 'utah-valley-convention', name: 'Utah Valley Convention Center', city: 'Provo, UT', region: 'utah-county', address: '220 W Center St, Provo, UT 84601', photo: 'https://intheevent.com/wp-content/uploads/2026/05/UtahValleyConventionCenter.jpg', style: 'Convention Center', capacity: '2,000', sqft: '83,578 sq ft', ceiling: "32' exhibit hall", desc: "Utah County's premier convention facility with mountain views. The Grand Ballroom seats 1,000 for galas; combined with a 19,600 sq ft exhibit hall it anchors major regional conferences and trade shows." },
      { id: 'thanksgiving-point', name: 'Thanksgiving Point', city: 'Lehi, UT', region: 'utah-county', address: '3003 Thanksgiving Way, Lehi, UT 84043', photo: 'https://intheevent.com/wp-content/uploads/2026/05/ThanksgivingPoint.jpg', style: 'Event Campus', capacity: '3,000+', sqft: '55-acre grounds', ceiling: 'Pavilion + outdoor', desc: 'Fifty-five acres of manicured gardens, multiple indoor event halls, and a farm-themed venue campus. Handles everything from a 50-person board dinner to a 3,000-guest outdoor festival.' },
      { id: 'scera-theater', name: 'SCERA Theater', city: 'Orem, UT', region: 'utah-county', address: '745 South State Street, Orem, UT 84058', photo: 'https://intheevent.com/wp-content/uploads/2026/05/SCERA_Center.jpg', style: 'Performing Arts Center', capacity: '600', sqft: 'Theater + lobby', ceiling: 'Theatrical grid', desc: "Orem's beloved community arts center with a proscenium theater, lobby, and outdoor shell amphitheater. Versatile for keynotes, award shows, concerts, and cultural galas." },
      // MOUNTAIN — COTTONWOOD CANYONS
      { id: 'snowbird', name: 'Snowbird Resort', city: 'Snowbird, UT', region: 'mountain', address: '9385 Snowbird Center Trail, Snowbird, UT 84092', photo: 'https://intheevent.com/wp-content/uploads/2026/05/Snowbird.jpg', style: 'Mountain Resort', capacity: '1,200', sqft: '80,000 sq ft', ceiling: "16' Cliff Ballroom + aerial tram", desc: 'Year-round mountain resort at the top of Little Cottonwood Canyon. The iconic Cliff Lodge, aerial tram arrival, and alpine meadows create a production canvas unlike anywhere in the Wasatch.' },
      { id: 'snowpine-lodge', name: 'Snowpine Lodge', city: 'Alta, UT', region: 'mountain', address: '10420 Little Cottonwood Rd, Alta, UT 84092', photo: 'https://intheevent.com/wp-content/uploads/2026/05/SnowpineLodge.jpg', style: 'Mountain Lodge', capacity: '200', sqft: '5,000 sq ft', ceiling: 'Vaulted timber', desc: 'Boutique alpine lodge at the base of Alta ski area. Intimate and refined — the perfect setting for executive retreats, incentive dinners, and small destination events.' },
      { id: 'solitude', name: 'Solitude Mountain Resort', city: 'Solitude, UT', region: 'mountain', address: '12000 Big Cottonwood Canyon Rd, Solitude, UT 84121', photo: 'https://intheevent.com/wp-content/uploads/2026/05/Solitude.jpg', style: 'Mountain Resort', capacity: '800', sqft: 'Inn + base lodge', ceiling: 'Vaulted timber', desc: 'Quiet and exclusive Big Cottonwood Canyon resort. The Inn at Solitude and ski-beach terrace offer a genuine mountain-village feel for incentive groups and intimate destination events.' },
      { id: 'snowbasin', name: 'Snowbasin Resort', city: 'Huntsville, UT', region: 'mountain', address: '3925 Snow Basin Rd, Huntsville, UT 84317', photo: 'https://intheevent.com/wp-content/uploads/2026/05/Snowbasin.jpg', style: 'Ski Resort', capacity: '900', sqft: 'Needles Lodge + base', ceiling: 'Open timber', desc: 'Olympic-legacy ski resort. Mid-mountain Needles Lodge unlocks a gondola-arrival reception unlike anything else in the state.' },
      // MOUNTAIN — HEBER / MIDWAY
      { id: 'zermatt', name: 'Zermatt Resort', city: 'Midway, UT', region: 'mountain', address: '784 W Resort Dr, Midway, UT 84049', photo: 'https://intheevent.com/wp-content/uploads/2026/05/Zermatt.jpg', style: 'Mountain Resort', capacity: '1,500', sqft: '65,000 sq ft', ceiling: "18' ballroom", desc: "Swiss-themed resort in Midway's pastoral Heber Valley. Eighteen distinct event spaces totaling 65,000 sq ft — the largest dedicated event facility between SLC and Park City." },
      { id: 'homestead-resort', name: 'Homestead Resort', city: 'Midway, UT', region: 'mountain', address: '700 Homestead Dr, Midway, UT 84049', photo: 'https://intheevent.com/wp-content/uploads/2026/05/Homestead.jpg', style: 'Historic Resort', capacity: '600', sqft: '18,000 sq ft', ceiling: "14' ballroom", desc: "Utah's oldest resort, anchored by a famous hot spring crater. Charming barn venues, garden spaces, and a grand ballroom — ideal for Western-themed galas and corporate retreats." },
      { id: 'soldier-hollow', name: 'Soldier Hollow', city: 'Midway, UT', region: 'mountain', address: '2002 Olympic Drive, Midway, UT 84049', photo: 'https://intheevent.com/wp-content/uploads/2026/05/SoldierHollow.jpg', style: 'Olympic Venue', capacity: '2,000', sqft: 'Lodge + outdoor terrain', ceiling: 'Open timber', desc: 'Olympic legacy biathlon and cross-country venue in Heber Valley. The lodge, meadow, and sweeping Wasatch views make it a stunning backdrop for outdoor festivals and team-building events.' },
      { id: 'high-west-distillery', name: 'High West Distillery', city: 'Wanship, UT', region: 'mountain', address: '27649 Old Lincoln Hwy, Wanship, UT 84017', photo: 'https://intheevent.com/wp-content/uploads/2026/05/HighWest.jpg', style: 'Distillery & Event Space', capacity: '250', sqft: '8,000 sq ft', ceiling: 'Barrel-house loft', desc: "Award-winning craft distillery perched above Parley's Canyon. Reclaimed wood, copper stills, and canyon views set a premium backdrop for brand dinners and incentive events." },
      { id: 'sundance', name: 'Sundance Mountain Resort', city: 'Sundance, UT', region: 'mountain', address: 'N Fork Rd 8841, Sundance, UT 84604', photo: 'https://intheevent.com/wp-content/uploads/2026/05/Sundance.jpeg', style: 'Mountain Resort', capacity: '450', sqft: 'Tree Room + decks', ceiling: 'Open timber', desc: "Artist-driven retreat at the base of Mount Timpanogos. Multiple intimate venues spread across a creek-side campus — a perennial favorite for brand dinners and incentive events." },
      // PARK CITY / DEER VALLEY
      { id: 'grand-hyatt-deer-valley', name: 'Grand Hyatt Deer Valley', city: 'Park City, UT', region: 'mountain', address: '1702 Glencoe Mountain Way, Park City, UT 84060', photo: 'https://intheevent.com/wp-content/uploads/2026/05/GrandHyattDeerValley.jpg', style: 'Luxury Mountain Resort', capacity: '800', sqft: '30,000 sq ft', ceiling: "24' great room", desc: "Brand-new slopeside resort at Deer Valley's East Village. Expansive ballrooms, rooftop terraces, and true ski-in/ski-out access — setting a new standard for mountain event hospitality." },
      { id: 'deer-valley-resort', name: 'Deer Valley Resort', city: 'Park City, UT', region: 'mountain', address: '2250 Deer Valley Drive South, Park City, UT 84060', photo: 'https://intheevent.com/wp-content/uploads/2026/05/DeerValleyResort.jpg', style: 'Ski Resort', capacity: '1,200', sqft: 'Mid-mountain + Snow Park Lodge', ceiling: 'Timber great room', desc: 'The crown jewel of Utah skiing, rated #1 resort in North America. Multiple mid-mountain and base lodges host everything from chairlift-arrival receptions to gala dinners with Wasatch panoramas.' },
      { id: 'montage-deer-valley', name: 'Montage Deer Valley', city: 'Park City, UT', region: 'mountain', address: '9100 Marsac Ave, Park City, UT 84060', photo: 'https://intheevent.com/wp-content/uploads/2026/05/MontageDeerValley.jpeg', style: 'Mountain Resort', capacity: '650', sqft: '22,000 sq ft', ceiling: "20'", desc: 'Empire Pass mountain resort with the largest ballroom in Park City. We rig and floral the Empire Ballroom for galas and conferences nationally.' },
      { id: 'st-regis-deer-valley', name: 'St. Regis Deer Valley', city: 'Park City, UT', region: 'mountain', address: '2300 Deer Valley Dr E, Park City, UT 84060', photo: 'https://intheevent.com/wp-content/uploads/2026/05/StRegisDeerValley.jpg', style: 'Mountain Resort', capacity: '400', sqft: '14,000 sq ft indoor + decks', ceiling: "18' great room", desc: "Slope-side luxury with a vaulted great room, multiple terraces, and unforgettable winter views. We've produced executive retreats and weddings here through every season." },
      { id: 'pendry-park-city', name: 'Pendry Park City', city: 'Park City, UT', region: 'mountain', address: '2417 W High Mountain Rd, Park City, UT 84098', photo: 'https://intheevent.com/wp-content/uploads/2026/05/PendryParkCity.jpg', style: 'Luxury Resort', capacity: '500', sqft: '22,000 sq ft', ceiling: "20' ballroom", desc: "Canyons-slope luxury — the newest premier property in Park City. The Canyons Ballroom, rooftop deck, and slope-side terrace deliver a modern resort experience with world-class production access." },
      { id: 'canyons-village', name: 'Canyons Village at PCMR', city: 'Park City, UT', region: 'mountain', address: '4000 Canyons Resort Dr, Park City, UT 84098', photo: 'https://intheevent.com/wp-content/uploads/2026/05/CanyonsVillage.jpeg', style: 'Resort Village', capacity: '2,500', sqft: 'Village + base lodges', ceiling: 'Open plaza + lodges', desc: 'A full resort village at the base of Park City Mountain. Gondola-arrival events, outdoor plazas, and multiple lodge venues make this the hub for large-scale mountain gatherings.' },
      { id: 'waldorf-park-city', name: 'Waldorf Astoria Park City', city: 'Park City, UT', region: 'mountain', address: '2100 W Frostwood Blvd, Park City, UT 84098', photo: 'https://intheevent.com/wp-content/uploads/2026/05/WaldorfParkCity.jpeg', style: 'Mountain Resort', capacity: '300', sqft: '8,200 sq ft', ceiling: "16'", desc: 'Canyons-side property at the base of the gondola. Polished ballroom, exterior plazas, and a private lounge — strong for incentive trips and brand summits.' },
      { id: 'stein-eriksen', name: 'Stein Eriksen Lodge', city: 'Park City, UT', region: 'mountain', address: '7700 Stein Way, Park City, UT 84060', photo: 'https://intheevent.com/wp-content/uploads/2026/05/SteinEriksen-scaled.jpg', style: 'Mountain Lodge', capacity: '350', sqft: '10,500 sq ft', ceiling: 'Vaulted timber', desc: 'Norwegian-style mountain lodge with timber-framed ballrooms and Silver Lake views. A perennial favorite for incentive groups and intimate galas.' },
      { id: 'hotel-park-city', name: 'Hotel Park City', city: 'Park City, UT', region: 'mountain', address: '2001 Park Ave, Park City, UT 84060', photo: 'https://intheevent.com/wp-content/uploads/2026/05/HotelParkCity.jpg', style: 'Luxury Hotel', capacity: '350', sqft: '10,000 sq ft', ceiling: "16' ballroom", desc: 'All-suite luxury hotel minutes from Main Street and the ski slopes. Intimate ballroom, wraparound deck, and exceptional service make it a top choice for executive retreats and incentive groups.' },
      { id: 'goldener-hirsch', name: 'Goldener Hirsch', city: 'Park City, UT', region: 'mountain', address: '7520 Royal St, Park City, UT 84060', photo: 'https://intheevent.com/wp-content/uploads/2026/05/GoldenerHirsch.jpg', style: 'Alpine Inn', capacity: '120', sqft: '3,000 sq ft', ceiling: 'Intimate timber', desc: 'Intimate Austrian-style inn nestled in Silver Lake Village at Deer Valley. Fireside dining rooms and a terrace overlooking the slopes create an exclusive setting for small destination events.' },
      { id: 'kimball-art-center', name: 'Kimball Art Center', city: 'Park City, UT', region: 'mountain', address: '1401 Kearns Blvd, Park City, UT 84060', photo: 'https://intheevent.com/wp-content/uploads/2026/05/KimballArtCenter.jpeg', style: 'Arts Venue', capacity: '400', sqft: '8,000 sq ft', ceiling: 'Industrial loft', desc: 'Contemporary arts center in the heart of Park City. Gallery halls, outdoor sculpture garden, and a unique industrial loft aesthetic — a favorite for Sundance Film Festival activations and brand events.' },
      // JACKSON HOLE
      { id: 'four-seasons-jackson', name: 'Four Seasons Jackson Hole', city: 'Jackson, WY', region: 'jackson', address: '7680 Granite Loop Rd, Teton Village, WY 83025', photo: 'https://intheevent.com/wp-content/uploads/2026/05/JacksonFourSeasons.jpg', style: 'Mountain Resort', capacity: '500', sqft: '12,000 sq ft', ceiling: "22' great room", desc: 'Teton Village luxury, walking distance to the tram. We travel a full crew for incentive groups, brand summits, and destination events.' },
      { id: 'amangani', name: 'Amangani', city: 'Jackson, WY', region: 'jackson', address: '5000 N Spring Gulch Rd, Jackson, WY 83001', photo: 'https://intheevent.com/wp-content/uploads/2026/05/Amangani.jpg', style: 'Resort', capacity: '180', sqft: 'Indoor + butte terrace', ceiling: 'Stone + glass', desc: 'Sandstone resort perched on a butte over Jackson Hole. Intimate scale, cinematic horizon — perfect for executive retreats and small destination events.' },
      { id: 'jackson-hole-golf', name: 'Jackson Hole Golf & Tennis Club', city: 'Jackson, WY', region: 'jackson', address: '5000 N Spring Gulch Rd, Jackson, WY 83001', photo: 'https://intheevent.com/wp-content/uploads/2026/05/JacksonHoleGolfAndTennisClub.gif.jpeg', style: 'Golf Club & Event Venue', capacity: '300', sqft: '8,000 sq ft', ceiling: "14' clubhouse", desc: 'Iconic Robert Trent Jones Jr. course with a stunning clubhouse overlooking the Tetons. Outdoor pavilion, terrace dining, and mountain panoramas for outdoor receptions and incentive groups.' },
    ];
  </script>

  <script type="text/babel">
    // ═══════════════════════════════════════════════════════════
    // Primitives
    // ═══════════════════════════════════════════════════════════
    const Button = ({ kind = 'filled', children, onClick, href, target, rel, style, disabled }) => {
      const Tag = href ? 'a' : 'button';
      return (
        <Tag className={`btn ${kind}`} onClick={onClick} href={href}
             target={target} rel={rel} style={style} disabled={disabled}>
          {children}
        </Tag>
      );
    };

    const resolvePhoto = (p) => p && p.startsWith('http') ? p : `assets/photos/${p}`;

    // ═══════════════════════════════════════════════════════════
    // Nav
    // ═══════════════════════════════════════════════════════════
    const NAV_ITEMS = [
      ['services','Services'],['work','Work'],['venues','Venues'],
      ['rentals','Rentals'],['about','About'],['contact','Contact'],
    ];
    const Nav = () => (
      <nav className="nav">
        <div className="brand">
          <img src="https://intheevent.com/wp-content/uploads/2024/05/ite_nav_logo.png" alt="In The Event" />
        </div>
        <div className="links">
          {NAV_ITEMS.map(([key, label]) => (
            <a key={key} className={key === 'venues' ? 'active' : ''}>{label}</a>
          ))}
        </div>
        <Button kind="filled">Start a Project</Button>
      </nav>
    );

    // ═══════════════════════════════════════════════════════════
    // Footer
    // ═══════════════════════════════════════════════════════════
    const Footer = () => (
      <footer className="footer">
        <div className="container">
          <div className="cols">
            <div className="brand-block">
              <img src="https://intheevent.com/wp-content/uploads/2024/05/ite_logo_white.png" alt="In The Event"
                   style={{ filter: 'invert(1) brightness(1.15)', height: 120 }} />
              <p className="lead">Bridging creative design and technical execution, from Salt Lake City to events nationwide.</p>
            </div>
            <div>
              <h4>Services</h4>
              <ul>
                <li><a>Design</a></li><li><a>Production</a></li><li><a>Florals</a></li>
                <li><a>Audio &amp; Video</a></li><li><a>Rentals</a></li>
              </ul>
            </div>
            <div>
              <h4>Company</h4>
              <ul>
                <li><a>About</a></li><li><a>Work</a></li>
                <li><a>Venues</a></li><li><a>Contact</a></li>
              </ul>
            </div>
            <div>
              <h4>Contact</h4>
              <ul>
                <li><a href="mailto:info@intheevent.com">info@intheevent.com</a></li>
                <li><a href="tel:18018861144">801.886.1144</a></li>
                <li>3008 S 300 W</li>
                <li>Salt Lake City, UT 84115</li>
              </ul>
            </div>
          </div>
          <div className="fine">
            <div>© 2026 In The Event, LLC · All rights reserved</div>
            <div>Est. 2007 · ITE · Business Jaunt · Mannequin Rental Co.</div>
          </div>
        </div>
      </footer>
    );

    // ═══════════════════════════════════════════════════════════
    // Page Header
    // ═══════════════════════════════════════════════════════════
    const VenuesHeader = () => {
      const overlayRef = React.useRef(null);
      React.useEffect(() => {
        const section = overlayRef.current && overlayRef.current.closest('.page-header');
        if (!section) return;
        let rect = section.getBoundingClientRect();
        const refreshRect = () => { rect = section.getBoundingClientRect(); };
        window.addEventListener('resize', refreshRect, { passive: true });
        const onMove = (e) => {
          const x = e.clientX - rect.left, y = e.clientY - rect.top;
          overlayRef.current.style.background =
            `radial-gradient(600px circle at ${x}px ${y}px, rgba(160,200,255,0.10), transparent 70%)`;
        };
        const onLeave = () => { overlayRef.current.style.background = 'none'; };
        section.addEventListener('mousemove', onMove, { passive: true });
        section.addEventListener('mouseleave', onLeave);
        return () => {
          section.removeEventListener('mousemove', onMove);
          section.removeEventListener('mouseleave', onLeave);
          window.removeEventListener('resize', refreshRect);
        };
      }, []);
      return (
        <header className="page-header reveal">
          <div ref={overlayRef} className="spotlight-overlay" />
          <div className="container">
            <div className="eyebrow">Venues</div>
            <h1>Venues we <span className="accent">know inside out</span>.</h1>
            <p className="lead">
              We have rigged, staged, and floraled every room on this list — most of them many times over.
              Pick the venue you love, and we'll bring the production playbook with us.
            </p>
            <div className="meta">
              <div className="item"><div className="l">Curated</div><div className="v">49 Venues</div></div>
              <div className="item"><div className="l">Coverage</div><div className="v">Utah · Wyoming · Nationwide</div></div>
              <div className="item"><div className="l">Established</div><div className="v">2007 · ITE</div></div>
            </div>
          </div>
        </header>
      );
    };

    // ═══════════════════════════════════════════════════════════
    // Filter Bar
    // ═══════════════════════════════════════════════════════════
    const FILTERS = [
      { id: 'all',         label: 'All Venues' },
      { id: 'slc',         label: 'Salt Lake City' },
      { id: 'utah-county', label: 'Utah County' },
      { id: 'mountain',    label: 'Mountain' },
      { id: 'jackson',     label: 'Jackson Hole' },
    ];

    const FilterBar = ({ active, setActive, total }) => {
      const counts = React.useMemo(() => {
        const map = { all: window.VENUES.length };
        window.VENUES.forEach((v) => { map[v.region] = (map[v.region] || 0) + 1; });
        return map;
      }, []);
      const barRef = React.useRef(null);
      React.useEffect(() => {
        const venuesSection = document.querySelector('.venues-section');
        if (!venuesSection || !barRef.current) return;
        const update = () => {
          const top = venuesSection.getBoundingClientRect().top;
          barRef.current?.classList.toggle('scrolled', top <= 64);
        };
        update();
        window.addEventListener('scroll', update, { passive: true });
        return () => window.removeEventListener('scroll', update);
      }, []);
      return (
        <div className="filter-bar" ref={barRef}>
          <div className="container">
            <div className="row">
              <div className="chips">
                {FILTERS.map((f) => (
                  <button key={f.id}
                    className={`chip ${active === f.id ? 'active' : ''}`}
                    onClick={() => setActive(f.id)}>
                    {f.label}
                    <span className="ct">{counts[f.id] || 0}</span>
                  </button>
                ))}
              </div>
              <div className="count">
                Showing <strong>{total}</strong> of <strong>{window.VENUES.length}</strong>
              </div>
            </div>
          </div>
        </div>
      );
    };

    // ═══════════════════════════════════════════════════════════
    // Featured Venue
    // ═══════════════════════════════════════════════════════════
    const FeaturedVenue = ({ v, onView }) => {
      const [activeIdx, setActiveIdx] = React.useState(0);
      const gallery = v.gallery || [v.photo];
      const photo = resolvePhoto(gallery[activeIdx]);
      return (
        <section className="featured-section">
          <div className="container">
            <div className="featured-card reveal">
              <div key={photo} className="photo-bg" style={{ backgroundImage: `url(${photo})` }} />
              <div className="photo">
                <div className="overlay" />
                <div className="badge">
                  <i data-lucide="star" />
                  Featured Partner
                </div>
                <div className="thumbs">
                  {gallery.map((g, i) => (
                    <div key={i}
                      className={`thumb ${i === activeIdx ? 'active' : ''}`}
                      style={{ backgroundImage: `url(${resolvePhoto(g)})` }}
                      onClick={() => setActiveIdx(i)} />
                  ))}
                </div>
              </div>
              <div className="body">
                <div className="eyebrow">Venue Spotlight</div>
                <div className="heading-hairline" />
                <h2>{v.name}</h2>
                <div className="desc">{v.desc}</div>
                <div className="specs">
                  <div className="spec">
                    <div className="l"><i data-lucide="map-pin" />Location</div>
                    <div className="v">{v.city}</div>
                  </div>
                  <div className="spec">
                    <div className="l"><i data-lucide="users" />Capacity</div>
                    <div className="v">{v.capacity} guests</div>
                  </div>
                  <div className="spec">
                    <div className="l"><i data-lucide="ruler" />Footprint</div>
                    <div className="v">{v.sqft}</div>
                  </div>
                  <div className="spec">
                    <div className="l"><i data-lucide="move-vertical" />Ceiling</div>
                    <div className="v">{v.ceiling}</div>
                  </div>
                </div>
                <div className="actions">
                  <Button kind="filled" onClick={() => onView(v)}>View Venue</Button>
                  <Button kind="outline" href="https://intheevent.com/contact-us/" target="_blank" rel="noopener noreferrer">Book a Walk-Through →</Button>
                </div>
              </div>
            </div>
          </div>
        </section>
      );
    };

    // ═══════════════════════════════════════════════════════════
    // Venue Card
    // ═══════════════════════════════════════════════════════════
    const VenueCard = ({ v, onClick }) => (
      <div className="venue-card reveal"
        onClick={onClick}
        onKeyDown={(e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); onClick(); } }}
        tabIndex={0} role="button" aria-label={`View ${v.name}`}>
        <div className="photo-bg" style={{ backgroundImage: `url(${resolvePhoto(v.photo)})` }} />
        <div className="media">
          <div className="city-tag">{v.city}</div>
          <div className="cap-tag">
            <i data-lucide="users" />
            Up to {v.capacity}
          </div>
        </div>
        <div className="info">
          <h3 className="name">{v.name}</h3>
          <div className="city-tag-mobile">{v.city}</div>
          <div className="address">
            <i data-lucide="map-pin" />
            <span>{v.address}</span>
          </div>
          <div className="row-bottom">
            <span className="style-pill">{v.style}</span>
            <span className="view">View Venue →</span>
          </div>
        </div>
      </div>
    );

    // ═══════════════════════════════════════════════════════════
    // Venues Grid
    // ═══════════════════════════════════════════════════════════
    const VenuesGrid = ({ list, onSelect }) => (
      <section className="venues-section">
        <div className="container">
          <div className="section-head">
            <div>
              <div className="eyebrow">The Full List</div>
              <div className="heading-hairline" style={{ margin: '8px 0 14px' }} />
              <h2>All Venues</h2>
            </div>
            <p>Sorted by frequency of recent ITE production work. Don't see your venue? We work nationwide.</p>
          </div>
          <div className="venues-grid">
            {list.map((v) => (
              <VenueCard key={v.id} v={v} onClick={() => onSelect(v)} />
            ))}
          </div>
        </div>
      </section>
    );

    // ═══════════════════════════════════════════════════════════
    // CTA Band — with working Flatpickr date picker
    // ═══════════════════════════════════════════════════════════
    const VenueCTA = ({ prefillVenue = '' }) => {
      const FLUENT_FORM_ID = '17';
      const [nameVal,  setNameVal]  = React.useState('');
      const [emailVal, setEmailVal] = React.useState('');
      const [guestVal, setGuestVal] = React.useState('');
      const [venueVal, setVenueVal] = React.useState('');
      const [submitting, setSubmitting] = React.useState(false);
      const [submitted,  setSubmitted]  = React.useState(false);
      const [submitError, setSubmitError] = React.useState('');
      const dateInputRef = React.useRef(null);
      const overlayRef   = React.useRef(null);

      React.useEffect(() => { if (prefillVenue) setVenueVal(prefillVenue); }, [prefillVenue]);

      React.useEffect(() => {
        if (!dateInputRef.current || !window.flatpickr) return;
        const fp = window.flatpickr(dateInputRef.current, {
          dateFormat: 'F j, Y', minDate: 'today', disableMobile: true,
        });
        return () => fp.destroy();
      }, []);

      React.useEffect(() => {
        const section = overlayRef.current && overlayRef.current.closest('.cta-band');
        if (!section) return;
        let rect = section.getBoundingClientRect();
        const refreshRect = () => { rect = section.getBoundingClientRect(); };
        window.addEventListener('resize', refreshRect, { passive: true });
        const onMove = (e) => {
          const x = e.clientX - rect.left, y = e.clientY - rect.top;
          overlayRef.current.style.background =
            `radial-gradient(600px circle at ${x}px ${y}px, rgba(160,200,255,0.10), transparent 70%)`;
        };
        const onLeave = () => { overlayRef.current.style.background = 'none'; };
        section.addEventListener('mousemove', onMove, { passive: true });
        section.addEventListener('mouseleave', onLeave);
        return () => {
          section.removeEventListener('mousemove', onMove);
          section.removeEventListener('mouseleave', onLeave);
          window.removeEventListener('resize', refreshRect);
        };
      }, []);

      const handleSubmit = async () => {
        if (!nameVal.trim() || !emailVal.trim()) {
          setSubmitError('Please enter your name and email so we can follow up.');
          return;
        }
        setSubmitError(''); setSubmitting(true);
        try {
          const body = new FormData();
          body.append('action', 'fluentform_submit');
          body.append('form_id', FLUENT_FORM_ID);
          body.append('_fluentform_' + FLUENT_FORM_ID + '_fluentformpro_submit', window.ITE_FF_NONCE || '');
          body.append('name',  nameVal.trim());
          body.append('email', emailVal.trim());
          body.append('event_date', dateInputRef.current?.value || '');
          body.append('guest_count', guestVal.trim());
          body.append('venue', venueVal.trim());
          const res  = await fetch('https://intheevent.com/wp-admin/admin-ajax.php', { method: 'POST', body });
          const json = await res.json();
          if (json.success) { setSubmitted(true); }
          else { setSubmitError(json.message || 'Something went wrong. Please try again.'); }
        } catch {
          setSubmitError('Could not reach the server. Please try again.');
        } finally { setSubmitting(false); }
      };

      return (
        <section className="cta-band reveal">
          <div ref={overlayRef} className="spotlight-overlay" />
          <div className="container">
            <div className="inner">
              <div>
                <div className="eyebrow">Don't See Your Venue?</div>
                <div className="heading-hairline" style={{ margin: '0 0 20px' }} />
                <h2>We travel the production with you.</h2>
                <p>
                  We've built shows in 30+ states. Tell us where, and our team will bring the rig,
                  the rentals, and the crew. Out-of-state or out-of-country logistics flow through our
                  sister company.
                </p>
                <div className="actions">
                  <Button kind="outline" href="https://intheevent.com/wishlist/" target="_blank" rel="noopener noreferrer">Start a Project</Button>
                  <Button kind="outline" href="https://intheevent.com/contact-us/" target="_blank" rel="noopener noreferrer">Talk to an Account Lead →</Button>
                </div>
                <div className="sister">
                  International? <a href="https://businessjaunt.com" target="_blank" rel="noopener noreferrer">Visit Business Jaunt →</a>
                </div>
              </div>
              <div className="cta-inquiry-card">
                <div className="eyebrow">Quick Inquiry</div>
                <h3>Tell us about your event</h3>
                {submitted ? (
                  <div className="cta-success">
                    <i data-lucide="check-circle" />
                    <p>We'll be in touch!</p>
                    <span>Our team will reach out within one business day.</span>
                  </div>
                ) : (
                  <div className="fields">
                    <label className="form-field">
                      <i data-lucide="user" />
                      <input className="cta-input" placeholder="Your name" value={nameVal} onChange={(e) => setNameVal(e.target.value)} />
                    </label>
                    <label className="form-field">
                      <i data-lucide="mail" />
                      <input className="cta-input" type="email" placeholder="Email address" value={emailVal} onChange={(e) => setEmailVal(e.target.value)} />
                    </label>
                    <label className="form-field">
                      <i data-lucide="calendar" />
                      <input className="cta-input" placeholder="Event date" ref={dateInputRef} readOnly />
                    </label>
                    <label className="form-field">
                      <i data-lucide="users" />
                      <input className="cta-input" placeholder="Estimated guest count" value={guestVal} onChange={(e) => setGuestVal(e.target.value)} />
                    </label>
                    <label className="form-field">
                      <i data-lucide="map-pin" />
                      <input className="cta-input" placeholder="Preferred venue or city" value={venueVal} onChange={(e) => setVenueVal(e.target.value)} />
                    </label>
                    {submitError && <p className="cta-error">{submitError}</p>}
                    <Button kind="filled" style={{ marginTop: 4 }} onClick={handleSubmit} disabled={submitting}>
                      {submitting ? 'Sending…' : 'Request a Quote'}
                    </Button>
                  </div>
                )}
              </div>
            </div>
          </div>
        </section>
      );
    };

    // ═══════════════════════════════════════════════════════════
    // Venue Modal — with gallery nav
    // ═══════════════════════════════════════════════════════════
    const VenueModal = ({ v, onClose, onPlanHere }) => {
      const [photoIdx, setPhotoIdx] = React.useState(0);

      React.useEffect(() => { setPhotoIdx(0); }, [v]);
      React.useEffect(() => {
        const onKey = (e) => {
          if (!v) return;
          if (e.key === 'Escape') onClose();
          if (e.key === 'ArrowLeft')  setPhotoIdx(i => (i - 1 + gallery.length) % gallery.length);
          if (e.key === 'ArrowRight') setPhotoIdx(i => (i + 1) % gallery.length);
        };
        window.addEventListener('keydown', onKey);
        return () => window.removeEventListener('keydown', onKey);
      }, [v]);

      const gallery = v
        ? (v.gallery ? v.gallery.map(resolvePhoto) : [resolvePhoto(v.photo)])
        : [];

      return (
        <div className={`modal-backdrop ${v ? 'open' : ''}`}
          onClick={onClose} role="dialog" aria-modal="true"
          aria-label={v ? v.name : 'Venue detail'}>
          <div className="modal-wrap" onClick={(e) => e.stopPropagation()}>
            {v && (
              <div className="modal">
                <button className="close" onClick={onClose} aria-label="Close">
                  <i data-lucide="x" style={{ width: 16, height: 16 }} />
                </button>
                <div className="modal-gallery">
                  <div className="modal-photo" style={{ backgroundImage: `url(${gallery[photoIdx]})` }} />
                  {gallery.length > 1 && (
                    <>
                      <button className="gal-prev" onClick={() => setPhotoIdx(i => (i - 1 + gallery.length) % gallery.length)}>
                        <i data-lucide="chevron-left" />
                      </button>
                      <button className="gal-next" onClick={() => setPhotoIdx(i => (i + 1) % gallery.length)}>
                        <i data-lucide="chevron-right" />
                      </button>
                      <div className="gal-counter">{photoIdx + 1} / {gallery.length}</div>
                    </>
                  )}
                </div>
                <div className="body">
                  <div className="eyebrow">{v.style}</div>
                  <h3>{v.name}</h3>
                  <div className="addr">
                    <i data-lucide="map-pin" />
                    <span>{v.address}</span>
                  </div>
                  <p className="desc">{v.desc}</p>
                  <div className="specs">
                    <div className="spec">
                      <div className="l"><i data-lucide="users" />Capacity</div>
                      <div className="v">Up to {v.capacity} guests</div>
                    </div>
                    <div className="spec">
                      <div className="l"><i data-lucide="ruler" />Footprint</div>
                      <div className="v">{v.sqft}</div>
                    </div>
                    <div className="spec">
                      <div className="l"><i data-lucide="move-vertical" />Ceiling</div>
                      <div className="v">{v.ceiling}</div>
                    </div>
                    <div className="spec">
                      <div className="l"><i data-lucide="map-pin" />Region</div>
                      <div className="v">{v.city}</div>
                    </div>
                  </div>
                  <div className="actions">
                    <Button kind="filled" onClick={() => {
                      onPlanHere(v.name);
                      onClose();
                      setTimeout(() => {
                        document.querySelector('.cta-band')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                      }, 280);
                    }}>Plan an Event Here</Button>
                    <Button kind="outline"
                      href={`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(v.address)}`}
                      target="_blank" rel="noopener noreferrer">
                      Get Directions →
                    </Button>
                  </div>
                </div>
              </div>
            )}
          </div>
        </div>
      );
    };

    // ═══════════════════════════════════════════════════════════
    // Tweaks Panel (dev tool — remove before launch)
    // ═══════════════════════════════════════════════════════════
    const TWEAKS_STYLE = `
      .twk-panel{position:fixed;right:16px;bottom:16px;z-index:2147483646;width:260px;max-height:calc(100vh - 32px);display:flex;flex-direction:column;background:rgba(250,249,247,.82);color:#29261b;-webkit-backdrop-filter:blur(24px) saturate(160%);backdrop-filter:blur(24px) saturate(160%);border:.5px solid rgba(255,255,255,.6);border-radius:14px;box-shadow:0 1px 0 rgba(255,255,255,.5) inset,0 12px 40px rgba(0,0,0,.18);font:11.5px/1.4 ui-sans-serif,system-ui,-apple-system,sans-serif;overflow:hidden}
      .twk-hd{display:flex;align-items:center;justify-content:space-between;padding:10px 8px 10px 14px;cursor:move;user-select:none}
      .twk-hd b{font-size:12px;font-weight:600}
      .twk-x{appearance:none;border:0;background:transparent;color:rgba(41,38,27,.55);width:22px;height:22px;border-radius:6px;cursor:default;font-size:13px;line-height:1}
      .twk-x:hover{background:rgba(0,0,0,.06);color:#29261b}
      .twk-body{padding:2px 14px 14px;display:flex;flex-direction:column;gap:10px;overflow-y:auto}
      .twk-sect{font-size:10px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:rgba(41,38,27,.45);padding:10px 0 0}
      .twk-row{display:flex;flex-direction:column;gap:5px}
      .twk-row-h{flex-direction:row;align-items:center;justify-content:space-between;gap:10px}
      .twk-lbl{display:flex;justify-content:space-between;color:rgba(41,38,27,.72)}
      .twk-lbl>span:first-child{font-weight:500}
      .twk-toggle{position:relative;width:32px;height:18px;border:0;border-radius:999px;background:rgba(0,0,0,.15);transition:background .15s;cursor:default;padding:0}
      .twk-toggle[data-on="1"]{background:#34c759}
      .twk-toggle i{position:absolute;top:2px;left:2px;width:14px;height:14px;border-radius:50%;background:#fff;box-shadow:0 1px 2px rgba(0,0,0,.25);transition:transform .15s}
      .twk-toggle[data-on="1"] i{transform:translateX(14px)}
      .twk-seg{position:relative;display:flex;padding:2px;border-radius:8px;background:rgba(0,0,0,.06);user-select:none}
      .twk-seg-thumb{position:absolute;top:2px;bottom:2px;border-radius:6px;background:rgba(255,255,255,.9);box-shadow:0 1px 2px rgba(0,0,0,.12);transition:left .15s,width .15s}
      .twk-seg button{appearance:none;position:relative;z-index:1;flex:1;border:0;background:transparent;color:inherit;font:inherit;font-weight:500;min-height:22px;border-radius:6px;cursor:default;padding:4px 6px}
    `;

    function TweaksPanel({ title = 'Tweaks', children }) {
      const [open, setOpen] = React.useState(false);
      const dragRef = React.useRef(null);
      const offsetRef = React.useRef({ x: 16, y: 16 });
      const PAD = 16;
      const clamp = React.useCallback(() => {
        const el = dragRef.current; if (!el) return;
        const maxR = Math.max(PAD, window.innerWidth - el.offsetWidth - PAD);
        const maxB = Math.max(PAD, window.innerHeight - el.offsetHeight - PAD);
        offsetRef.current = {
          x: Math.min(maxR, Math.max(PAD, offsetRef.current.x)),
          y: Math.min(maxB, Math.max(PAD, offsetRef.current.y)),
        };
        el.style.right  = offsetRef.current.x + 'px';
        el.style.bottom = offsetRef.current.y + 'px';
      }, []);
      React.useEffect(() => {
        if (!open) return;
        clamp();
        const ro = typeof ResizeObserver !== 'undefined' ? new ResizeObserver(clamp) : null;
        if (ro) ro.observe(document.documentElement);
        else window.addEventListener('resize', clamp);
        return () => { if (ro) ro.disconnect(); else window.removeEventListener('resize', clamp); };
      }, [open, clamp]);
      React.useEffect(() => {
        const onMsg = (e) => {
          const t = e?.data?.type;
          if (t === '__activate_edit_mode')   setOpen(true);
          if (t === '__deactivate_edit_mode') setOpen(false);
        };
        window.addEventListener('message', onMsg);
        window.parent.postMessage({ type: '__edit_mode_available' }, '*');
        return () => window.removeEventListener('message', onMsg);
      }, []);
      const onDragStart = (e) => {
        const el = dragRef.current; if (!el) return;
        const r = el.getBoundingClientRect();
        const sx = e.clientX, sy = e.clientY;
        const sr = window.innerWidth - r.right, sb = window.innerHeight - r.bottom;
        const move = (ev) => {
          offsetRef.current = { x: sr - (ev.clientX - sx), y: sb - (ev.clientY - sy) };
          clamp();
        };
        const up = () => { window.removeEventListener('mousemove', move); window.removeEventListener('mouseup', up); };
        window.addEventListener('mousemove', move);
        window.addEventListener('mouseup', up);
      };
      if (!open) return null;
      return (
        <>
          <style>{TWEAKS_STYLE}</style>
          <div ref={dragRef} className="twk-panel" style={{ right: offsetRef.current.x, bottom: offsetRef.current.y }}>
            <div className="twk-hd" onMouseDown={onDragStart}>
              <b>{title}</b>
              <button className="twk-x" onMouseDown={(e) => e.stopPropagation()}
                onClick={() => { setOpen(false); window.parent.postMessage({ type: '__edit_mode_dismissed' }, '*'); }}>✕</button>
            </div>
            <div className="twk-body">{children}</div>
          </div>
        </>
      );
    }

    function TweakSection({ label, children }) {
      return <><div className="twk-sect">{label}</div>{children}</>;
    }
    function TweakToggle({ label, value, onChange }) {
      return (
        <div className="twk-row twk-row-h">
          <div className="twk-lbl"><span>{label}</span></div>
          <button type="button" className="twk-toggle" data-on={value ? '1' : '0'}
            role="switch" aria-checked={!!value} onClick={() => onChange(!value)}><i /></button>
        </div>
      );
    }
    function TweakRadio({ label, value, options, onChange }) {
      const opts = options.map((o) => typeof o === 'object' ? o : { value: o, label: o });
      const idx  = Math.max(0, opts.findIndex((o) => o.value === value));
      const n    = opts.length;
      return (
        <div className="twk-row">
          <div className="twk-lbl"><span>{label}</span></div>
          <div className="twk-seg">
            <div className="twk-seg-thumb"
              style={{ left: `calc(2px + ${idx} * (100% - 4px) / ${n})`, width: `calc((100% - 4px) / ${n})` }} />
            {opts.map((o) => (
              <button key={o.value} type="button" role="radio" aria-checked={o.value === value}
                onClick={() => onChange(o.value)}>{o.label}</button>
            ))}
          </div>
        </div>
      );
    }

    function useTweaks(defaults) {
      const [values, setValues] = React.useState(defaults);
      const setTweak = React.useCallback((key, val) => {
        const edits = typeof key === 'object' ? key : { [key]: val };
        setValues((prev) => ({ ...prev, ...edits }));
        window.parent.postMessage({ type: '__edit_mode_set_keys', edits }, '*');
      }, []);
      return [values, setTweak];
    }

    // ═══════════════════════════════════════════════════════════
    // App
    // ═══════════════════════════════════════════════════════════
    const { useState, useEffect, useMemo } = React;

    const TWEAK_DEFAULTS = /*EDITMODE-BEGIN*/{
      "showFeatured": true,
      "headerStyle": "photo",
      "cardLayout": "3col"
    }/*EDITMODE-END*/;

    function App() {
      const [filter,       setFilter]       = useState('all');
      const [selected,     setSelected]     = useState(null);
      const [prefillVenue, setPrefillVenue] = useState('');
      const [tweaks,       setTweak]        = useTweaks(TWEAK_DEFAULTS);

      const featured = window.VENUES.find((v) => v.featured);
      const others   = window.VENUES.filter((v) => !v.featured);

      const filtered = useMemo(() => {
        if (filter === 'all') return others;
        return others.filter((v) => v.region === filter);
      }, [filter, others]);

      useEffect(() => {
        if (window.lucide) window.lucide.createIcons();
      });

      useEffect(() => {
        const root = document.documentElement;
        root.style.setProperty('--venues-grid-cols',
          tweaks.cardLayout === '2col' ? '2' :
          tweaks.cardLayout === '4col' ? '4' : '3');
      }, [tweaks.cardLayout]);

      useEffect(() => {
        document.body.style.overflow = selected ? 'hidden' : '';
        return () => { document.body.style.overflow = ''; };
      }, [selected]);

      const featuredCount = tweaks.showFeatured && filter === 'all' && featured ? 1 : 0;

      return (
        <>
          {tweaks.headerStyle !== 'minimal' && <VenuesHeader />}
          <FilterBar active={filter} setActive={setFilter} total={featuredCount + filtered.length} />
          {tweaks.showFeatured && featured && filter === 'all' && (
            <FeaturedVenue v={featured} onView={setSelected} />
          )}
          <VenuesGrid list={filtered} onSelect={setSelected} />
          <VenueCTA prefillVenue={prefillVenue} />

          <VenueModal v={selected} onClose={() => setSelected(null)} onPlanHere={setPrefillVenue} />

          <TweaksPanel title="Tweaks">
            <TweakSection label="Layout">
              <TweakToggle label="Show featured venue" value={tweaks.showFeatured}
                onChange={(v) => setTweak('showFeatured', v)} />
              <TweakRadio label="Header style" value={tweaks.headerStyle}
                onChange={(v) => setTweak('headerStyle', v)}
                options={[{ value: 'photo', label: 'Photo' }, { value: 'minimal', label: 'Off' }]} />
              <TweakRadio label="Card grid" value={tweaks.cardLayout}
                onChange={(v) => setTweak('cardLayout', v)}
                options={[{ value: '2col', label: '2 col' }, { value: '3col', label: '3 col' }, { value: '4col', label: '4 col' }]} />
            </TweakSection>
          </TweaksPanel>
        </>
      );
    }

    ReactDOM.createRoot(document.getElementById('root')).render(<App />);

    // ── Scroll reveal ──────────────────────────────────────────
    (function initReveal() {
      const root = document.getElementById('root');
      root.classList.add('js-reveal-ready');
      const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('in-view');
            io.unobserve(entry.target);
          }
        });
      }, { threshold: 0.12 });
      const observe = () => document.querySelectorAll('.reveal').forEach((el) => io.observe(el));
      setTimeout(observe, 80);
      const mo = new MutationObserver(() => {
        document.querySelectorAll('.reveal:not(.in-view)').forEach((el) => {
          if (!el.dataset.revealWatched) {
            el.dataset.revealWatched = '1';
            const io2 = new IntersectionObserver((entries) => {
              entries.forEach((entry) => {
                if (entry.isIntersecting) { entry.target.classList.add('in-view'); io2.unobserve(entry.target); }
              });
            }, { threshold: 0.12 });
            io2.observe(el);
          }
        });
      });
      mo.observe(root, { childList: true, subtree: true });
    })();
  </script>

<?php echo ob_get_clean(); ?>