const CACHE_NAME = 'simpleakunting-v3-5-v5'; // Increment version
const assets = [
  './login',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
  'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
  './img/icon-pwa-512.png'
];




// Install service worker
self.addEventListener('install', evt => {
  evt.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      console.log('Caching assets');
      // Use try-catch or individually add to prevent one failure from stopping all
      return Promise.allSettled(
        assets.map(asset => {
          return cache.add(asset).catch(err => console.log('Failed to cache:', asset, err));
        })
      );
    })
  );
  self.skipWaiting();
});

// Activate event
self.addEventListener('activate', evt => {
  evt.waitUntil(
    caches.keys().then(keys => {
      return Promise.all(keys
        .filter(key => key !== CACHE_NAME)
        .map(key => caches.delete(key))
      );
    })
  );
});

// Fetch event
self.addEventListener('fetch', evt => {
  // Skip cross-origin requests unless they are in our assets list
  if (evt.request.method !== 'GET') return;

  evt.respondWith(
    caches.match(evt.request).then(cacheRes => {
      return cacheRes || fetch(evt.request).then(fetchRes => {
        // Optional: Cache new requests on the fly
        // return caches.open(CACHE_NAME).then(cache => {
        //   cache.put(evt.request.url, fetchRes.clone());
        //   return fetchRes;
        // });
        return fetchRes;
      }).catch(() => {
        // Offline fallback if needed
      });
    })
  );
});

