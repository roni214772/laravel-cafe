<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Kayıt Ol — Kafe POS</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
  *{box-sizing:border-box;margin:0;padding:0}
  :root{--bg:#0d0d0d;--s1:#131313;--s2:#1a1a1a;--s3:#222;--border:#2e2e2e;--border2:#333;
        --text:#f0f0f0;--muted:#6b7280;--primary:#27A0B1;--green:#10b981;--red:#ef4444}
  body{background:var(--bg);color:var(--text);font-family:'Inter',sans-serif;min-height:100vh;
       display:flex;align-items:center;justify-content:center;padding:20px}
  .card{background:var(--s2);border:1px solid var(--border2);border-radius:16px;padding:40px 36px;
        width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.5)}
  .logo{display:flex;align-items:center;gap:10px;margin-bottom:32px;justify-content:center}
  .logo-icon{width:42px;height:42px;background:var(--primary);border-radius:12px;
             display:flex;align-items:center;justify-content:center;font-size:1.3rem}
  .logo-name{font-size:1.35rem;font-weight:800;color:var(--text)}
  .logo-sub{font-size:.72rem;color:var(--muted);margin-top:1px}
  h2{font-size:1.1rem;font-weight:700;margin-bottom:24px;color:var(--text);text-align:center}
  .fg{display:flex;flex-direction:column;gap:5px;margin-bottom:14px}
  .fg label{font-size:.7rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px}
  .fg input{background:var(--s3);border:1px solid var(--border);color:var(--text);border-radius:9px;
            padding:11px 14px;font-size:.88rem;font-family:inherit;outline:none;transition:border-color .15s}
  .fg input:focus{border-color:var(--primary)}
  .fg .hint{font-size:.68rem;color:var(--muted);margin-top:2px}
  .error{background:#2a1010;border:1px solid #5a2020;color:#f87171;border-radius:8px;
         padding:10px 14px;font-size:.78rem;margin-bottom:16px}
  .btn-submit{width:100%;padding:12px;background:var(--primary);color:#fff;border:none;
              border-radius:10px;font-size:.9rem;font-weight:700;cursor:pointer;
              font-family:inherit;transition:opacity .15s;margin-top:6px}
  .btn-submit:hover{opacity:.88}
  .footer{text-align:center;margin-top:22px;font-size:.78rem;color:var(--muted)}
  .footer a{color:var(--primary);text-decoration:none;font-weight:600}
  .footer a:hover{text-decoration:underline}
</style>
</head>
<body>
<div class="card">
  <div class="logo">
    <div class="logo-icon">☕</div>
    <div>
      <div class="logo-name">Kafe POS</div>
      <div class="logo-sub">Ücretsiz Hesap Oluştur</div>
    </div>
  </div>
  <h2>Yeni Hesap Oluştur</h2>

  <?php if($errors->any()): ?>
    <div class="error">
      <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div><?php echo e($error); ?></div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  <?php endif; ?>

  <form method="POST" action="<?php echo e(route('register')); ?>">
    <?php echo csrf_field(); ?>
    <div class="fg">
      <label>İşletme / Ad Soyad</label>
      <input type="text" name="name" value="<?php echo e(old('name')); ?>" placeholder="Örn: Café Luna" required autofocus>
    </div>
    <div class="fg">
      <label>E-posta</label>
      <input type="email" name="email" value="<?php echo e(old('email')); ?>" placeholder="kafe@ornek.com" required>
    </div>
    <div class="fg">
      <label>Şifre</label>
      <input type="password" name="password" placeholder="En az 6 karakter" required>
    </div>
    <div class="fg">
      <label>Şifre Tekrar</label>
      <input type="password" name="password_confirmation" placeholder="Şifreyi tekrar gir" required>
    </div>
    <button type="submit" class="btn-submit">Hesap Oluştur →</button>
  </form>

  <div class="footer">
    Zaten hesabın var mı? <a href="<?php echo e(route('login')); ?>">Giriş yap</a>
  </div>
</div>
</body>
</html>
<?php /**PATH C:\Users\brusk\OneDrive\Masaüstü\eto\laravel-cafe\resources\views/auth/register.blade.php ENDPATH**/ ?>