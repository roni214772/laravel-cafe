<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=<title>Ödeme Sonucu  Kafe POS</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{--bg:#0d0d0d;--s2:#1a1a1a;--border2:#333;
      --text:#f0f0f0;--muted:#6b7280;--primary:#27A0B1;--green:#10b981;--orange:#f59e0b;}
body{background:var(--bg);color:var(--text);font-family:'Inter',sans-serif;
     min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
.card{
  background:var(--s2);border:1px solid var(--border2);border-radius:18px;
  padding:48px 40px;width:100%;max-width:440px;text-align:center;
}
.icon{font-size:3.5rem;margin-bottom:20px;display:block}
h1{font-size:1.4rem;font-weight:800;margin-bottom:10px}
p{font-size:.85rem;color:var(--muted);line-height:1.6;margin-bottom:28px}
.badge{
  display:inline-block;padding:5px 16px;border-radius:99px;font-size:.72rem;font-weight:700;
  margin-bottom:18px;
}
.badge.pending{background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.3);color:var(--orange)}
.btn{
  display:inline-block;padding:12px 28px;border-radius:10px;
  font-size:.88rem;font-weight:700;text-decoration:none;
  cursor:pointer;border:none;transition:opacity .15s;font-family:inherit;
}
.btn.primary{background:var(--primary);color:#fff}
.btn.primary:hover{opacity:.88}
</style>
</head>
<body>
<div class="card">
  <?php if(session('payment_pending')): ?>
    <span class="icon"></span>
    <div class="badge pending">Ödeme Bekleniyor</div>
    <h1 style="color:var(--orange)">Bildiriminiz Alındı!</h1>
    <p>
      Havale bildiriminiz iletildi.<br>
      Admin ödemenizi onayladıktan sonra aboneliğiniz aktif olacak.<br>
      <span style="color:#6b7280;font-size:.78rem">(Genellikle birkaç dakika içinde onaylanır)</span>
    </p>
    <a href="<?php echo e(route('subscription.pending')); ?>" class="btn primary">Durumu Takip Et</a>
  <?php else: ?>
    <span class="icon"></span>
    <h1 style="color:#ef4444">Bir sorun oluştu</h1>
    <p><?php echo e(session('error_msg') ?: 'Lütfen tekrar deneyin.'); ?></p>
    <a href="<?php echo e(route('subscription.select')); ?>" class="btn primary">Tekrar Dene</a>
primary">Tekrar Dene</a>
    <a href="<?php echo e(route('adisyon.index')); ?>" class="btn outline">Vazgeç</a>
  <?php endif; ?>
</div>
</body>
</html>
<?php /**PATH C:\Users\brusk\OneDrive\Masaüstü\eto\laravel-cafe\resources\views/payment/result.blade.php ENDPATH**/ ?>