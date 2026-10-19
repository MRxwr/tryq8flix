/**
 * A TV app is a single window: a stray `target="_blank"` or off-site link used to
 * hand the viewer to the system browser, or worse, replace the app with a blank
 * page and lose the video session. This keeps every navigation inside the app.
 */

function isOffApp(href: string | null): boolean {
  if (!href) return false;
  try {
    const url = new URL(href, window.location.href);
    if (url.protocol !== 'http:' && url.protocol !== 'https:') return false;
    return url.origin !== window.location.origin;
  } catch {
    return false;
  }
}

export function keepLinksInsideApp(): void {
  document.addEventListener(
    'click',
    (event) => {
      const target = event.target as Element | null;
      const anchor = target?.closest?.('a[href]');
      if (!anchor) return;

      const href = anchor.getAttribute('href');

      // Same-origin links keep working; anything that leaves the app is dropped.
      if (anchor.getAttribute('target') === '_blank' || isOffApp(href)) {
        event.preventDefault();
        event.stopPropagation();
      }
    },
    true,
  );

  // Middle-click / long-press on a TV remote or touch device.
  document.addEventListener('auxclick', (event) => {
    const anchor = (event.target as Element | null)?.closest?.('a[href]');
    if (anchor && isOffApp(anchor.getAttribute('href'))) event.preventDefault();
  }, true);

  // No new windows or system-browser handoffs.
  window.open = () => null;
}
