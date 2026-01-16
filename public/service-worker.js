// Service Worker para PWA con Push Notifications
const CACHE_NAME = 'autogestion-v1';

// Install event
self.addEventListener('install', (event) => {
    self.skipWaiting();
});

// Activate event
self.addEventListener('activate', (event) => {
    event.waitUntil(clients.claim());
});

// Fetch event - estrategia network-first
self.addEventListener('fetch', (event) => {
    event.respondWith(
        fetch(event.request)
            .catch(() => {
                return caches.match(event.request);
            })
    );
});

// Push event - recibir notificaciones
self.addEventListener('push', (event) => {
    const data = event.data ? event.data.json() : {};
    const title = data.title || 'Nueva notificación';
    const options = {
        body: data.body || '',
        icon: '/images/icons/icon-192x192.png',
        badge: '/images/icons/icon-72x72.png',
        data: { url: data.url || '/dashboard' }
    };
    event.waitUntil(self.registration.showNotification(title, options));
});

// Click en notificación - abrir la URL
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const url = event.notification.data.url || '/dashboard';
    event.waitUntil(
        clients.matchAll({ type: 'window' }).then((clientList) => {
            for (const client of clientList) {
                if (client.url.includes(url) && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});
