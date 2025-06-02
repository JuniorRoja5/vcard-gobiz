<?php

namespace App\Classes;

use Illuminate\Support\Facades\Storage;

class ServiceWorker
{
    public function generateServiceWorker($id, $url)
    {
        // Get assets from the database or any other source
        $assets = [
            '/'. $url,
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

        // Create the service worker script content
        $serviceWorkerContent = $this->generateServiceWorkerScript($assets);

        // Define the file path to save the service worker
        $filePath = 'public/manifest/service-worker/'. $id . '.js';

        // Store the service worker in the storage folder
        Storage::disk('local')->put($filePath, $serviceWorkerContent);

        // Return the file path or URL
        $url = Storage::url('manifest/service-worker/'. $id . '.js');
        return response()->json(['serviceWorkerUrl' => $url]);
    }

    private function generateServiceWorkerScript($assets)
    {
        $assetsJson = json_encode($assets);

        $uId = uniqid();

        return <<<EOL
    var staticCacheName = "{$uId}";
    var filesToCache = $assetsJson;

    if ('serviceWorker' in navigator) {
        caches.keys().then(function (cacheNames) {
            cacheNames.forEach(function (cacheName) {
                caches.delete(cacheName);
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
                        .filter(cacheName => (cacheName.startsWith("{$uId}-")))
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
    EOL;
    }
}