<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Yönetim Girişi — Kafe POS</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
  *{box-sizing:border-box;margin:0;padding:0}
  :root{
    --bg:#080a0f;--s1:#0e1117;--s2:#111419;--s3:#181c24;
    --border:#1e2330;--border2:#252c3a;
    --text:#e8ecf4;--muted:#5a6480;--muted2:#8892a8;
    --accent:#e8a020;--accent2:#c47800;--red:#ef4444;
  }
  body{
    background:var(--bg);color:var(--text);font-family:'Inter',sans-serif;
    min-height:100vh;display:flex;align-items:center;justify-content:center;
    padding:20px;
    background-image: radial-gradient(ellipse 60% 50% at 50% 0%, rgba(232,160,32,.07) 0%, transparent 70%);
  }
  .wrap{width:100%;max-width:400px;display:flex;flex-direction:column;align-items:center;gap:24px}

  /* top badge */
  .sys-badge{
    display:inline-flex;align-items:center;gap:7px;
    background:rgba(232,160,32,.08);border:1px solid rgba(232,160,32,.2);
    border-radius:99px;padding:5px 14px 5px 10px;
    font-size:.68rem;font-weight:700;letter-spacing:.8px;color:var(--accent);
    text-transform:uppercase;
  }
  .sys-badge .dot{width:6px;height:6px;background:var(--accent);border-radius:50%;animation:blink 2s infinite}
  @keyframes blink{0%,100%{opacity:1}50%{opacity:.3}}

  /* card */
  .card{
    background:var(--s2);border:1px solid var(--border2);border-radius:18px;
    padding:40px 36px;width:100%;
    box-shadow:0 30px 80px rgba(0,0,0,.7),0 0 0 1px rgba(232,160,32,.04);
    position:relative;overflow:hidden;
  }
  .card::before{
    content:'';position:absolute;top:0;left:0;right:0;height:2px;
    background:linear-gradient(90deg,transparent,var(--accent),transparent);
  }

  /* logo */
  .logo{display:flex;align-items:center;gap:12px;margin-bottom:30px;justify-content:center}
  .logo-icon{
    width:48px;height:48px;background:linear-gradient(135deg,#c47800,#e8a020);
    border-radius:13px;display:flex;align-items:center;justify-content:center;
    font-size:1.4rem;box-shadow:0 4px 20px rgba(232,160,32,.3);
  }
  .logo-name{font-size:1.3rem;font-weight:800;color:var(--text)}
  .logo-sub{font-size:.68rem;color:var(--muted2);margin-top:2px;letter-spacing:.3px}

  h2{
    font-size:.88rem;font-weight:700;text-align:center;margin-bottom:26px;
    color:var(--muted2);letter-spacing:.4px;text-transform:uppercase;
  }

  /* form */
  .fg{display:flex;flex-direction:column;gap:5px;margin-bottom:14px}
  .fg label{font-size:.67rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px}
  .fg input{
    background:var(--s3);border:1px solid var(--border2);color:var(--text);
    border-radius:9px;padding:11px 14px;font-size:.87rem;font-family:inherit;
    outline:none;transition:border-color .15s;
  }
  .fg input:focus{border-color:var(--accent)}
  .fg input::placeholder{color:var(--muted)}

  .error{background:#1a0f0a;border:1px solid #5a2010;color:#f87171;
         border-radius:8px;padding:10px 14px;font-size:.78rem;margin-bottom:16px}
  .success{background:#0f180a;border:1px solid #2a5a10;color:#86efac;
           border-radius:8px;padding:10px 14px;font-size:.78rem;margin-bottom:16px}

  .btn-submit{
    width:100%;padding:13px;
    background:linear-gradient(135deg,#c47800,#e8a020);
    color:#000;border:none;border-radius:10px;
    font-size:.9rem;font-weight:800;cursor:pointer;
    font-family:inherit;transition:opacity .15s;margin-top:6px;
    letter-spacing:.3px;
  }
  .btn-submit:hover{opacity:.88}

  /* footer */
  .footer{text-align:center;font-size:.73rem;color:var(--muted)}
  .footer a{color:var(--muted2);text-decoration:none;font-weight:600}
  .footer a:hover{color:var(--accent)}

  /* divider */
  .divider{display:flex;align-items:center;gap:10px;margin:18px 0 16px;color:var(--muted);font-size:.7rem}
  .divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--border2)}
</style>
</head>
<body>
<div class="wrap">
  <div class="sys-badge">
    <span class="dot"></span>
    YÖNETİM PANELİ
  </div>

  <div class="card">
    <div class="logo">
      <div class="logo-icon">⚙️</div>
      <div>
        <div class="logo-name">Kafe POS</div>
        <div class="logo-sub">Admin Erişimi — Yetkili Kullanım</div>
      </div>
    </div>

    <h2>Yönetici Girişi</h2>

    @if(session('status'))
      <div class="success">✓ {{ session('status') }}</div>
    @endif
    @if($errors->any())
      <div class="error">⚠ {{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
      @csrf
      <div class="fg">
        <label>E-posta Adresi</label>
        <input type="email" name="email" value="{{ old('email') }}"
               placeholder="admin@ornek.com" required autofocus>
      </div>
      <div class="fg">
        <label>Şifre</label>
        <input type="password" name="password" placeholder="••••••••" required>
      </div>
      <button type="submit" class="btn-submit">Sisteme Giriş Yap →</button>
    </form>

    <div class="divider">veya</div>

    <div class="footer">
      Normal kullanıcı mısınız? <a href="{{ route('login') }}">Standart giriş</a>
    </div>
  </div>
</div>
</body>
</html>
