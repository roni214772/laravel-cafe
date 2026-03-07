<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Paneli — Kafe POS</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#0d0d0d;--s1:#131313;--s2:#1a1a1a;--s3:#1e1e1e;
  --border:#2a2a2a;--border2:#333;
  --text:#f0f0f0;--muted:#6b7280;--muted2:#9ca3af;
  --primary:#27A0B1;--green:#10b981;--red:#ef4444;
}
body{background:var(--bg);color:var(--text);font-family:'Inter',sans-serif;min-height:100vh}

/* ── Topbar ── */
.topbar{
  height:52px;background:var(--s2);border-bottom:1px solid var(--border2);
  display:flex;align-items:center;padding:0 16px;gap:10px;
  position:sticky;top:0;z-index:10;
}
.topbar-brand{font-size:.95rem;font-weight:800;color:var(--text);
  display:flex;align-items:center;gap:8px;white-space:nowrap;min-width:0}
.topbar-brand .badge-admin{
  background:var(--primary);color:#fff;border-radius:6px;
  padding:2px 8px;font-size:.65rem;font-weight:700;letter-spacing:.5px;flex-shrink:0}
.topbar-brand .badge-notify{
  background:var(--orange);color:#000;border-radius:99px;
  padding:2px 10px;font-size:.65rem;font-weight:800;flex-shrink:0;animation:pulse 1.5s infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.6}}
.topbar-spacer{flex:1;min-width:0}
.topbar-right{display:flex;align-items:center;gap:6px;flex-shrink:0}
.tb-btn{
  display:inline-flex;align-items:center;gap:4px;
  padding:0 11px;height:30px;border-radius:6px;
  font-size:.72rem;font-weight:600;cursor:pointer;
  border:1px solid var(--border2);background:var(--s3);
  color:var(--muted2);font-family:inherit;transition:all .15s;
  white-space:nowrap;text-decoration:none;
}
.tb-btn:hover{color:var(--text);border-color:var(--primary)}
.tb-btn.danger{color:var(--red);border-color:#5a2020}
.tb-btn.danger:hover{background:#2a1010;border-color:var(--red)}

/* ── Page ── */
.page{max-width:1100px;margin:0 auto;padding:24px 16px}

/* ── Alert ── */
.alert{border-radius:8px;padding:10px 16px;font-size:.78rem;margin-bottom:18px;line-height:1.5}
.alert.success{background:#0a2a1a;border:1px solid #1a5a3a;color:#6ee7b7}
.alert.error{background:#2a1010;border:1px solid #5a2020;color:#f87171}

/* ── Stats ── */
.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:24px}
.stat-card{
  background:var(--s2);border:1px solid var(--border);
  border-radius:12px;padding:16px 18px;
}
.stat-card .val{font-size:1.9rem;font-weight:800;color:var(--text);line-height:1}
.stat-card .lbl{font-size:.68rem;color:var(--muted);margin-top:5px;
  text-transform:uppercase;letter-spacing:.5px}

/* ── Card shell ── */
.card{background:var(--s2);border:1px solid var(--border);border-radius:14px;overflow:hidden}
.card-head{
  padding:14px 18px;border-bottom:1px solid var(--border);
  display:flex;align-items:center;justify-content:space-between;
}
.card-head h2{font-size:.85rem;font-weight:700}
.card-head small{font-size:.72rem;color:var(--muted)}

/* ── Desktop table ── */
.tbl-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}
table{width:100%;border-collapse:collapse;min-width:620px}
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
td.muted{color:var(--muted2)}
td.mono{font-family:'Courier New',monospace;font-size:.72rem;color:var(--muted2)}

.badge{
  display:inline-flex;align-items:center;padding:2px 9px;
  border-radius:99px;font-size:.65rem;font-weight:700;white-space:nowrap}
.badge.admin{background:rgba(39,160,177,.15);color:var(--primary);border:1px solid rgba(39,160,177,.3)}
.badge.user{background:rgba(156,163,175,.08);color:var(--muted2);border:1px solid var(--border)}
.badge.green{background:rgba(16,185,129,.12);color:var(--green);border:1px solid rgba(16,185,129,.25)}
.badge.orange{background:rgba(245,158,11,.12);color:var(--orange);border:1px solid rgba(245,158,11,.3)}
.badge.red{background:rgba(239,68,68,.12);color:var(--red);border:1px solid rgba(239,68,68,.3)}
.badge.grey{background:rgba(107,114,128,.1);color:var(--muted);border:1px solid var(--border)}

.td-actions{display:flex;gap:5px;align-items:center}
.btn-imp,.btn-del{
  padding:3px 9px;height:25px;font-size:.68rem;font-weight:600;
  border-radius:5px;cursor:pointer;font-family:inherit;transition:all .12s;
  display:inline-flex;align-items:center;gap:3px;white-space:nowrap;
}
.btn-approve{border:1px solid rgba(16,185,129,.4);background:transparent;color:var(--green)}
.btn-approve:hover{background:rgba(16,185,129,.1)}
.extend-select{
  background:var(--s3);border:1px solid var(--border2);color:var(--muted2);
  border-radius:5px;padding:0 6px;height:26px;font-size:.7rem;font-family:inherit;
  cursor:pointer;
}
.extend-select:focus{outline:none;border-color:var(--primary)}
.btn-reject{border:1px solid #5a2020;background:transparent;color:var(--red)}
.btn-reject:hover{background:#2a1010}

/* ── Mobile cards (< 640px) ── */
.mob-list{display:none;flex-direction:column}
.mob-item{
  padding:14px 16px;
  border-bottom:1px solid var(--border);
}
.mob-item:last-child{border-bottom:none}
.mob-item-top{display:flex;align-items:center;gap:10px;margin-bottom:8px}
.mob-avatar{
  width:36px;height:36px;border-radius:9px;background:var(--s3);
  border:1px solid var(--border);display:flex;align-items:center;
  justify-content:center;font-size:1.1rem;font-weight:700;flex-shrink:0;
  color:var(--primary);
}
.mob-name{font-size:.88rem;font-weight:700;line-height:1.2}
.mob-email{font-size:.68rem;color:var(--muted2);font-family:'Courier New',monospace;margin-top:2px;word-break:break-all}
.mob-meta{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:10px}
.mob-tag{
  font-size:.68rem;color:var(--muted2);background:var(--s3);
  border:1px solid var(--border);border-radius:6px;padding:2px 8px;
}
.mob-tag strong{color:var(--text)}
.mob-actions{display:flex;gap:7px;flex-wrap:wrap}

@media(max-width:640px){
  .tbl-wrap{display:none}
  .mob-list{display:flex}
  .stat-card .val{font-size:1.5rem}
  .stat-card .lbl{font-size:.6rem}
  .stat-card{padding:12px 10px}
}
@media(max-width:380px){
  .stats{grid-template-columns:1fr 1fr}
  .stats .stat-card:last-child{grid-column:span 2}
}
</style>
</head>
<body>

<div class="topbar">
  <div class="topbar-brand">
    🍽️ Kafe POS
    <span class="badge-admin">ADMİN</span>
    <?php if($pendingCount > 0): ?>
      <span class="badge-notify"><?php echo e($pendingCount); ?> bekliyor</span>
    <?php endif; ?>
  </div>
  <div class="topbar-spacer"></div>
  <div class="topbar-right">
    <a href="<?php echo e(route('adisyon.index')); ?>" class="tb-btn">← Adisyon</a>
    <a href="<?php echo e(route('admin.login')); ?>" class="tb-btn" title="Admin giriş sayfası">🔑 Giriş URL</a>
    <form method="POST" action="<?php echo e(route('logout')); ?>" style="display:inline">
      <?php echo csrf_field(); ?>
      <button type="submit" class="tb-btn danger">↪ Çıkış</button>
    </form>
  </div>
</div>

<div class="page">

  <?php if(session('status')): ?>
    <div class="alert success">✓ <?php echo e(session('status')); ?></div>
  <?php endif; ?>
  <?php if($errors->any()): ?>
    <div class="alert error"><?php echo e($errors->first()); ?></div>
  <?php endif; ?>

  
  <div class="card" style="margin-bottom:18px">
    <div class="card-head">
      <h2>💳 Abonelik Fiyatları</h2>
      <small>Kullanıcılara gösterilen ücretler</small>
    </div>
    <div style="padding:18px 20px">
      <form method="POST" action="<?php echo e(route('admin.update-prices')); ?>" style="display:flex;gap:14px;flex-wrap:wrap;align-items:flex-end">
        <?php echo csrf_field(); ?>
        <div style="display:flex;flex-direction:column;gap:5px">
          <label style="font-size:.67rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px">Aylık (₺)</label>
          <input type="number" name="price_monthly" value="<?php echo e($priceMonthly); ?>" step="0.01" min="1"
                 style="background:var(--s3);border:1px solid var(--border2);color:var(--text);border-radius:8px;padding:8px 12px;font-size:.88rem;font-family:inherit;outline:none;width:130px">
        </div>
        <div style="display:flex;flex-direction:column;gap:5px">
          <label style="font-size:.67rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px">3 Aylık (₺)</label>
          <input type="number" name="price_quarterly" value="<?php echo e($priceQuarterly); ?>" step="0.01" min="1"
                 style="background:var(--s3);border:1px solid var(--border2);color:var(--text);border-radius:8px;padding:8px 12px;font-size:.88rem;font-family:inherit;outline:none;width:130px">
        </div>
        <div style="display:flex;flex-direction:column;gap:5px">
          <label style="font-size:.67rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px">6 Aylık (₺)</label>
          <input type="number" name="price_semi_yearly" value="<?php echo e($priceSemi); ?>" step="0.01" min="1"
                 style="background:var(--s3);border:1px solid var(--border2);color:var(--text);border-radius:8px;padding:8px 12px;font-size:.88rem;font-family:inherit;outline:none;width:130px">
        </div>
        <div style="display:flex;flex-direction:column;gap:5px">
          <label style="font-size:.67rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px">Yıllık (₺)</label>
          <input type="number" name="price_yearly" value="<?php echo e($priceYearly); ?>" step="0.01" min="1"
                 style="background:var(--s3);border:1px solid var(--border2);color:var(--text);border-radius:8px;padding:8px 12px;font-size:.88rem;font-family:inherit;outline:none;width:130px">
        </div>
        <button type="submit" class="btn-imp btn-approve" style="height:36px;padding:0 18px;font-size:.78rem">
          ✓ Kaydet
        </button>
      </form>
    </div>
  </div>

  
  <div class="card" style="margin-bottom:18px">
    <div class="card-head">
      <h2>🏦 Havale / EFT Bilgileri</h2>
      <small>Kullanıcılara gösterilecek banka hesabı</small>
    </div>
    <div style="padding:18px 20px">
      <form method="POST" action="<?php echo e(route('admin.update-bank')); ?>" style="display:flex;gap:14px;flex-wrap:wrap;align-items:flex-end">
        <?php echo csrf_field(); ?>
        <div style="display:flex;flex-direction:column;gap:5px">
          <label style="font-size:.67rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px">Banka Adı</label>
          <input type="text" name="bank_name" value="<?php echo e($bankName); ?>" placeholder="Ziraat Bankası"
                 style="background:var(--s3);border:1px solid var(--border2);color:var(--text);border-radius:8px;padding:8px 12px;font-size:.85rem;font-family:inherit;outline:none;width:180px">
        </div>
        <div style="display:flex;flex-direction:column;gap:5px">
          <label style="font-size:.67rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px">IBAN</label>
          <input type="text" name="bank_iban" value="<?php echo e($bankIban); ?>" placeholder="TR00 0000 0000 0000 0000 0000 00"
                 style="background:var(--s3);border:1px solid var(--border2);color:var(--text);border-radius:8px;padding:8px 12px;font-size:.85rem;font-family:'Courier New',monospace;outline:none;width:280px;letter-spacing:.5px">
        </div>
        <div style="display:flex;flex-direction:column;gap:5px">
          <label style="font-size:.67rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px">Hesap Sahibi</label>
          <input type="text" name="bank_account_holder" value="<?php echo e($bankHolder); ?>" placeholder="Ad Soyad"
                 style="background:var(--s3);border:1px solid var(--border2);color:var(--text);border-radius:8px;padding:8px 12px;font-size:.85rem;font-family:inherit;outline:none;width:200px">
        </div>
        <button type="submit" class="btn-imp btn-approve" style="height:36px;padding:0 18px;font-size:.78rem">
          ✓ Kaydet
        </button>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-head">
      <h2>Kullanıcılar</h2>
      <small><?php echo e($totalUsers); ?> kayıt</small>
    </div>
    
    <div class="tbl-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Ad</th>
          <th>E-posta</th>
          <th>Kayıt</th>
          <th>Masa</th>
          <th>Abonelik</th>
          <th>Bitiş</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
          <td class="muted"><?php echo e($i + 1); ?></td>
          <td><strong><?php echo e($user->name); ?></strong></td>
          <td class="mono"><?php echo e($user->email); ?></td>
          <td class="muted" style="white-space:nowrap"><?php echo e($user->created_at->format('d.m.Y H:i')); ?></td>
          <td><span class="badge green"><?php echo e($user->rooms_count); ?></span></td>
          <td>
            <?php
              $st = $user->subscription_status;
              $tp = $user->subscription_type;
            ?>
            <?php if($user->email === auth()->user()->email): ?>
              <span class="badge admin">Admin</span>
            <?php elseif($st === 'pending'): ?>
              <span class="badge orange">⏳ Bekliyor — <?php echo e($tp === 'yearly' ? 'Yıllık' : 'Aylık'); ?></span>
            <?php elseif($st === 'active'): ?>
              <span class="badge green">✓ Aktif — <?php echo e($tp === 'yearly' ? 'Yıllık' : 'Aylık'); ?></span>
            <?php elseif($st === 'rejected'): ?>
              <span class="badge red">✕ Reddedildi</span>
            <?php elseif($st === 'expired'): ?>
              <span class="badge red">⏰ Süresi Doldu</span>
            <?php else: ?>
              <span class="badge grey">Talep Yok</span>
            <?php endif; ?>
          </td>
          <td class="muted" style="white-space:nowrap;font-size:.72rem">
            <?php echo e($user->subscription_expires_at ? $user->subscription_expires_at->format('d.m.Y') : '—'); ?>

          </td>
          <td>
            <?php if($user->email !== auth()->user()->email): ?>
              <div class="td-actions">
                <?php if($user->subscription_status === 'pending'): ?>
                  <form method="POST" action="<?php echo e(route('admin.approve', $user)); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn-imp btn-approve">✓ Onayla</button>
                  </form>
                  <form method="POST" action="<?php echo e(route('admin.reject', $user)); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn-del btn-reject">✕ Reddet</button>
                  </form>
                <?php elseif($user->subscription_status === 'active'): ?>
                  <form method="POST" action="<?php echo e(route('admin.approve', $user)); ?>" style="display:flex;align-items:center;gap:4px">
                    <?php echo csrf_field(); ?>
                    <select name="extend_type" class="extend-select">
                      <option value="monthly">Aylık</option>
                      <option value="quarterly">3 Aylık</option>
                      <option value="semi_yearly">6 Aylık</option>
                      <option value="yearly">Yıllık</option>
                    </select>
                    <button type="submit" class="btn-imp" title="Süreyi uzat">🔄 Uzat</button>
                  </form>
                  <form method="POST" action="<?php echo e(route('admin.cancel', $user)); ?>"
                        onsubmit="return confirm('<?php echo e(addslashes($user->name)); ?> aboneliği iptal edilsin mi?')">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn-del">✕ İptal</button>
                  </form>
                <?php endif; ?>
                <button type="button" class="btn-imp" onclick="openChgPw(<?php echo e($user->id); ?>, '<?php echo e(addslashes($user->name)); ?>')">🔑 Şifre</button>
                <form method="POST" action="<?php echo e(route('admin.impersonate', $user)); ?>">
                  <?php echo csrf_field(); ?>
                  <button type="submit" class="btn-imp">👁 Giriş</button>
                </form>
                <form method="POST" action="<?php echo e(route('admin.delete-user', $user)); ?>"
                      onsubmit="return confirm('<?php echo e(addslashes($user->name)); ?> silinsin mi?')">
                  <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                  <button type="submit" class="btn-del">Sil</button>
                </form>
              </div>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
    </div>

    
    <div class="mob-list">
      <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="mob-item">
        <div class="mob-item-top">
          <div class="mob-avatar"><?php echo e(mb_substr($user->name, 0, 1)); ?></div>
          <div style="min-width:0">
            <div class="mob-name">
              <?php echo e($user->name); ?>

              <?php if($user->email === auth()->user()->email): ?>
                <span class="badge admin" style="margin-left:4px">Admin</span>
              <?php endif; ?>
            </div>
            <div class="mob-email"><?php echo e($user->email); ?></div>
          </div>
        </div>
        <div class="mob-meta">
          <span class="mob-tag"><strong><?php echo e($user->rooms_count); ?></strong> masa</span>
          <?php $st = $user->subscription_status; ?>
          <?php if($st === 'pending'): ?>
            <span class="mob-tag" style="color:var(--orange);border-color:rgba(245,158,11,.3)">⏳ Bekliyor — <?php echo e($user->subscription_type === 'yearly' ? 'Yıllık' : 'Aylık'); ?></span>
          <?php elseif($st === 'active'): ?>
            <span class="mob-tag" style="color:var(--green);border-color:rgba(16,185,129,.3)">✓ Aktif</span>
          <?php elseif($st === 'rejected'): ?>
            <span class="mob-tag" style="color:var(--red);border-color:rgba(239,68,68,.3)">✕ Reddedildi</span>
          <?php elseif($st === 'expired'): ?>
            <span class="mob-tag" style="color:var(--red)">⏰ Süresi Doldu</span>
          <?php else: ?>
            <span class="mob-tag">Talep Yok</span>
          <?php endif; ?>
          <span class="mob-tag"><?php echo e($user->created_at->format('d.m.Y')); ?></span>
        </div>
        <?php if($user->email !== auth()->user()->email): ?>
        <div class="mob-actions">
          <?php if($user->subscription_status === 'pending'): ?>
            <form method="POST" action="<?php echo e(route('admin.approve', $user)); ?>">
              <?php echo csrf_field(); ?>
              <button type="submit" class="btn-imp btn-approve">✓ Onayla</button>
            </form>
            <form method="POST" action="<?php echo e(route('admin.reject', $user)); ?>">
              <?php echo csrf_field(); ?>
              <button type="submit" class="btn-del btn-reject">✕ Reddet</button>
            </form>
          <?php elseif($user->subscription_status === 'active'): ?>
            <form method="POST" action="<?php echo e(route('admin.approve', $user)); ?>" style="display:flex;align-items:center;gap:4px">
              <?php echo csrf_field(); ?>
              <select name="extend_type" class="extend-select">
                <option value="monthly">Aylık</option>
                <option value="quarterly">3 Aylık</option>
                <option value="semi_yearly">6 Aylık</option>
                <option value="yearly">Yıllık</option>
              </select>
              <button type="submit" class="btn-imp">🔄 Uzat</button>
            </form>
            <form method="POST" action="<?php echo e(route('admin.cancel', $user)); ?>"
                  onsubmit="return confirm('<?php echo e(addslashes($user->name)); ?> aboneliği iptal edilsin mi?')">
              <?php echo csrf_field(); ?>
              <button type="submit" class="btn-del">✕ İptal</button>
            </form>
          <?php endif; ?>
          <button type="button" class="btn-imp" onclick="openChgPw(<?php echo e($user->id); ?>, '<?php echo e(addslashes($user->name)); ?>')">🔑 Şifre</button>
          <form method="POST" action="<?php echo e(route('admin.impersonate', $user)); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn-imp">👁 Hesaba Giriş</button>
          </form>
          <form method="POST" action="<?php echo e(route('admin.delete-user', $user)); ?>"
                onsubmit="return confirm('<?php echo e(addslashes($user->name)); ?> silinsin mi?')">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button type="submit" class="btn-del">Sil</button>
          </form>
        </div>
        <?php endif; ?>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>

</div>


<div id="chgpw-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9000;align-items:center;justify-content:center">
  <div style="background:#1e1e2e;border-radius:12px;padding:28px 32px;width:360px;max-width:95vw;box-shadow:0 8px 32px rgba(0,0,0,.5)">
    <h3 style="margin:0 0 6px;color:#e2e8f0;font-size:1.1rem">🔑 Şifre Değiştir</h3>
    <p id="chgpw-username" style="margin:0 0 18px;color:#94a3b8;font-size:.85rem"></p>
    <form id="chgpw-form" method="POST">
      <?php echo csrf_field(); ?>
      <label style="display:block;margin-bottom:14px">
        <span style="font-size:.8rem;color:#94a3b8;display:block;margin-bottom:4px">Yeni Şifre</span>
        <input type="password" name="new_password" required minlength="6"
               style="width:100%;padding:9px 12px;border-radius:8px;border:1px solid #334155;background:#0f172a;color:#e2e8f0;font-size:.95rem;box-sizing:border-box">
      </label>
      <label style="display:block;margin-bottom:20px">
        <span style="font-size:.8rem;color:#94a3b8;display:block;margin-bottom:4px">Şifre Tekrar</span>
        <input type="password" name="new_password_confirmation" required minlength="6"
               style="width:100%;padding:9px 12px;border-radius:8px;border:1px solid #334155;background:#0f172a;color:#e2e8f0;font-size:.95rem;box-sizing:border-box">
      </label>
      <div style="display:flex;gap:10px;justify-content:flex-end">
        <button type="button" onclick="closeChgPw()"
                style="padding:8px 18px;border-radius:8px;border:1px solid #334155;background:transparent;color:#94a3b8;cursor:pointer">İptal</button>
        <button type="submit"
                style="padding:8px 18px;border-radius:8px;border:none;background:#6366f1;color:#fff;cursor:pointer;font-weight:600">Kaydet</button>
      </div>
    </form>
  </div>
</div>
<script>
function openChgPw(userId, userName) {
  document.getElementById('chgpw-username').textContent = userName;
  document.getElementById('chgpw-form').action = '/admin/user/' + userId + '/change-password';
  document.getElementById('chgpw-overlay').style.display = 'flex';
}
function closeChgPw() {
  document.getElementById('chgpw-overlay').style.display = 'none';
  document.getElementById('chgpw-form').reset();
}
document.getElementById('chgpw-overlay').addEventListener('click', function(e) {
  if (e.target === this) closeChgPw();
});
</script>
</body>
</html>
<?php /**PATH C:\Users\brusk\OneDrive\Masaüstü\eto\laravel-cafe\resources\views/admin/index.blade.php ENDPATH**/ ?>