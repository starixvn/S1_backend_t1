var cacheName = 'sbe-6.09.01.001'; // 缓存名称
var filesToCache = [ // 需要缓存的文件
  '/resources/vue/live/css/app.css',
  '/resources/vue/live/css/chunk-vendors.css',
  '/resources/vue/live/js/app.js',
  '/resources/vue/live/js/chunk-vendors.js',
];
self.addEventListener('install', e => {
  e.waitUntil(
    caches.open(cacheName).then((cache) => {
      return cache.addAll(filesToCache);
    })
  );
});
self.addEventListener('activate',  e => {
  e.waitUntil(
    caches.keys().then((keyList) => {
      return Promise.all(keyList.map((key) => {
        if (key !== cacheName) {
          return caches.delete(key);
        }
      }));
    })
  );
  return self.clients.claim();
});
self.addEventListener('fetch', e => {
  e.respondWith(
    caches.match(e.request, {ignoreSearch:true}).then(response => {
      return response || fetch(e.request);
    })
  );
});

