<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $user->name }} — Admin Paneli</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#0d0d0d;--s1:#131313;--s2:#1a1a1a;--s3:#1e1e1e;
  --border:#2a2a2a;--border2:#333;
  --text:#f0f0f0;--muted:#6b7280;--muted2:#9ca3af;
  --primary:#27A0B1;--green:#10b981;--red:#ef4444;--orange:#f59e0b;
}
body{background:var(--bg);color:var(--text);font-family:'Inter',sans-serif;min-height:100vh}

.topbar{
  height:52px;background:var(--s2);border-bottom:1px solid var(--border2);
  display:flex;align-items:center;padding:0 16px;gap:10px;
  position:sticky;top:0;z-index:10;
}
.topbar-brand{font-size:.95rem;font-weight:800;color:var(--text);
  display:flex;align-items:center;gap:8px;white-space:nowrap}
.topbar-brand .badge-admin{
  background:var(--primary);color:#fff;border-radius:6px;
  padding:2px 8px;font-size:.65rem;font-weight:700;letter-spacing:.5px}
.topbar-spacer{flex:1}
.tb-btn{
  display:inline-flex;align-items:center;gap:4px;
  padding:0 11px;height:30px;border-radius:6px;
  font-size:.72rem;font-weight:600;cursor:pointer;
  border:1px solid var(--border2);background:var(--s3);
  color:var(--muted2);font-family:inherit;transition:all .15s;
  white-space:nowrap;text-decoration:none;
}
.tb-btn:hover{color:var(--text);border-color:var(--primary)}

.page{max-width:900px;margin:0 auto;padding:24px 16px}

.card{background:var(--s2);border:1px solid var(--border);border-radius:14px;overflow:hidden;margin-bottom:18px}
.card-head{
  padding:14px 18px;border-bottom:1px solid var(--border);
  display:flex;align-items:center;justify-content:space-between;
}
.card-head h2{font-size:.85rem;font-weight:700}
.card-head small{font-size:.72rem;color:var(--muted)}

.badge{
  display:inline-flex;align-items:center;padding:2px 9px;
  border-radius:99px;font-size:.65rem;font-weight:700;white-space:nowrap}
.badge.green{background:rgba(16,185,129,.12);color:var(--green);border:1px solid rgba(16,185,129,.25)}
.badge.orange{background:rgba(245,158,11,.12);color:var(--orange);border:1px solid rgba(245,158,11,.3)}
.badge.red{background:rgba(239,68,68,.12);color:var(--red);border:1px solid rgba(239,68,68,.3)}
.badge.grey{background:rgba(107,114,128,.1);color:var(--muted);border:1px solid var(--border)}
.badge.primary{background:rgba(39,160,177,.15);color:var(--primary);border:1px solid rgba(39,160,177,.3)}

/* Info grid */
.info-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px;padding:18px}
.info-item{display:flex;flex-direction:column;gap:4px}
.info-label{font-size:.65rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px}
.info-value{font-size:.88rem;font-weight:600;color:var(--text)}
.info-value.mono{font-family:'Courier New',monospace;font-size:.78rem;color:var(--muted2)}

/* Waiter table */
.tbl-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse}
thead tr{border-bottom:1px solid var(--border)}
thead th{
  padding:9px 14px;font-size:.65rem;font-weight:700;color:var(--muted);
  text-transform:uppercase;letter-spacing:.5px;text-align:left;
  background:var(--s1);white-space:nowrap;
}
tbody tr{border-bottom:1px solid var(--border);transition:background .12s}
tbody tr:last-child{border-bottom:none}
tbody tr:hover{background:var(--s3)}
td{padding:11px 14px;font-size:.78rem;color:var(--text);vertical-align:middle}

.empty-msg{padding:28px 18px;text-align:center;color:var(--muted);font-size:.85rem}

/* Stats row */
.stat-row{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:18px}
.stat-card{
  background:var(--s2);border:1px solid var(--border);
  border-radius:12px;padding:16px 18px;
}
.stat-card .val{font-size:1.7rem;font-weight:800;color:var(--text);line-height:1}
.stat-card .lbl{font-size:.65rem;color:var(--muted);margin-top:5px;
  text-transform:uppercase;letter-spacing:.5px}

@media(max-width:640px){
  .stat-row{grid-template-columns:1fr 1fr}
  .stat-card .val{font-size:1.3rem}
  .info-grid{grid-template-columns:1fr}
}
</style>
</head>
<body>

<div class="topbar">
  <div class="topbar-brand">
    🍽️ Kafe POS
    <span class="badge-admin">ADMİN</span>
  </div>
  <div class="topbar-spacer"></div>
  <a href="{{ route('admin.index') }}" class="tb-btn">← Kullanıcılar</a>
</div>

<div class="page">

  {{-- ── Kullanıcı Bilgileri ── --}}
  <div class="card">
    <div class="card-head">
      <h2>👤 {{ $user->name }}</h2>
      <small>Kullanıcı Detayı</small>
    </div>
    <div class="info-grid">
      <div class="info-item">
        <span class="info-label">E-posta</span>
        <span class="info-value mono">{{ $user->email }}</span>
      </div>
      <div class="info-item">
        <span class="info-label">Kayıt Tarihi</span>
        <span class="info-value">{{ $user->created_at->format('d.m.Y H:i') }}</span>
      </div>
      <div class="info-item">
        <span class="info-label">Masa Sayısı</span>
        <span class="info-value">{{ $user->rooms_count }}</span>
      </div>
      <div class="info-item">
        <span class="info-label">Ürün Sayısı</span>
        <span class="info-value">{{ $user->products_count }}</span>
      </div>
      <div class="info-item">
        <span class="info-label">Abonelik Durumu</span>
        <span class="info-value">
          @php $st = $user->subscription_status; $tp = $user->subscription_type; @endphp
          @if($st === 'active')
            <span class="badge green">✓ Aktif{{ $tp ? ' — '.(['monthly'=>'Aylık','quarterly'=>'3 Aylık','semi_yearly'=>'6 Aylık','yearly'=>'Yıllık'][$tp] ?? $tp) : '' }}</span>
          @elseif($st === 'pending')
            <span class="badge orange">⏳ Bekliyor{{ $tp ? ' — '.(['monthly'=>'Aylık','quarterly'=>'3 Aylık','semi_yearly'=>'6 Aylık','yearly'=>'Yıllık'][$tp] ?? $tp) : '' }}</span>
          @elseif($st === 'rejected')
            <span class="badge red">✕ Reddedildi</span>
          @elseif($st === 'expired')
            <span class="badge red">⏰ Süresi Doldu</span>
          @else
            <span class="badge grey">Talep Yok</span>
          @endif
        </span>
      </div>
      <div class="info-item">
        <span class="info-label">Abonelik Bitiş</span>
        <span class="info-value">
          @if($user->subscription_expires_at)
            {{ $user->subscription_expires_at->format('d.m.Y') }}
            @php $kalan = (int) now()->startOfDay()->diffInDays($user->subscription_expires_at->startOfDay(), false); @endphp
            @if($kalan > 0)
              <span style="color:var(--muted2);font-size:.75rem;margin-left:4px">({{ $kalan }} gün kaldı)</span>
            @elseif($kalan === 0)
              <span style="color:var(--red);font-size:.75rem;margin-left:4px">(Bugün bitiyor)</span>
            @else
              <span style="color:var(--red);font-size:.75rem;margin-left:4px">({{ abs($kalan) }} gün önce doldu)</span>
            @endif
          @else
            <span style="color:var(--muted)">—</span>
          @endif
        </span>
      </div>
      <div class="info-item">
        <span class="info-label">Rol</span>
        <span class="info-value">
          <span class="badge primary">{{ $user->role === 'owner' ? 'İşletme Sahibi' : ucfirst($user->role) }}</span>
        </span>
      </div>
      <div class="info-item">
        <span class="info-label">Garson Sayısı</span>
        <span class="info-value">{{ $user->waiters->count() }}</span>
      </div>
    </div>
  </div>

  {{-- ── İstatistikler ── --}}
  <div class="stat-row">
    <div class="stat-card">
      <div class="val">{{ $user->rooms_count }}</div>
      <div class="lbl">Masa</div>
    </div>
    <div class="stat-card">
      <div class="val">{{ $user->products_count }}</div>
      <div class="lbl">Ürün</div>
    </div>
    <div class="stat-card">
      <div class="val">{{ $user->waiters->count() }}</div>
      <div class="lbl">Garson</div>
    </div>
    <div class="stat-card">
      <div class="val">
        @if($user->subscription_expires_at)
          @php $k = (int) now()->startOfDay()->diffInDays($user->subscription_expires_at->startOfDay(), false); @endphp
          {{ max($k, 0) }}
        @else
          0
        @endif
      </div>
      <div class="lbl">Kalan Gün</div>
    </div>
  </div>

  {{-- ── Garsonlar ── --}}
  <div class="card">
    <div class="card-head">
      <h2>👥 Garsonlar</h2>
      <small>{{ $user->waiters->count() }} garson</small>
    </div>
    @if($user->waiters->isEmpty())
      <div class="empty-msg">Bu kullanıcının henüz garson eklenmemiş.</div>
    @else
      <div class="tbl-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Ad</th>
              <th>E-posta</th>
              <th>Eklenme Tarihi</th>
              <th>Abonelik Durumu</th>
              <th>Abonelik Bitiş</th>
            </tr>
          </thead>
          <tbody>
            @foreach($user->waiters as $i => $waiter)
            <tr>
              <td style="color:var(--muted)">{{ $i + 1 }}</td>
              <td><strong>{{ $waiter->name }}</strong></td>
              <td style="font-family:'Courier New',monospace;font-size:.72rem;color:var(--muted2)">{{ $waiter->email }}</td>
              <td style="color:var(--muted2);white-space:nowrap">{{ $waiter->created_at->format('d.m.Y H:i') }}</td>
              <td>
                @if($waiter->subscription_status === 'active')
                  <span class="badge green">✓ Aktif</span>
                @else
                  <span class="badge grey">{{ $waiter->subscription_status ?? '—' }}</span>
                @endif
              </td>
              <td style="white-space:nowrap;color:var(--muted2);font-size:.75rem">
                @if($waiter->subscription_expires_at)
                  {{ $waiter->subscription_expires_at->format('d.m.Y') }}
                @else
                  Sahibine bağlı
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

</div>

</body>
</html>
