/**
 * Where the TryQ8Flix backend lives.
 *
 * - Native (Android TV) builds call the live site directly. CapacitorHttp
 *   (see capacitor.config.ts) performs the requests natively, so CORS does not apply.
 * - `npm run dev` and `npm run preview` send /api and /videoPlayer.php through the
 *   Vite proxy (see vite.config.ts), so the browser needs no CORS headers.
 * - `npm run build:web` sets VITE_SAME_ORIGIN_API=1, so the built web app calls
 *   /api on whatever host serves it. The website sends no CORS headers, so the web
 *   build only works when it is uploaded to tryq8flix.com itself (e.g. /tv-app/).
 *
 * Set VITE_API_BASE in a .env file to point the app at another host.
 */
const override = import.meta.env.VITE_API_BASE as string | undefined;

/** Empty when the app is served from the same origin as the website. */
const sameOrigin = import.meta.env.VITE_SAME_ORIGIN_API === '1';

/** Absolute origin of the website (used for iframes such as videoPlayer.php). */
export const SITE_ORIGIN: string = sameOrigin
  ? ''
  : (override || 'https://tryq8flix.com').replace(/\/+$/, '');

/** Prefix for fetch() calls. Empty when requests are same-origin. */
export const API_BASE: string =
  sameOrigin || (import.meta.env.DEV && !override) ? '' : SITE_ORIGIN;
