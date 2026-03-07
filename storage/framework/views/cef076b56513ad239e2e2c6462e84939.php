<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <title>Mutfak Ekrani</title>
  
  <link rel="manifest" href="/manifest-mutfak.json">
  <meta name="theme-color" content="#27A0B1">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Mutfak">
  <link rel="apple-touch-icon" href="/icons/icon-mutfak-192.png">
  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    :root{
      --bg:#0a0a0a;--surface:#141414;--surface2:#1c1c1c;--border:#272727;
      --primary:#27A0B1;--primary2:#1d8a9a;--primary-glow:rgba(39,160,177,.22);
      --green:#10b981;--red:#ef4444;--orange:#f59e0b;
      --text:#f0ece0;--muted:#666;--r:14px;
    }
    html,body{height:100%;background:var(--bg);color:var(--text);font-family:'Segoe UI',system-ui,sans-serif;overflow-x:hidden}

    /* TOP BAR */
    .topbar{
      display:flex;align-items:center;justify-content:space-between;
      background:var(--surface);border-bottom:2px solid var(--primary);
      padding:0 28px;height:68px;position:sticky;top:0;z-index:100;
    }
    .brand{display:flex;align-items:center;gap:14px}
    .brand-ico{font-size:2rem;line-height:1}
    .brand h1{font-size:1.25rem;font-weight:900;color:var(--primary);letter-spacing:.8px;text-transform:uppercase}
    .brand small{font-size:.72rem;color:var(--muted);font-weight:400;letter-spacing:.4px}
    .topbar-right{display:flex;align-items:center;gap:10px}
    .badge-count{
      background:var(--primary);color:#fff;font-weight:800;border-radius:999px;
      padding:6px 20px;font-size:.9rem;min-width:80px;text-align:center;transition:background .3s;
    }
    .badge-count.zero{background:#222;color:var(--muted)}
    .icon-btn{
      width:40px;height:40px;border-radius:10px;border:1px solid var(--border);
      background:transparent;color:var(--muted);cursor:pointer;font-size:1.1rem;
      display:flex;align-items:center;justify-content:center;transition:all .15s;
    }
    .icon-btn:hover{border-color:var(--primary);color:var(--primary)}
    .live-dot{
      width:10px;height:10px;border-radius:50%;background:var(--green);
      display:inline-block;flex-shrink:0;animation:blink 1.4s ease-in-out infinite;
    }
    @keyframes blink{0%,100%{opacity:1;box-shadow:0 0 6px var(--green)}50%{opacity:.4;box-shadow:none}}
    .live-txt{font-size:.78rem;color:var(--muted)}

    /* MAIN */
    .main{padding:22px 28px 80px;min-height:calc(100vh - 68px)}
    .orders-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(350px,1fr));gap:20px}

    /* CARD — PENDING (urgent) */
    .kcard{
      background:var(--surface);border:2px solid var(--border);border-radius:var(--r);
      overflow:hidden;display:flex;flex-direction:column;
      transition:border-color .4s,box-shadow .4s;
    }
    .kcard.new-card{
      animation:popIn .3s cubic-bezier(.34,1.56,.64,1);
    }
    @keyframes popIn{from{opacity:0;transform:scale(.95) translateY(10px)}to{opacity:1;transform:scale(1) translateY(0)}}
    .kcard.urgent{
      border-color:var(--primary);
      box-shadow:0 0 0 1px rgba(39,160,177,.18),0 12px 40px rgba(39,160,177,.1);
    }
    /* CARD — ALL READY (done) */
    .kcard.done{
      border-color:#1a5c3a;
      box-shadow:0 0 0 1px rgba(16,185,129,.15),0 8px 24px rgba(16,185,129,.07);
      opacity:.85;
    }
    .kcard.done .kcard-head{background:linear-gradient(135deg,#0f1f0f 0%,#121c12 100%)}
    .kcard.done .table-name{color:var(--green)}

    /* CARD HEAD */
    .kcard-head{
      background:linear-gradient(135deg,var(--surface2) 0%,#1a1a1a 100%);
      padding:16px 20px;border-bottom:1px solid var(--border);
      display:flex;align-items:flex-start;justify-content:space-between;gap:10px;
      transition:background .4s;
    }
    .table-label{font-size:.66rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:4px}
    .table-name{font-size:1.2rem;font-weight:900;color:var(--primary);line-height:1.2;transition:color .4s}
    .table-time{font-size:.72rem;color:var(--muted);margin-top:4px;display:flex;align-items:center;gap:5px}
    .time-dot{width:6px;height:6px;border-radius:50%;background:var(--green);flex-shrink:0}
    .kcard-head-right{display:flex;flex-direction:column;align-items:flex-end;gap:4px}
    .item-badge{background:#1a1a1a;border:1px solid var(--border);border-radius:8px;padding:5px 12px;font-size:.78rem;font-weight:700;color:var(--text);white-space:nowrap}
    .order-num{font-size:.68rem;color:var(--muted)}

    /* ITEMS */
    .kcard-items{padding:14px 20px;flex:1;display:flex;flex-direction:column;gap:0}
    .item-row{display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid rgba(255,255,255,.04);transition:opacity .3s}
    .item-row:last-child{border-bottom:none}
    .item-row.ready-row{opacity:.6}

    .item-qty-box{
      min-width:38px;height:38px;background:var(--primary);color:#fff;
      font-weight:900;border-radius:10px;display:flex;align-items:center;justify-content:center;
      font-size:.9rem;flex-shrink:0;letter-spacing:-.5px;transition:background .3s;
      padding:0 8px;box-sizing:border-box;width:auto;
    }
    .item-row.ready-row .item-qty-box{background:#1a5c3a;color:var(--green)}
    .item-details{flex:1;min-width:0}
    .item-name{font-size:.95rem;font-weight:700;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .item-cat{font-size:.72rem;color:var(--muted);margin-top:2px}
    .item-note{font-size:.7rem;color:#f59e0b;font-style:italic;margin-top:3px;font-weight:600}

    .item-status-badge{font-size:.7rem;font-weight:700;border-radius:6px;padding:3px 9px;white-space:nowrap;flex-shrink:0}
    .item-status-badge.pending{color:var(--orange);background:#2a1e00;border:1px solid #4a3500}
    .item-status-badge.ready{color:var(--green);background:#0d2a1a;border:1px solid #1a5c34}

    /* HAZIR BUTTON */
    .kcard-foot{padding:0 20px 20px}
    .btn-hazir{
      width:100%;padding:14px;border-radius:10px;
      background:linear-gradient(135deg,#0d9964 0%,#10b981 100%);
      color:#fff;font-weight:800;font-size:1rem;border:none;cursor:pointer;
      transition:all .2s;display:flex;align-items:center;justify-content:center;gap:8px;
      letter-spacing:.3px;
    }
    .btn-hazir:hover{background:linear-gradient(135deg,#0b8557 0%,#0ea571 100%);transform:translateY(-1px);box-shadow:0 6px 20px rgba(16,185,129,.25)}
    .btn-hazir:disabled{background:#1a3d2e;color:#2a7a54;cursor:not-allowed;transform:none;box-shadow:none;font-size:.88rem}
    .btn-hazir .spin{display:none;width:16px;height:16px;border:2px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:spin .6s linear infinite}
    .btn-hazir.loading .spin{display:block}
    .btn-hazir.loading .btn-text{display:none}
    @keyframes spin{to{transform:rotate(360deg)}}

    /* EMPTY */
    .empty{grid-column:1/-1;text-align:center;padding:90px 24px}
    .empty-ico{font-size:5rem;display:block;margin-bottom:20px;opacity:.25;filter:grayscale(1)}
    .empty h2{font-size:1.3rem;font-weight:700;color:var(--muted);margin-bottom:8px}
    .empty p{font-size:.85rem;color:#444}

    /* BOTTOM STATUS BAR */
    .statusbar{
      position:fixed;bottom:0;left:0;right:0;
      background:var(--surface);border-top:1px solid var(--border);
      padding:10px 28px;display:flex;align-items:center;justify-content:space-between;font-size:.78rem;color:var(--muted);
    }
    .statusbar-left{display:flex;align-items:center;gap:8px}

    ::-webkit-scrollbar{width:5px}::-webkit-scrollbar-thumb{background:#292929;border-radius:5px}::-webkit-scrollbar-track{background:transparent}
  </style>
  <?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.js']); ?>
</head>
<body>

<div class="topbar">
  <div class="brand">
    <span class="brand-ico">🍳</span>
    <div>
      <h1>Mutfak Ekrani</h1>
      <small>Canli siparis takibi</small>
    </div>
  </div>
  <div class="topbar-right">
    <span class="live-dot"></span>
    <span class="live-txt" id="liveText">Baglaniyor...</span>
    <span class="badge-count <?php if(count($orders)==0): ?> zero <?php endif; ?>" id="orderCount">
      <?php echo e(count($orders)); ?> Siparis
    </span>
    <button class="icon-btn" id="btnSound" title="Ses">🔊</button>
    <button class="icon-btn" id="btnFull" title="Tam Ekran">⛶</button>
    <button class="icon-btn" onclick="location.reload()" title="Yenile">↺</button>
    <form method="POST" action="<?php echo e(route('logout')); ?>" style="display:inline">
      <?php echo csrf_field(); ?>
      <button type="submit" class="icon-btn" title="Çıkış Yap">↪</button>
    </form>
  </div>
</div>

<div class="main">
  <div class="orders-grid" id="kitchenGrid">
    <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <?php $hasPending = collect($order['items'])->contains('status', 'pending'); ?>
      <div class="kcard <?php echo e($hasPending ? 'urgent' : 'done'); ?>" data-order-id="<?php echo e($order['id']); ?>">
        <div class="kcard-head">
          <div class="kcard-head-left">
            <div class="table-label">Masa</div>
            <div class="table-name"><?php echo e($order['name']); ?></div>
            <div class="table-time">
              <span class="time-dot"></span>
              <?php echo e($order['opened'] ?? now()->format('H:i')); ?>

            </div>
          </div>
          <div class="kcard-head-right">
            <div class="item-badge"><?php echo e(count($order['items'])); ?> kalem</div>
            <div class="order-num">#<?php echo e($order['id']); ?></div>
          </div>
        </div>
        <div class="kcard-items">
          <?php $__currentLoopData = $order['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $q=$item['qty']; $qL=fmod((float)$q,1.0)==0?(int)$q:number_format((float)$q,1); ?>
            <div class="item-row <?php echo e($item['status'] === 'ready' ? 'ready-row' : ''); ?>">
              <div class="item-qty-box"><?php echo e($qL); ?></div>
              <div class="item-details">
                <span class="item-name"><?php echo e($item['name']); ?></span>
                <div class="item-cat"><?php echo e($item['cat']); ?></div>
                <?php if(!empty($item['note'])): ?>
                <div class="item-note">💬 <?php echo e($item['note']); ?></div>
                <?php endif; ?>
              </div>
              <div class="item-status-badge <?php echo e($item['status'] === 'ready' ? 'ready' : 'pending'); ?>">
                <?php echo e($item['status'] === 'ready' ? '✅ Hazır' : '⏳ Bekliyor'); ?>

              </div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="kcard-foot">
          <?php if($hasPending): ?>
            <button class="btn-hazir hazir-btn" data-order-id="<?php echo e($order['id']); ?>">
              <span class="spin"></span>
              <span class="btn-text">✅ HAZIR — Teslim Edildi</span>
            </button>
          <?php else: ?>
            <button class="btn-hazir" disabled>✅ Teslim Edildi</button>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <div class="empty">
        <span class="empty-ico">😌</span>
        <h2>Tum siparisler teslim edildi</h2>
        <p>Su an bekleyen siparis yok</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<div class="statusbar">
  <div class="statusbar-left">
    <span class="live-dot"></span>
    <span id="statusBar">Yukleniyor...</span>
  </div>
  <span id="lastUpdate"></span>
</div>

<script>
const CSRF     = '<?php echo e(csrf_token()); ?>';
const POLL_URL = '<?php echo e(route("mutfak.poll")); ?>';
const READY    = '<?php echo e(route("mutfak.mark-ready")); ?>';
const USER_ID  = <?php echo e(auth()->id()); ?>;

let soundOn = localStorage.getItem('mutfak_sound') !== '0';

// TTS ses listesini önceden yükle (bazı tarayıcılar ilk çağrıda boş döner)
if (window.speechSynthesis) {
  window.speechSynthesis.getVoices();
  window.speechSynthesis.onvoiceschanged = () => window.speechSynthesis.getVoices();
}
let knownIds = new Set();
let pendingCounts = {};

document.querySelectorAll('[data-order-id]').forEach(el => {
  const id = +el.dataset.orderId;
  knownIds.add(id);
  pendingCounts[id] = el.querySelectorAll('.item-row:not(.ready-row)').length;
});

// Sound
const btnSound = document.getElementById('btnSound');
btnSound.textContent = soundOn ? '🔊' : '🔇';
btnSound.onclick = () => {
  soundOn = !soundOn;
  localStorage.setItem('mutfak_sound', soundOn ? '1' : '0');
  btnSound.textContent = soundOn ? '🔊' : '🔇';
};

// Fullscreen
document.getElementById('btnFull').onclick = () => {
  document.fullscreenElement ? document.exitFullscreen() : document.documentElement.requestFullscreen();
};

function beep() {
  if (!soundOn) return;

  // Önce Web Speech API (TTS) dene
  if (window.speechSynthesis) {
    window.speechSynthesis.cancel(); // Kuyrukta bekleyeni temizle
    const utt = new SpeechSynthesisUtterance('Yeni sipariş var');
    utt.lang  = 'tr-TR';
    utt.rate  = 0.95;
    utt.pitch = 1.1;
    utt.volume = 1;

    // Türkçe ses varsa seç, yoksa mevcut varsayılanı kullan
    const voices = window.speechSynthesis.getVoices();
    const trVoice = voices.find(v => v.lang === 'tr-TR') || voices.find(v => v.lang.startsWith('tr'));
    if (trVoice) utt.voice = trVoice;

    window.speechSynthesis.speak(utt);
    return;
  }

  // TTS yoksa eski bip sesi
  try {
    const A = new (window.AudioContext || window.webkitAudioContext)();
    [[880, 0, 0.12], [1100, 0.2, 0.1], [880, 0.38, 0.08]].forEach(([f, d, dr]) => {
      const o = A.createOscillator(), g = A.createGain();
      o.frequency.value = f; g.gain.value = 0.06;
      o.connect(g); g.connect(A.destination);
      o.start(A.currentTime + d); o.stop(A.currentTime + d + dr);
    });
  } catch(e) {}
}

const esc = s => String(s ?? '').replace(/[&<>"']/g, c =>
  ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const ts = () => new Date().toLocaleTimeString('tr-TR', {hour:'2-digit',minute:'2-digit'});

function buildCard(o) {
  const hasPending = o.items.some(i => i.status === 'pending');
  const cardClass  = hasPending ? 'urgent' : 'done';
  const items = o.items.map(i => {
    const isReady = i.status === 'ready';
    return `
      <div class="item-row${isReady ? ' ready-row' : ''}">
        <div class="item-qty-box">${i.qty%1===0?i.qty:parseFloat(i.qty).toFixed(1)}</div>
        <div class="item-details">
          <span class="item-name">${esc(i.name)}</span>
          <div class="item-cat">${esc(i.cat)}</div>
          ${i.note ? `<div class="item-note">💬 ${esc(i.note)}</div>` : ''}
        </div>
        <div class="item-status-badge ${isReady ? 'ready' : 'pending'}">
          ${isReady ? '&#x2705; Haz&#x131;r' : '&#x23F3; Bekliyor'}
        </div>
      </div>`;
  }).join('');

  const footBtn = hasPending
    ? `<button class="btn-hazir hazir-btn" data-order-id="${o.id}"><span class="spin"></span><span class="btn-text">&#x2705; HAZIR &mdash; Teslim Edildi</span></button>`
    : `<button class="btn-hazir" disabled>&#x2705; Teslim Edildi</button>`;

  return `
    <div class="kcard new-card ${cardClass}" data-order-id="${o.id}">
      <div class="kcard-head">
        <div class="kcard-head-left">
          <div class="table-label">Masa</div>
          <div class="table-name">${esc(o.name)}</div>
          <div class="table-time"><span class="time-dot"></span>${esc(o.opened ?? ts())}</div>
        </div>
        <div class="kcard-head-right">
          <div class="item-badge">${o.items.length} kalem</div>
          <div class="order-num">#${o.id}</div>
        </div>
      </div>
      <div class="kcard-items">${items}</div>
      <div class="kcard-foot">${footBtn}</div>
    </div>`;
}

function renderGrid(orders) {
  let hasNew = false;
  orders.forEach(o => {
    const pending = o.items.filter(i => i.status === 'pending').length;
    if (!knownIds.has(o.id)) hasNew = true;
    else if ((pendingCounts[o.id] ?? 0) < pending) hasNew = true;
  });
  if (hasNew) beep();

  knownIds = new Set(orders.map(o => o.id));
  pendingCounts = {};
  orders.forEach(o => { pendingCounts[o.id] = o.items.filter(i => i.status === 'pending').length; });

  const oc = document.getElementById('orderCount');
  const pendingOrders = orders.filter(o => o.items.some(i => i.status === 'pending'));
  oc.textContent = pendingOrders.length + ' Siparis';
  oc.className = 'badge-count' + (pendingOrders.length === 0 ? ' zero' : '');

  const grid = document.getElementById('kitchenGrid');

  // Boş durum
  if (orders.length === 0) {
    grid.innerHTML = '<div class="empty"><span class="empty-ico">&#x1F60C;</span><h2>Tum siparisler teslim edildi</h2><p>Su an bekleyen siparis yok</p></div>';
    return;
  }

  // Kaldırılan siparişleri sil
  const incomingIds = new Set(orders.map(o => String(o.id)));
  grid.querySelectorAll('.kcard[data-order-id]').forEach(el => {
    if (!incomingIds.has(el.dataset.orderId)) el.remove();
  });

  // Var olanları güncelle, yenileri ekle (sıra korunur)
  orders.forEach((o, idx) => {
    const existing = grid.querySelector(`.kcard[data-order-id="${o.id}"]`);
    if (existing) {
      // Kart zaten var — sadece içini ve sınıfını güncelle (animasyon YOK)
      const hasPending = o.items.some(i => i.status === 'pending');
      existing.className = 'kcard ' + (hasPending ? 'urgent' : 'done');

      // Items güncelle
      const itemsHtml = o.items.map(i => {
        const isReady = i.status === 'ready';
        return `
          <div class="item-row${isReady ? ' ready-row' : ''}">
            <div class="item-qty-box">${i.qty%1===0?i.qty:parseFloat(i.qty).toFixed(1)}</div>
            <div class="item-details">
              <span class="item-name">${esc(i.name)}</span>
              <div class="item-cat">${esc(i.cat)}</div>
              ${i.note ? `<div class="item-note">💬 ${esc(i.note)}</div>` : ''}
            </div>
            <div class="item-status-badge ${isReady ? 'ready' : 'pending'}">
              ${isReady ? '&#x2705; Haz&#x131;r' : '&#x23F3; Bekliyor'}
            </div>
          </div>`;
      }).join('');
      existing.querySelector('.kcard-items').innerHTML = itemsHtml;

      // Footer butonu güncelle
      const foot = existing.querySelector('.kcard-foot');
      foot.innerHTML = hasPending
        ? `<button class="btn-hazir hazir-btn" data-order-id="${o.id}"><span class="spin"></span><span class="btn-text">&#x2705; HAZIR &mdash; Teslim Edildi</span></button>`
        : `<button class="btn-hazir" disabled>&#x2705; Teslim Edildi</button>`;
      wireButtons(existing);

      // Sıralama: doğru konuma taşı
      if (grid.children[idx] !== existing) grid.insertBefore(existing, grid.children[idx] ?? null);
    } else {
      // Yeni kart — animasyonla ekle
      const tmp = document.createElement('div');
      tmp.innerHTML = buildCard(o).trim();
      const newCard = tmp.firstChild;
      const refNode = grid.children[idx] ?? null;
      grid.insertBefore(newCard, refNode);
      wireButtons(newCard);
    }
  });
}

function wireButtons(root = document) {
  root.querySelectorAll('.hazir-btn:not([data-wired])').forEach(btn => {
    btn.dataset.wired = '1';
    btn.onclick = async () => {
      btn.disabled = true;
      btn.classList.add('loading');
      try {
        const r = await fetch(READY, {
          method: 'POST',
          headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
          body: JSON.stringify({table_id: btn.dataset.orderId})
        });
        // WebSocket varsa broadcast zaten günceller, poll’a gerek yok
        if (r.ok && !window.Echo) await poll();
        else if (!r.ok) { btn.disabled = false; btn.classList.remove('loading'); }
      } catch(e) { btn.disabled = false; btn.classList.remove('loading'); }
    };
  });
}
wireButtons();

async function poll() {
  try {
    const r = await fetch(POLL_URL, {headers:{'Accept':'application/json'}});
    const data = await r.json();
    renderGrid(data);
    const t = new Date().toLocaleTimeString('tr-TR');
    document.getElementById('statusBar').textContent = 'Canli baglanti aktif';
    document.getElementById('lastUpdate').textContent = 'Son guncelleme: ' + t;
    document.getElementById('liveText').textContent = 'Canli';
  } catch(e) {
    document.getElementById('statusBar').textContent = 'Baglanti hatasi — yeniden deneniyor...';
    document.getElementById('liveText').textContent = 'Baglanamadi';
  }
}

// ── WebSocket ile gerçek zamanlı güncelleme ────────────────────────
if (window.Echo) {
  window.Echo.private('kitchen.' + USER_ID)
    .listen('.updated', (e) => {
      renderGrid(e.orders);
      const t = new Date().toLocaleTimeString('tr-TR');
      document.getElementById('statusBar').textContent = 'Canli baglanti aktif';
      document.getElementById('lastUpdate').textContent = 'Son guncelleme: ' + t;
      document.getElementById('liveText').textContent = 'Canli';
      document.querySelector('.live-dot').style.background = '';
    });
  document.getElementById('liveText').textContent = 'Canli';
  // Reverb bağlantısı koparsa yedek pollinge geç
  window.Echo.connector.pusher.connection.bind('disconnected', () => {
    document.getElementById('liveText').textContent = 'Bağlantı kesildi';
    document.querySelector('.live-dot').style.background = '#ef4444';
    setInterval(poll, 3000);
  });
  window.Echo.connector.pusher.connection.bind('connected', () => {
    document.getElementById('liveText').textContent = 'Canli';
    document.querySelector('.live-dot').style.background = '';
  });
} else {
  // Reverb bağlı yoksa yedek polling
  setInterval(poll, 3000);
}
// İlk yükleme için bir kez poll yap
setTimeout(poll, 600);


</script>
<script>
  // PWA Service Worker kaydı
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(() => {});
  }
</script>
</body>
</html>
<?php /**PATH C:\Users\brusk\OneDrive\Masaüstü\eto\laravel-cafe\resources\views/mutfak/index.blade.php ENDPATH**/ ?>