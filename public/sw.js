const CACHE_NAME = 'simpleakunting-v3-5-v6';
const assets = [
  './',
  './login',
  './kurasi',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
  'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
  './img/icon-512.png',
  './img/icon-pwa-512.png'
];

// Install service worker
self.addEventListener('install', evt => {
  self.skipWaiting();
  evt.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      console.log('Caching essential assets for offline support');
      return Promise.allSettled(
        assets.map(asset => cache.add(asset).catch(err => console.log('Failed to cache:', asset, err)))
      );
    })
  );
});

// Activate event: Hapus semua cache versi lama seketika
self.addEventListener('activate', evt => {
  evt.waitUntil(
    caches.keys().then(keys => {
      return Promise.all(
        keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key))
      );
    }).then(() => self.clients.claim())
  );
});

// Fetch event: Network-First Strategy (selalu ambil konten terbaru dari server, fallback ke cache jika offline)
self.addEventListener('fetch', evt => {
  if (evt.request.method !== 'GET') return;

  evt.respondWith(
    fetch(evt.request)
      .then(networkRes => {
        if (networkRes && networkRes.status === 200 && networkRes.type === 'basic') {
          const resClone = networkRes.clone();
          caches.open(CACHE_NAME).then(cache => {
            cache.put(evt.request, resClone);
          });
        }
        return networkRes;
      })
      .catch(() => {
        return caches.match(evt.request);
      })
  );
});
