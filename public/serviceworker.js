var staticCacheName = "pwa-v" + new Date().getTime();
var filesToCache = [
    '/',
    '/css/tailwind.min.css',
    '/css/swiper-bundle.min.css',
    '/css/fontawesome.min.css',
    '/js/qrious.min.js',
    '/css/flatpickr.min.css',
    '/js/jquery.min.js',
    '/js/smooth-scroll.polyfills.min.js',
    '/js/flatpickr.min.js',
    '/js/swiper-bundle.min.js',
    '/app/js/footer.js',
    '/js/swiper-element-bundle.min.js'
];

if ('serviceWorker' in navigator) {
    fetch('/generate-service-worker')
    .then(response => response.json())
    .then(data => {
        navigator.serviceWorker.register(data.serviceWorkerUrl)
            .then((registration) => {
                console.log('Service Worker registered with scope:', registration.scope);
            })
            .catch((error) => {
                console.log('Service Worker registration failed:', error);
            });
    });
}

// Cache on install
self.addEventListener("install", event => {
    this.skipWaiting();
    event.waitUntil(
        caches.open(staticCacheName)
            .then(cache => cache.addAll(filesToCache))
            .then(self.skipWaiting())
    );
});

// Clear cache on activate
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => (cacheName.startsWith("pwa-")))
                    .filter(cacheName => (cacheName !== staticCacheName))
                    .map(cacheName => caches.delete(cacheName))
            );
        })
    );
});

// Serve from Cache
self.addEventListener("fetch", event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                return response || fetch(event.request);
            })
            .catch(() => {
                return caches.match('offline');
            })
    )
});