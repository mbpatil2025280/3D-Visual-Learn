const CACHE_NAME = 'visual-learn-v1';

// We specify basic app shell files here to cache instantly on install
const urlsToCache = [
  './index.html',
  './style.css'
];

// Install Event
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Opened cache');
        return cache.addAll(urlsToCache);
      })
  );
});

// Fetch Event - network first, falling back to cache so dynamic PHP stuff still works
self.addEventListener('fetch', event => {
  event.respondWith(
    fetch(event.request).catch(function() {
      return caches.match(event.request);
    })
  );
});
