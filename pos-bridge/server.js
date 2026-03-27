/**
 * Kafe POS - Fiziksel POS Cihazı Köprüsü
 *
 * Bu servis, tarayıcıdaki adisyon uygulaması ile fiziksel POS cihazı
 * arasında köprü görevi görür.
 *
 * Desteklenen bağlantı tipleri:
 *   1. Seri Port (COM) — USB/RS232 bağlantılı POS cihazları
 *   2. TCP/IP Soket  — Ağ bağlantılı POS cihazları
 *
 * Kullanım:
 *   node server.js
 *
 * Ortam değişkenleri (.env veya komut satırı):
 *   POS_PORT=3457              HTTP sunucu portu
 *   POS_MODE=serial            Bağlantı tipi: serial | tcp
 *   POS_SERIAL_PATH=COM3       Seri port adı
 *   POS_SERIAL_BAUD=9600       Seri port hızı
 *   POS_TCP_HOST=192.168.1.100 TCP POS cihaz IP
 *   POS_TCP_PORT=8000          TCP POS cihaz portu
 */

const http = require('http');
const net  = require('net');
const fs   = require('fs');
const path = require('path');

// ─── Konfigürasyon ────────────────────────────────────────────
const CONFIG_FILE = path.join(__dirname, 'pos-config.json');

function loadConfig() {
  const defaults = {
    httpPort:    parseInt(process.env.POS_PORT) || 3457,
    mode:        process.env.POS_MODE || 'serial',       // serial | tcp
    serial: {
      path:     process.env.POS_SERIAL_PATH || 'COM3',
      baudRate: parseInt(process.env.POS_SERIAL_BAUD) || 9600,
    },
    tcp: {
      host: process.env.POS_TCP_HOST || '192.168.1.100',
      port: parseInt(process.env.POS_TCP_PORT) || 8000,
    },
  };

  if (fs.existsSync(CONFIG_FILE)) {
    try {
      const saved = JSON.parse(fs.readFileSync(CONFIG_FILE, 'utf8'));
      return { ...defaults, ...saved, serial: { ...defaults.serial, ...saved.serial }, tcp: { ...defaults.tcp, ...saved.tcp } };
    } catch { /* use defaults */ }
  }
  return defaults;
}

function saveConfig(cfg) {
  fs.writeFileSync(CONFIG_FILE, JSON.stringify(cfg, null, 2), 'utf8');
}

let config = loadConfig();

// ─── ECR Protokol Yardımcıları ────────────────────────────────
const STX = 0x02;
const ETX = 0x03;

/**
 * Basit ECR satış komutu oluştur
 * Tutar kuruş cinsinden (ör: 150.50 TL = 15050)
 */
function buildSaleCommand(amountTL) {
  const kurus = Math.round(amountTL * 100);
  const amountStr = kurus.toString().padStart(12, '0');
  // Basit ECR format: STX + "SALE" + amount(12) + ETX + LRC
  const payload = `SALE${amountStr}`;
  const data = Buffer.from([STX, ...Buffer.from(payload), ETX]);
  // LRC hesapla (XOR of all bytes between STX and ETX inclusive)
  let lrc = 0;
  for (let i = 1; i < data.length; i++) lrc ^= data[i];
  return Buffer.concat([data, Buffer.from([lrc])]);
}

/**
 * ECR yanıtını ayrıştır
 */
function parseResponse(buffer) {
  const str = buffer.toString('utf8').replace(/[\x02\x03]/g, '');
  // Tipik yanıt: APPROVED / DECLINED / ERROR + ek bilgiler
  if (str.includes('APPROVED') || str.includes('00')) {
    return { success: true, message: 'Ödeme onaylandı', raw: str };
  }
  if (str.includes('DECLINED') || str.includes('05')) {
    return { success: false, message: 'Ödeme reddedildi', raw: str };
  }
  return { success: false, message: 'Bilinmeyen yanıt', raw: str };
}

// ─── Seri Port İletişimi ──────────────────────────────────────
let serialPort = null;

async function sendViaSerial(amountTL) {
  const { SerialPort } = require('serialport');

  return new Promise((resolve, reject) => {
    const cmd = buildSaleCommand(amountTL);
    let responseBuffer = Buffer.alloc(0);
    let timeout;

    // Port aç
    const port = new SerialPort({
      path: config.serial.path,
      baudRate: config.serial.baudRate,
      autoOpen: false,
    });

    port.open((err) => {
      if (err) {
        return reject(new Error(`Seri port açılamadı (${config.serial.path}): ${err.message}`));
      }

      // Komutu gönder
      port.write(cmd, (writeErr) => {
        if (writeErr) {
          port.close();
          return reject(new Error(`Veri gönderilemedi: ${writeErr.message}`));
        }
      });

      // Yanıt dinle
      port.on('data', (chunk) => {
        responseBuffer = Buffer.concat([responseBuffer, chunk]);
        // ETX bulundu mu?
        if (chunk.includes(ETX)) {
          clearTimeout(timeout);
          const result = parseResponse(responseBuffer);
          port.close();
          resolve(result);
        }
      });

      // Zaman aşımı (60 saniye — müşteri kartını okutana kadar bekle)
      timeout = setTimeout(() => {
        port.close();
        reject(new Error('POS cihazından yanıt gelmedi (zaman aşımı 60s)'));
      }, 60000);
    });

    port.on('error', (err) => {
      clearTimeout(timeout);
      reject(new Error(`Seri port hatası: ${err.message}`));
    });
  });
}

// ─── TCP/IP İletişimi ─────────────────────────────────────────
async function sendViaTcp(amountTL) {
  return new Promise((resolve, reject) => {
    const cmd = buildSaleCommand(amountTL);
    let responseBuffer = Buffer.alloc(0);

    const client = new net.Socket();
    let timeout;

    client.connect(config.tcp.port, config.tcp.host, () => {
      client.write(cmd);
    });

    client.on('data', (chunk) => {
      responseBuffer = Buffer.concat([responseBuffer, chunk]);
      if (chunk.includes(ETX)) {
        clearTimeout(timeout);
        const result = parseResponse(responseBuffer);
        client.destroy();
        resolve(result);
      }
    });

    client.on('error', (err) => {
      clearTimeout(timeout);
      reject(new Error(`TCP bağlantı hatası (${config.tcp.host}:${config.tcp.port}): ${err.message}`));
    });

    // 60 saniye timeout
    timeout = setTimeout(() => {
      client.destroy();
      reject(new Error('POS cihazından yanıt gelmedi (zaman aşımı 60s)'));
    }, 60000);
  });
}

// ─── Ödeme Gönder (Ana Fonksiyon) ────────────────────────────
async function sendPayment(amountTL) {
  console.log(`[POS] Ödeme gönderiliyor: ${amountTL} TL (mod: ${config.mode})`);

  if (config.mode === 'tcp') {
    return sendViaTcp(amountTL);
  }
  return sendViaSerial(amountTL);
}

// ─── Seri Portları Listele ────────────────────────────────────
async function listSerialPorts() {
  try {
    const { SerialPort } = require('serialport');
    const ports = await SerialPort.list();
    return ports.map(p => ({
      path: p.path,
      manufacturer: p.manufacturer || '',
      vendorId: p.vendorId || '',
      productId: p.productId || '',
    }));
  } catch {
    return [];
  }
}

// ─── HTTP Sunucu ──────────────────────────────────────────────
function readBody(req) {
  return new Promise((resolve) => {
    let body = '';
    req.on('data', (chunk) => { body += chunk; });
    req.on('end', () => {
      try { resolve(JSON.parse(body)); }
      catch { resolve({}); }
    });
  });
}

const server = http.createServer(async (req, res) => {
  // CORS — tarayıcıdan localhost'a istek izni
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');
  res.setHeader('Content-Type', 'application/json; charset=utf-8');

  if (req.method === 'OPTIONS') {
    res.writeHead(204);
    return res.end();
  }

  const url = req.url.split('?')[0];

  // ─── Durum kontrolü ─────────────────
  if (url === '/status' && req.method === 'GET') {
    res.writeHead(200);
    return res.end(JSON.stringify({
      online: true,
      mode: config.mode,
      serial: config.serial,
      tcp: config.tcp,
    }));
  }

  // ─── Seri portları listele ──────────
  if (url === '/ports' && req.method === 'GET') {
    const ports = await listSerialPorts();
    res.writeHead(200);
    return res.end(JSON.stringify({ ports }));
  }

  // ─── Ayarları güncelle ──────────────
  if (url === '/config' && req.method === 'POST') {
    const body = await readBody(req);
    if (body.mode) config.mode = body.mode;
    if (body.serial) config.serial = { ...config.serial, ...body.serial };
    if (body.tcp) config.tcp = { ...config.tcp, ...body.tcp };
    saveConfig(config);
    res.writeHead(200);
    return res.end(JSON.stringify({ success: true, config }));
  }

  // ─── Ayarları oku ──────────────────
  if (url === '/config' && req.method === 'GET') {
    res.writeHead(200);
    return res.end(JSON.stringify(config));
  }

  // ─── Ödeme gönder ──────────────────
  if (url === '/pay' && req.method === 'POST') {
    const body = await readBody(req);
    const amount = parseFloat(body.amount);

    if (!amount || amount <= 0) {
      res.writeHead(400);
      return res.end(JSON.stringify({ success: false, message: 'Geçersiz tutar' }));
    }

    try {
      const result = await sendPayment(amount);
      console.log(`[POS] Sonuç:`, result);
      res.writeHead(200);
      return res.end(JSON.stringify(result));
    } catch (err) {
      console.error(`[POS] Hata:`, err.message);
      res.writeHead(500);
      return res.end(JSON.stringify({ success: false, message: err.message }));
    }
  }

  // ─── Kasa Çekmecesi Aç ──────────────
  if (url === '/cash-drawer' && req.method === 'POST') {
    console.log('[POS] Kasa çekmecesi açılıyor...');
    // ESC/POS cash drawer kick command: ESC p 0 25 250
    const drawerCmd = Buffer.from([0x1B, 0x70, 0x00, 0x19, 0xFA]);
    try {
      if (config.mode === 'tcp') {
        await new Promise((resolve, reject) => {
          const client = new net.Socket();
          client.connect(config.tcp.port, config.tcp.host, () => {
            client.write(drawerCmd, () => { client.destroy(); resolve(); });
          });
          client.on('error', (err) => reject(err));
          setTimeout(() => { client.destroy(); resolve(); }, 3000);
        });
      } else {
        const { SerialPort } = require('serialport');
        await new Promise((resolve, reject) => {
          const port = new SerialPort({ path: config.serial.path, baudRate: config.serial.baudRate, autoOpen: false });
          port.open((err) => {
            if (err) return reject(err);
            port.write(drawerCmd, () => { setTimeout(() => { port.close(); resolve(); }, 500); });
          });
          port.on('error', reject);
        });
      }
      res.writeHead(200);
      return res.end(JSON.stringify({ success: true, message: 'Kasa çekmecesi açıldı' }));
    } catch (err) {
      console.error('[POS] Kasa çekmecesi hatası:', err.message);
      res.writeHead(500);
      return res.end(JSON.stringify({ success: false, message: err.message }));
    }
  }

  // ─── Test (simülasyon) ─────────────
  if (url === '/test' && req.method === 'POST') {
    const body = await readBody(req);
    const amount = parseFloat(body.amount) || 1;
    console.log(`[POS] Test ödeme: ${amount} TL (simülasyon)`);
    // 2 saniye bekle (gerçek POS cihazını simüle et)
    await new Promise(r => setTimeout(r, 2000));
    res.writeHead(200);
    return res.end(JSON.stringify({
      success: true,
      message: `Test başarılı — ${amount} TL (simülasyon)`,
      simulated: true,
    }));
  }

  // 404
  res.writeHead(404);
  res.end(JSON.stringify({ error: 'Bilinmeyen endpoint' }));
});

server.listen(config.httpPort, '127.0.0.1', () => {
  console.log('');
  console.log('╔══════════════════════════════════════════════════╗');
  console.log('║        🏧  KAFE POS — Cihaz Köprüsü            ║');
  console.log('╠══════════════════════════════════════════════════╣');
  console.log(`║  HTTP:   http://127.0.0.1:${config.httpPort}                 ║`);
  console.log(`║  Mod:    ${config.mode.padEnd(39)}║`);
  if (config.mode === 'serial') {
    console.log(`║  Port:   ${config.serial.path.padEnd(39)}║`);
    console.log(`║  Baud:   ${String(config.serial.baudRate).padEnd(39)}║`);
  } else {
    console.log(`║  Cihaz:  ${(config.tcp.host + ':' + config.tcp.port).padEnd(39)}║`);
  }
  console.log('╠══════════════════════════════════════════════════╣');
  console.log('║  Endpoints:                                     ║');
  console.log('║    GET  /status   — Köprü durumu                ║');
  console.log('║    GET  /ports    — Seri portları listele        ║');
  console.log('║    GET  /config   — Ayarları oku                 ║');
  console.log('║    POST /config   — Ayarları güncelle            ║');
  console.log('║    POST /pay      — Ödeme gönder {amount: TL}   ║');
  console.log('║    POST /test     — Test (simülasyon)            ║');
  console.log('╚══════════════════════════════════════════════════╝');
  console.log('');
});
