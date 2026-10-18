/**
 * Where the TryQ8Flix backend lives.
 *
 * - Native (Android TV) builds call the live site directly. CapacitorHttp
 *   (see capacitor.config.ts) performs the requests natively, so CORS does not apply.
 * - `npm run dev` sends /api and /videoPlayer.php through the Vite proxy
 *   (see vite.config.ts), so the browser needs no CORS headers.
 *
 * Set VITE_API_BASE in a .env file to point the app at another host.
 */
const override = import.meta.env.VITE_API_BASE as string | undefined;

/** Absolute origin of the website (used for iframes such as videoPlayer.php). */
export const SITE_ORIGIN: string = (override || 'https://tryq8flix.com').replace(/\/+$/, '');

/** Prefix for fetch() calls. Empty in the browser dev server so the Vite proxy handles them. */
export const API_BASE: string = import.meta.env.DEV && !override ? '' : SITE_ORIGIN;
