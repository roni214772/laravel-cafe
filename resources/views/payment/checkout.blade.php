<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Ödeme  Kafe POS</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#111;--s2:#1c1c1c;--s3:#252525;
  --border:#2e2e2e;--text:#f0f0f0;--muted:#6b7280;--muted2:#9ca3af;
  --primary:#27A0B1;--green:#10b981;
}
body{
  background:var(--bg);color:var(--text);font-family:"Inter",sans-serif;
  min-height:100vh;display:flex;align-items:flex-start;justify-content:center;
  padding:28px 16px;
}
.wrap{width:100%;max-width:460px}

/* nav */
.mini-nav{display:flex;align-items:center;gap:10px;margin-bottom:20px}
.back-btn{
  display:inline-flex;align-items:center;gap:6px;
  background:var(--s2);border:1px solid var(--border);border-radius:8px;
  padding:7px 14px;font-size:.78rem;font-weight:600;color:var(--muted2);
  text-decoration:none;transition:all .15s;
}
.back-btn:hover{color:var(--text);border-color:#555}
.mini-spacer{flex:1}
.mini-brand{font-size:.85rem;font-weight:800;color:var(--muted)}

/* plan pill */
.plan-pill{
  display:flex;align-items:center;justify-content:space-between;
  background:var(--s2);border:1px solid var(--border);border-radius:12px;
  padding:13px 18px;margin-bottom:24px;
}
.plan-pill-name{font-size:.78rem;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.4px}
.plan-pill-price{font-size:1.15rem;font-weight:800;color:var(--primary)}
.plan-pill-price small{font-size:.68rem;font-weight:400;color:var(--muted)}

/* 
   CUSTOM CARD
   aspect-ratio ISO 1.5856
 */
.card-wrap{
  width:100%;
  aspect-ratio:1.5856;
  position:relative;
  border-radius:clamp(12px,3%,20px);
  overflow:hidden;
  margin-bottom:16px;
  box-shadow:
    0 0 0 1px rgba(255,255,255,.06),
    0 24px 64px rgba(0,0,0,.75),
    0 8px 24px rgba(0,0,0,.5);
}

/* zemin gradient */
.c-bg{
  position:absolute;inset:0;
  background:
    radial-gradient(ellipse at 20% 50%, #b5001a 0%, transparent 60%),
    radial-gradient(ellipse at 80% 10%, #7a0012 0%, transparent 55%),
    linear-gradient(135deg,#c20018 0%,#8a0014 40%,#5c000e 100%);
  z-index:1;
}

/* geometrik şekiller */
.c-geo{
  position:absolute;inset:0;z-index:2;overflow:hidden;
}
.c-geo::before{
  content:"";position:absolute;
  width:90%;padding-bottom:90%;border-radius:50%;
  border:1.5px solid rgba(255,255,255,.07);
  top:-20%;right:-25%;
}
.c-geo::after{
  content:"";position:absolute;
  width:65%;padding-bottom:65%;border-radius:50%;
  border:1px solid rgba(255,255,255,.05);
  bottom:-30%;left:-10%;
}

/* ince çizgi desen */
.c-lines{
  position:absolute;inset:0;z-index:2;
  background-image:repeating-linear-gradient(
    -45deg,
    transparent,
    transparent 18px,
    rgba(255,255,255,.022) 18px,
    rgba(255,255,255,.022) 19px
  );
}

/* holografik parlaklık */
.c-holo{
  position:absolute;inset:0;z-index:3;
  background:linear-gradient(
    105deg,
    transparent 30%,
    rgba(255,200,200,.08) 45%,
    rgba(255,255,255,.12) 50%,
    rgba(255,200,200,.08) 55%,
    transparent 70%
  );
}

/* içerik */
.c-content{
  position:absolute;inset:0;z-index:5;
  display:flex;flex-direction:column;justify-content:space-between;
  padding:clamp(10px,3.2%,18px) clamp(14px,4.5%,24px);
}

/* üst bölüm */
.c-top{display:flex;align-items:flex-start;justify-content:space-between}

/* banka ismi / logo alanı */
.c-bank{
  display:flex;flex-direction:column;gap:1px;
}
.c-bank-icon{
  display:flex;align-items:center;gap:5px;margin-bottom:2px;
}
.c-bank-dot{
  width:clamp(6px,1.8%,10px);height:clamp(6px,1.8%,10px);
  background:rgba(255,255,255,.9);border-radius:50%;flex-shrink:0;
}
.c-bank-dot2{
  width:clamp(4px,1.2%,7px);height:clamp(4px,1.2%,7px);
  background:rgba(255,255,255,.5);border-radius:50%;flex-shrink:0;
}
.c-bank-name{
  font-size:clamp(.48rem,1.8vw,.75rem);
  font-weight:800;color:#fff;letter-spacing:.3px;
  text-shadow:0 1px 6px rgba(0,0,0,.5);
}
.c-bank-sub{
  font-size:clamp(.32rem,1vw,.48rem);
  color:rgba(255,255,255,.5);letter-spacing:.8px;
  text-transform:uppercase;font-weight:500;
  padding-left:clamp(11px,3.2%,17px);
}

/* sağ üst: NFC + dekoratif metin */
.c-top-right{display:flex;flex-direction:column;align-items:flex-end;gap:3px}
.c-nfc{
  font-size:clamp(.7rem,2.4vw,1rem);
  color:rgba(255,255,255,.6);letter-spacing:-2px;line-height:1;
}
.c-card-type{
  font-size:clamp(.3rem,.9vw,.42rem);
  color:rgba(255,255,255,.35);letter-spacing:1px;
  text-transform:uppercase;font-weight:700;
}

/* çip */
.c-chip-row{display:flex;align-items:center;gap:clamp(8px,2.5%,14px)}
.c-chip{
  width:clamp(28px,8.5%,42px);
  aspect-ratio:.77;
  border-radius:4px;
  background:linear-gradient(
    145deg,
    #a07020 0%,#e8c840 20%,#c89030 40%,
    #f5e060 60%,#c09028 80%,#e8c840 100%
  );
  box-shadow:0 2px 8px rgba(0,0,0,.6),inset 0 1px 0 rgba(255,255,255,.3);
  position:relative;overflow:hidden;flex-shrink:0;
}
.c-chip::before{
  content:"";position:absolute;inset:0;
  background:
    linear-gradient(transparent 28%,rgba(0,0,0,.2) 28%,rgba(0,0,0,.2) 35%,transparent 35%),
    linear-gradient(transparent 52%,rgba(0,0,0,.2) 52%,rgba(0,0,0,.2) 59%,transparent 59%),
    linear-gradient(90deg,transparent 28%,rgba(0,0,0,.15) 28%,rgba(0,0,0,.15) 35%,transparent 35%),
    linear-gradient(90deg,transparent 52%,rgba(0,0,0,.15) 52%,rgba(0,0,0,.15) 59%,transparent 59%);
}
.c-chip::after{
  content:"";position:absolute;
  top:50%;left:50%;transform:translate(-50%,-50%);
  width:40%;height:35%;
  border:1px solid rgba(0,0,0,.2);border-radius:1px;
  background:rgba(255,220,80,.3);
}

/* IBAN / kart numarası */
.c-number{
  font-family:"Space Mono","Courier New",monospace;
  font-size:clamp(.55rem,2.1vw,.85rem);
  font-weight:700;
  letter-spacing:clamp(1.5px,.5vw,4px);
  color:rgba(255,255,255,.95);
  text-shadow:0 2px 8px rgba(0,0,0,.7);
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
}

/* alt bölüm */
.c-bottom{display:flex;align-items:flex-end;justify-content:space-between}
.c-field{line-height:1.2}
.c-field-lbl{
  font-size:clamp(.28rem,.8vw,.42rem);
  color:rgba(255,255,255,.45);
  text-transform:uppercase;letter-spacing:.7px;margin-bottom:2px;
}
.c-field-val{
  font-size:clamp(.42rem,1.4vw,.6rem);
  font-weight:700;color:#fff;
  text-transform:uppercase;letter-spacing:.4px;
  text-shadow:0 1px 4px rgba(0,0,0,.5);
}

/* VISA sağ alt */
.c-visa{
  display:flex;align-items:center;
  font-size:clamp(.9rem,3vw,1.3rem);
  font-weight:900;font-style:italic;
  color:rgba(255,255,255,.92);
  letter-spacing:-1px;
  text-shadow:0 1px 8px rgba(0,0,0,.5);
  gap:0;
}

/*  bilgi satırları  */
.info-row{
  display:flex;align-items:center;justify-content:space-between;
  background:var(--s2);border:1px solid var(--border);border-radius:10px;
  padding:11px 16px;margin-bottom:10px;gap:12px;
}
.info-lbl{font-size:.63rem;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px}
.info-val{font-size:.8rem;font-weight:700;color:var(--text);font-family:"Space Mono",monospace;word-break:break-all}
.info-val.amount{font-family:"Inter",sans-serif;font-size:1rem;color:var(--green)}
.copy-btn{
  flex-shrink:0;
  background:var(--s3);border:1px solid #3a3a3a;color:var(--muted2);
  border-radius:7px;padding:6px 14px;font-size:.65rem;font-weight:700;
  cursor:pointer;font-family:inherit;transition:all .15s;white-space:nowrap;
}
.copy-btn:hover{background:var(--primary);border-color:var(--primary);color:#fff}
.copy-btn.copied{background:var(--green);border-color:var(--green);color:#fff}

/* onayla */
.confirm-box{
  background:var(--s2);border:1px solid var(--border);border-radius:14px;
  padding:20px;text-align:center;margin-top:4px;
}
.confirm-box p{font-size:.8rem;color:var(--muted);margin-bottom:16px;line-height:1.65}
.btn-confirm{
  width:100%;padding:14px;background:var(--green);color:#fff;border:none;
  border-radius:10px;font-size:.92rem;font-weight:700;cursor:pointer;
  font-family:inherit;transition:all .15s;
  box-shadow:0 4px 16px rgba(16,185,129,.3);
}
.btn-confirm:hover{opacity:.88;transform:translateY(-1px)}
.btn-confirm:active{transform:translateY(0)}
</style>
</head>
<body>
<div class="wrap">

  <div class="mini-nav">
    <a href="{{ route('subscription.select') }}" class="back-btn"> Geri</a>
    <div class="mini-spacer"></div>
    <span class="mini-brand"> Kafe POS</span>
  </div>

  <div class="plan-pill">
    <span class="plan-pill-name">{{ $label }}</span>
    <span class="plan-pill-price">
      ₺{{ number_format((float)$price, 0, ',', '.') }}
      <small> / {{ $plan === 'yearly' ? 'yıl' : 'ay' }}</small>
    </span>
  </div>

  {{-- KART --}}
  <div class="card-wrap">
    <div class="c-bg"></div>
    <div class="c-lines"></div>
    <div class="c-geo"></div>
    <div class="c-holo"></div>

    <div class="c-content">

      {{-- Üst --}}
      <div class="c-top">
        <div class="c-bank">
          <div class="c-bank-icon">
            <div class="c-bank-dot"></div>
            <div class="c-bank-dot2"></div>
            <span class="c-bank-name">{{ $bankName }}</span>
          </div>
          <span class="c-bank-sub">Havale / EFT</span>
        </div>
        <div class="c-top-right">
          <div class="c-nfc">)))</div>
          <div class="c-card-type">Debit</div>
        </div>
      </div>

      {{-- Çip --}}
      <div class="c-chip-row">
        <div class="c-chip"></div>
      </div>

      {{-- IBAN --}}
      <div class="c-number" id="ibanVal">{{ $iban }}</div>

      {{-- Alt --}}
      <div class="c-bottom">
        <div class="c-field">
          <div class="c-field-lbl">Hesap Sahibi</div>
          <div class="c-field-val">{{ $accountHolder }}</div>
        </div>
        <div class="c-visa">VISA</div>
      </div>

    </div>
  </div>

  {{-- Tutar --}}
  <div class="info-row">
    <div>
      <div class="info-lbl">Ödenecek Tutar</div>
      <div class="info-val amount">₺{{ number_format((float)$price, 2, ',', '.') }}</div>
    </div>
  </div>

  {{-- IBAN kopyala --}}
  <div class="info-row">
    <div style="min-width:0">
      <div class="info-lbl">IBAN</div>
      <div class="info-val">{{ $iban }}</div>
    </div>
    <button class="copy-btn" id="copyIban" onclick="doCopy()">Kopyala</button>
  </div>

  {{-- Admin Notu --}}
  @if(!empty($checkoutNote))
  <div style="background:var(--s2);border:1px solid rgba(39,160,177,.3);border-radius:10px;padding:13px 16px;margin-bottom:10px">
    <div style="font-size:.63rem;color:var(--primary);text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px;font-weight:700">📝 Bilgilendirme</div>
    <div style="font-size:.82rem;color:var(--muted2);line-height:1.6">{!! nl2br(e($checkoutNote)) !!}</div>
  </div>
  @endif

  {{-- Onayla --}}
  <div class="confirm-box">
    <p>
      Havaleyi yaptıktan sonra aşağıdaki butona basın.<br>
      <span style="color:#374151;font-size:.75rem">Admin onayladıktan sonra sisteme giriş yapabilirsiniz.</span>
    </p>
    <form method="POST" action="{{ route('payment.confirm-transfer') }}">
      @csrf
      <input type="hidden" name="plan" value="{{ $plan }}">
      <button type="submit" class="btn-confirm"> Havaleyi Yaptım, Bildir</button>
    </form>
  </div>

</div>
<script>
function doCopy(){
  const text=document.getElementById('ibanVal').innerText;
  navigator.clipboard.writeText(text).then(()=>{
    const b=document.getElementById('copyIban');
    b.textContent='Kopyalandı!';b.classList.add('copied');
    setTimeout(()=>{b.textContent='Kopyala';b.classList.remove('copied');},2200);
  });
}
</script>
</body>
</html>
