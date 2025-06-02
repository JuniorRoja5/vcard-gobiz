// Placeholder Service Worker for Pusher Beams
self.addEventListener('push', function (event) {
    const payload = event.data ? event.data.json() : {};
    const title = payload.notification.title || 'Notification';
    const options = {
        body: payload.notification.body || '',
        icon: payload.notification.icon || '/img/favicon.png',
        data: payload.notification.deep_link || '/',
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    const url = event.notification.data;

    if (url) {
        event.waitUntil(clients.openWindow(url));
    }
});
