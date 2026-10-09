# Q8Flix V2

A standalone upgrade of the active PHP movie/TV app. All changes are inside `v2/`; the original root app and `tryq8flix` database remain unchanged.

## Local setup (XAMPP PHP 8.2+, MySQL)

1. `php v2/bin/migrate.php` creates **only** `tryq8flix_v2` and its schema.
2. `cd v2 && npm ci --ignore-scripts --no-audit --no-fund` restores the pinned HLS library dependency. The compiled HLS script and its license are already in `assets/`.
3. `cd v2 && php -S 127.0.0.1:8788 -t . router.php` starts the local app.
4. Open `http://127.0.0.1:8788`, then register a new V2 account. Legacy accounts are intentionally not imported or modified.
5. Optionally warm catalogs: `php v2/bin/refresh.php --warm`.

Configuration is in `config/defaults.php`, overridden by ignored **private** `config/local.php` or environment variables. Existing provider URLs and the existing TMDB credential were copied privately without exposing their values. No legacy database password, API-key upload helper, or session-cookie downloader is used.

Supported environment settings: `V2_ORIGIN`, `V2_ENV`, `V2_DB_DSN`, `V2_DB_USER`, `V2_DB_PASSWORD`, `TMDB_TOKEN`. The database guard requires `dbname=tryq8flix_v2`; SQLite is reserved for isolated tests. Production must use a dedicated least-privilege database user, not XAMPP root.

For Apache localhost under `/v2`, set `origin` to the **exact** local URL, including `/v2` (e.g. `http://localhost/tryq8flix/v2`). Do not derive it from the request Host. For a subdomain, point its document root at this folder, set `origin` to its exact HTTPS URL, set `environment` to `production`, and keep rewrite/access rules enabled. No DNS, remote hosting, or production publishing has been performed.

## What changed

- Explicit page/API allowlists; internal files are inaccessible through the development router and Apache rules.
- Isolated accounts, `password_hash`/`password_verify`, cryptographically random expiring sessions stored as hashes, HttpOnly cookies, same-origin/CSRF mutation checks, rate limits, revocation on logout/password change.
- Single-use 30-minute reset links. In development, messages are written into **private** `storage/mail/`; production uses configured PHP mail and must be tested before publishing.
- Prepared SQL throughout the active V2 API. No legacy SQL helpers are loaded.
- Central bounded HTTP transport with TLS verification, public-IP DNS pinning, host/port/protocol restrictions, redirect revalidation, body-size limits, a per-provider deadline, and one bounded transient GET retry.
- Existing provider parsing code is retained in a separate namespace; network calls go through the safe transport. Empty documents/layout failures return structured errors instead of corrupting JSON.
- Stable movie/TV identities (media type is included), sources separate from titles, favorites independent of artwork, numbered episode identities and direct-player progress.
- Local normalized search with Arabic text/digit normalization, filters, cancellation of stale requests, explicit pagination, optional source discovery/import.
- Curated fixed-position homepage rows, lazy images/lower rows, local image fallbacks, accessible links/buttons, reusable safe DOM components, no inline handlers or scraped HTML execution.
- Real title details, season/episode controls, source selection, direct MP4/HLS playback, previous/next navigation, honest embedded-player history, preserved signed URLs.
- Cached provider results with refresh locks, stale-while-refresh catalog responses, health diagnostics, background refresh jobs.
- Static-only PWA caching, relative manifest paths; personalized HTML/auth/API/media are never cached by the service worker.

## Identity matching

Names/year alone are **not** reliable enough to merge remakes or translations. New scraper titles retain stable source identities until an administrator confirms a match. TMDB IDs are automatically stable within media type. An administrator can match alternate sources on Source Health; favorites/progress move transactionally to the canonical title. No automatic fuzzy merges are performed.

Create an admin explicitly, locally:

```bash
php v2/bin/admin.php your-v2-username
```

This only promotes an existing V2 account; never the legacy account database.

## Provider boundaries

- Metadata labels correctly identify TMDB, retaining provider IDs 13/14.
- All existing scraper families are retained, including the LAT helper, without its duplicate function collision. They require current URLs/approved destination hosts and parser tests against current pages.
- Legacy relay-based Shahid/Space integrations (4/5) are disabled; V2 does not depend on unrelated websites’ scraping relays.
- Scraped playback hosts must be explicitly approved in `extra_hosts` / `embed_hosts` before resolution. Add only verified authorized domains, not arbitrary caller-provided hosts.
- TMDB embed choices retain a subset of existing choices; availability is **unverified**, not inferred from metadata. Only use sources/content you are authorized to access.
- Live sports, social downloader, quiz/game APIs are intentionally not enabled in the active V2 router yet. They return a clear integration-pending error rather than execute unsafe copied routes. Their copied files are not publicly routable. They need dedicated provider contracts, rights review, fixtures, and live verification.
- The open video proxy is disabled (HTTP 410). Direct playback preserves the source URL; sources that require CORS/referer proxying need a provider-specific, correctly tested streaming proxy. V2 does not forge range headers or fetch entire videos twice.
- Image proxy accepts authenticated approved URLs and verified bounded raster images, never SVG/HTML or arbitrary files.
- Cross-origin embeds cannot provide reliable progress without documented origin-checked events. These are marked **recently opened**, not watched/resumable. Iframe permissions are restricted; providers that require popups/navigation may not work.
- Avatar uploads are not enabled yet; the hardcoded-key external upload roundtrip is not used. A bounded local decode/re-encode implementation should be added if needed.

## Maintenance

Run every 5 minutes with your hosting scheduler:

```bash
php /absolute/path/v2/bin/refresh.php
```

Run `--warm` separately when refreshing the first page of all enabled providers is wanted. Background jobs do not run themselves. Cache refreshes are shared across users; no session data enters catalog cache payloads. Scrape within provider terms and respectful limits. Schedule raster image-cache cleanup as needed; V2 does not scan the whole cache directory for every image request.

## Verification

```bash
php v2/tests/run.php
php v2/tests/api.php
cd v2 && npm run check
```

`api.php` requires the localhost server and the isolated database. It creates and cleans only its own temporary V2 users and fixtures, and never writes the reference database. Static tests check original-file hashes saved privately at copy time.

See `VERIFICATION.md` for results and remaining live-provider limitations. No production-readiness or speedup claim should be inferred from passing syntax checks or metadata requests alone.
