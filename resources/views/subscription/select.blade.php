<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Abonelik Seçin — Kafe POS</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{--bg:#0d0d0d;--s1:#131313;--s2:#1a1a1a;--s3:#222;--border:#2e2e2e;--border2:#333;
      --text:#f0f0f0;--muted:#6b7280;--primary:#27A0B1;--green:#10b981;--orange:#f59e0b}
body{background:var(--bg);color:var(--text);font-family:'Inter',sans-serif;min-height:100vh;
     display:flex;align-items:center;justify-content:center;padding:20px}
.wrap{width:100%;max-width:560px}
.logo{display:flex;align-items:center;gap:10px;margin-bottom:36px;justify-content:center}
.logo-icon{width:42px;height:42px;background:var(--primary);border-radius:12px;
           display:flex;align-items:center;justify-content:center;font-size:1.3rem}
.logo-name{font-size:1.35rem;font-weight:800}
.logo-sub{font-size:.72rem;color:var(--muted);margin-top:1px}
h2{font-size:1.15rem;font-weight:800;text-align:center;margin-bottom:8px}
.sub{font-size:.82rem;color:var(--muted);text-align:center;margin-bottom:32px;line-height:1.6}

.plans{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:28px}
.plan{
  background:var(--s2);border:2px solid var(--border2);border-radius:16px;
  padding:24px 20px;cursor:pointer;transition:all .18s;position:relative;text-align:center;
}
.plan:hover{border-color:var(--primary);transform:translateY(-3px);box-shadow:0 16px 40px rgba(0,0,0,.4)}
.plan.selected{border-color:var(--primary);background:rgba(39,160,177,.06)}
.plan-badge{
  position:absolute;top:-11px;left:50%;transform:translateX(-50%);
  background:var(--orange);color:#000;font-size:.65rem;font-weight:800;
  padding:2px 10px;border-radius:99px;white-space:nowrap}
.plan-period{font-size:.72rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px}
.plan-price{font-size:2rem;font-weight:800;color:var(--text);line-height:1}
.plan-price span{font-size:.8rem;font-weight:600;color:var(--muted)}
.plan-desc{font-size:.72rem;color:var(--muted);margin-top:10px;line-height:1.5}
.plan input[type=radio]{position:absolute;opacity:0;pointer-events:none}

.btn-submit{
  width:100%;padding:14px;background:var(--primary);color:#fff;border:none;
  border-radius:12px;font-size:.95rem;font-weight:700;cursor:pointer;
  font-family:inherit;transition:opacity .15s;
}
.btn-submit:hover{opacity:.88}
.btn-submit:disabled{opacity:.4;cursor:not-allowed}
.footer{text-align:center;margin-top:18px;font-size:.75rem;color:var(--muted)}
.footer a{color:var(--primary);text-decoration:none}

/* --- kalan süre kartı --- */
.status-card{
  border-radius:14px;padding:16px 20px;margin-bottom:24px;
  display:flex;align-items:center;gap:14px;
}
.status-card.active{background:rgba(16,185,129,.07);border:1px solid rgba(16,185,129,.2)}
.status-card.pending{background:rgba(245,158,11,.07);border:1px solid rgba(245,158,11,.2)}
.status-card.expiring{background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2)}
.status-icon{font-size:1.8rem;flex-shrink:0}
.status-info{flex:1}
.status-title{font-size:.82rem;font-weight:700;margin-bottom:3px}
.status-title.green{color:#10b981}
.status-title.orange{color:#f59e0b}
.status-title.red{color:#ef4444}
.status-desc{font-size:.73rem;color:var(--muted);line-height:1.5}
/* progress bar */
.progress-wrap{margin-top:10px;background:rgba(255,255,255,.06);border-radius:99px;height:5px;overflow:hidden}
.progress-bar{height:100%;border-radius:99px;transition:width .4s}
.progress-bar.green{background:var(--green)}
.progress-bar.orange{background:var(--orange)}
.progress-bar.red{background:#ef4444}
/* günler badge */
.days-badge{
  background:rgba(255,255,255,.06);border-radius:10px;
  padding:8px 14px;text-align:center;flex-shrink:0;
}
.days-num{font-size:1.5rem;font-weight:800;line-height:1}
.days-lbl{font-size:.58rem;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-top:1px}

@media(max-width:420px){
  .plans{grid-template-columns:1fr}
}
</style>
</head>
<body>
<div class="wrap">
  <div style="margin-bottom:18px">
    <a href="/" style="display:inline-block;padding:8px 18px;background:var(--s2);color:var(--muted);border-radius:8px;text-decoration:none;font-size:.95rem;font-weight:600;margin-bottom:12px;transition:background .15s;">← Geri</a>
  </div>
  <div class="logo">
    <div class="logo-icon">🍽️</div>
    <div>
      <div class="logo-name">Kafe POS</div>
      <div class="logo-sub">Abonelik Sistemi</div>
    </div>
  </div>

  <h2>Merhaba, {{ auth()->user()->name }}! 👋</h2>
  <p class="sub" style="margin-bottom:18px">
    @if(auth()->user()->isSubscriptionActive())
      Aboneliğiniz aktif. Daha fazla süre eklemek için aşağıdan plan seçin.
    @else
      Sistemi kullanmak için bir abonelik planı seçin ve ödemenizi tamamlayın.
    @endif
  </p>

  @php
    $user         = auth()->user();
    $isActive     = $user->isSubscriptionActive();
    $isPending    = $user->subscription_status === 'pending';
    $expiresAt    = $user->subscription_expires_at;
    $daysLeft     = $expiresAt ? (int) now()->diffInDays($expiresAt, false) : null;
    // Abonelik toplam süresi (ay=30, yıl=365 gün)
    $totalDays    = ($user->subscription_type === 'yearly') ? 365 : 30;
    $progressPct  = ($daysLeft !== null && $totalDays > 0)
                    ? max(0, min(100, round($daysLeft / $totalDays * 100)))
                    : 0;
    $colorClass   = $daysLeft !== null
                    ? ($daysLeft > 10 ? 'green' : ($daysLeft > 3 ? 'orange' : 'red'))
                    : 'green';
  @endphp

  {{-- Kalan süre kartı --}}
  @if($isActive && $expiresAt)
    <div class="status-card {{ $daysLeft <= 3 ? 'expiring' : ($daysLeft <= 10 ? 'pending' : 'active') }}">
      <div class="status-icon">
        {{ $daysLeft <= 3 ? '⚠️' : ($daysLeft <= 10 ? '⏰' : '✅') }}
      </div>
      <div class="status-info">
        <div class="status-title {{ $colorClass }}">
          @if($daysLeft <= 3)
            Aboneliğiniz bitiyor!
          @elseif($daysLeft <= 10)
            Süreniz azalıyor
          @else
            Aboneliğiniz aktif
          @endif
        </div>
        <div class="status-desc">
          Bitiş tarihi: <strong style="color:var(--text)">{{ $expiresAt->format('d.m.Y') }}</strong>
          &nbsp;•&nbsp;
          {{ $user->subscription_type === 'yearly' ? 'Yıllık' : 'Aylık' }} plan
        </div>
        <div class="progress-wrap">
          <div class="progress-bar {{ $colorClass }}" style="width:{{ $progressPct }}%"></div>
        </div>
      </div>
      <div class="days-badge">
        <div class="days-num" style="color:var(--{{ $colorClass === 'green' ? 'green' : ($colorClass === 'orange' ? 'orange' : 'red') }})">{{ $daysLeft }}</div>
        <div class="days-lbl">gün kaldı</div>
      </div>
    </div>
  @endif

  {{-- Bekleyen yenileme talebi --}}
  @if($isPending)
    <div class="status-card pending" style="margin-bottom:24px">
      <div class="status-icon">⏳</div>
      <div class="status-info">
        <div class="status-title orange">Yenileme Talebiniz Bekliyor</div>
        <div class="status-desc">
          {{ $user->subscription_type === 'yearly' ? 'Yıllık' : 'Aylık' }} plan talebi admin onayı bekliyor.<br>
          @if($expiresAt && $expiresAt->isFuture())
            Mevcut aboneliğiniz <strong style="color:var(--text)">{{ $expiresAt->format('d.m.Y') }}</strong> tarihine kadar geçerli, erişiminiz devam ediyor.
          @else
            Onaylanınca sisteme erişebilirsiniz.
          @endif
        </div>
      </div>
    </div>
  @endif

  <form method="GET" action="{{ route('payment.checkout') }}" id="planForm">
    <div class="plans">
      <label class="plan" id="planMonthly" onclick="selectPlan('monthly')">
        <input type="radio" name="plan" value="monthly" id="rMonthly">
        <div class="plan-period">Aylık</div>
        <div class="plan-price">₺{{ number_format((float) \App\Models\Setting::get('price_monthly', '299.00'), 0, ',', '.') }}<span>/ay</span></div>
        <div class="plan-desc">Her ay yenilenir.<br>İstediğin zaman değiştirebilirsin.</div>
      </label>

      <label class="plan" id="planQuarterly" onclick="selectPlan('quarterly')">
        <div class="plan-badge" style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">Popüler</div>
        <input type="radio" name="plan" value="quarterly" id="rQuarterly">
        <div class="plan-period">3 Aylık</div>
        <div class="plan-price">₺{{ number_format((float) \App\Models\Setting::get('price_quarterly', '799.00'), 0, ',', '.') }}<span>/3ay</span></div>
        <div class="plan-desc">3 ay tek ödeme.<br>Aylık ödemeden daha uygun.</div>
      </label>

      <label class="plan" id="planSemi" onclick="selectPlan('semi_yearly')">
        <div class="plan-badge" style="background:linear-gradient(135deg,#059669,#10b981)">Tasarruf</div>
        <input type="radio" name="plan" value="semi_yearly" id="rSemi">
        <div class="plan-period">6 Aylık</div>
        <div class="plan-price">₺{{ number_format((float) \App\Models\Setting::get('price_semi_yearly', '1490.00'), 0, ',', '.') }}<span>/6ay</span></div>
        <div class="plan-desc">6 ay tek ödeme.<br>En iyi kısa vadeli seçenek.</div>
      </label>

      <label class="plan" id="planYearly" onclick="selectPlan('yearly')">
        <div class="plan-badge">💰 Daha Uygun</div>
        <input type="radio" name="plan" value="yearly" id="rYearly">
        <div class="plan-period">Yıllık</div>
        <div class="plan-price">₺{{ number_format((float) \App\Models\Setting::get('price_yearly', '2799.00'), 0, ',', '.') }}<span>/yıl</span></div>
        <div class="plan-desc">12 ay tek ödeme.<br>2 ay ücretsiz kazanırsın.</div>
      </label>
    </div>

    <button type="submit" class="btn-submit" id="btnSubmit" disabled>
      💳 Ödeme Yap →
    </button>
  </form>

  <div class="footer">
    @if(auth()->user()->isSubscriptionActive())
      <a href="{{ route('adisyon.index') }}">← Adisyon'a Dön</a>
    @else
      <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logoutForm').submit()">Çıkış Yap</a>
    @endif
  </div>
  <form id="logoutForm" method="POST" action="{{ route('logout') }}" style="display:none">@csrf</form>
</div>

<script>
function selectPlan(type) {
  document.getElementById('rMonthly').checked   = (type === 'monthly');
  document.getElementById('rQuarterly').checked = (type === 'quarterly');
  document.getElementById('rSemi').checked      = (type === 'semi_yearly');
  document.getElementById('rYearly').checked    = (type === 'yearly');
  ['planMonthly','planQuarterly','planSemi','planYearly'].forEach(function(id) {
    var map = {planMonthly:'monthly',planQuarterly:'quarterly',planSemi:'semi_yearly',planYearly:'yearly'};
    document.getElementById(id).classList.toggle('selected', map[id] === type);
  });
  document.getElementById('btnSubmit').disabled = false;
}
</script>
</body>
</html>
