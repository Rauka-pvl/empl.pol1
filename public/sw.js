    const CACHE_NAME = 'offline-cache-v3'; // Обновите версию кэша при необходимости
    const urlsToCache = [
        '/tab',
        'resources/css/app.css',
        'resources/js/app.js',
        'storage/smile/1.png',
        'storage/smile/2.png',
        'storage/smile/3.png',
    ];

    // Устанавливаем Service Worker и кэшируем нужные файлы
    self.addEventListener('install', event => {
        event.waitUntil(
            caches.open(CACHE_NAME).then(cache => {
                return cache.addAll(urlsToCache);
            })
        );
        self.skipWaiting(); // Принудительная активация нового Service Worker
    });

    // Активация нового Service Worker и удаление старого кэша
    self.addEventListener('activate', event => {
        event.waitUntil(
            caches.keys().then(cacheNames => {
                return Promise.all(
                    cacheNames.map(cacheName => {
                        if (cacheName !== CACHE_NAME) {
                            return caches.delete(cacheName); // Удаляем старый кэш
                        }
                    })
                );
            })
        );
        self.clients.claim(); // Немедленное управление всеми страницами новым Service Worker
    });

    // Обновляем кэш, если ресурс изменился
    self.addEventListener('fetch', event => {
        if (event.request.method === "GET") {
            event.respondWith(
                caches.match(event.request).then(cachedResponse => {
                    if (cachedResponse) {
                        // Проверяем на сервере обновленную версию ресурса
                        return fetch(event.request).then(networkResponse => {
                            if (networkResponse && networkResponse.status === 200) {
                                const networkResponseClone = networkResponse.clone(); // Клонируем перед использованием
                                caches.open(CACHE_NAME).then(cache => {
                                    cache.put(event.request, networkResponseClone); // Кэшируем новый ответ
                                });
                            }
                            return networkResponse || cachedResponse;
                        }).catch(() => cachedResponse); // Если сеть недоступна, возвращаем кэш
                    } else {
                        // Если ресурс не закэширован, загружаем его из сети и кэшируем
                        return fetch(event.request).then(networkResponse => {
                            const networkResponseClone = networkResponse.clone(); // Клонируем перед использованием
                            return caches.open(CACHE_NAME).then(cache => {
                                cache.put(event.request, networkResponseClone); // Кэшируем новый ответ
                                return networkResponse;
                            });
                        });
                    }
                })
            );
        }
    });