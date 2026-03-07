<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Abonelik Bekleniyor — Kafe POS</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{--bg:#0d0d0d;--s2:#1a1a1a;--s3:#222;--border:#2e2e2e;--border2:#333;
      --text:#f0f0f0;--muted:#6b7280;--primary:#27A0B1;--orange:#f59e0b;--red:#ef4444;--green:#10b981}
body{background:var(--bg);color:var(--text);font-family:'Inter',sans-serif;min-height:100vh;
     display:flex;align-items:center;justify-content:center;padding:20px}
.card{background:var(--s2);border:1px solid var(--border2);border-radius:20px;
      padding:44px 36px;width:100%;max-width:420px;text-align:center;
      box-shadow:0 20px 60px rgba(0,0,0,.5)}
.icon{font-size:3rem;margin-bottom:20px;display:block}
h2{font-size:1.2rem;font-weight:800;margin-bottom:10px}
p{font-size:.82rem;color:var(--muted);line-height:1.7;margin-bottom:24px}
.info{
  background:var(--s3);border:1px solid var(--border2);border-radius:10px;
  padding:14px 16px;margin-bottom:24px;text-align:left;
}
.info-row{display:flex;justify-content:space-between;font-size:.78rem;padding:3px 0}
.info-row .lbl{color:var(--muted)}
.info-row .val{font-weight:700;color:var(--text)}
.badge-status{display:inline-block;padding:3px 12px;border-radius:99px;font-size:.72rem;font-weight:700}
.badge-pending{background:rgba(245,158,11,.15);color:var(--orange);border:1px solid rgba(245,158,11,.3)}
.badge-rejected{background:rgba(239,68,68,.12);color:var(--red);border:1px solid rgba(239,68,68,.3)}
.badge-expired{background:rgba(239,68,68,.12);color:var(--red);border:1px solid rgba(239,68,68,.3)}

.btn{display:block;width:100%;padding:12px;border-radius:10px;font-size:.88rem;
     font-weight:700;cursor:pointer;font-family:inherit;transition:opacity .15s;
     border:none;margin-bottom:10px}
.btn-primary{background:var(--primary);color:#fff}
.btn-primary:hover{opacity:.88}
.btn-outline{background:transparent;color:var(--muted);border:1px solid var(--border2)}
.btn-outline:hover{color:var(--text);border-color:var(--primary)}

@media(max-width:400px){
  .card{padding:32px 20px}
}
</style>
</head>
<body>
<div class="card">

  @if($user->subscription_status === 'pending')
    <span class="icon">⏳</span>
    <h2>Talebiniz İnceleniyor</h2>
    <p>
      Abonelik talebiniz alındı. Admin onaylayana kadar sisteme erişemezsiniz.<br>
      Genellikle birkaç saat içinde onaylanır.
    </p>
    <div class="info">
      <div class="info-row">
        <span class="lbl">Plan</span>
        <span class="val">{{ ['monthly'=>'Aylık','quarterly'=>'3 Aylık','semi_yearly'=>'6 Aylık','yearly'=>'Yıllık'][$user->subscription_type] ?? $user->subscription_type }}</span>
      </div>
      <div class="info-row">
        <span class="lbl">Talep Tarihi</span>
        <span class="val">{{ $user->subscription_requested_at?->format('d.m.Y H:i') }}</span>
      </div>
      <div class="info-row">
        <span class="lbl">Durum</span>
        <span class="val"><span class="badge-status badge-pending">⏳ Bekliyor</span></span>
      </div>
    </div>

  @elseif($user->subscription_status === 'rejected')
    <span class="icon">❌</span>
    <h2>Talebiniz Reddedildi</h2>
    <p>
      Abonelik talebiniz admin tarafından reddedildi.<br>
      Farklı bir plan seçerek tekrar talep edebilirsiniz.
    </p>
    <form method="POST" action="{{ route('subscription.request') }}">
      @csrf
      <input type="hidden" name="type" value="{{ $user->subscription_type ?? 'monthly' }}">
      <button type="submit" class="btn btn-primary">🔄 Tekrar Talep Gönder</button>
    </form>

  @elseif($user->subscription_status === 'expired')
    <span class="icon">⏰</span>
    <h2>Aboneliğiniz Sona Erdi</h2>
    <p>
      Abonelik süreniz doldu. Devam etmek için yeni bir abonelik planı seçin.
    </p>
    <a href="{{ route('subscription.select') }}" class="btn btn-primary" style="text-decoration:none;display:block;padding:12px">
      🔄 Yeni Abonelik Seç
    </a>

  @else
    <span class="icon">🔒</span>
    <h2>Erişim Kısıtlandı</h2>
    <p>Bu sayfaya erişmek için aktif bir aboneliğe ihtiyacınız var.</p>
    <a href="{{ route('subscription.select') }}" class="btn btn-primary" style="text-decoration:none;display:block;padding:12px">
      Abonelik Seç
    </a>
  @endif

  <form method="POST" action="{{ route('logout') }}" style="margin-top:6px">
    @csrf
    <button type="submit" class="btn btn-outline">↪ Çıkış Yap</button>
  </form>

</div>

{{-- Otomatik yenile (pending durumunda) --}}
@if($user->subscription_status === 'pending')
<script>setTimeout(() => location.reload(), 30000)</script>
@endif

</body>
</html>
