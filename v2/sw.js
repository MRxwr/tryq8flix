'use strict';
const CACHE = 'q8flix-v2-static-4';
const ASSETS = ['assets/app.css', 'assets/app.js', 'assets/poster.svg', 'logos/icon-192x192.png', 'logos/icon-512x512.png', 'offline.html'];
const allowed = new Set(ASSETS.map(path => new URL(path, self.registration.scope).href));
self.addEventListener('install', event => event.waitUntil(caches.open(CACHE).then(cache => cache.addAll([...allowed])).then(() => self.skipWaiting())));
self.addEventListener('activate', event => event.waitUntil(caches.keys().then(keys => Promise.all(keys.filter(key => key.startsWith('q8flix-v2-') && key !== CACHE).map(key => caches.delete(key)))).then(() => self.clients.claim())));
self.addEventListener('fetch', event => {
    if (event.request.method !== 'GET') return;
    if (allowed.has(event.request.url)) {
        event.respondWith(caches.open(CACHE).then(async cache => { try { const response = await fetch(event.request); if (response.ok) await cache.put(event.request, response.clone()); return response; } catch (error) { const cached = await cache.match(event.request); if (cached) return cached; throw error; } }));
    } else if (event.request.mode === 'navigate') {
        event.respondWith(fetch(event.request).catch(() => caches.match(new URL('offline.html', self.registration.scope).href)));
    }
});
