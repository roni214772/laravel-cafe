// Kafe POS — Service Worker
// Statik dosyaları önbelleğe alır; API istekleri her zaman internetten çalışır.

const CACHE = 'cafepOS-v3';

// Önbelleğe alınacak statik dosyalar
const STATIC_ASSETS = [
  '/offline.html',
  '/icons/icon-192.png',
  '/icons/icon-512.png',
  '/manifest.json',
];

// Kurulum: statik dosyaları önbelleğe al
self.addEventListener('install', e => {
  e.waitUntil(
    caches.open(CACHE).then(c => c.addAll(STATIC_ASSETS))
  );
  self.skipWaiting();
});

// Aktivasyon: eski önbellekleri temizle
self.addEventListener('activate', e => {
  e.waitUntil(
    caches.keys().then(keys =>
      Promise.all(keys.filter(k => k !== CACHE).map(k => caches.delete(k)))
    )
  );
  self.clients.claim();
});

// Fetch: API ve dinamik sayfalar her zaman ağdan, statikler önbellekten
self.addEventListener('fetch', e => {
  const url = new URL(e.request.url);

  // POST istekleri ve farklı origin'ler — her zaman ağa gönder
  if (e.request.method !== 'GET' || url.origin !== location.origin) return;

  // API rotaları (/adisyon/masa/*, /mutfak/*, vb.) — her zaman ağa gönder
  const dynamicPaths = ['/adisyon', '/mutfak', '/admin', '/products', '/categories', '/menu', '/login', '/logout', '/register', '/forgot-password', '/reset-password', '/subscription', '/payment', '/api', '/broadcasting'];
  if (dynamicPaths.some(p => url.pathname.startsWith(p))) {
    e.respondWith(
      fetch(e.request).catch(() => caches.match('/offline.html'))
    );
    return;
  }

  // Statik dosyalar (build/, icons/) — önce önbellek, sonra ağ
  e.respondWith(
    caches.match(e.request).then(cached => {
      if (cached) return cached;
      return fetch(e.request).then(res => {
        if (res && res.status === 200) {
          const clone = res.clone();
          caches.open(CACHE).then(c => c.put(e.request, clone));
        }
        return res;
      }).catch(() => caches.match('/offline.html'));
    })
  );
});
