<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Giriş — Kafe POS</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
  *{box-sizing:border-box;margin:0;padding:0}
  :root{--bg:#0d0d0d;--s1:#131313;--s2:#1a1a1a;--s3:#222;--border:#2e2e2e;--border2:#333;
        --text:#f0f0f0;--muted:#6b7280;--primary:#27A0B1;--green:#10b981;--red:#ef4444;--orange:#f59e0b}
  body{background:var(--bg);color:var(--text);font-family:'Inter',sans-serif;min-height:100vh;
       display:flex;align-items:center;justify-content:center;padding:20px}
  .card{background:var(--s2);border:1px solid var(--border2);border-radius:16px;padding:40px 36px;
        width:100%;max-width:400px;box-shadow:0 20px 60px rgba(0,0,0,.5)}
  .logo{display:flex;align-items:center;gap:10px;margin-bottom:32px;justify-content:center}
  .logo-icon{width:42px;height:42px;background:var(--primary);border-radius:12px;
             display:flex;align-items:center;justify-content:center;font-size:1.3rem}
  .logo-name{font-size:1.35rem;font-weight:800;color:var(--text)}
  .logo-sub{font-size:.72rem;color:var(--muted);margin-top:1px}
  h2{font-size:1.1rem;font-weight:700;margin-bottom:24px;color:var(--text);text-align:center}
  .fg{display:flex;flex-direction:column;gap:5px;margin-bottom:16px}
  .fg label{font-size:.7rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px}
  .fg input{background:var(--s3);border:1px solid var(--border);color:var(--text);border-radius:9px;
            padding:11px 14px;font-size:.88rem;font-family:inherit;outline:none;transition:border-color .15s}
  .fg input:focus{border-color:var(--primary)}
  .error{background:#2a1010;border:1px solid #5a2020;color:#f87171;border-radius:8px;
         padding:10px 14px;font-size:.78rem;margin-bottom:16px}
  .btn-submit{width:100%;padding:12px;background:var(--primary);color:#fff;border:none;
              border-radius:10px;font-size:.9rem;font-weight:700;cursor:pointer;
              font-family:inherit;transition:opacity .15s;margin-top:4px}
  .btn-submit:hover{opacity:.88}
  .remember{display:flex;align-items:center;gap:8px;margin-bottom:18px;font-size:.78rem;color:var(--muted)}
  .remember input{width:15px;height:15px;accent-color:var(--primary)}
  .footer{text-align:center;margin-top:22px;font-size:.78rem;color:var(--muted)}
  .footer a{color:var(--primary);text-decoration:none;font-weight:600}
  .success{background:#0a2a1a;border:1px solid #1a5a3a;color:#6ee7b7;border-radius:8px;
           padding:10px 14px;font-size:.78rem;margin-bottom:16px}
</style>
</head>
<body>
<div class="card">
  <div class="logo">
    <div class="logo-icon">🍽️</div>
    <div>
      <div class="logo-name">Kafe POS</div>
      <div class="logo-sub">Sipariş Yönetimi</div>
    </div>
  </div>
  <h2>Hesabına Giriş Yap</h2>

  @if(session('status'))
    <div class="success">✓ {{ session('status') }}</div>
  @endif

  @if($errors->any())
    <div class="error">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="fg">
      <label>E-posta</label>
      <input type="email" name="email" value="{{ old('email') }}" placeholder="kafe@ornek.com" required autofocus>
    </div>
    <div class="fg">
      <label>Şifre</label>
      <input type="password" name="password" placeholder="••••••" required>
    </div>
    <div class="remember">
      <input type="checkbox" name="remember" id="remember">
      <label for="remember">Beni hatırla</label>
    </div>
    <button type="submit" class="btn-submit">Giriş Yap →</button>
  </form>

  <div class="footer">
    <a href="{{ route('password.request') }}">Şifremi unuttum</a>
    &nbsp;·&nbsp;
    Hesabın yok mu? <a href="{{ route('register') }}">Kayıt ol</a>
  </div>
  <div style="text-align:center;margin-top:14px;">
    <a href="{{ route('admin.login') }}" style="font-size:.68rem;color:#3a3a3a;text-decoration:none;letter-spacing:.3px">
      ⚙ Yönetici Girişi
    </a>
  </div>
</div>
</body>
</html>
