<!DOCTYPE html>
<html lang="tr"
  data-theme="<?php echo e($uiSettings['mode'] ?? 'dark'); ?>"
  data-accent="<?php echo e($uiSettings['accent'] ?? '#27A0B1'); ?>"
  data-surface="<?php echo e($uiSettings['surface'] ?? ''); ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <title>Adisyon</title>
  
  <link rel="manifest" href="/manifest.json">
  <meta name="theme-color" content="#27A0B1">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="KafePOS">
  <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  
  <script>
    (function(){
      var root = document.documentElement;
      var accent  = root.getAttribute('data-accent') || '#27A0B1';
      var surface = root.getAttribute('data-surface') || '';
      function hexToRgb(h){var r=parseInt(h.slice(1,3),16),g=parseInt(h.slice(3,5),16),b=parseInt(h.slice(5,7),16);return r+','+g+','+b;}
      function darken(h,a){var r=parseInt(h.slice(1,3),16),g=parseInt(h.slice(3,5),16),b=parseInt(h.slice(5,7),16);r=Math.max(0,Math.round(r*a));g=Math.max(0,Math.round(g*a));b=Math.max(0,Math.round(b*a));return'#'+r.toString(16).padStart(2,'0')+g.toString(16).padStart(2,'0')+b.toString(16).padStart(2,'0');}
      root.style.setProperty('--primary', accent);
      root.style.setProperty('--primary2', darken(accent, 0.85));
      root.style.setProperty('--primary-dim', 'rgba('+hexToRgb(accent)+',.15)');
      if(surface){ root.style.setProperty('--bg', surface); }
    })();
  </script>
  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    :root{
      --bg:#0d0d0d;--s1:#131313;--s2:#1a1a1a;--s3:#222;
      --border:#2e2e2e;--border2:#3a3a3a;
      --primary:#27A0B1;--primary2:#1d8a9a;--primary-dim:rgba(39,160,177,.15);
      --green:#10b981;--red:#ef4444;--orange:#f59e0b;
      --text:#e8e4d8;--muted:#555;--muted2:#777;--r:8px;
    }

    /* ── AÇIK TEMA ──────────────────────────────────────────── */
    html[data-theme="light"]{
      --bg:#f1f5f9;--s1:#ffffff;--s2:#f8fafc;--s3:#e2e8f0;
      --border:#cbd5e1;--border2:#94a3b8;
      --text:#0f172a;--muted:#64748b;--muted2:#475569;
    }
    html[data-theme="light"] body{background:var(--bg);color:var(--text)}
    html,body{height:100%;background:var(--bg);color:var(--text);font-family:'Inter','Segoe UI',system-ui,sans-serif;overflow:hidden}

    /*  Topbar  */
    .topbar{
      display:flex;align-items:center;
      height:52px;background:var(--s2);
      border-bottom:1px solid var(--border);
      flex-shrink:0;gap:0;
    }
    /* Marka */
    .topbar-brand{
      display:flex;align-items:center;gap:9px;
      padding:0 18px;height:100%;
      border-right:1px solid var(--border);
      flex-shrink:0;
    }
    .topbar-brand-logo{
      width:28px;height:28px;border-radius:7px;
      background:var(--primary);
      display:flex;align-items:center;justify-content:center;
      font-size:.9rem;flex-shrink:0;
    }
    .topbar-brand-text h1{
      font-size:.8rem;font-weight:800;
      color:var(--text);letter-spacing:-.3px;line-height:1.1;
    }
    .topbar-brand-text small{
      font-size:.6rem;color:var(--muted);display:block;
    }
    /* Aktif sayfa etiketi */
    .topbar-page{
      padding:0 16px;height:100%;
      display:flex;align-items:center;
      border-right:1px solid var(--border);
      flex-shrink:0;
    }
    #topTitle{
      font-size:.8rem;font-weight:600;
      color:var(--muted2);letter-spacing:-.1px;
      text-decoration:none;cursor:pointer;
    }
    #topTitle:hover{color:var(--primary)}
    .topbar-spacer{flex:1;min-width:0;}
    /* Sağ aksiyonlar */
    .topbar-right{
      display:flex;align-items:center;
      height:100%;padding:0 10px;gap:3px;
    }
    .topbar-div{
      width:1px;height:56%;background:var(--border);
      margin:0 5px;flex-shrink:0;
    }
    .tb-user{
      font-size:.72rem;font-weight:600;color:var(--muted2);
      max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;
      padding:0 4px;
    }
    .tb-logout{color:var(--muted2) !important;}
    .tb-btn{
      display:inline-flex;align-items:center;gap:5px;
      padding:0 11px;height:30px;
      border-radius:6px;font-size:.73rem;font-weight:600;
      cursor:pointer;border:1px solid transparent;
      background:transparent;color:var(--muted2);
      transition:background .13s,color .13s,border-color .13s;
      white-space:nowrap;text-decoration:none;font-family:inherit;
    }
    .tb-btn:hover{background:var(--s3);color:var(--text);border-color:var(--border)}
    .tb-btn.primary{background:var(--primary);border-color:var(--primary);color:#fff;font-weight:700}
    .tb-btn.primary:hover{background:var(--primary2);border-color:var(--primary2)}
    .tb-btn.back{color:var(--muted2)}
    .tb-btn.fire{color:var(--orange);border-color:rgba(245,158,11,.35);background:rgba(245,158,11,.07);font-weight:700}
    .tb-btn.fire:not([disabled]):hover{background:var(--orange);color:#fff;border-color:var(--orange)}
    .tb-btn.fire[disabled]{opacity:.35;cursor:default;pointer-events:none}
    .mob-only{display:none!important;}
    /* Mobil açılır menü */
    .mob-menu-wrap{position:relative;}
    .mob-dropdown{
      display:none;position:absolute;top:calc(100% + 6px);right:0;
      background:var(--s2);border:1px solid var(--border);
      border-radius:10px;min-width:160px;
      box-shadow:0 8px 24px rgba(0,0,0,.5);
      z-index:9999;overflow:hidden;
      flex-direction:column;
    }
    .mob-dropdown.open{display:flex;}
    .mob-dd-item{
      display:flex;align-items:center;gap:10px;
      padding:13px 16px;font-size:.82rem;font-weight:600;
      color:var(--text);background:transparent;
      border:none;cursor:pointer;font-family:inherit;
      text-decoration:none;white-space:nowrap;
      border-bottom:1px solid var(--border);
      transition:background .1s;
    }
    .mob-dd-item:last-child{border-bottom:none;}
    .mob-dd-item:hover,.mob-dd-item:active{background:var(--s3);}
    .mob-dd-icon{font-size:1rem;width:20px;text-align:center;}

    /*  Screens  */
    .screen{display:none;flex-direction:column;height:calc(100vh - 52px)}
    .screen.active{display:flex}

    /* 
       SCREEN 1  MASALAR
     */
    .masalar-body{
      flex:1;
      display:flex;
      flex-direction:column;
      overflow:hidden;
      background:var(--bg);
    }

    /* ─── Stats bar ─── */
    .masalar-stats{
      display:flex;
      align-items:center;
      gap:8px;
      padding:10px 16px;
      border-bottom:1px solid var(--border);
      flex-shrink:0;
      background:var(--s2);
    }
    .mstat{
      display:flex;
      align-items:center;
      gap:7px;
      padding:5px 12px;
      border-radius:6px;
      border:1px solid var(--border);
      background:var(--s1);
    }
    .mstat-dot{
      width:7px;height:7px;border-radius:50%;flex-shrink:0;
    }
    .mstat-dot.green{background:var(--green);box-shadow:0 0 6px var(--green);}
    .mstat-dot.grey{background:var(--muted);}
    .mstat-dot.orange{background:var(--orange);box-shadow:0 0 6px var(--orange);}
    .mstat span{font-size:.72rem;color:var(--muted2);}
    .mstat strong{font-size:.82rem;font-weight:800;color:var(--text);}
    .masalar-stats-spacer{flex:1;}

    /* ─── Scroll alanı ─── */
    .masalar-scroll{
      flex:1;
      overflow-y:auto;
      padding:20px;
      -webkit-overflow-scrolling:touch;
    }
    .masalar-main-grid{
      display:grid;
      grid-template-columns:repeat(auto-fill,minmax(200px,220px));
      gap:14px;
    }

    /* ─── Masa Kartı ─── */
    .tcard{
      height:190px;
      background:var(--s1);
      border:1px solid var(--border);
      border-radius:14px;
      cursor:pointer;
      display:flex;
      flex-direction:column;
      overflow:hidden;
      position:relative;
      transition:transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }
    .tcard:hover{
      transform:translateY(-4px);
      box-shadow:0 20px 48px rgba(0,0,0,.6);
    }
    .tcard:active{transform:translateY(-1px);}
    .tcard.selected{border-color:var(--primary);}

    /* Boş masa */
    .tcard:not(.occupied){
      border-color:var(--border);
    }
    .tcard:not(.occupied):hover{
      border-color:var(--border2);
      box-shadow:0 20px 48px rgba(0,0,0,.5);
    }

    /* Dolu masa */
    .tcard.occupied{
      border-color:rgba(39,160,177,.4);
      background:var(--s1);
    }
    .tcard.occupied:hover{
      border-color:var(--primary);
      box-shadow:0 20px 48px rgba(39,160,177,.18);
    }

    /* Hazır ürün */
    .tcard.has-ready{
      border-color:rgba(245,158,11,.55) !important;
    }
    .tcard.has-ready:hover{
      box-shadow:0 20px 48px rgba(245,158,11,.15) !important;
    }

    /* Kart üst şeridi */
    .tcard-stripe{
      height:3px;
      flex-shrink:0;
      background:var(--border);
      transition:background .18s;
    }
    .tcard.occupied .tcard-stripe{background:var(--primary);}
    .tcard.has-ready .tcard-stripe{background:var(--orange) !important;}

    /* Kart içi */
    .tcard-inner{
      flex:1;
      padding:14px 16px 10px;
      display:flex;
      flex-direction:column;
      gap:0;
      min-height:0;
    }

    /* Başlık satırı */
    .tcard-head{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:8px;
      margin-bottom:10px;
    }
    .tcard-name{
      font-size:.95rem;
      font-weight:800;
      letter-spacing:-.2px;
      white-space:nowrap;
      overflow:hidden;
      text-overflow:ellipsis;
      color:var(--text);
      flex:1;
    }
    .tcard-status-pill{
      display:flex;
      align-items:center;
      gap:4px;
      padding:3px 8px;
      border-radius:999px;
      font-size:.6rem;
      font-weight:800;
      letter-spacing:.3px;
      flex-shrink:0;
    }
    .tcard-status-pill.open{
      background:rgba(16,185,129,.12);
      border:1px solid rgba(16,185,129,.25);
      color:#34d399;
    }
    .tcard-status-pill.closed{
      background:rgba(255,255,255,.04);
      border:1px solid var(--border);
      color:var(--muted);
    }
    .tcard-status-pill .pill-dot{
      width:5px;height:5px;border-radius:50%;flex-shrink:0;
    }
    .tcard-status-pill.open .pill-dot{
      background:var(--green);
      box-shadow:0 0 4px var(--green);
      animation:kpulse 2s ease-in-out infinite;
    }
    .tcard-status-pill.closed .pill-dot{background:var(--muted);}
    @keyframes kpulse{0%,100%{opacity:1}50%{opacity:.3}}

    /* Orta alan — dolu masa bilgisi */
    .tcard-info{
      flex:1;
      display:flex;
      flex-direction:column;
      justify-content:center;
      gap:5px;
    }
    .tcard-empty-label{
      font-size:.75rem;
      color:var(--muted);
      font-weight:500;
    }
    .tcard-item-row{
      display:flex;
      align-items:center;
      gap:6px;
    }
    .tcard-item-count{
      font-size:.72rem;
      color:var(--muted2);
      background:var(--s3);
      padding:2px 8px;
      border-radius:4px;
      border:1px solid var(--border);
    }
    .tcard-amount{
      font-size:1.2rem;
      font-weight:900;
      color:var(--primary);
      letter-spacing:-.5px;
      line-height:1;
    }
    .tcard-amount-label{
      font-size:.65rem;
      color:var(--muted);
      margin-top:1px;
    }
    .tcard-ready-badge{
      display:inline-flex;
      align-items:center;
      gap:4px;
      font-size:.65rem;
      font-weight:800;
      color:var(--orange);
      background:rgba(245,158,11,.1);
      border:1px solid rgba(245,158,11,.25);
      border-radius:4px;
      padding:2px 7px;
      margin-top:2px;
      width:fit-content;
    }

    /* Alt buton alanı */
    .tcard-actions{
      display:flex;
      border-top:1px solid var(--border);
      flex-shrink:0;
      background:rgba(0,0,0,.15);
    }
    .tcard-act-btn{
      flex:1;
      padding:9px 6px;
      font-size:.7rem;
      font-weight:700;
      cursor:pointer;
      border:none;
      background:transparent;
      color:var(--muted);
      transition:color .14s, background .14s;
      letter-spacing:.1px;
    }
    .tcard-act-btn:hover{
      color:var(--text);
      background:rgba(255,255,255,.05);
    }
    .tcard-act-btn+.tcard-act-btn{
      border-left:1px solid var(--border);
    }

    /* 
       SCREEN 2  POS
     */
    .pos{display:grid;grid-template-columns:1fr 380px;flex:1;overflow:hidden}

    /*  Products panel  */
    .pane-mid{display:flex;flex-direction:column;background:var(--bg);border-right:1px solid var(--border);overflow:hidden}
    .prod-tools{display:flex;align-items:center;gap:7px;padding:7px 10px;border-bottom:1px solid var(--border);flex-shrink:0}
    .search-wrap{position:relative;flex:1}
    .search-wrap input{width:100%;background:var(--s2);border:1px solid var(--border);color:var(--text);border-radius:6px;padding:7px 10px 7px 30px;font-size:.8rem;outline:none;transition:border-color .15s}
    .search-wrap input:focus{border-color:var(--primary)}
    .search-wrap .sico{position:absolute;left:8px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:.78rem}
    .tool-btn{padding:6px 11px;background:var(--s2);border:1px solid var(--border);color:var(--muted2);border-radius:6px;font-size:.75rem;font-weight:600;cursor:pointer;transition:all .15s;white-space:nowrap;text-decoration:none}
    .tool-btn:hover{border-color:var(--primary);color:var(--primary);background:var(--primary-dim)}
    .cat-tabs{display:flex;gap:5px;padding:7px 10px;border-bottom:1px solid var(--border);flex-wrap:wrap;flex-shrink:0;overflow-x:auto}
    .ctab{padding:4px 12px;border-radius:999px;font-size:.72rem;font-weight:700;border:1px solid var(--border);background:transparent;color:var(--muted2);cursor:pointer;transition:all .14s;white-space:nowrap}
    .ctab:hover,.ctab.active{background:var(--primary);border-color:var(--primary);color:#fff}
    .prod-scroll{flex:1;overflow-y:auto;padding:10px}
    .prod-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(128px,1fr));gap:5px}
    .pcard{background:var(--s1);border:1px solid var(--border);border-radius:var(--r);padding:9px 10px;cursor:pointer;transition:all .15s}
    .pcard:hover{border-color:var(--primary);background:var(--s2);transform:translateY(-1px)}
    .pcard:active{transform:scale(.97)}
    .pcard.flash{border-color:var(--green)!important;background:rgba(16,185,129,.1)!important;transition:none}
    .pcard h4{font-size:.78rem;font-weight:700;margin-bottom:2px;line-height:1.25}
    .pcard .pcat{font-size:.62rem;color:var(--muted2);margin-bottom:4px}
    .pcard .pprice{font-size:.8rem;font-weight:800;color:var(--primary)}

    /*  Order panel  */
    .pane-right{display:flex;flex-direction:column;background:var(--s1);overflow:hidden}
    .pane-hdr{display:flex;align-items:center;justify-content:space-between;padding:0 14px;height:42px;background:var(--s2);border-bottom:2px solid var(--primary);flex-shrink:0}
    .pane-hdr h2{font-size:.85rem;font-weight:800;color:var(--text)}
    .pane-hdr small{font-size:.7rem;color:var(--muted2)}
    .order-items{flex:1;overflow-y:auto;min-height:0}
    .no-table{display:flex;align-items:center;justify-content:center;height:100%;color:var(--muted);font-size:2rem;opacity:.2}
    .oitem{display:flex;align-items:center;gap:8px;padding:8px 12px;border-bottom:1px solid rgba(255,255,255,.04)}
    .oitem:hover{background:rgba(255,255,255,.02)}
    .oitem-info{flex:1;min-width:0}
    .oitem-info strong{font-size:.84rem;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .oitem-info small{font-size:.68rem;color:var(--muted2)}
    .qty-ctrl{display:flex;align-items:center;gap:1px;border:1px solid var(--border);border-radius:6px;overflow:hidden;flex-shrink:0}
    .qty-ctrl button{width:24px;height:24px;background:var(--s2);border:none;color:var(--muted2);cursor:pointer;font-size:.88rem;transition:background .1s;display:flex;align-items:center;justify-content:center}
    .qty-ctrl button:hover{background:var(--primary);color:#fff}
    .qty-ctrl span{width:28px;text-align:center;font-size:.78rem;font-weight:700;background:var(--s3);height:24px;display:flex;align-items:center;justify-content:center}
    .oitem-del{background:none;border:none;color:#3a1818;cursor:pointer;font-size:.82rem;padding:3px;border-radius:4px;transition:color .15s}
    .oitem-del:hover{color:var(--red)}
    .oitem-note-btn{background:none;border:none;cursor:pointer;font-size:.75rem;padding:3px 5px;border-radius:4px;color:var(--muted2);transition:color .13s;flex-shrink:0}
    .oitem-note-btn:hover{color:var(--orange)}
    .oitem-note-btn.has-note{color:var(--orange)}
    .oitem-note{font-size:.63rem;color:var(--orange);font-style:italic;margin-top:1px;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:150px}
    /* Note modal chips */
    .note-chips{display:flex;flex-wrap:wrap;gap:5px;margin-bottom:7px}
    .chip-wrap{display:inline-flex;align-items:center;border-radius:20px;border:1px solid var(--border);background:var(--s3);overflow:hidden;transition:border-color .12s}
    .chip-wrap:hover{border-color:var(--orange)}
    .chip-wrap.active{background:var(--orange);border-color:var(--orange)}
    .chip-label{background:none;border:none;cursor:pointer;padding:5px 8px 5px 11px;font-size:.72rem;font-weight:600;color:var(--muted2);font-family:inherit;white-space:nowrap}
    .chip-wrap.active .chip-label{color:#fff}
    .chip-wrap:not(.active):hover .chip-label{color:var(--orange)}
    .chip-del{background:none;border:none;border-left:1px solid rgba(255,255,255,.1);cursor:pointer;padding:4px 8px;font-size:.6rem;color:var(--muted2);opacity:.55;font-family:inherit;line-height:1}
    .chip-wrap.active .chip-del{color:#fff;opacity:.7}
    .chip-del:hover{opacity:1!important;color:var(--red)!important}
    .note-add-row{display:flex;gap:5px;margin-bottom:9px}
    .note-add-input{flex:1;background:var(--s2);border:1px solid var(--border);color:var(--text);border-radius:8px;padding:5px 9px;font-size:.75rem;font-family:inherit;outline:none}
    .note-add-input:focus{border-color:var(--orange)}
    .note-add-btn{padding:5px 10px;border-radius:8px;background:var(--s3);border:1px solid var(--border);color:var(--muted2);font-size:.8rem;cursor:pointer;font-family:inherit;white-space:nowrap}
    .note-add-btn:hover{border-color:var(--orange);color:var(--orange)}
    .order-totals{border-top:1px solid var(--border);flex-shrink:0;background:var(--s2)}
    .totals-toggle{
      display:flex;align-items:center;justify-content:space-between;
      padding:7px 13px;cursor:pointer;user-select:none;
      font-size:.72rem;font-weight:600;color:var(--muted2);
      transition:background .12s;
    }
    .totals-toggle:hover{background:var(--s3)}
    .totals-toggle-arrow{font-size:.7rem;transition:transform .2s;}
    .order-totals.collapsed .totals-toggle-arrow{transform:rotate(180deg)}
    .totals-body{padding:6px 13px 8px;}
    .order-totals.collapsed .totals-body{display:none}
    .total-row{display:flex;justify-content:space-between;align-items:center;font-size:.74rem;padding:2px 0;color:var(--muted2)}
    .total-row.main{font-size:.9rem;font-weight:800;color:var(--text);padding:5px 0;border-top:1px solid var(--border);margin-top:3px}
    .payment-area{padding:9px 12px;border-top:1px solid var(--border);flex-shrink:0;display:flex;flex-direction:column;gap:6px}
    .payment-row{display:grid;grid-template-columns:1fr 1fr;gap:7px}
    .prow-group{display:flex;flex-direction:column;gap:3px}
    .prow-group label{font-size:.65rem;color:var(--muted);font-weight:700;text-transform:uppercase}
    .form-control{background:var(--s2);border:1px solid var(--border);color:var(--text);border-radius:6px;padding:6px 9px;font-size:.78rem;outline:none;width:100%;transition:border-color .15s;font-family:inherit}
    .form-control:focus{border-color:var(--primary)}
    select.form-control{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%23777'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 8px center;padding-right:22px}
    .payment-btns{display:grid;grid-template-columns:1fr 1fr 1fr;gap:5px}
    .pbtn{padding:9px 5px;border-radius:6px;font-size:.75rem;font-weight:700;border:1px solid var(--border);cursor:pointer;transition:all .15s;text-align:center}
    .pbtn.primary{background:var(--primary);border-color:var(--primary);color:#fff}
    .pbtn.primary:hover{background:var(--primary2)}
    .pbtn.secondary{background:var(--s2);color:var(--muted2)}
    .pbtn.secondary:hover{border-color:var(--primary);color:var(--primary);background:var(--primary-dim)}
    .danger-btns{display:grid;grid-template-columns:1fr 1fr;gap:5px}
    .dbtn{padding:7px;border-radius:6px;font-size:.72rem;font-weight:700;text-align:center;cursor:pointer;transition:all .15s;border:1px solid}
    .dbtn.warn{background:rgba(245,158,11,.1);border-color:rgba(245,158,11,.25);color:var(--orange)}
    .dbtn.warn:hover{background:rgba(245,158,11,.2)}
    .dbtn.danger{background:rgba(239,68,68,.1);border-color:rgba(239,68,68,.25);color:var(--red)}
    .dbtn.danger:hover{background:rgba(239,68,68,.2)}

    ::-webkit-scrollbar{width:4px}::-webkit-scrollbar-thumb{background:#262626;border-radius:4px}::-webkit-scrollbar-track{background:transparent}

    /* ── TEMA SEÇICI ────────────────────────────────────────────── */
    .theme-section{margin-bottom:14px}
    .theme-section-label{font-size:.65rem;font-weight:700;color:var(--muted2);text-transform:uppercase;letter-spacing:.5px;margin-bottom:7px}
    /* Mod toggle */
    .mode-toggle{display:grid;grid-template-columns:1fr 1fr;gap:5px;margin-bottom:12px}
    .mode-btn{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:3px;padding:10px 6px;border-radius:9px;border:2px solid var(--border);background:var(--s3);cursor:pointer;transition:all .15s;font-size:.72rem;font-weight:700;color:var(--muted2);font-family:inherit}
    .mode-btn .mode-icon{font-size:1.3rem;line-height:1}
    .mode-btn:hover{border-color:var(--primary);color:var(--primary);background:var(--primary-dim)}
    .mode-btn.active{border-color:var(--primary);background:var(--primary-dim);color:var(--primary)}
    /* Renk paletleri */
    .color-palette{display:flex;flex-wrap:wrap;gap:7px;align-items:center}
    .color-swatch{width:28px;height:28px;border-radius:50%;cursor:pointer;border:3px solid transparent;transition:transform .12s,border-color .12s;flex-shrink:0}
    .color-swatch:hover{transform:scale(1.18)}
    .color-swatch.active{border-color:var(--text)!important;transform:scale(1.15)}
    .color-custom-wrap{display:flex;align-items:center;gap:6px;margin-top:6px}
    .color-custom-label{font-size:.68rem;color:var(--muted2);font-weight:600;white-space:nowrap}
    .color-custom-input{width:36px;height:28px;border-radius:6px;border:1px solid var(--border);cursor:pointer;background:none;padding:1px 2px}
    /* Divider */
    .theme-divider{border:none;border-top:1px solid var(--border);margin:12px 0}

    /* MODALS */
    .modal-bg{position:fixed;inset:0;background:rgba(0,0,0,.72);z-index:10000;display:none;align-items:center;justify-content:center}
    .modal-bg.open{display:flex}
    .modal{background:var(--s2);border:1px solid var(--border2);border-radius:12px;padding:24px;width:360px;max-width:92vw}
    .modal h3{font-size:.9rem;font-weight:800;color:var(--primary);margin-bottom:16px}
    .modal .form-control{margin-bottom:11px}
    .modal-actions{display:flex;gap:8px}
    .modal-actions button{flex:1;padding:9px;border-radius:6px;font-size:.8rem;font-weight:700;cursor:pointer;border:none}
    .modal-actions .btn-ok{background:var(--primary);color:#fff}.modal-actions .btn-ok:hover{background:var(--primary2)}
    .modal-actions .btn-cancel{background:var(--s3);color:var(--muted2)}.modal-actions .btn-cancel:hover{background:var(--border)}
    .modal .row2{display:grid;grid-template-columns:1fr 1fr;gap:10px}
    .modal .fg{display:flex;flex-direction:column;gap:4px;margin-bottom:11px}
    .modal .fg label{font-size:.68rem;color:var(--muted2);font-weight:700;text-transform:uppercase}

    /* TOAST */
    .toast{position:fixed;bottom:20px;left:50%;transform:translateX(-50%) translateY(60px);background:var(--s2);border:1px solid var(--border);color:var(--text);border-radius:8px;padding:10px 20px;font-size:.8rem;font-weight:600;z-index:999;transition:transform .25s;pointer-events:none}
    .toast.show{transform:translateX(-50%) translateY(0)}

    /* HAZIR NOTIFICATION BANNER */
    .notif-banner{
      position:fixed;top:52px;right:16px;
      background:linear-gradient(135deg,#0d2a1a,#0f3320);
      border:1.5px solid var(--green);border-radius:12px;
      padding:14px 16px 12px;min-width:270px;max-width:340px;
      z-index:800;transform:translateX(calc(100% + 24px));
      transition:transform .35s cubic-bezier(.22,.68,0,1.2);
      box-shadow:0 4px 28px rgba(16,185,129,.25);
    }
    .notif-banner.show{transform:translateX(0)}
    .notif-banner .nb-title{font-size:.82rem;font-weight:800;color:var(--green);margin-bottom:6px}
    .notif-banner .nb-items{font-size:.76rem;color:#a7f3d0;line-height:1.6;margin-bottom:10px}
    .notif-banner .nb-footer{display:flex;align-items:center;justify-content:space-between;gap:8px}
    .notif-banner .nb-queue{font-size:.67rem;color:#4ade80;min-height:16px}
    .notif-banner .nb-ok{
      background:var(--green);border:none;color:#fff;
      border-radius:7px;padding:6px 16px;font-size:.78rem;font-weight:800;
      cursor:pointer;transition:background .15s;white-space:nowrap;
    }
    .notif-banner .nb-ok:hover{background:#0ea371}

    /* ─────────── RESPONSIVE ─────────── */
    /* Telefon - ≤ 640px */
    @media(max-width:640px){
      html,body{overflow:hidden}
      .screen{height:calc(100dvh - 50px)}
      .topbar{height:50px;}
      .topbar-brand small,.topbar-brand-text small{display:none;}
      .topbar-brand{padding:0 12px;}
      .topbar-page{display:none;}
      .mob-hide{display:none!important;}
      .mob-only{display:inline-flex!important;}
      .tb-btn{height:36px;padding:0 13px;font-size:.75rem;}

      /* Masalar: 2 sütun, scroll */
      .masalar-scroll{padding:10px;}
      .masalar-stats{padding:8px 10px;gap:6px;flex-wrap:wrap;}
      .mstat{padding:4px 10px;}
      .masalar-main-grid{
        grid-template-columns:repeat(2,1fr)!important;
      }
      .tcard{height:168px!important;}
      .tcard-act-btn{padding:9px 4px;font-size:.66rem;touch-action:manipulation;}

      /* POS: tek panel, sekmeli */
      .pos{display:flex;flex-direction:column;padding-bottom:52px;overflow:hidden}
      .pane-mid,.pane-right{display:none;flex:1;min-height:0}
      .pane-mid.mob-active,.pane-right.mob-active{display:flex}

      /* Bigger touch targets */
      .ctab{padding:6px 14px;font-size:.74rem}
      .qty-ctrl button{width:34px;height:34px;font-size:1rem}
      .qty-ctrl span{width:38px;height:34px;font-size:.86rem}
      .pbtn{padding:13px 5px;font-size:.8rem}
      .dbtn{padding:11px}
      .pcard{padding:11px 12px}
      .pcard h4{font-size:.84rem}
      .pcard .pprice{font-size:.86rem}
      .oitem{padding:10px 12px}
      .oitem-info strong{font-size:.88rem}

      /* Notification: bottom sheet on mobile */
      .notif-banner{
        top:auto!important;bottom:64px!important;
        right:10px!important;left:10px!important;
        min-width:auto!important;max-width:none!important;
        transform:translateY(calc(100% + 80px))!important;
        transition:transform .35s cubic-bezier(.22,.68,0,1.2)!important;
      }
      .notif-banner.show{transform:translateY(0)!important}
    }

    /* Hazır masa rengi - handled in .tcard.has-ready above */
    .tcard.has-ready .tcard-status-pill.open{animation:rpulse 1.2s ease-in-out infinite}
    @keyframes rpulse{0%,100%{opacity:1}50%{opacity:.45}}
    /* Tablet - 641px..1024px */
    @media(min-width:641px) and (max-width:1024px){
      .pos{grid-template-columns:1fr 320px}
    }
    /* Draft item */
    .oitem.is-draft{border-left:3px solid var(--orange);opacity:.82}
    .ks-tag{font-size:.57rem;font-weight:700;border-radius:3px;padding:1px 5px;margin-left:5px;vertical-align:middle}
    .ks-draft{color:var(--orange);background:rgba(245,158,11,.12)}
    .ks-pending{color:var(--primary);background:rgba(39,160,177,.1)}
    .ks-ready{color:var(--green);background:rgba(16,185,129,.1)}
    /* Fire button */
    .pbtn.fire{background:rgba(245,158,11,.12);border:1px solid var(--orange);color:var(--orange);font-weight:700}
    .pbtn.fire:not([disabled]):hover{background:var(--orange);color:#fff}
    .pbtn.fire[disabled]{opacity:.35;cursor:default;pointer-events:none}
    /* pcard image */
    .pimg-wrap{width:100%;height:80px;border-radius:6px;margin-bottom:7px;overflow:hidden;background:var(--s3);display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .pimg-wrap .pimg{width:100%;height:100%;object-fit:cover;display:block}
    .pimg-wrap .pimg-empty{font-size:1.4rem;opacity:.25}

    /* Mobil sekme bar */
    .pos-tabs{
      display:none;position:absolute;bottom:0;left:0;right:0;height:52px;
      background:var(--s2);border-top:1px solid var(--border);z-index:50;
      flex-shrink:0;
    }
    .pos-tab{
      flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;
      gap:2px;font-size:.66rem;font-weight:700;color:var(--muted2);cursor:pointer;
      border:none;background:none;transition:color .15s;touch-action:manipulation;
      position:relative;
    }
    .pos-tab.active{color:var(--primary)}
    .pos-tab .tab-icon{font-size:1.15rem;line-height:1}
    .pos-tab .tab-count{
      position:absolute;top:5px;right:calc(50% - 22px);
      background:var(--primary);color:#fff;border-radius:99px;
      font-size:.52rem;padding:1px 5px;min-width:14px;text-align:center;
      display:none;
    }
    .pos-tab .tab-count.visible{display:block}
    @media(max-width:640px){.pos-tabs{display:flex}.pos{position:relative}}
  </style>
</head>
<body>

<?php if(session('impersonating_original')): ?>
<div style="background:#7c3aed;color:#fff;text-align:center;padding:7px 16px;font-size:.78rem;font-weight:600;display:flex;align-items:center;justify-content:center;gap:14px;position:sticky;top:0;z-index:9999">
  <span>👁 <strong><?php echo e(auth()->user()->name); ?></strong> hesabını görüntülüyorsunuz</span>
  <form method="POST" action="<?php echo e(route('admin.stop-impersonate')); ?>" style="display:inline">
    <?php echo csrf_field(); ?>
    <button type="submit" style="background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.4);color:#fff;padding:3px 12px;border-radius:5px;cursor:pointer;font-size:.73rem;font-weight:700;font-family:inherit">
      ← Kendi Hesabıma Dön
    </button>
  </form>
</div>
<?php endif; ?>

<!--  TOPBAR  -->
<div class="topbar">
  <div class="topbar-brand">
    <div class="topbar-brand-logo">🍽️</div>
    <div class="topbar-brand-text">
      <h1>Kafe POS</h1>
      <small>Adisyon Sistemi</small>
    </div>
  </div>
  <div class="topbar-page">
    <a id="topTitle" href="/adisyon">Masalar</a>
  </div>
  <div class="topbar-spacer"></div>
  <div class="topbar-right">
    <a href="/mutfak" target="_blank" class="tb-btn mob-hide">Mutfak</a>
    <button class="tb-btn primary" id="btnAddTable" onclick="showModal('addTable')">+ Masa</button>
    <div class="topbar-div mob-hide"></div>
    <button class="tb-btn mob-hide" onclick="openUrunler()">Ürünler</button>
    <button class="tb-btn mob-hide" onclick="openRapor()">Rapor</button>
    <button class="tb-btn mob-hide" onclick="openGecmis()">Geçmiş</button>
    <div class="topbar-div mob-hide"></div>
    <button class="tb-btn mob-hide" onclick="openSettings()" title="Ayarlar">&#9881;</button>
    <button class="tb-btn mob-hide" onclick="openQr()" title="QR Menü">QR</button>
    <div class="topbar-div mob-hide"></div>
    <span class="tb-user mob-hide"><?php echo e(auth()->user()->name); ?></span>
    <a href="<?php echo e(route('subscription.select')); ?>" class="tb-btn mob-hide" title="Aboneliğini yönet / Uzat">💳 Abonelik</a>
    <form method="POST" action="<?php echo e(route('logout')); ?>" class="mob-hide" style="display:inline">
      <?php echo csrf_field(); ?>
      <button type="submit" class="tb-btn tb-logout" title="Çıkış Yap">↪ Çıkış</button>
    </form>
    <button class="tb-btn mob-only" onclick="location.reload()" title="Yenile" style="font-size:1rem">&#8635;</button>
    <div class="mob-only mob-menu-wrap">
      <button class="tb-btn" onclick="toggleMobMenu(event)" title="Menü" id="btnMobMenu">&#8942;</button>
      <div class="mob-dropdown" id="mobDropdown">
        <a href="/mutfak" target="_blank" class="mob-dd-item"><span class="mob-dd-icon">🍳</span>Mutfak</a>
        <button class="mob-dd-item" onclick="closeMobMenu();openUrunler()"><span class="mob-dd-icon">🛒</span>Ürünler</button>
        <button class="mob-dd-item" onclick="closeMobMenu();openRapor()"><span class="mob-dd-icon">📊</span>Rapor</button>
        <button class="mob-dd-item" onclick="closeMobMenu();openGecmis()"><span class="mob-dd-icon">📝</span>Geçmiş</button>
        <button class="mob-dd-item" onclick="closeMobMenu();openQr()"><span class="mob-dd-icon">📱</span>QR Menü</button>
        <button class="mob-dd-item" onclick="closeMobMenu();openSettings()"><span class="mob-dd-icon">⚙️</span>Ayarlar</button>
        <a href="<?php echo e(route('subscription.select')); ?>" class="mob-dd-item"><span class="mob-dd-icon">💳</span>Aboneliği Uzat</a>
        <div style="border-top:1px solid var(--border);margin:4px 0"></div>
        <button class="mob-dd-item" onclick="document.getElementById('mobLogoutForm').submit()"><span class="mob-dd-icon">↪</span>Çıkış Yap</button>
      </div>
    </div>
    <form id="mobLogoutForm" method="POST" action="<?php echo e(route('logout')); ?>" style="display:none"><?php echo csrf_field(); ?></form>
  </div>
</div>

<!--  SCREEN 1: MASALAR  -->
<div class="screen active" id="screen-masalar">
  <div class="masalar-body">

    <?php
      $totalRooms    = $rooms->count();
      $occupiedRooms = $rooms->filter(fn($r) => $r->status === 'open')->count();
      $emptyRooms    = $totalRooms - $occupiedRooms;
    ?>

    <!-- Stats bar -->
    <div class="masalar-stats">
      <div class="mstat">
        <span class="mstat-dot green"></span>
        <strong id="stat-occupied"><?php echo e($occupiedRooms); ?></strong>
        <span>Dolu</span>
      </div>
      <div class="mstat">
        <span class="mstat-dot grey"></span>
        <strong id="stat-empty"><?php echo e($emptyRooms); ?></strong>
        <span>Boş</span>
      </div>
      <div class="mstat">
        <span class="mstat-dot orange" style="background:var(--muted);box-shadow:none"></span>
        <strong><?php echo e($totalRooms); ?></strong>
        <span>Toplam Masa</span>
      </div>

    </div>

    <!-- Masa grid -->
    <div class="masalar-scroll">
      <div class="masalar-main-grid" id="masalarGrid">
        <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php
            $openOrder = $room->pos_orders()->where('status','open')->first();
            $itemCount = $openOrder ? $openOrder->order_items()->count() : 0;
            $total     = $openOrder ? $openOrder->order_items()->sum('total') : 0;
            $hasReady  = $openOrder ? $openOrder->order_items()->where('kitchen_status','ready')->exists() : false;
          ?>
          <div class="tcard <?php echo e($room->status === 'open' ? 'occupied' : ''); ?> <?php echo e($hasReady ? 'has-ready' : ''); ?>"
               data-id="<?php echo e($room->id); ?>"
               data-name="<?php echo e($room->name); ?>"
               data-status="<?php echo e($room->status); ?>"
               onclick="openMasa(<?php echo e($room->id); ?>, this)">

            <div class="tcard-stripe"></div>

            <div class="tcard-inner">
              <div class="tcard-head">
                <div class="tcard-name"><?php echo e($room->name); ?></div>
                <div class="tcard-status-pill <?php echo e($room->status === 'open' ? 'open' : 'closed'); ?>">
                  <span class="pill-dot"></span>
                  <?php echo e($room->status === 'open' ? 'AÇIK' : 'KAPALI'); ?>

                </div>
              </div>

              <div class="tcard-info">
                <?php if($itemCount > 0): ?>
                  <div class="tcard-item-row">
                    <span class="tcard-item-count"><?php echo e($itemCount); ?> ürün</span>
                  </div>
                  <div class="tcard-amount"><?php echo e(number_format($total, 2)); ?> ₺</div>
                  <div class="tcard-amount-label">Güncel tutar</div>
                  <?php if($hasReady): ?>
                    <div class="tcard-ready-badge">⚡ Mutfaktan hazır</div>
                  <?php endif; ?>
                <?php else: ?>
                  <div class="tcard-empty-label">Sipariş yok</div>
                <?php endif; ?>
              </div>
            </div>

            <div class="tcard-actions" onclick="event.stopPropagation()">
              <button class="tcard-act-btn" onclick="selectAndRename(<?php echo e($room->id); ?>, '<?php echo e(addslashes($room->name)); ?>')">✎ Adlandır</button>
              <button class="tcard-act-btn" onclick="toggleMasaDirect(<?php echo e($room->id); ?>)">⏻ Aç/Kapat</button>
            </div>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>

  </div>
</div>

<!--  SCREEN 2: POS  -->
<div class="screen" id="screen-pos">
  <div class="pos">

    <!-- Products -->
    <div class="pane-mid">
      <div class="prod-tools">
        <div class="search-wrap">
          <span class="sico"></span>
          <input type="text" id="prodSearch" placeholder="Ara..." oninput="filterProds()">
        </div>
        <button class="tool-btn" onclick="openUrunler()">+ Ürün</button>
      </div>
      <div class="cat-tabs" id="catTabs">
        <button class="ctab active" data-cat="all" onclick="filterCat('all',this)">Tümü</button>
        <?php $__currentLoopData = $products->keys(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <button class="ctab" data-cat="<?php echo e($kat); ?>" onclick="filterCat('<?php echo e(addslashes($kat)); ?>',this)"><?php echo e($kat ?: 'Genel'); ?></button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <div class="prod-scroll">
        <div id="prodContainer">
          <?php if($products->isEmpty()): ?>
            <div style="text-align:center;padding:60px 20px;color:var(--muted)">
              <div style="font-size:3rem;margin-bottom:10px"></div>
              <p>Ürün yok.</p>
            </div>
          <?php else: ?>
            <div class="prod-grid">
              <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori => $urunler): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $__currentLoopData = $urunler; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $urun): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <div class="pcard"
                       data-id="<?php echo e($urun->id); ?>"
                       data-name="<?php echo e(strtolower($urun->name)); ?>"
                       data-cat="<?php echo e($kategori); ?>"
                       onclick="addProduct(<?php echo e($urun->id); ?>, this)">
                    <div class="pimg-wrap">
                      <?php if($urun->image_url): ?>
                        <img class="pimg" src="<?php echo e($urun->image_url); ?>" alt="" loading="lazy" onerror="this.parentElement.innerHTML='<span class=pimg-empty>🍽️</span>'">
                      <?php else: ?>
                        <span class="pimg-empty">🍽️</span>
                      <?php endif; ?>
                    </div>
                    <h4><?php echo e($urun->name); ?></h4>
                    <div class="pcat"><?php echo e($urun->category ?: 'Genel'); ?></div>
                    <div class="pprice"><?php echo e(number_format($urun->price, 2)); ?> ₺</div>
                  </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Order -->
    <div class="pane-right">
      <div class="pane-hdr">
        <h2 id="orderTitle"></h2>
        <small id="orderMeta"></small>
      </div>
      <div class="order-items" id="orderItems">
        <div class="no-table"></div>
      </div>
      <div class="order-totals" id="orderTotals" style="display:none">
        <div class="totals-toggle" onclick="toggleTotals()">
          <span id="totalToggleLabel">Ara Toplam &amp; Özet</span>
          <span class="totals-toggle-arrow">&#9650;</span>
        </div>
        <div class="totals-body" id="totalsBody">
        <div class="total-row"><span>Ara Toplam</span><span id="araToplam">0.00 ₺</span></div>
        <div class="total-row"><span id="servisLabel">Servis</span><span id="servisAmt">0.00 ₺</span></div>
        <div class="total-row"><span>İndirim</span><span id="indirimAmt">0.00 ₺</span></div>
        <div class="total-row"><span id="kdvLabel">KDV</span><span id="kdvAmt">0.00 ₺</span></div>
        <div class="total-row main"><span>Genel Toplam</span><span id="genelToplam">0.00 ₺</span></div>
        <div class="total-row"><span>Ödenen</span><span id="odenenAmt">0.00 ₺</span></div>
        <div class="total-row"><span>Kalan</span><span id="kalanAmt">0.00 ₺</span></div>
        </div>
      </div>
      <div class="payment-area" id="paymentArea" style="display:none">
        <div class="payment-row">
          <div class="prow-group">
            <label>Ödeme Tipi</label>
            <select id="odemeTipi" class="form-control">
              <option>Nakit</option>
              <option>Kredi Kartı</option>
              <option>Havale</option>
            </select>
          </div>
          <div class="prow-group">
            <label>Alınan Tutar (₺)</label>
            <input id="alinanTutar" type="number" class="form-control" placeholder="Opsiyonel" min="0" step="0.01">
          </div>
        </div>
        <textarea id="orderNote" class="form-control" rows="2" placeholder="Sipariş notu..." style="resize:none;font-size:.74rem;margin-bottom:6px" oninput="debounceSaveNote()"></textarea>
        <div class="payment-btns">
          <button class="pbtn primary" onclick="odemeAl()"> Ödeme Al</button>
          <button class="pbtn fire" id="btnFire" onclick="fireTokitchen()" disabled>🍳 Mutfağa Gönder</button>
          <button class="pbtn secondary" onclick="goMasalar()">◄ Masalar</button>
        </div>
        <div class="payment-btns" style="margin-top:4px">
          <button class="pbtn secondary" onclick="hesabiBol()">Hesabı Böl</button>
          <button class="pbtn secondary" onclick="openTransfer()">⇔ Transfer</button>
          <button class="pbtn secondary" onclick="printFis()"> Fiş</button>
        </div>
        <div class="danger-btns">
          <button class="dbtn warn" onclick="adisyonuTemizle()"> Temizle</button>
          <button class="dbtn danger" onclick="masayiSil()"> Masayı Sil</button>
        </div>
      </div>
    </div>

    <!-- Mobil sekme bar -->
    <div class="pos-tabs">
      <button class="pos-tab active" data-tab="products" onclick="switchPosTab('products')">
        <span class="tab-icon">🍽️</span>
        <span>Ürünler</span>
      </button>
      <button class="pos-tab" data-tab="orders" onclick="switchPosTab('orders')">
        <span class="tab-icon">🧾</span>
        <span>Adisyon</span>
        <span class="tab-count" id="tabOrderCount">0</span>
      </button>
    </div>
  </div>
</div>

<!--  Masa Ekle Modal  -->
<!--  Kalem Notu Modal  -->
<div class="modal-bg" id="modal-itemNote">
  <div class="modal" style="width:320px;max-width:95vw">
    <h3 id="noteModalTitle">Kalem Notu</h3>
    <div class="note-chips" id="noteChips"></div>
    <div class="note-add-row">
      <input type="text" id="noteChipInput" class="note-add-input" placeholder="Yeni kısayol ekle..." maxlength="30"
        onkeydown="if(event.key==='Enter'){event.preventDefault();addNoteChipFromInput();}">
      <button type="button" class="note-add-btn" onclick="addNoteChipFromInput()">+ Ekle</button>
    </div>
    <textarea id="noteInput" class="form-control" rows="2" placeholder="Serbest not..." style="resize:none;font-size:.8rem"></textarea>
    <div class="modal-actions">
      <button class="btn-cancel" onclick="closeModal('itemNote')">&#x2715; Vazgeç</button>
      <button class="btn-ok" onclick="saveItemNote()">&#x2714; Kaydet</button>
    </div>
  </div>
</div>

<div class="modal-bg" id="modal-addTable">
  <div class="modal">
    <h3>+ Masa Ekle</h3>
    <input id="newMasaName" type="text" class="form-control" placeholder="Masa adı...">
    <div class="modal-actions">
      <button class="btn-cancel" onclick="closeModal('addTable')">İptal</button>
      <button class="btn-ok" onclick="doAddTable()">Ekle</button>
    </div>
  </div>
</div>

<!--  Ad Değiştir Modal  -->
<div class="modal-bg" id="modal-renameTable">
  <div class="modal">
    <h3> Ad Değiştir</h3>
    <input id="renameMasaName" type="text" class="form-control" placeholder="Yeni masa adı">
    <div class="modal-actions">
      <button class="btn-cancel" onclick="closeModal('renameTable')">İptal</button>
      <button class="btn-ok" onclick="doRenameTable()">Kaydet</button>
    </div>
  </div>
</div>

<!--  Hesabı Böl Modal  -->
<div class="modal-bg" id="modal-hesabiBol">
  <div class="modal">
    <h3> Hesabı Böl</h3>
    <div class="fg"><label>Kişi Sayısı</label>
      <input id="bolKisi" type="number" class="form-control" value="2" min="2" max="20">
    </div>
    <div id="bolResult" style="color:var(--primary);font-size:.82rem;margin-bottom:14px;font-weight:700;min-height:20px"></div>
    <div class="modal-actions">
      <button class="btn-cancel" onclick="closeModal('hesabiBol')">Kapat</button>
      <button class="btn-ok" onclick="calcBol()">Hesapla</button>
    </div>
  </div>
</div>

<!-- Gizli ayar alanları (JS tarafından okunur) -->
<div style="display:none" aria-hidden="true">
  <input id="kdv" type="number" value="0">
  <input id="servis" type="number" value="0">
  <select id="indirimTipi"><option value="Yok">Yok</option><option value="Tutar">Tutar</option><option value="Yuzde">Yüzde</option></select>
  <input id="indirimDeger" type="number" value="0">
  <input id="isletmeAdi" type="text" value="Dewo Caffe">
</div>

<!-- ── Hazır Bildirim Banner ── -->
<div class="notif-banner" id="notifBanner">
  <div class="nb-title" id="notifTitle">✅ Mutfaktan Hazır!</div>
  <div class="nb-items" id="notifItems"></div>
  <div class="nb-footer">
    <span class="nb-queue" id="notifQueue"></span>
    <button class="nb-ok" onclick="closeNotif()">Tamam ✓</button>
  </div>
</div>

<!-- Rapor Modal -->
<div class="modal-bg" id="modal-rapor">
  <div class="modal" style="width:560px;max-width:96vw">
    <h3>&#128202; Satış Raporu</h3>
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
      <input type="date" id="raporDate" class="form-control" style="flex:1;max-width:160px" onchange="loadRapor()">
      <button class="tb-btn" onclick="document.getElementById('raporDate').value=todayStr();loadRapor()">Bugün</button>
    </div>
    <div id="raporContent" style="max-height:420px;overflow-y:auto;font-size:.8rem"></div>
    <div class="modal-actions" style="margin-top:12px">
      <button class="btn-cancel" onclick="closeModal('rapor')">Kapat</button>
    </div>
  </div>
</div>
<!-- Geçmiş Modal -->
<div class="modal-bg" id="modal-gecmis">
  <div class="modal" style="width:580px;max-width:96vw">
    <h3>&#128203; Ödeme Geçmişi</h3>
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
      <input type="date" id="gecmisDate" class="form-control" style="flex:1;max-width:160px" onchange="loadGecmis()">
      <button class="tb-btn" onclick="document.getElementById('gecmisDate').value=todayStr();loadGecmis()">Bugün</button>
    </div>
    <div id="gecmisContent" style="max-height:420px;overflow-y:auto;font-size:.8rem"></div>
    <div class="modal-actions" style="margin-top:12px">
      <button class="btn-cancel" onclick="closeModal('gecmis')">Kapat</button>
    </div>
  </div>
</div>
<!-- Ürün Yönetimi Modal -->
<div class="modal-bg" id="modal-urunler">
  <div class="modal" style="width:660px;max-width:96vw">
    <h3>&#127869; Ürün Yönetimi</h3>
    <!-- Sekmeler -->
    <div style="display:flex;gap:0;border-bottom:1px solid var(--border);margin-bottom:12px">
      <button id="tab-urunler-btn" onclick="switchUrunTab('urunler')"
        style="flex:1;padding:8px 0;font-size:.78rem;font-weight:700;border:none;background:none;cursor:pointer;border-bottom:2px solid var(--primary);color:var(--primary)">
        Ürünler
      </button>
      <button id="tab-catimg-btn" onclick="switchUrunTab('catimg')"
        style="flex:1;padding:8px 0;font-size:.78rem;font-weight:700;border:none;background:none;cursor:pointer;border-bottom:2px solid transparent;color:var(--muted2)">
        📷 Menü Kategori Görselleri
      </button>
    </div>
    <!-- Ürünler sekmesi -->
    <div id="tab-urunler">
      <div style="margin-bottom:10px">
        <button class="tb-btn primary" onclick="openAddUrun()">+ Yeni Ürün</button>
      </div>
      <div id="urunlerList" style="max-height:400px;overflow-y:auto"></div>
    </div>
    <!-- Kategori Görselleri sekmesi -->
    <div id="tab-catimg" style="display:none">
      <p style="font-size:.75rem;color:var(--muted2);margin-bottom:12px;line-height:1.5">
        Her kategori için bir arka plan görseli yükleyin. Müşterilerin gördüğü QR menüde kategori kartlarında görünür.
      </p>
      <div id="catImgList" style="max-height:420px;overflow-y:auto;display:flex;flex-direction:column;gap:8px"></div>
    </div>
    <div class="modal-actions" style="margin-top:12px">
      <button class="btn-cancel" onclick="closeModal('urunler')">Kapat</button>
    </div>
  </div>
</div>
<!-- Ürün Ekle/Düzenle Modal -->
<div class="modal-bg" id="modal-urunForm">
  <div class="modal" style="width:400px">
    <h3 id="urunFormTitle">+ Yeni Ürün</h3>
    <input id="urunId" type="hidden" value="">
    <input id="urunImageClear" type="hidden" value="0">
    <div class="fg"><label>Ürün Adı</label>
      <input id="urunAdi" type="text" class="form-control" placeholder="Ürün adı...">
    </div>
    <div class="row2">
      <div class="fg"><label>Fiyat (₺)</label>
        <input id="urunFiyat" type="number" class="form-control" placeholder="0.00" step="0.01" min="0">
      </div>
      <div class="fg"><label>Kategori</label>
        <input id="urunKategori" type="text" class="form-control" placeholder="Kahve, Yiyecek...">
      </div>
    </div>
    <!-- Görsel -->
    <div class="fg">
      <label>Görsel</label>
      <div id="imgPreviewWrap" style="display:none;margin-bottom:7px">
        <img id="imgPreview" src="" alt="" style="width:100%;max-height:130px;object-fit:cover;border-radius:7px;border:1px solid var(--border)">
        <button onclick="clearImage()" style="margin-top:4px;font-size:.7rem;color:var(--red);background:none;border:none;cursor:pointer">✕ Görseli kaldır</button>
      </div>
      <!-- Upload buton -->
      <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:9px 12px;background:var(--s3);border:1px dashed var(--border);border-radius:7px;transition:border-color .15s" id="uploadLabel">
        <span style="font-size:1.2rem">📷</span>
        <span id="uploadLabelText" style="font-size:.78rem;color:var(--muted2)">Fotoğ ekle (bilgisayar / telefon)</span>
        <input id="urunDosya" type="file" accept="image/*" style="display:none" onchange="previewImage(this)">
      </label>
      <div style="display:flex;align-items:center;gap:8px;margin-top:7px">
        <div style="flex:1;height:1px;background:var(--border)"></div>
        <span style="font-size:.68rem;color:var(--muted)">veya link yapıştır</span>
        <div style="flex:1;height:1px;background:var(--border)"></div>
      </div>
      <input id="urunGorsel" type="url" class="form-control" placeholder="https://...görsel.jpg" style="margin-top:7px" oninput="previewUrl(this.value)">
    </div>
    <div class="modal-actions">
      <button class="btn-cancel" onclick="closeModal('urunForm')">İptal</button>
      <button class="btn-ok" onclick="saveUrun()">Kaydet</button>
    </div>
  </div>
</div>
<!-- Masa Transfer Modal -->
<div class="modal-bg" id="modal-transfer">
  <div class="modal" style="width:360px">
    <h3>⇔ Masa Transfer</h3>
    <p style="font-size:.75rem;color:var(--muted2);margin-bottom:14px">Tüm siparişi başka masaya taşı</p>
    <div class="fg"><label>Hedef Masa</label>
      <select id="transferTarget" class="form-control">
        <option value="">Masa seçin...</option>
      </select>
    </div>
    <div class="modal-actions">
      <button class="btn-cancel" onclick="closeModal('transfer')">İptal</button>
      <button class="btn-ok" onclick="doTransfer()">Transfer Et</button>
    </div>
  </div>
</div>
<!-- QR Menü Modal -->
<div class="modal-bg" id="modal-qrmenu">
  <div class="modal" style="width:340px;text-align:center">
    <h3>&#128241; QR Menü</h3>
    <p style="font-size:.75rem;color:var(--muted2);margin-bottom:14px">Müşteriler bu kodu okutarak menüye ulaşabilir</p>
    <div style="background:#fff;border-radius:10px;padding:14px;display:inline-block;margin-bottom:12px">
      <img id="qrImg" src="" alt="QR" style="width:220px;height:220px;display:block">
    </div>
    <div id="qrUrl" style="font-size:.68rem;color:var(--muted2);word-break:break-all;margin-bottom:14px;background:var(--s3);border-radius:6px;padding:7px 10px"></div>
    <div class="modal-actions">
      <button class="btn-cancel" onclick="closeModal('qrmenu')">Kapat</button>
      <button class="btn-ok" onclick="printQr()">&#128438; Yazdır</button>
    </div>
  </div>
</div>
<!-- Ayarlar Modal -->
<div class="modal-bg" id="modal-ayarlar">
  <div class="modal" style="width:380px;max-height:90vh;overflow-y:auto">
    <h3>&#9881;&#65039; Ayarlar</h3>

    <div class="fg">
      <label>İşletme Adı</label>
      <input id="ayarIsletme" type="text" class="form-control" placeholder="Cafe adı...">
    </div>

    <hr class="theme-divider">

    <!-- TEMA -->
    <div class="theme-section">
      <div class="theme-section-label">🎨 Tema</div>

      <!-- Mod -->
      <div class="theme-section-label" style="margin-top:0;margin-bottom:5px;font-size:.6rem">GÖRÜNÜM MODU</div>
      <div class="mode-toggle" id="modeBtns">
        <button class="mode-btn" data-mode="dark" onclick="setThemeMode('dark')">
          <span class="mode-icon">🌙</span>Karanlık
        </button>
        <button class="mode-btn" data-mode="light" onclick="setThemeMode('light')">
          <span class="mode-icon">☀️</span>Aydınlık
        </button>
      </div>

      <!-- Renk -->
      <div class="theme-section-label" style="margin-top:10px;margin-bottom:5px;font-size:.6rem">TEMA RENGİ</div>
      <div class="color-palette" id="colorPalette">
        <div class="color-swatch" data-color="#27A0B1" style="background:#27A0B1" title="Cyan (Varsayılan)" onclick="setAccentColor('#27A0B1',this)"></div>
        <div class="color-swatch" data-color="#3b82f6" style="background:#3b82f6" title="Mavi" onclick="setAccentColor('#3b82f6',this)"></div>
        <div class="color-swatch" data-color="#8b5cf6" style="background:#8b5cf6" title="Mor" onclick="setAccentColor('#8b5cf6',this)"></div>
        <div class="color-swatch" data-color="#ec4899" style="background:#ec4899" title="Pembe" onclick="setAccentColor('#ec4899',this)"></div>
        <div class="color-swatch" data-color="#f59e0b" style="background:#f59e0b" title="Turuncu" onclick="setAccentColor('#f59e0b',this)"></div>
        <div class="color-swatch" data-color="#10b981" style="background:#10b981" title="Yeşil" onclick="setAccentColor('#10b981',this)"></div>
        <div class="color-swatch" data-color="#ef4444" style="background:#ef4444" title="Kırmızı" onclick="setAccentColor('#ef4444',this)"></div>
        <div class="color-swatch" data-color="#eab308" style="background:#eab308" title="Sarı" onclick="setAccentColor('#eab308',this)"></div>
      </div>
      <div class="color-custom-wrap">
        <span class="color-custom-label">Özel renk:</span>
        <input type="color" id="customAccentInput" class="color-custom-input" value="#27A0B1" oninput="setAccentColor(this.value,null,true)">
        <span id="customAccentHex" style="font-size:.68rem;color:var(--muted2);">#27A0B1</span>
      </div>
    </div>

    <hr class="theme-divider">

    <!-- MENÜ RENK AYARLARI -->
    <div class="theme-section">
      <div class="theme-section-label">&#127860; Menü Görünümü</div>

      <div class="theme-section-label" style="margin-top:0;margin-bottom:6px;font-size:.6rem">HAZIR TEMA</div>
      <div style="display:flex;gap:7px;flex-wrap:wrap;margin-bottom:12px;" id="menuPresetBtns">
        <button class="menu-preset" data-preset="warm"  onclick="applyMenuPreset('warm',this)"  style="background:linear-gradient(135deg,#d4b08c,#fff);width:30px;height:30px;border:2px solid transparent;border-radius:50%;cursor:pointer;" title="Sıcak"></button>
        <button class="menu-preset" data-preset="dark"  onclick="applyMenuPreset('dark',this)"  style="background:linear-gradient(135deg,#1a1a2e,#16213e);width:30px;height:30px;border:2px solid transparent;border-radius:50%;cursor:pointer;" title="Koyu"></button>
        <button class="menu-preset" data-preset="green" onclick="applyMenuPreset('green',this)" style="background:linear-gradient(135deg,#c8e6c9,#fff);width:30px;height:30px;border:2px solid transparent;border-radius:50%;cursor:pointer;" title="Doğa"></button>
        <button class="menu-preset" data-preset="pink"  onclick="applyMenuPreset('pink',this)"  style="background:linear-gradient(135deg,#fce4ec,#fff);width:30px;height:30px;border:2px solid transparent;border-radius:50%;cursor:pointer;" title="Pembe"></button>
        <button class="menu-preset" data-preset="blue"  onclick="applyMenuPreset('blue',this)"  style="background:linear-gradient(135deg,#bbdefb,#fff);width:30px;height:30px;border:2px solid transparent;border-radius:50%;cursor:pointer;" title="Mavi"></button>
        <button class="menu-preset" data-preset="light" onclick="applyMenuPreset('light',this)" style="background:linear-gradient(135deg,#f5f5f5,#fff);width:30px;height:30px;border:2px solid transparent;border-radius:50%;cursor:pointer;" title="Beyaz"></button>
      </div>

      <div style="display:flex;flex-direction:column;gap:9px;">
        <div style="display:flex;align-items:center;justify-content:space-between;">
          <label style="font-size:.75rem;font-weight:600;">Arka Plan</label>
          <input type="color" id="mcp-bg"      value="#d4b08c" style="width:36px;height:28px;border:none;border-radius:8px;cursor:pointer;padding:2px;">
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;">
          <label style="font-size:.75rem;font-weight:600;">Kart Rengi</label>
          <input type="color" id="mcp-surface"  value="#ffffff" style="width:36px;height:28px;border:none;border-radius:8px;cursor:pointer;padding:2px;">
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;">
          <label style="font-size:.75rem;font-weight:600;">Vurgu / Fiyat</label>
          <input type="color" id="mcp-primary"  value="#c8922a" style="width:36px;height:28px;border:none;border-radius:8px;cursor:pointer;padding:2px;">
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;">
          <label style="font-size:.75rem;font-weight:600;">Yazı Rengi</label>
          <input type="color" id="mcp-text"     value="#1a1a1a" style="width:36px;height:28px;border:none;border-radius:8px;cursor:pointer;padding:2px;">
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;">
          <label style="font-size:.75rem;font-weight:600;">Kenar / Boşluk</label>
          <input type="color" id="mcp-border"   value="#e8ddd0" style="width:36px;height:28px;border:none;border-radius:8px;cursor:pointer;padding:2px;">
        </div>
      </div>
    </div>

    <div class="modal-actions">
      <button class="btn-cancel" onclick="closeModal('ayarlar')">İptal</button>
      <button class="btn-ok" onclick="saveSettings()">Kaydet</button>
    </div>
  </div>
</div>
<div class="toast" id="toast"></div>

<script>
const CSRF = '<?php echo e(csrf_token()); ?>';
let selectedRoomId = null;
let currentItems = [];
let currentOrder = null;
let seenReadyIds = new Set();
let pollInterval = null;
let notifQueue    = [];   // [{roomId, roomName, items}]
let notifShowing  = false;
let currentNotifRoomId  = null;
let currentNotifItemIds = [];

//  AJAX 
async function api(url, method='GET', data=null) {
  const r = await fetch(url, {
    method,
    headers: {'Accept':'application/json','X-CSRF-TOKEN':CSRF,...(data&&method!=='GET'?{'Content-Type':'application/json'}:{})},
    body: data && method !== 'GET' ? JSON.stringify(data) : undefined,
  });
  const json = await r.json().catch(() => ({error: 'Sunucu hatası ('+ r.status +')'}));
  if (!r.ok && !json.error) json.error = 'HTTP ' + r.status;
  return json;
}

function toast(msg,ms=2200){
  const el=document.getElementById('toast');
  el.textContent=msg;el.classList.add('show');
  setTimeout(()=>el.classList.remove('show'),ms);
}
function showModal(n){document.getElementById('modal-'+n).classList.add('open')}
function closeModal(n){document.getElementById('modal-'+n).classList.remove('open')}

//  Screen switching 

// Grid sabit boyutlu — resize gerekmez
function resizeGrid() { /* no-op: sabit 180px kart, grid auto-fill */ }
window.addEventListener('resize', resizeGrid);
document.addEventListener('DOMContentLoaded', () => {
  requestAnimationFrame(resizeGrid);
  startKitchenPoll();
  refreshProdGrid();
});

// ─────────────────────────────────────────────────────────────────────────────

// ─── Kitchen polling ───────────────────────────────────────────
// ─── Tüm masaları dinle ───────────────────────────────────────────
async function pollAllRooms() {
  const cards = document.querySelectorAll('.tcard[data-id]');
  for (const card of cards) {
    const id = card.dataset.id;
    try {
      const d = await api(`/adisyon/masa/${id}/ready-check`);
      const allReady = d.ready_items || [];
      card.classList.toggle('has-ready', allReady.length > 0);
      const newItems = allReady.filter(i => !seenReadyIds.has(i.id));
      if (newItems.length) {
        newItems.forEach(i => seenReadyIds.add(i.id));
        notifQueue.push({ roomId: parseInt(id), roomName: d.room_name, items: newItems });
      }
    } catch(e) {}
  }
  if (!notifShowing) showNextNotif();
}
function showNextNotif() {
  if (!notifQueue.length) return;
  notifShowing = true;
  const { roomId, roomName, items } = notifQueue.shift();
  currentNotifRoomId  = roomId;
  currentNotifItemIds = items.map(i => i.id);
  document.getElementById('notifTitle').textContent = '\u2705 ' + roomName + ' \u2014 Haz\u0131r!';
  document.getElementById('notifItems').innerHTML = items.map(i => '\u2022 ' + esc(i.name) + ' \u00d7' + i.quantity).join('<br>');
  const q = notifQueue.length;
  document.getElementById('notifQueue').textContent = q > 0 ? `+${q} daha bekliyor` : '';
  document.getElementById('notifBanner').classList.add('show');
}
async function closeNotif() {
  document.getElementById('notifBanner').classList.remove('show');
  // Backend'de o item'ları 'notified' olarak işaretle → bir daha geri gelmez
  if (currentNotifRoomId && currentNotifItemIds.length) {
    try {
      await api(`/adisyon/masa/${currentNotifRoomId}/notified`, 'POST', { item_ids: currentNotifItemIds });
      const rc = document.querySelector(`.tcard[data-id="${currentNotifRoomId}"]`);
      if (rc) rc.classList.remove('has-ready');
    } catch(e) {}
  }
  currentNotifRoomId  = null;
  currentNotifItemIds = [];
  notifShowing = false;
  setTimeout(showNextNotif, 450);
}
function startKitchenPoll() {
  stopKitchenPoll();
  pollInterval = setInterval(pollAllRooms, 5000);
}
function stopKitchenPoll() {
  if (pollInterval) { clearInterval(pollInterval); pollInterval = null; }
}
// ──────────────────────────────────────────────────────────────────
// ────────────────────────────────────────────────────────────────

function goMasalar() {
  document.getElementById('screen-masalar').classList.add('active');
  document.getElementById('screen-pos').classList.remove('active');
  document.getElementById('topTitle').textContent = 'Masalar';
  requestAnimationFrame(resizeGrid);
  startKitchenPoll(); // Poll kesintisiz devam etsin
}

function goPOS(roomName) {
  document.getElementById('screen-masalar').classList.remove('active');
  document.getElementById('screen-pos').classList.add('active');
  document.getElementById('topTitle').textContent = roomName;
  // Mobilde ürünler sekmesinden başlat
  if (window.innerWidth <= 640) {
    document.querySelector('.pane-mid').classList.add('mob-active');
    document.querySelector('.pane-right').classList.remove('mob-active');
    document.querySelectorAll('.pos-tab').forEach(t => t.classList.toggle('active', t.dataset.tab === 'products'));
  } else {
    document.querySelector('.pane-mid').classList.remove('mob-active');
    document.querySelector('.pane-right').classList.remove('mob-active');
  }
}

//  Open table  go to POS 
async function openMasa(id, el) {
  selectedRoomId = id;
  // seenReadyIds temizlemiyoruz — onaylanmış bildirimler tekrar gelmesin
  document.querySelectorAll('.tcard').forEach(c=>c.classList.remove('selected'));
  el.classList.add('selected');
  goPOS(el.dataset.name);
  const d = await api(`/adisyon/masa/${id}/data`);
  renderOrder(d);
}

//  Render order 
function renderOrder(data) {
  currentOrder = data.order;
  currentItems = data.items || [];
  const room = data.room;

  const meta = room?.status==='open' ? `Açık  ${room.opened_at||''}` : (room?'Kapalı':'');
  document.getElementById('orderMeta').textContent = meta;
  document.getElementById('orderTitle').textContent = room?.name || '';

  const container = document.getElementById('orderItems');
  if (!currentItems.length) {
    container.innerHTML = '<div class="no-table"></div>';
  } else {
    container.innerHTML = currentItems.map(item=>{
      const q = item.quantity;
      const qLabel = q % 1 === 0 ? q : q.toFixed(1);
      const noteLine = item.note ? `<span class="oitem-note">${esc(item.note)}</span>` : '';
      return `
      <div class="oitem" data-item-id="${item.id}">
        <div class="oitem-info">
          <strong>${esc(item.name)}</strong>
          <small>${esc(item.category)} &nbsp;${fmt(item.price)} ₺</small>
          ${noteLine}
        </div>
        <div class="qty-ctrl">
          <button onclick="changeQty(${item.id},${q-0.5})">-</button>
          <span>${qLabel}</span>
          <button onclick="changeQty(${item.id},${q+0.5})">+</button>
        </div>
        <button class="oitem-note-btn${item.note?' has-note':''}" onclick="openNoteModal(${item.id})" title="Not ekle">💬</button>
        <button class="oitem-del" onclick="removeItem(${item.id})">✕</button>
      </div>`;
    }).join('');
  }

  const sub = currentItems.reduce((s,i)=>s+i.total,0);
  const kdvP  = parseFloat(document.getElementById('kdv').value)||0;
  const serP  = parseFloat(document.getElementById('servis').value)||0;
  const indT  = document.getElementById('indirimTipi').value;
  const indV  = parseFloat(document.getElementById('indirimDeger').value)||0;
  let indirim = 0;
  if(indT==='Tutar') indirim=indV;
  if(indT==='Yuzde') indirim=sub*indV/100;
  const serAmt = sub*serP/100;
  const kdvAmt = (sub+serAmt-indirim)*kdvP/100;
  const total  = sub+serAmt-indirim+kdvAmt;

  document.getElementById('orderTotals').style.display = 'block';
  // Mobilde varsayılan kapalı
  if(window.innerWidth <= 640){
    document.getElementById('orderTotals').classList.add('collapsed');
  } else {
    document.getElementById('orderTotals').classList.remove('collapsed');
  }
  document.getElementById('paymentArea').style.display  = 'flex';
  document.getElementById('araToplam').textContent  = fmt(sub)+' ₺';
  document.getElementById('servisLabel').textContent = serP>0?`Servis (${serP}%)`:'Servis';
  document.getElementById('servisAmt').textContent  = fmt(serAmt)+' ₺';
  document.getElementById('indirimAmt').textContent = '- '+fmt(indirim)+' ₺';
  document.getElementById('kdvLabel').textContent   = kdvP>0?`KDV (${kdvP}%)`:'KDV';
  document.getElementById('kdvAmt').textContent     = fmt(kdvAmt)+' ₺';
  document.getElementById('genelToplam').textContent= fmt(total)+' ₺';
  const lbl = document.getElementById('totalToggleLabel');
  if(lbl) lbl.textContent = 'Toplam: ' + fmt(total) + ' ₺';
  const paid = parseFloat(document.getElementById('alinanTutar').value)||0;
  const effectivePaid = paid > 0 ? paid : total;
  document.getElementById('odenenAmt').textContent  = fmt(effectivePaid)+' ₺';
  document.getElementById('kalanAmt').textContent   = fmt(Math.max(0,total-effectivePaid))+' ₺';

  if(room) updateTableCard(room);
  document.getElementById('orderNote').value = data.order?.note || '';
  // Fire button
  const draftCount = currentItems.filter(i => i.kitchen_status === 'draft').length;
  const btnFire = document.getElementById('btnFire');
  if(btnFire){
    btnFire.disabled = draftCount === 0;
    btnFire.innerHTML = draftCount > 0 ? `🍳 Mutfağa Gönder (${draftCount})` : '🍳 Mutfağa Gönder';
  }
  // Mobil adisyon sekmesi badge güncelle
  const cnt = currentItems.reduce((s,i) => s + i.quantity, 0);
  const badge = document.getElementById('tabOrderCount');
  if (badge) { badge.textContent = cnt > 0 ? cnt : 0; badge.classList.toggle('visible', cnt > 0); }
}

function fmt(n){return parseFloat(n||0).toFixed(2)}
const esc=s=>String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

//  Products 
async function addProduct(productId, cardEl) {
  if(!selectedRoomId){toast(' Önce bir masa seçin');return}
  if(cardEl){cardEl.classList.add('flash');setTimeout(()=>cardEl.classList.remove('flash'),350)}
  const d = await api(`/adisyon/masa/${selectedRoomId}/ekle`,'POST',{product_id:productId,adet:1});
  if(d.error){toast(' '+d.error);return}
  renderOrder(d);
  const oi=document.getElementById('orderItems');
  oi.scrollTop=oi.scrollHeight;
}

// ── Kalem notu ──
const _defChips=['Çok pişmiş','Az pişmiş','Acısız','Ekstra sos','Az yağlı','Soğansız','İyi pişmiş','Az tuzlu'];
let _chips=JSON.parse(localStorage.getItem('kafe_chips')||'null')||[..._defChips];
let _noteItemId=null;
function _renderChips(curNote){
  const box=document.getElementById('noteChips');
  if(!box)return;
  const active=(curNote||'').split(',').map(s=>s.trim()).filter(Boolean);
  box.innerHTML=_chips.map(c=>{
    const on=active.includes(c);
    const q=JSON.stringify(c);
    return `<div class="chip-wrap${on?' active':''}">`
      +`<button type="button" class="chip-label" onclick='_chipToggle(${q})'>${esc(c)}</button>`
      +`<button type="button" class="chip-del" onclick='_chipDel(${q})'>&#x2715;</button>`
      +`</div>`;
  }).join('');
}
function _chipToggle(text){
  const ta=document.getElementById('noteInput');
  let parts=ta.value.split(',').map(s=>s.trim()).filter(Boolean);
  const i=parts.indexOf(text);
  if(i>=0)parts.splice(i,1);else parts.push(text);
  ta.value=parts.join(', ');
  _renderChips(ta.value);
}
function _chipDel(text){
  _chips=_chips.filter(c=>c!==text);
  localStorage.setItem('kafe_chips',JSON.stringify(_chips));
  _renderChips(document.getElementById('noteInput').value);
}
function addNoteChipFromInput(){
  const inp=document.getElementById('noteChipInput');
  const t=inp.value.trim();
  if(!t)return;
  if(!_chips.includes(t)){_chips.push(t);localStorage.setItem('kafe_chips',JSON.stringify(_chips));}
  _chipToggle(t);
  inp.value='';
}
function openNoteModal(itemId){
  _noteItemId=itemId;
  const item=currentItems.find(i=>i.id===itemId);
  if(!item)return;
  document.getElementById('noteModalTitle').textContent=item.name;
  document.getElementById('noteInput').value=item.note||'';
  document.getElementById('noteChipInput').value='';
  _renderChips(item.note||'');
  showModal('itemNote');
}
async function saveItemNote(){
  if(!_noteItemId||!selectedRoomId)return;
  const note=document.getElementById('noteInput').value.trim();
  const d=await api(`/adisyon/masa/${selectedRoomId}/item-note`,'POST',{item_id:_noteItemId,note});
  if(d&&!d.error){currentItems=d.items;renderOrder(d);}
  closeModal('itemNote');
}
async function removeItem(itemId){
  if(!selectedRoomId)return;
  const d=await api(`/adisyon/masa/${selectedRoomId}/sil-item`,'POST',{item_id:itemId});
  renderOrder(d);
}

async function changeQty(itemId,newQty){
  if(!selectedRoomId)return;
  if(newQty<0.5){removeItem(itemId);return}
  const d=await api(`/adisyon/masa/${selectedRoomId}/qty`,'POST',{item_id:itemId,qty:newQty});
  renderOrder(d);
}

//  Order actions 
async function adisyonuTemizle(){
  if(!selectedRoomId)return;
  if(!confirm('Tüm sipariş silinsin mi?'))return;
  const d=await api(`/adisyon/masa/${selectedRoomId}/temizle`,'POST');
  renderOrder(d);toast(' Temizlendi');
}

async function masayiSil(){
  if(!selectedRoomId)return;
  if(!confirm('Masa silinsin mi?'))return;
  await api(`/adisyon/masa/${selectedRoomId}`,'DELETE');
  document.querySelector(`.tcard[data-id="${selectedRoomId}"]`)?.remove();
  resizeGrid();
  selectedRoomId=null;
  goMasalar();
  toast(' Masa silindi');
}

async function odemeAl(){
  if(!selectedRoomId){toast(' Masa seçili değil');return}
  if(!currentItems.length){toast(' Sipariş boş');return}
  if(!confirm('Ödeme alınsın mı?'))return;
  try {
    const d=await api(`/adisyon/masa/${selectedRoomId}/odeme`,'POST',{
      payment_type:document.getElementById('odemeTipi').value,
      paid:parseFloat(document.getElementById('alinanTutar').value)||0,
      kdv:parseFloat(document.getElementById('kdv').value)||0,
      servis:parseFloat(document.getElementById('servis').value)||0,
      indirim_tipi:document.getElementById('indirimTipi').value,
      indirim:parseFloat(document.getElementById('indirimDeger').value)||0,
    });
    if(d.success){
      updateTableCard(d.room);
      currentItems=[];currentOrder=null;
      document.getElementById('orderItems').innerHTML='<div class="no-table"></div>';
      document.getElementById('orderTotals').style.display='none';
      document.getElementById('paymentArea').style.display='none';
      document.getElementById('alinanTutar').value='';
      document.getElementById('orderNote').value='';
      toast('✓ Ödeme alındı');
      setTimeout(goMasalar, 1200);
    } else {
      toast('⚠️ ' + (d.error || d.message || 'Ödeme alınamadı'));
    }
  } catch(e) {
    toast('⚠️ Bağlantı hatası: ' + e.message);
  }
}

//  Table card update 
function updateTableCard(room){
  const el=document.querySelector(`.tcard[data-id="${room.id}"]`);
  if(!el)return;
  el.dataset.status=room.status;
  el.classList.toggle('occupied',room.status==='open');
  el.classList.toggle('has-ready',!!room.has_ready);
  // İsim
  const nameEl=el.querySelector('.tcard-name');
  if(nameEl) nameEl.textContent=room.name;
  // Statüs pill
  const pill=el.querySelector('.tcard-status-pill');
  if(pill){
    pill.className='tcard-status-pill '+(room.status==='open'?'open':'closed');
    pill.innerHTML='<span class="pill-dot"></span>'+(room.status==='open'?'AÇIK':'KAPALI');
  }
  // Bilgi alanı
  const info=el.querySelector('.tcard-info');
  if(info){
    if(room.item_count>0){
      info.innerHTML=
        '<div class="tcard-item-row"><span class="tcard-item-count">'+room.item_count+' ürün</span></div>'+
        '<div class="tcard-amount">'+fmt(room.total)+' ₺</div>'+
        '<div class="tcard-amount-label">Güncel tutar</div>'+
        (room.has_ready?'<div class="tcard-ready-badge">⚡ Mutfaktan hazır</div>':'');
    } else {
      info.innerHTML='<div class="tcard-empty-label">Sipariş yok</div>';
    }
  }
  // Rename butonunu güncelle
  const renameBtn=el.querySelector('.tcard-act-btn:first-child');
  if(renameBtn) renameBtn.setAttribute('onclick',`selectAndRename(${room.id},'${room.name.replace(/'/g,"\\'")}')` );
}

//  Table management 
async function doAddTable(){
  const name=document.getElementById('newMasaName').value.trim();
  if(!name){toast(' Masa adı girin');return}
  const d=await api('/adisyon/masa-olustur','POST',{name});
  if(d.success){
    const grid=document.getElementById('masalarGrid');
    const div=document.createElement('div');
    div.className='tcard';
    div.dataset.id=d.room.id;div.dataset.name=d.room.name;div.dataset.status='closed';
    div.innerHTML=
      '<div class="tcard-stripe"></div>'+
      '<div class="tcard-inner">'+
        '<div class="tcard-head">'+
          '<div class="tcard-name">'+esc(d.room.name)+'</div>'+
          '<div class="tcard-status-pill closed"><span class="pill-dot"></span>KAPALI</div>'+
        '</div>'+
        '<div class="tcard-info"><div class="tcard-empty-label">Sipariş yok</div></div>'+
      '</div>'+
      '<div class="tcard-actions" onclick="event.stopPropagation()">'+
        '<button class="tcard-act-btn" onclick="selectAndRename('+d.room.id+',\''+esc(d.room.name)+'\')">✎ Adlandır</button>'+
        '<button class="tcard-act-btn" onclick="toggleMasaDirect('+d.room.id+')">⏻ Aç/Kapat</button>'+
      '</div>';
    div.onclick=()=>openMasa(d.room.id,div);
    grid.appendChild(div);
    resizeGrid();
    document.getElementById('newMasaName').value='';
    closeModal('addTable');
    toast(' Masa eklendi');
  }
}

async function doRenameTable(){
  if(!selectedRoomId){toast(' Önce masa seçin');return}
  const name=document.getElementById('renameMasaName').value.trim();
  if(!name)return;
  const d=await api(`/adisyon/masa/${selectedRoomId}/rename`,'POST',{name});
  if(d.success){
    updateTableCard(d.room);
    // POS ekranındaysa başlığı güncelle
    if(document.getElementById('screen-pos').classList.contains('active')){
      document.getElementById('topTitle').textContent=d.room.name;
    }
    closeModal('renameTable');toast(' Ad değiştirildi');
  }
}

async function toggleMasaDirect(id) {
  const d = await api(`/adisyon/masa/${id}/toggle`, 'POST');
  if (d.success) { updateTableCard(d.room); toast('Masa durumu güncellendi'); }
}

function selectAndRename(id, name) {
  selectedRoomId = id;
  document.getElementById('renameMasaName').value = name;
  showModal('renameTable');
}

//  Filter 
function filterCat(cat,el){
  document.querySelectorAll('.ctab').forEach(t=>t.classList.remove('active'));
  el.classList.add('active');
  document.querySelectorAll('.pcard').forEach(c=>{c.style.display=(cat==='all'||c.dataset.cat===cat)?'':('none')});
  document.getElementById('prodSearch').value='';
}
function filterProds(){
  const q=document.getElementById('prodSearch').value.toLowerCase();
  document.querySelectorAll('.pcard').forEach(c=>{c.style.display=(!q||c.dataset.name.includes(q))?'':'none'});
  if(q)document.querySelectorAll('.ctab').forEach(t=>t.classList.remove('active'));
}

//  Hesabı Böl 
function hesabiBol(){document.getElementById('bolResult').textContent='';showModal('hesabiBol')}
function calcBol(){
  const n=parseInt(document.getElementById('bolKisi').value)||2;
  const sub=currentItems.reduce((s,i)=>s+i.total,0);
  document.getElementById('bolResult').textContent=`Her kişi: ${fmt(sub/n)} ₺`;
}

//  Print 
function printFis(){
  if(!currentItems.length){toast(' Sipariş boş');return}
  const isletme = document.getElementById('isletmeAdi').value || 'Cafe';
  const masa    = document.getElementById('orderTitle').textContent.trim();
  const sub     = currentItems.reduce((s,i)=>s+i.total,0);
  const kdvP    = parseFloat(document.getElementById('kdv').value)||0;
  const serP    = parseFloat(document.getElementById('servis').value)||0;
  const indT    = document.getElementById('indirimTipi').value;
  const indV    = parseFloat(document.getElementById('indirimDeger').value)||0;
  let indirim   = 0;
  if(indT==='Tutar') indirim=indV;
  if(indT==='Yuzde') indirim=sub*indV/100;
  const serAmt  = sub*serP/100;
  const kdvAmt  = (sub+serAmt-indirim)*kdvP/100;
  const total   = sub+serAmt-indirim+kdvAmt;
  const paid    = parseFloat(document.getElementById('alinanTutar').value)||0;
  const eff     = paid > 0 ? paid : total;
  const odeme   = document.getElementById('odemeTipi').value;
  const note    = document.getElementById('orderNote').value.trim();
  const now     = new Date();
  const tarih   = now.toLocaleDateString('tr-TR');
  const saat    = now.toLocaleTimeString('tr-TR', {hour:'2-digit',minute:'2-digit'});
  // helper: pad a string to fixed width
  const W = 32;
  const line  = () => '─'.repeat(W);
  const dline = () => '═'.repeat(W);
  const ctr   = s => { const p=Math.max(0,Math.floor((W-s.length)/2)); return ' '.repeat(p)+s; };
  const row2  = (l,r) => {
    const space = W - l.length - r.length;
    return l + (space>0?' '.repeat(space):' ') + r;
  };
  let items_html = '';
  currentItems.forEach(i => {
    const qty_price = `${i.quantity} x ${fmt(i.price)}`;
    const total_str = `${fmt(i.total)} ₺`;
    items_html += `<div class="item-name">${i.name}</div>`;
    items_html += `<div class="item-row"><span class="qty">${qty_price} ₺</span><span class="price">${total_str}</span></div>`;
  });
  let totals_html = '';
  totals_html += `<div class="total-row"><span>Ara Toplam</span><span>${fmt(sub)} ₺</span></div>`;
  if(serAmt>0) totals_html += `<div class="total-row"><span>Servis (%${serP})</span><span>${fmt(serAmt)} ₺</span></div>`;
  if(indirim>0) totals_html += `<div class="total-row"><span>İndirim</span><span class="neg">-${fmt(indirim)} ₺</span></div>`;
  if(kdvAmt>0) totals_html += `<div class="total-row"><span>KDV (%${kdvP})</span><span>${fmt(kdvAmt)} ₺</span></div>`;
  const html = `<!DOCTYPE html>
<html><head>
<meta charset="utf-8">
<title>Fiş - ${isletme}</title>
<style>
  @media print { body{margin:0} .no-print{display:none} }
  *{box-sizing:border-box;margin:0;padding:0}
  body{
    font-family:'Courier New',Courier,monospace;
    font-size:12.5px;line-height:1.55;
    background:#fff;color:#111;
    width:72mm;margin:0 auto;padding:6mm 4mm 10mm;
  }
  .center{text-align:center}
  .bold{font-weight:bold}
  .big{font-size:15px}
  .small{font-size:10.5px;color:#444}
  .dashed{border:none;border-top:1px dashed #999;margin:5px 0}
  .solid{border:none;border-top:2px solid #111;margin:5px 0}
  .item-name{font-size:12px;padding:3px 0 0;font-weight:600}
  .item-row{display:flex;justify-content:space-between;font-size:12px;padding:0 0 4px 10px;color:#333}
  .item-row .price{font-weight:700;color:#111;white-space:nowrap}
  .total-row{display:flex;justify-content:space-between;padding:2px 0;font-size:12px}
  .total-row .neg{color:#c00}
  .grand{display:flex;justify-content:space-between;padding:4px 0;font-size:16px;font-weight:900}
  .pay-row{display:flex;justify-content:space-between;padding:2px 0;font-size:12px;color:#444}
  .change{display:flex;justify-content:space-between;padding:2px 0;font-size:12px;font-weight:700}
  .note-box{border:1px dashed #999;padding:5px 7px;font-size:11px;margin:6px 0;color:#333;border-radius:3px}
  .footer{text-align:center;font-size:11px;color:#666;margin-top:8px;line-height:1.8}
  .print-btn{display:block;width:100%;margin:10px 0 0;padding:8px;background:#111;color:#fff;border:none;font-size:13px;cursor:pointer;border-radius:4px}
</style>
</head><body>

<div class="center bold big">${isletme}</div>
<div class="center small" style="margin-top:2px">${masa}</div>
<div class="center small">${tarih} &nbsp; Saat: ${saat}</div>

<hr class="solid" style="margin-top:6px">

${items_html}

<hr class="dashed">

${totals_html}

<hr class="solid">
<div class="grand"><span>TOPLAM</span><span>${fmt(total)} ₺</span></div>
<hr class="solid">

<div class="pay-row"><span>Ödeme Şekli</span><span>${odeme}</span></div>
<div class="pay-row"><span>Alınan Tutar</span><span>${fmt(eff)} ₺</span></div>
${eff>total ? `<div class="change"><span>Para Üstü</span><span>${fmt(eff-total)} ₺</span></div>` : ''}

${note ? `<div class="note-box">📝 ${note}</div>` : ''}

<hr class="dashed">
<div class="footer">
  Bizi tercih ettiğiniz için<br>
  <strong>teşekkür ederiz!</strong><br>
  ★ ★ ★
</div>

<button class="print-btn no-print" onclick="window.print()">🖨️ Yazdır</button>

</body></html>`;
  const w = window.open('','_blank','width=420,height=700');
  w.document.write(html);
  w.document.close();
  w.addEventListener('load', () => w.print());
}

// ── Rapor ─────────────────────────────────────────
function todayStr() {
  const d = new Date();
  return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0');
}
async function openRapor() {
  showModal('rapor');
  const dateEl = document.getElementById('raporDate');
  if (!dateEl.value) dateEl.value = todayStr();
  await loadRapor();
}
async function loadRapor() {
  const dateEl = document.getElementById('raporDate');
  document.getElementById('raporContent').innerHTML = '<div style="padding:20px;text-align:center;color:var(--muted)">Yükleniyor...</div>';
  const d = await api('/adisyon/rapor?date=' + (dateEl.value || todayStr()));
  let html = `<div style="display:flex;justify-content:space-between;align-items:center;padding:9px 12px;background:var(--s3);border-radius:8px;margin-bottom:12px">
    <span style="color:var(--muted2);font-size:.78rem">${d.date} &mdash; ${d.order_count} işlem</span>
    <span style="font-size:1.05rem;font-weight:800;color:var(--primary)">${fmt(d.total)} ₺</span>
  </div>`;
  if (d.items && d.items.length) {
    html += `<div style="font-size:.72rem;font-weight:800;color:var(--muted2);text-transform:uppercase;padding:4px 2px 6px;margin-top:4px">Ürün Bazlı</div>`;
    html += `<table style="width:100%;border-collapse:collapse;margin-bottom:14px">
      <tr style="color:var(--muted2);border-bottom:1px solid var(--border);font-size:.7rem">
        <th style="text-align:left;padding:4px 6px">Ürün</th>
        <th style="text-align:left;padding:4px 6px">Kategori</th>
        <th style="text-align:center;padding:4px 6px">Adet</th>
        <th style="text-align:right;padding:4px 6px">Toplam</th>
      </tr>`;
    d.items.forEach(i => {
      html += `<tr style="border-bottom:1px solid rgba(255,255,255,.04)">
        <td style="padding:5px 6px">${esc(i.name)}</td>
        <td style="padding:5px 6px;color:var(--muted2)">${esc(i.category||'')}</td>
        <td style="padding:5px 6px;text-align:center">${i.qty}</td>
        <td style="padding:5px 6px;text-align:right;color:var(--primary);font-weight:700">${fmt(i.total)} ₺</td>
      </tr>`;
    });
    html += '</table>';
  }
  if (d.masalar && d.masalar.length) {
    html += `<div style="font-size:.72rem;font-weight:800;color:var(--muted2);text-transform:uppercase;padding:4px 2px 6px">Ödeme Geçmişi</div>`;
    html += `<table style="width:100%;border-collapse:collapse">
      <tr style="color:var(--muted2);border-bottom:1px solid var(--border);font-size:.7rem">
        <th style="text-align:left;padding:4px 6px">Masa</th>
        <th style="text-align:left;padding:4px 6px">Ödeme</th>
        <th style="text-align:center;padding:4px 6px">Saat</th>
        <th style="text-align:right;padding:4px 6px">Tutar</th>
      </tr>`;
    d.masalar.forEach(m => {
      html += `<tr style="border-bottom:1px solid rgba(255,255,255,.04)">
        <td style="padding:5px 6px">${esc(m.room_name)}</td>
        <td style="padding:5px 6px;color:var(--muted2)">${esc(m.payment_type)}</td>
        <td style="padding:5px 6px;text-align:center;color:var(--muted2);font-size:.72rem">${esc(m.closed_at||'')}</td>
        <td style="padding:5px 6px;text-align:right;color:var(--primary);font-weight:700">${fmt(m.total)} ₺</td>
        <td style="padding:5px 6px"><button onclick="deleteOrder(${m.id},this)" style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:var(--red);border-radius:5px;padding:3px 8px;font-size:.68rem;cursor:pointer" title="Sil">🗑</button></td>
      </tr>`;
    });
    html += '</table>';
  }
  if (!d.items?.length && !d.masalar?.length) {
    html += '<div style="text-align:center;padding:30px;color:var(--muted)">Bu tarihte satış kaydı yok.</div>';
  }
  document.getElementById('raporContent').innerHTML = html;
}

// ── Sipariş Sil ─────────────────────────────────
async function deleteOrder(id, btn) {
  if (!confirm('Bu kaydı silmek istediğinize emin misiniz?')) return;
  btn.disabled = true;
  btn.textContent = '...';
  const d = await api(`/adisyon/order/${id}`, 'DELETE');
  if (d.success) {
    const row = btn.closest('tr');
    if (row) row.style.opacity = '0.3';
    setTimeout(() => row?.remove(), 300);
  } else {
    btn.disabled = false;
    btn.textContent = '🗑';
    toast('Silinemedi');
  }
}

// ── Geçmiş ───────────────────────────────────────
async function openGecmis() {
  showModal('gecmis');
  document.getElementById('gecmisContent').innerHTML = '<div style="padding:20px;text-align:center;color:var(--muted)">Yükleniyor...</div>';
  const d = await api('/adisyon/gecmis');
  if (!d.orders || !d.orders.length) {
    document.getElementById('gecmisContent').innerHTML = '<div style="text-align:center;padding:30px;color:var(--muted)">Geçmiş yok.</div>';
    return;
  }
  let html = `<table style="width:100%;border-collapse:collapse">
    <tr style="color:var(--muted2);border-bottom:1px solid var(--border);font-size:.7rem">
      <th style="text-align:left;padding:4px 6px">Masa</th>
      <th style="text-align:left;padding:4px 6px">Ödeme</th>
      <th style="text-align:center;padding:4px 6px">Tarih</th>
      <th style="text-align:right;padding:4px 6px">Tutar</th>
    </tr>`;
  d.orders.forEach(o => {
    html += `<tr style="border-bottom:1px solid rgba(255,255,255,.04)">
      <td style="padding:5px 6px">${esc(o.room_name)}</td>
      <td style="padding:5px 6px;color:var(--muted2)">${esc(o.payment_type)}</td>
      <td style="padding:5px 6px;text-align:center;color:var(--muted2);font-size:.72rem">${esc(o.closed_at||'')}</td>
      <td style="padding:5px 6px;text-align:right;color:var(--primary);font-weight:700">${fmt(o.total)} ₺</td>
      <td style="padding:5px 6px"><button onclick="deleteOrder(${o.id},this)" style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:var(--red);border-radius:5px;padding:3px 8px;font-size:.68rem;cursor:pointer" title="Sil">🗑</button></td>
    </tr>`;
  });
  html += '</table>';
  document.getElementById('gecmisContent').innerHTML = html;
}

// ── Ürün Yönetimi ───────────────────────────────
async function openUrunler() {
  showModal('urunler');
  switchUrunTab('urunler');
  await loadUrunler();
}
async function loadUrunler() {
  document.getElementById('urunlerList').innerHTML = '<div style="padding:20px;text-align:center;color:var(--muted)">Yükleniyor...</div>';
  const prods = await api('/products');
  if (!prods.length) {
    document.getElementById('urunlerList').innerHTML = '<div style="text-align:center;padding:30px;color:var(--muted)">Ürün yok.</div>';
    return;
  }
  const cats = {};
  prods.forEach(p => { const c = p.category||'Genel'; (cats[c]||(cats[c]=[])).push(p); });
  let html = '';
  Object.keys(cats).sort().forEach(cat => {
    html += `<div style="font-size:.66rem;font-weight:800;color:var(--muted2);text-transform:uppercase;padding:6px 4px 3px">${esc(cat)}</div>`;
    cats[cat].forEach(p => {
      html += `<div style="display:flex;align-items:center;gap:8px;padding:7px 8px;border-radius:6px;border:1px solid var(--border);margin-bottom:4px;background:var(--s3)">
        ${p.image_url ? `<img src="${esc(p.image_url)}" style="width:40px;height:40px;object-fit:cover;border-radius:5px;flex-shrink:0" alt="" loading="lazy" onerror="this.style.display='none'">` : ''}
        <div style="flex:1">
          <div style="font-weight:700;font-size:.82rem">${esc(p.name)}</div>
          <div style="font-size:.68rem;color:var(--muted2)">${esc(p.category||'Genel')}</div>
        </div>
        <div style="font-weight:800;color:var(--primary);font-size:.82rem;white-space:nowrap">${fmt(p.price)} ₺</div>
        <button class="tcard-act-btn" style="width:58px" onclick='editUrun(${p.id},${JSON.stringify(p.name)},${p.price},${JSON.stringify(p.category||'')},${JSON.stringify(p.image_url||'')})'>&#9998; Düz</button>
        <button class="tcard-act-btn" style="width:42px;border-color:rgba(239,68,68,.3);color:var(--red)" onclick="deleteUrun(${p.id})">&#128465;</button>
      </div>`;
    });
  });
  document.getElementById('urunlerList').innerHTML = html;
}

// ── Sekme geçişi ─────────────────────────────────────
function switchUrunTab(tab) {
  const isUrunler = (tab === 'urunler');
  document.getElementById('tab-urunler').style.display = isUrunler ? '' : 'none';
  document.getElementById('tab-catimg').style.display  = isUrunler ? 'none' : '';
  const ub = document.getElementById('tab-urunler-btn');
  const cb = document.getElementById('tab-catimg-btn');
  ub.style.borderBottomColor = isUrunler ? 'var(--primary)' : 'transparent';
  ub.style.color             = isUrunler ? 'var(--primary)' : 'var(--muted2)';
  cb.style.borderBottomColor = isUrunler ? 'transparent' : 'var(--primary)';
  cb.style.color             = isUrunler ? 'var(--muted2)' : 'var(--primary)';
  if (!isUrunler) loadCatImages();
}

// ── Kategori görselleri ──────────────────────────────
async function loadCatImages() {
  const listEl = document.getElementById('catImgList');
  listEl.innerHTML = '<div style="padding:16px;text-align:center;color:var(--muted);font-size:.8rem">Yükleniyor...</div>';

  // Ürünlerden kategori listesi çek
  const [prods, imgMap] = await Promise.all([
    api('/products'),
    api('/categories/images'),
  ]);
  const cats = [...new Set(prods.map(p => p.category).filter(Boolean))].sort();
  if (!cats.length) {
    listEl.innerHTML = '<div style="padding:20px;text-align:center;color:var(--muted);font-size:.8rem">Henüz kategori yok.<br>Ürünlere kategori ekleyin.</div>';
    return;
  }
  listEl.innerHTML = cats.map(cat => {
    const imgUrl = imgMap[cat] || null;
    const slug   = cat.replace(/[^a-z0-9]/gi, '_');
    return `<div style="display:flex;align-items:center;gap:10px;background:var(--s3);border:1px solid var(--border);border-radius:8px;padding:10px 12px">
      <div id="catprev-${slug}" style="width:60px;height:45px;border-radius:6px;overflow:hidden;background:var(--s2);flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:1.2rem;opacity:.4;background-size:cover;background-position:center;${imgUrl?'background-image:url('+JSON.stringify(imgUrl)+');opacity:1;':''}">${imgUrl?'':' 📂'}</div>
      <div style="flex:1;font-size:.82rem;font-weight:700">${esc(cat)}</div>
      <label style="display:flex;align-items:center;gap:5px;cursor:pointer;padding:6px 11px;background:rgba(6,182,212,.1);border:1px solid rgba(6,182,212,.25);border-radius:7px;font-size:.72rem;font-weight:600;color:var(--primary)">
        📷 Görsel Yükle
        <input type="file" accept="image/*" style="display:none" data-cat="${esc(cat)}" data-slug="${slug}" onchange="uploadCatImg(this)">
      </label>
      ${imgUrl?`<button onclick="deleteCatImg('${esc(cat)}','${slug}')" style="padding:6px 9px;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);border-radius:7px;color:#f87171;font-size:.72rem;cursor:pointer">🗑</button>`:''}
      <span id="catstat-${slug}" style="font-size:.7rem;color:var(--muted)"></span>
    </div>`;
  }).join('');
}

async function uploadCatImg(input) {
  if (!input.files[0]) return;
  const cat  = input.dataset.cat;
  const slug = input.dataset.slug;
  const stat = document.getElementById('catstat-' + slug);
  stat.textContent = '⏳';
  const fd = new FormData();
  fd.append('name',  cat);
  fd.append('image', input.files[0]);
  try {
    const r = await fetch('/categories/image', {method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}, body:fd});
    const d = await r.json();
    if (!d.success) throw new Error(d.message || 'Hata');
    const prev = document.getElementById('catprev-' + slug);
    prev.style.backgroundImage = 'url(' + JSON.stringify(d.url + '?t=' + Date.now()) + ')';
    prev.style.opacity = '1';
    prev.textContent = '';
    stat.textContent = '✓';
    setTimeout(() => { stat.textContent = ''; }, 2000);
  } catch(e) {
    stat.textContent = '✗ ' + e.message;
  }
  input.value = '';
}

async function deleteCatImg(cat, slug) {
  if (!confirm('"' + cat + '" kategorisinin görselini sil?')) return;
  const stat = document.getElementById('catstat-' + slug);
  stat.textContent = '⏳';
  try {
    const r = await fetch('/categories/image', {method:'DELETE', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'}, body:JSON.stringify({name:cat})});
    const d = await r.json();
    if (!d.success) throw new Error(d.message || 'Hata');
    // reload
    await loadCatImages();
  } catch(e) {
    stat.textContent = '✗ ' + e.message;
  }
}
function openAddUrun() {
  document.getElementById('urunFormTitle').textContent = '+ Yeni Ürün';
  document.getElementById('urunId').value = '';
  document.getElementById('urunAdi').value = '';
  document.getElementById('urunFiyat').value = '';
  document.getElementById('urunKategori').value = '';
  document.getElementById('urunGorsel').value = '';
  document.getElementById('urunDosya').value = '';
  document.getElementById('urunImageClear').value = '0';
  document.getElementById('imgPreviewWrap').style.display = 'none';
  document.getElementById('uploadLabelText').textContent = 'Fotoğ ekle (bilgisayar / telefon)';
  showModal('urunForm');
}
function editUrun(id, name, price, category, imageUrl) {
  document.getElementById('urunFormTitle').textContent = '✏️ Düzenle';
  document.getElementById('urunId').value = id;
  document.getElementById('urunAdi').value = name;
  document.getElementById('urunFiyat').value = price;
  document.getElementById('urunKategori').value = category;
  document.getElementById('urunGorsel').value = '';
  document.getElementById('urunDosya').value = '';
  document.getElementById('urunImageClear').value = '0';
  // Show existing image preview
  if (imageUrl) {
    document.getElementById('imgPreview').src = imageUrl;
    document.getElementById('imgPreviewWrap').style.display = 'block';
    document.getElementById('uploadLabelText').textContent = 'Farklı fotoğ seç';
  } else {
    document.getElementById('imgPreviewWrap').style.display = 'none';
    document.getElementById('uploadLabelText').textContent = 'Fotoğ ekle (bilgisayar / telefon)';
  }
  showModal('urunForm');
}
function previewImage(input) {
  if (!input.files || !input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    document.getElementById('imgPreview').src = e.target.result;
    document.getElementById('imgPreviewWrap').style.display = 'block';
    document.getElementById('uploadLabelText').textContent = input.files[0].name;
    document.getElementById('urunGorsel').value = ''; // clear URL if file selected
  };
  reader.readAsDataURL(input.files[0]);
}
function previewUrl(url) {
  if (url && url.startsWith('http')) {
    document.getElementById('imgPreview').src = url;
    document.getElementById('imgPreviewWrap').style.display = 'block';
    document.getElementById('urunDosya').value = ''; // clear file if URL typed
    document.getElementById('uploadLabelText').textContent = 'Fotoğ ekle (bilgisayar / telefon)';
  }
}
function clearImage() {
  document.getElementById('imgPreview').src = '';
  document.getElementById('imgPreviewWrap').style.display = 'none';
  document.getElementById('urunGorsel').value = '';
  document.getElementById('urunDosya').value = '';
  document.getElementById('urunImageClear').value = '1';
  document.getElementById('uploadLabelText').textContent = 'Fotoğ ekle (bilgisayar / telefon)';
}
async function saveUrun() {
  const id       = document.getElementById('urunId').value;
  const name     = document.getElementById('urunAdi').value.trim();
  const price    = parseFloat(document.getElementById('urunFiyat').value);
  const category = document.getElementById('urunKategori').value.trim();
  const fileInput = document.getElementById('urunDosya');
  const urlInput  = document.getElementById('urunGorsel').value.trim();
  if (!name || isNaN(price)) { toast('Ürün adı ve fiyat giriniz'); return; }

  const fd = new FormData();
  fd.append('name', name);
  fd.append('price', price);
  fd.append('category', category);
  if (fileInput.files && fileInput.files[0]) {
    fd.append('image', fileInput.files[0]);
  } else if (urlInput) {
    fd.append('image_url', urlInput);
  }
  fd.append('image_clear', document.getElementById('urunImageClear').value);
  // Reset clear flag
  document.getElementById('urunImageClear').value = '0';
  // Laravel method spoofing for PUT
  const url    = id ? `/products/${id}` : '/products';
  if (id) fd.append('_method', 'PUT');

  const r = await fetch(url, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    body: fd,
  });
  const d = await r.json().catch(() => ({error: 'Sunucu hatası'}));
  if (d.success) {
    closeModal('urunForm');
    toast(id ? 'Ürün güncellendi' : 'Ürün eklendi');
    await loadUrunler();
    await refreshProdGrid();
  } else {
    toast('⚠️ ' + (d.message || d.error || 'Hata'));
  }
}
async function deleteUrun(id) {
  if (!confirm('Ürün silinsin mi?')) return;
  const d = await api(`/products/${id}`, 'DELETE');
  if (d.success) { toast('Ürün silindi'); await loadUrunler(); await refreshProdGrid(); }
  else { toast('⚠️ ' + (d.error || d.message || 'Ürün silinemedi')); }
}
async function refreshProdGrid() {
  const prods = await api('/products');
  const grouped = {};
  prods.forEach(p => { const c = p.category||''; (grouped[c]||(grouped[c]=[])).push(p); });
  const cats = Object.keys(grouped).sort();
  // rebuild category tabs
  const tabsEl = document.getElementById('catTabs');
  tabsEl.innerHTML = `<button class="ctab active" data-cat="all" onclick="filterCat('all',this)">Tümü</button>` +
    cats.map(c => `<button class="ctab" data-cat="${esc(c)}" onclick="filterCat('${esc(c).replace(/'/g,"\\'")}'  ,this)">${esc(c)||'Genel'}</button>`).join('');
  // rebuild product grid
  const container = document.getElementById('prodContainer');
  if (!prods.length) { container.innerHTML = '<div style="text-align:center;padding:60px 20px;color:var(--muted)"><p>Ürün yok.</p></div>'; return; }
  let html = '<div class="prod-grid">';
  prods.forEach(p => {
    const imgHtml = p.image_url
      ? `<img class="pimg" src="${esc(p.image_url)}" alt="" loading="lazy" onerror="this.parentElement.innerHTML='<span class=pimg-empty>🍽️</span>'">`
      : `<span class="pimg-empty">🍽️</span>`;
    html += `<div class="pcard" data-id="${p.id}" data-name="${esc(p.name.toLowerCase())}" data-cat="${esc(p.category||'')}" onclick="addProduct(${p.id},this)">`+
      `<div class="pimg-wrap">${imgHtml}</div>`+
      `<h4>${esc(p.name)}</h4><div class="pcat">${esc(p.category||'Genel')}</div><div class="pprice">${fmt(p.price)} ₺</div></div>`;
  });
  html += '</div>';
  container.innerHTML = html;
}

// ── Sipariş notu debounce ──────────────────────────
let noteTimer = null;
function debounceSaveNote() {
  clearTimeout(noteTimer);
  noteTimer = setTimeout(async () => {
    if (!selectedRoomId) return;
    const note = document.getElementById('orderNote').value;
    await api(`/adisyon/masa/${selectedRoomId}/note`, 'POST', { note });
  }, 700);
}

// ── Mutfağa Gönder ──────────────────────────────────────────────────
let _firing = false;
async function fireTokitchen() {
  if (!selectedRoomId || _firing) return;
  _firing = true;
  const btn = document.getElementById('btnFire');
  if (btn) { btn.disabled = true; btn.textContent = '⏳ Gönderiliyor...'; }
  try {
    const d = await api(`/adisyon/masa/${selectedRoomId}/fire`, 'POST');
    if (d.error) { toast('⚠️ ' + d.error); return; }
    renderOrder(d);
    toast(`✓ ${d.fired || ''} ürün mutfağa gönderildi`);
  } catch(e) {
    toast('⚠️ Bağlantı hatası, tekrar deneyin');
  } finally {
    _firing = false;
    // Buton metnini renderOrder günceller; sadece disabled'ı kaldır
    const b = document.getElementById('btnFire');
    if (b && b.textContent === '⏳ Gönderiliyor...') b.textContent = '🍳 Mutfağa Gönder';
  }
}

// ── Masa Transfer ────────────────────────────────────────────────────
function openTransfer() {
  if (!selectedRoomId) { toast('Masa seçili değil'); return; }
  if (!currentItems.length) { toast('Sipariş boş'); return; }
  const sel = document.getElementById('transferTarget');
  sel.innerHTML = '<option value="">Masa seçin...</option>';
  document.querySelectorAll('.tcard[data-id]').forEach(c => {
    if (c.dataset.id != selectedRoomId) {
      const opt = document.createElement('option');
      opt.value = c.dataset.id;
      opt.textContent = c.dataset.name + (c.dataset.status === 'open' ? ' (açık)' : '');
      sel.appendChild(opt);
    }
  });
  showModal('transfer');
}
async function doTransfer() {
  const targetId = document.getElementById('transferTarget').value;
  if (!targetId) { toast('Hedef masa seçin'); return; }
  if (!confirm('Sipariş transfer edilsin mi?')) return;
  const d = await api(`/adisyon/masa/${selectedRoomId}/transfer`, 'POST', { target_room_id: targetId });
  if (d.success) {
    updateTableCard(d.source_room);
    updateTableCard(d.target_room);
    closeModal('transfer');
    currentItems = []; currentOrder = null;
    document.getElementById('orderItems').innerHTML = '<div class="no-table"></div>';
    document.getElementById('orderTotals').style.display = 'none';
    document.getElementById('paymentArea').style.display = 'none';
    toast('✓ Transfer tamamlandı');
    setTimeout(goMasalar, 800);
  } else {
    toast('⚠️ ' + (d.error || 'Transfer başarısız'));
  }
}

// ── Mobil menü ──
function toggleTotals(){
  const el = document.getElementById('orderTotals');
  el.classList.toggle('collapsed');
}
function toggleMobMenu(e){
  e.stopPropagation();
  const dd = document.getElementById('mobDropdown');
  dd.classList.toggle('open');
  if(dd.classList.contains('open')){
    setTimeout(()=>document.addEventListener('click',closeMobMenuOutside,{once:true}),10);
  }
}
function closeMobMenu(){
  const dd = document.getElementById('mobDropdown');
  if(dd) dd.classList.remove('open');
}
function closeMobMenuOutside(e){
  const wrap = document.getElementById('btnMobMenu')?.closest('.mob-menu-wrap');
  if(wrap && !wrap.contains(e.target)) closeMobMenu();
}
function openSettings() {
  document.getElementById('ayarIsletme').value = document.getElementById('isletmeAdi').value || '';
  // Mevcut tema durumunu modal'a yansıt
  const root   = document.documentElement;
  const mode   = root.getAttribute('data-theme') || 'dark';
  const accent = root.getAttribute('data-accent') || '#27A0B1';
  document.querySelectorAll('#modeBtns .mode-btn').forEach(b => b.classList.toggle('active', b.dataset.mode === mode));
  document.querySelectorAll('#colorPalette .color-swatch').forEach(s => s.classList.toggle('active', s.dataset.color.toLowerCase() === accent.toLowerCase()));
  const customInput = document.getElementById('customAccentInput');
  if(customInput){ customInput.value = accent; document.getElementById('customAccentHex').textContent = accent; }
  // Menü renklerini localStorage'dan oku
  const MT_DEFAULTS = {'--bg':'#d4b08c','--surface':'#ffffff','--primary':'#c8922a','--text':'#1a1a1a','--border':'#e8ddd0'};
  const mt = Object.assign({}, MT_DEFAULTS, JSON.parse(localStorage.getItem('menu_theme')||'{}'));
  const MAP = {'--bg':'mcp-bg','--surface':'mcp-surface','--primary':'mcp-primary','--text':'mcp-text','--border':'mcp-border'};
  Object.keys(MAP).forEach(k=>{ const el=document.getElementById(MAP[k]); if(el) el.value=mt[k]; });
  showModal('ayarlar');
}

// ── Menü Tema Presetleri ───────────────────────────────────────────
const MENU_PRESETS = {
  warm:  {'--bg':'#d4b08c','--surface':'#ffffff','--primary':'#c8922a','--text':'#1a1a1a','--border':'#e8ddd0'},
  dark:  {'--bg':'#1a1a2e','--surface':'#16213e','--primary':'#e94560','--text':'#eaeaea','--border':'#0f3460'},
  green: {'--bg':'#c8e6c9','--surface':'#ffffff','--primary':'#2e7d32','--text':'#1b1b1b','--border':'#a5d6a7'},
  pink:  {'--bg':'#fce4ec','--surface':'#ffffff','--primary':'#c2185b','--text':'#1a1a1a','--border':'#f8bbd0'},
  blue:  {'--bg':'#bbdefb','--surface':'#ffffff','--primary':'#1565c0','--text':'#0d1b2a','--border':'#90caf9'},
  light: {'--bg':'#f5f5f5','--surface':'#ffffff','--primary':'#555555','--text':'#111111','--border':'#dddddd'},
};
function applyMenuPreset(name, btn){
  const t = MENU_PRESETS[name] || MENU_PRESETS.warm;
  const MAP = {'--bg':'mcp-bg','--surface':'mcp-surface','--primary':'mcp-primary','--text':'mcp-text','--border':'mcp-border'};
  Object.keys(MAP).forEach(k=>{ const el=document.getElementById(MAP[k]); if(el) el.value=t[k]; });
  document.querySelectorAll('.menu-preset').forEach(b=>b.style.borderColor='transparent');
  if(btn) btn.style.borderColor='#555';
}

// ── Tema: canlı önizleme ───────────────────────────────────────────
function _hexToRgb(h){var r=parseInt(h.slice(1,3),16),g=parseInt(h.slice(3,5),16),b=parseInt(h.slice(5,7),16);return r+','+g+','+b;}
function _darken(h,a){var r=parseInt(h.slice(1,3),16),g=parseInt(h.slice(3,5),16),b=parseInt(h.slice(5,7),16);r=Math.max(0,Math.round(r*a));g=Math.max(0,Math.round(g*a));b=Math.max(0,Math.round(b*a));return'#'+r.toString(16).padStart(2,'0')+g.toString(16).padStart(2,'0')+b.toString(16).padStart(2,'0');}

function setThemeMode(mode) {
  document.documentElement.setAttribute('data-theme', mode);
  document.querySelectorAll('#modeBtns .mode-btn').forEach(b => b.classList.toggle('active', b.dataset.mode === mode));
}

function setAccentColor(color, el, fromCustom) {
  // Normalise hex length
  if(color.length === 4) color = '#' + color[1]+color[1]+color[2]+color[2]+color[3]+color[3];
  if(!/^#[0-9a-fA-F]{6}$/.test(color)) return;
  document.documentElement.setAttribute('data-accent', color);
  document.documentElement.style.setProperty('--primary', color);
  document.documentElement.style.setProperty('--primary2', _darken(color, 0.85));
  document.documentElement.style.setProperty('--primary-dim', 'rgba('+_hexToRgb(color)+',.15)');
  // Update swatch active state
  document.querySelectorAll('#colorPalette .color-swatch').forEach(s => s.classList.toggle('active', s.dataset.color.toLowerCase() === color.toLowerCase()));
  // Sync custom picker
  const inp = document.getElementById('customAccentInput');
  if(inp && !fromCustom) inp.value = color;
  document.getElementById('customAccentHex').textContent = color.toUpperCase();
}

function saveSettings() {
  const isletme = document.getElementById('ayarIsletme').value.trim();
  if (isletme) {
    document.getElementById('isletmeAdi').value = isletme;
    try { const s = JSON.parse(localStorage.getItem('pos_settings')||'{}'); s.isletmeAdi = isletme; localStorage.setItem('pos_settings', JSON.stringify(s)); } catch(e){}
  }
  // Tema kaydet
  const root   = document.documentElement;
  const mode   = root.getAttribute('data-theme') || 'dark';
  const accent = root.getAttribute('data-accent') || '#27A0B1';
  fetch('<?php echo e(route("settings.theme")); ?>', {
    method:'POST',
    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
    body: JSON.stringify({ mode, accent })
  }).catch(()=>{});
  // localStorage yedek
  try{ localStorage.setItem('pos_theme', JSON.stringify({mode, accent})); }catch(e){}
  // Menü tema kaydet
  const menuTheme = {
    '--bg':      document.getElementById('mcp-bg').value,
    '--surface': document.getElementById('mcp-surface').value,
    '--primary': document.getElementById('mcp-primary').value,
    '--text':    document.getElementById('mcp-text').value,
    '--border':  document.getElementById('mcp-border').value,
  };
  try{ localStorage.setItem('menu_theme', JSON.stringify(menuTheme)); }catch(e){}
  toast('Ayarlar kaydedildi');
  closeModal('ayarlar');
}

// ── QR Menü ─────────────────────────────────────────────────────────
function openQr() {
  const menuUrl = window.location.origin + '/menu/<?php echo e(auth()->user()->menu_token); ?>';
  document.getElementById('qrUrl').textContent = menuUrl;
  // qrserver.com free API — internet bağlantısı gerektirir
  document.getElementById('qrImg').src =
    'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' +
    encodeURIComponent(menuUrl) + '&bgcolor=ffffff&color=000000&margin=10';
  showModal('qrmenu');
}
function printQr() {
  const menuUrl = window.location.origin + '/menu/<?php echo e(auth()->user()->menu_token); ?>';
  const isletme = (document.getElementById('isletmeAdi')?.value?.trim()) || 'Menü';
  const w = window.open('', '_blank', 'width=400,height=500');
  w.document.write(`<!DOCTYPE html><html><head><meta charset="UTF-8">
    <title>QR Menü</title>
    <style>
      body{font-family:'Segoe UI',sans-serif;text-align:center;padding:30px;margin:0}
      h2{font-size:1.3rem;margin-bottom:6px}
      p{font-size:.75rem;color:#666;margin-bottom:16px}
      img{width:220px;height:220px;border:2px solid #eee;border-radius:8px}
      .url{font-size:.62rem;color:#888;margin-top:10px;word-break:break-all}
      .footer{margin-top:20px;font-size:.65rem;color:#aaa}
    </style></head><body>
    <h2>${isletme}</h2>
    <p>Menümüzü görmek için QR kodu okutun</p>
    <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=${encodeURIComponent(menuUrl)}&bgcolor=ffffff&color=000000&margin=10" alt="QR">
    <div class="url">${menuUrl}</div>
    <div class="footer">Sadece görüntüleme amaçlıdır</div>
    <script>window.onload=()=>window.print()<\/script>
    </body></html>`);
  w.document.close();
}

//  Load localStorage settings 
(function(){
  try{
    const s=JSON.parse(localStorage.getItem('pos_settings')||'{}');
    if(s.kdv) document.getElementById('kdv').value=s.kdv;
    if(s.servis) document.getElementById('servis').value=s.servis;
    if(s.indirimTipi) document.getElementById('indirimTipi').value=s.indirimTipi;
    if(s.indirimDeger) document.getElementById('indirimDeger').value=s.indirimDeger;
    if(s.isletmeAdi) document.getElementById('isletmeAdi').value=s.isletmeAdi;
  }catch(e){}
})();

//  Alinan tutar input 
document.getElementById('alinanTutar').addEventListener('input',()=>{
  if(currentItems.length) renderOrder({room:null,order:currentOrder,items:currentItems});
});

// Mobil POS sekme geçişi
function switchPosTab(tab) {
  document.querySelectorAll('.pos-tab').forEach(t => t.classList.toggle('active', t.dataset.tab === tab));
  document.querySelector('.pane-mid').classList.toggle('mob-active', tab === 'products');
  document.querySelector('.pane-right').classList.toggle('mob-active', tab === 'orders');
}

//  Modal backdrop close 
document.querySelectorAll('.modal-bg').forEach(bg=>{
  bg.addEventListener('click',e=>{if(e.target===bg)bg.classList.remove('open')});
});
</script>
<script>
  // PWA Service Worker kaydı
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(() => {});
  }
</script>
</body>
</html><?php /**PATH C:\Users\brusk\OneDrive\Masaüstü\eto\laravel-cafe\resources\views/adisyon/index.blade.php ENDPATH**/ ?>