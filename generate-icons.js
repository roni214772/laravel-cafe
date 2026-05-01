const sharp = require('sharp');
const path = require('path');

const outDir = path.join(__dirname, 'laravel-cafe', 'public', 'icons');

// ── 1. Uygulama Simgesi 512x512 ──
async function createAppIcon() {
  const size = 512;
  const svg = `
  <svg width="${size}" height="${size}" xmlns="http://www.w3.org/2000/svg">
    <defs>
      <linearGradient id="bg" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" style="stop-color:#1a8a9b"/>
        <stop offset="50%" style="stop-color:#27A0B1"/>
        <stop offset="100%" style="stop-color:#2bc4d8"/>
      </linearGradient>
      <linearGradient id="cup" x1="0%" y1="0%" x2="0%" y2="100%">
        <stop offset="0%" style="stop-color:#ffffff"/>
        <stop offset="100%" style="stop-color:#e8e8e8"/>
      </linearGradient>
      <filter id="shadow" x="-10%" y="-10%" width="130%" height="130%">
        <feDropShadow dx="0" dy="4" stdDeviation="8" flood-color="#000" flood-opacity="0.3"/>
      </filter>
      <filter id="glow">
        <feGaussianBlur stdDeviation="3" result="blur"/>
        <feComposite in="SourceGraphic" in2="blur" operator="over"/>
      </filter>
    </defs>
    
    <!-- Background rounded square -->
    <rect x="0" y="0" width="${size}" height="${size}" rx="100" ry="100" fill="url(#bg)"/>
    
    <!-- Subtle pattern overlay -->
    <circle cx="80" cy="80" r="200" fill="white" opacity="0.04"/>
    <circle cx="430" cy="430" r="180" fill="black" opacity="0.06"/>
    
    <!-- Coffee cup body -->
    <g filter="shadow" transform="translate(256,240)">
      <!-- Cup -->
      <path d="M-80,-80 L80,-80 L65,80 C63,95 50,105 35,105 L-35,105 C-50,105 -63,95 -65,80 Z" 
            fill="url(#cup)" stroke="white" stroke-width="2"/>
      
      <!-- Cup handle -->
      <path d="M80,-50 C120,-50 130,-10 130,20 C130,50 120,60 80,60" 
            fill="none" stroke="white" stroke-width="12" stroke-linecap="round"/>
      
      <!-- Coffee liquid -->
      <path d="M-68,-30 L68,-30 L60,50 C58,62 48,70 36,70 L-36,70 C-48,70 -58,62 -60,50 Z" 
            fill="#6B3E26" opacity="0.8"/>
      
      <!-- Coffee shine -->
      <ellipse cx="0" cy="-10" rx="45" ry="12" fill="white" opacity="0.15"/>
    </g>
    
    <!-- Steam lines -->
    <g transform="translate(256,100)" fill="none" stroke="white" stroke-width="5" stroke-linecap="round" opacity="0.7">
      <path d="M-30,40 C-30,20 -15,20 -15,0 C-15,-20 -30,-20 -30,-40"/>
      <path d="M0,50 C0,30 15,30 15,10 C15,-10 0,-10 0,-30"/>
      <path d="M30,40 C30,20 45,20 45,0 C45,-20 30,-20 30,-40"/>
    </g>
    
    <!-- POS text badge -->
    <g transform="translate(256,420)">
      <rect x="-65" y="-22" width="130" height="44" rx="22" fill="white" opacity="0.95"/>
      <text x="0" y="8" font-family="Arial,Helvetica,sans-serif" font-size="28" font-weight="bold" 
            fill="#1a8a9b" text-anchor="middle" letter-spacing="4">POS</text>
    </g>
  </svg>`;

  await sharp(Buffer.from(svg))
    .resize(512, 512)
    .png()
    .toFile(path.join(outDir, 'icon-512-new.png'));

  await sharp(Buffer.from(svg))
    .resize(192, 192)
    .png()
    .toFile(path.join(outDir, 'icon-192-new.png'));

  console.log('✅ App icon created: icon-512-new.png, icon-192-new.png');
}

// ── 2. Özellik Grafiği 1024x500 ──
async function createFeatureGraphic() {
  const w = 1024, h = 500;
  const svg = `
  <svg width="${w}" height="${h}" xmlns="http://www.w3.org/2000/svg">
    <defs>
      <linearGradient id="fbg" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" style="stop-color:#0d2f36"/>
        <stop offset="40%" style="stop-color:#134a54"/>
        <stop offset="100%" style="stop-color:#1a8a9b"/>
      </linearGradient>
      <linearGradient id="accent" x1="0%" y1="0%" x2="100%" y2="0%">
        <stop offset="0%" style="stop-color:#27A0B1"/>
        <stop offset="100%" style="stop-color:#2bc4d8"/>
      </linearGradient>
      <filter id="fshadow">
        <feDropShadow dx="0" dy="3" stdDeviation="6" flood-color="#000" flood-opacity="0.4"/>
      </filter>
    </defs>
    
    <!-- Background -->
    <rect width="${w}" height="${h}" fill="url(#fbg)"/>
    
    <!-- Decorative circles -->
    <circle cx="900" cy="100" r="250" fill="white" opacity="0.03"/>
    <circle cx="100" cy="400" r="300" fill="white" opacity="0.02"/>
    <circle cx="750" cy="350" r="150" fill="#27A0B1" opacity="0.08"/>
    
    <!-- Decorative line -->
    <line x1="0" y1="460" x2="1024" y2="460" stroke="url(#accent)" stroke-width="3" opacity="0.4"/>
    
    <!-- Left side: Icon -->
    <g transform="translate(200,250)" filter="fshadow">
      <!-- Mini cup icon -->
      <g transform="scale(0.8)">
        <circle cx="0" cy="0" r="90" fill="url(#accent)" opacity="0.2"/>
        <path d="M-45,-50 L45,-50 L38,45 C36,55 28,62 18,62 L-18,62 C-28,62 -36,55 -38,45 Z" 
              fill="white" opacity="0.95"/>
        <path d="M45,-30 C65,-30 72,-5 72,15 C72,35 65,42 45,42" 
              fill="none" stroke="white" stroke-width="7" stroke-linecap="round" opacity="0.9"/>
        <path d="M-38,-15 L38,-15 L33,28 C31,36 25,42 17,42 L-17,42 C-25,42 -31,36 -33,28 Z" 
              fill="#6B3E26" opacity="0.5"/>
        <!-- Steam -->
        <g fill="none" stroke="white" stroke-width="3" stroke-linecap="round" opacity="0.5">
          <path d="M-15,-60 C-15,-75 -5,-75 -5,-90"/>
          <path d="M5,-55 C5,-70 15,-70 15,-85"/>
          <path d="M25,-58 C25,-73 35,-73 35,-88"/>
        </g>
      </g>
    </g>
    
    <!-- Right side: Text -->
    <g transform="translate(550,200)">
      <!-- App name -->
      <text x="0" y="0" font-family="Arial,Helvetica,sans-serif" font-size="72" font-weight="bold" fill="white" letter-spacing="2">
        Kafe POS
      </text>
      
      <!-- Accent underline -->
      <rect x="0" y="20" width="180" height="4" rx="2" fill="url(#accent)"/>
      
      <!-- Tagline -->
      <text x="0" y="70" font-family="Arial,Helvetica,sans-serif" font-size="24" fill="#8ec8d0" letter-spacing="1">
        Adisyon &amp; Sipariş Takip
      </text>
      
      <!-- Features -->
      <g transform="translate(0,115)" fill="#a0d5dc" font-family="Arial,Helvetica,sans-serif" font-size="16">
        <circle cx="8" cy="-5" r="4" fill="#27A0B1"/>
        <text x="20" y="0">Masa Yönetimi</text>
        
        <circle cx="178" cy="-5" r="4" fill="#27A0B1"/>
        <text x="190" y="0">Mutfak Ekranı</text>
        
        <circle cx="348" cy="-5" r="4" fill="#27A0B1"/>
        <text x="360" y="0">Paket Sipariş</text>
      </g>
    </g>
    
    <!-- Bottom bar -->
    <rect x="0" y="470" width="${w}" height="30" fill="#0a1a1e"/>
    <text x="512" y="490" font-family="Arial,Helvetica,sans-serif" font-size="13" fill="#4a9aa5" text-anchor="middle" letter-spacing="3">
      caffe-pos.com
    </text>
  </svg>`;

  await sharp(Buffer.from(svg))
    .resize(1024, 500)
    .png()
    .toFile(path.join(outDir, 'feature-graphic.png'));

  console.log('✅ Feature graphic created: feature-graphic.png');
}

(async () => {
  await createAppIcon();
  await createFeatureGraphic();
  console.log('\n🎉 Tüm görseller oluşturuldu:', outDir);
})();
