// Service Worker básico para PWA instalable
const CACHE_NAME = 'autogestion-v1';

// Install event - solo activar el service worker
self.addEventListener('install', (event) => {
    self.skipWaiting();
});

// Activate event
self.addEventListener('activate', (event) => {
    event.waitUntil(clients.claim());
});

// Fetch event - estrategia network-first (no cachear para mantener datos actualizados)
self.addEventListener('fetch', (event) => {
    event.respondWith(
        fetch(event.request)
            .catch(() => {
                // Si falla la red, intentar desde cache
                return caches.match(event.request);
            })
    );
});
