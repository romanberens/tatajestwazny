if ('serviceWorker' in navigator) {
  addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js')
      .then(reg => console.log('[SW] scope:', reg.scope))
      .catch(err => console.error('[SW] error:', err));
  });
}
