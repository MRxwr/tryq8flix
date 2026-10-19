# TryQ8Flix — Project Guide

This guide explains how the TryQ8Flix codebase works, step by step: how a request
enters the app, how the API is routed, how login works, how content is scraped
from third-party sites, how video and live matches are played, and what the
known problems are.

It is based on reading the source files directly. Paths are relative to the
project root.

---

## 1. What the project is

TryQ8Flix (`package.json` name `tryq8flix`) is a PHP web app that works as a
streaming aggregator. It does **not** host video itself. It:

1. Scrapes listing and detail pages from many third-party movie, series, anime,
   and sports sites.
2. Exposes the results through a small JSON API (`api/`).
3. Renders a Netflix-style web UI (`views/`) that calls that API with jQuery.
4. Stores users, favorites, and watch history in MySQL.
5. Adds a PWA shell (`manifest.json`, `sw.js`) and a few mini-games (`games/`).

There is no Node/JS build step. The "package.json" only exists for metadata; its
`test` script is a placeholder. The `.htaccess` file suggests Apache with `mod_rewrite`.
Third-party HTML parsing uses `simple_html_dom` (see the warning in §12).

### Top-level layout

| Path | Purpose |
|---|---|
| `index.php` | Web entry point. Loads the page for `?v=...`. |
| `api/index.php` | JSON API router. Loads `api/views/api<Name>.php`. |
| `api/views/` | One file per API endpoint. |
| `api/views/Games/` | Game-list endpoint (`Games/Home`). |
| `views/` | Page templates (`blade*.php`) plus `header.php` / `footer.php`. |
| `admin/includes/config.php` | DB connection, third-party tokens, and site base URLs. |
| `admin/includes/functions.php` | Loads `functions/index.php`. |
| `admin/includes/functions/` | Shared helpers (`general`, `sql`, `system`, `notification`) and one scraper file per source site. |
| `videoPlayer.php` | Standalone player page that resolves a `link` to a playable source. |
| `video-proxy.php` | Streams a remote video file or passes it through. |
| `image-proxy.php` | Fetches an image through a server-side cache (5 min). |
| `debug_live.php` | Temporary debug page for live-match scraping (marked "DELETE AFTER USE"). |
| `games/*.php` | Mini-game front-ends, loaded by the Games page. |
| `logos/` | Uploaded avatars and PWA icons. |
| `test/stream-test.php` | Standalone diagnostic page for embed providers. |
| `sw.js`, `manifest.json` | Service worker and PWA manifest. |
| `vendor/` | Composer packages: `puphpeteer`, `rialto`, `symfony/process`, `php-semver`, and others. |

---

## 2. Setup and runtime requirements

1. Serve the folder with PHP and Apache. `.htaccess` enables `mod_rewrite` and
   maps `name` to `name.php` for existing files.
2. PHP needs these extensions: `mysqli`, `curl`, `fileinfo`, `json`. The image
   upload helper (`uploadImage`) needs `file_get_contents` with HTTP access.
3. MySQL/MariaDB with the database named in `config.php`. The app does not
   ship a schema file. Tables are inferred from the code (see §11).
4. Make sure `admin/includes/config.php` points at a reachable database.
   The app will `die()` on connection failure.
5. Make `logos/` and `temp_images/` writable (the image proxy creates
   `temp_images/`).
6. Install `try2/templates/simple_html_dom.php` (see §12). It is not in this
   repository.

---

## 3. Step by step: a web page request

Example: a user opens `index.php?v=Home`.

1. **`index.php` loads config and helpers.**
   - `require_once admin/includes/config.php` opens the MySQL connection (`$dbconnect`) and sets timezone `Asia/Kuwait`.
   - `require_once admin/includes/functions.php` loads every helper file.
   - `require_once try2/templates/simple_html_dom.php` loads the HTML parser (missing in this repo).
2. **Login is checked.** `$profileData = checkLogin();` (in `functions/general.php`):
   - Reads the `tryq8flix2` cookie.
   - Looks up `users.keepalive` equal to that cookie.
   - If found, returns `username`, `uptobox`, `logo`, `email`, `id`.
   - If not found, clears the cookie and emits a `location.reload()` script.
3. **Optional `q` decoding.** If `?q=...` is present, `index.php` reverses the string, base64-decodes it, URL-decodes it, and merges the result into `$_GET`. This is how the UI hides parameters (see §8).
4. **Choose the view.** `$view = $_GET['v'] ?? 'Home'`.
   - Public views: `Login`, `Forget`, `Register`.
   - Any other view without a logged-in user redirects to `?v=Login`.
   - A logged-in user who requests `Login` is redirected to `?v=Home`.
5. **Load the template.** If `views/blade<View>.php` exists, it is included. If no `v` is given, `bladeHome.php` is included. Otherwise the app prints a JSON `404 view Not Found`.

### Available views (`views/`)

| `?v=` | File | What it does |
|---|---|---|
| Home | `bladeHome.php` | Hero banner, "Continue Watching", "My Favorites", then one row per source server. |
| Category | `bladeCategory.php` | Full listing of one server with paging and favorites. |
| Search | `bladeSearch.php` | Search across a chosen server. |
| More | `bladeMore.php` | Detail page for a title: seasons, episodes, favorite and history. |
| Servers | `bladeServers.php` | Server picker for an episode/movie or live match. Hosts the player iframe. |
| Favorites / History | `bladeFavorites.php`, `bladeHistory.php` | User lists. |
| Live | `bladeLiveMatchesList.php` | Live sports matches list. |
| Games | `bladeGames.php` | Loads `games/*.php` and the game list. |
| Download | `bladeDownload.php` | Video downloader UI for X, Instagram, TikTok, and YouTube. |
| Profile / Settings | `bladeProfile.php`, `bladeSettings.php` | Edit username/email/avatar, change password, delete account, app version info. |
| Login / Register / Forget / Logout | `bladeLogin.php`, `bladeRegister.php`, `bladeForget.php`, `bladeLogout.php` | Auth screens. |
| About / Policy / Terms | `bladeAbout.php`, `bladePolicy.php`, `bladeTerms.php` | Static pages. The text comes from `Settings`. |

`header.php` contains the Bootstrap/Font Awesome CSS, the PWA install banner,
service-worker registration, the link helpers `encryptLink` and `decryptLink`,
and the page-transition overlay. `footer.php` contains the shared JS, including
`getCookie('tryq8flix2')`.

---

## 4. Step by step: the JSON API

Every API call goes to `api/index.php?endpoint=<Name>&action=<...>`.

### 4.1 Router (`api/index.php`)

1. Sends `Content-Type: application/json; charset=UTF-8`.
2. Loads config, functions, and `simple_html_dom`.
3. **Finds the token.** Checks, in order:
   - `Authorization: Bearer <token>` header,
   - `tryq8flix2` cookie.
   - If neither exists, the token is empty.
4. **Requires `endpoint`.** Missing returns `400 ... No endpoint specified`.
5. **Maps the endpoint to a file.**
   - `Games/Home` becomes `api/views/Games/apiHome.php`.
   - `Main` becomes `api/views/apiMain.php`.
   - If the file name does not match exactly, a case-insensitive lookup runs over `views/api*.php`. This is for Linux case-sensitivity.
6. Includes the file. If no file matches, returns `dataError` with `404 endpoint Not Found`.

### 4.2 Response envelope (`admin/includes/functions/general.php`)

Every endpoint returns one of these JSON shapes:

```json
{ "ok": true,  "error": "0", "status": "successful", "data": { ... } }
{ "ok": false, "error": "1", "status": "Error",      "data": { "msg": "..." } }
```

Use `dataOutput($x)` for success and `dataError($x)` for errors. Clients check
`response.ok`.

### 4.3 Endpoint reference

Endpoints marked "token" return `Unauthorized`/`token is required` without a valid token.

| Endpoint | Actions / params | Auth | Purpose |
|---|---|---|---|
| `Main` | — | none | Returns the server list (ids 2, 13, 14, 8, 9, 10, 16 are enabled) and visible banners from table `banners`. |
| `Settings` | — | none | Returns `about`, `terms`, `policy`, `whatsapp`, `instagram`, `twitter`, `tiktok` from table `settings`. |
| `Banners` | — | none | Lists visible banners (`id`, `url`, `imageurl`). Not used by the views. |
| `Version` | `action=version` | none | Returns the app version (`1.0.4`) and download links for iOS, Android, and Windows. |
| `User` | `login`, `register`, `forget`, `logout`, `delete`, `appFire`, `change`, `profile` (`&update=1` to edit) | token for most | Accounts. See §5. |
| `Home` | `action=view&server=N&page=P&search=S` | token | Scrapes one source server. See §6. |
| `More` | `action=list&server=N&href=URL` | token | Returns seasons/episodes/details for a title. |
| `Servers` | `action=list&server=N&href=URL` | token | Returns playable server links for an episode or movie page. |
| `Favorites` | `add` (POST server, title, poster, link), `remove` (POST poster), `list`, `check` (POST poster) | token | Per-user favorites. |
| `History` | `add` (POST server, title, poster, link), `list` (optional `&server=`), `clear` | token | Watch history. `add` updates the date if the same link already exists. |
| `Live` | `action=live`, `action=match&match=URL`, `action=debug&url=..&referer=..` | none | Scrapes live matches and their player links. See §7. |
| `VideoPlayer` | `link=URL` | **none** | Fetches a page and extracts an `.m3u8`/`.mp4` URL. See §8 and §13. |
| `Downloader` | `action=info|link&url=URL` | token | Resolves downloadable media for X/Twitter, Instagram, TikTok, and YouTube. See §9. |
| `Games/Home` | `action=list` | none | Returns the list of mini-games (id, title, description, image, tag). |
| `Scores` | `list`, `save` (POST game, score) | token | Game high scores. Not used by any view yet. Creates table `gamescores` on first use. |
| `Firebase`, `News`, `QA*` (`QACategories`, `QAQuestions`, `QARooms`), `SubmitRoom`, `LiveOld` | — | — | Stub or legacy files. `apiFirebase.php` and `apiNews.php` are empty. No view calls the QA/SubmitRoom/LiveOld endpoints. |

### 4.4 Example requests

```text
GET  api/index.php?endpoint=Main
GET  api/index.php?endpoint=Home&action=view&server=2&page=2     (Authorization or cookie required)
GET  api/index.php?endpoint=Servers&action=list&server=2&href=<episode-url>
POST api/index.php?endpoint=User&action=login        username, password, firebaseToken?
GET  api/index.php?endpoint=Live&action=live
```

---

## 5. Step by step: accounts and sessions

Table `users` is central. The fields used in code are `id`, `username`,
`usernameSmall`, `password` (SHA-1), `email`, `keepalive` (session token),
`firebaseToken`, `avatar`, `uptoboxToken`, `status`, and `hidden`.

1. **Register** (`User&action=register`, POST `username`, `password`, `confirmPassword`, `email`).
   - Validates all fields and that the two passwords match.
   - Rejects a duplicate `usernameSmall` or `email` where `status = 0`.
   - Stores `password` as `sha1(password)`.
   - Returns `{msg: "User registered successfully"}`.
   - **Bug:** `bladeRegister.php` expects `res.data.keepalive` and writes it to the cookie. The endpoint does not return it, so the cookie becomes the string `undefined`. See §13.
2. **Login** (`User&action=login`, POST `username`, `password`, optional `firebaseToken`).
   - Matches `usernameSmall = lower(username)` and `password = sha1(password)`.
   - Rejects `status == 1` (deleted) and `hidden == 1` (blocked).
   - Creates a new token: `md5(uniqid())`, saves it to `keepalive` and `firebaseToken`.
   - Returns `{keepalive, username, id}`.
   - The browser (`bladeLogin.php`) writes the token to the cookie `tryq8flix2`, path `/`.
3. **Every later request** sends the cookie (or a Bearer header). `checkLogin()` and each endpoint look up `users.keepalive` to find the user.
4. **Logout** (`action=logout`) clears `keepalive`. The client clears the cookie.
5. **Forget password** (`action=forget`, POST `email`) sets a random numeric password, stores its SHA-1, and emails it through `sendMail()` (PHP `mail()`).
6. **Change password** (`action=change`) requires a token and matching POST fields.
7. **Profile** (`action=profile`) returns `username`, `email`, `avatar`, `id`. With `&update=1` it updates `email` (validated) and/or `avatar`. Avatars are checked with `getimagesize` and `finfo` (JPG/PNG/GIF/WEBP only), then uploaded to imgbb and saved in `logos/`.
8. **Delete account** marks `status = 1`, clears `keepalive`, and emails a notice. Data is not deleted.

---

## 6. Step by step: scraping a source site (`Home&action=view`)

The app scrapes third-party sites on each request. There is no database cache.

### 6.1 Server map

`views/bladeHome.php` and `api/views/apiMain.php` list the servers. The
`server` id in `Home`, `More`, and `Servers` selects the scraper.

| id | Source | Base URL (from config) | Home scraper | Listing | Server-link |
|---|---|---|---|---|---|
| 1 | Wecima | `$website3` | `scrapeWecima` | `wecimaListing` | `scrapeWecimaServers` |
| 2 | EgyDead | `$website4` | `scrapEgyDead` | `egyDeadListing` | `egyDeadServers` |
| 3 | TopCinema | `$website2` | `domTopCinema` | `TopCenimaListings` | `topCinemaServers` |
| 4 | Shahid | `$website` | `searchShahidListing` | `shahidMore` | `shahidServers` |
| 5 | Shahid Space | `$website5` | `searchShahidSpaceListing` | `shahidSpaceMore` | `shahidSpaceServers` |
| 6 | ShahidwBs | `$website6` | `scrapeShahidwBs` | `shahidwBsListing` | `scrapeShahidwBsServers` |
| 7 | Top Cinema Zone (MyCima) | `$website7` | `myCimaHome` | `myCimaListings` | `myCimaServers` |
| 8 | TukTuk | `$website8` | `tuktukHome` | `tuktukListings` | `tuktukServers` |
| 9 | Qesset | `$website9` | `qessetHome` | `qessetListings` | `qessetServers` |
| 10 | Esq | `$website10` | `esqHome` | `esqListings` | `esqServers` |
| 11 | Anime Slayer | `$website11` | `animeSlayerHome` | `animeSlayerListings` | `animeSlayerServers` |
| 12 | Anime Pec (anime4up) | `$website12` | `animePecHome` | `animePecListings` | `animePecServers` |
| 13 | TVDB TV Shows | (TVDB API) | `tvdbTvShowsHome` | `tvdbTvShowsListings` | `tvdbTvShowsServers` |
| 14 | TVDB Movies | (TVDB API) | `tvdbMoviesHome` | `tvdbMoviesListings` | `tvdbMoviesServers` |
| 15 | EgyDead LAT | `$website13` | `scrapEgyDeadLAT` | `EgyDeadLATListing` | `EgyDeadLATServers` |
| 16 | Wit Anime | `$website14` | `witanimeHome` | `witanimeListings` | `witanimeServers` |

Only servers 2, 8, 9, 10, 13, 14, and 16 are enabled in `apiMain.php`. The
other scrapers are still wired up and reachable by id.

### 6.2 Steps inside a scraper

Using `scrapEgyDead` (`admin/includes/functions/egydead.php`) as the example:

1. **Build the URL** in `api/views/apiHome.php` from the base URL plus `page` and `search` query params. Each server has its own paging and search rules.
2. **Fetch** with `curlCall($url)` (cURL, follows redirects, 60 s timeout, SSL verification off, passes the caller's User-Agent).
3. **Parse** with `str_get_html($html)` from `simple_html_dom`.
4. **Pick the container** (`.main-section`, `.cat-page`, or `.posts-list`, depending on the URL).
5. **Map each card** (`.movieItem`) to:
   `href`, `image` (rewritten to `video-proxy.php?url=...` so the image is proxied), `title`, `category`, `episode`, `description`.
6. **Return** `{shows: [...]}`. The API wraps it in `dataOutput`.

Other scrapers follow the same pattern: fetch the page, parse with DOM selectors, return `shows`. Each site has its own selectors, which break when the site changes HTML.

### 6.3 Drilling into a title (`More` then `Servers`)

1. The UI calls `More&action=list&server=N&href=<title-url>` (`bladeMore.php`).
   It returns seasons, episodes, or details from the matching `*Listings` function.
2. Clicking an episode calls `Servers&action=list&server=N&href=<episode-url>` (`bladeServers.php`).
   It returns the playable servers from the matching `*Servers` function.
3. The chosen server URL is opened in the player (§8).
4. `History&action=add` records the watch.

### 6.4 Shared helpers in `general.php`

- `scrapePage`, `curlCall`, `curlPost` (sends Wecima-style headers), and `makeRequest` for generic HTTP.
- `extractLink($html)` finds the first `<iframe src>` or the first `http(s)` URL in HTML.
- `fix_arabic_url` percent-encodes non-ASCII characters so Arabic URLs work with cURL.
- `getJsonDataApi` turns scraper output into a common JSON shape for mobile clients.
- `searchFile` and `validateInput` are small utilities.

---

## 7. Step by step: live sports matches (`Live`)

Config sets `$websiteLive` (yala2live.tv) and `$websiteLive2`.

1. **`Live&action=live`** (`bladeLiveMatchesList.php`): `searchMatches()` fetches the live site, finds `.albaflex .AY_Match` cards, and returns per match: teams, logos, time, result, status, channel, commentator, and league.
2. **`Live&action=match&match=<url>`** (`bladeServers.php`): `liveMatch()` fetches the match page and:
   - reads the match header into `details`,
   - finds `a.aplr-link` menu items (each `?serv=N` player),
   - follows iframes up to two levels deep, passing the match URL as `Referer`,
   - falls back to the first iframe if no menu is found,
   - drops URLs containing `wallplaster`,
   - returns `{matches: [{live, name, src}], details}`.
3. The page shows the teams and lets the user pick a `live` URL, which is then played in the iframe.

`debug_live.php` and `Live&action=debug` are developer tools for this flow.

---

## 8. Step by step: playing video

### 8.1 Where the iframe lives

`bladeServers.php` sets `#video-player` to the chosen server URL. Most sources are
embedded iframes, so the app does not need to proxy them.

### 8.2 `videoPlayer.php` (resolver page)

Given `?link=<url>`:

1. Fetches the link with cURL and sends `Referer: $website3`.
2. If the link itself ends in `.mp4` or `.m3u8`, uses it directly.
3. Otherwise tries, in order: a `jwplayer("vplayer").setup({sources:[{file:"..."}]})` block, then `video.mp4`, any `.mp4`, any `.m3u8`, `<source src>`, and `file: "..."`.
4. **Always** echoes the original link as an `<iframe>`. The direct-video branch is commented out, so the resolved URL is not used.

### 8.3 `video-proxy.php` (media proxy)

- Takes `?url=` (validated with `filter_var`) and optional `download=1`.
- Sends a request for headers, then streams the body with a `CURLOPT_WRITEFUNCTION`.
- Passes through `Range` requests (returns `206`) for seeking.
- Sets `Content-Type`, `Accept-Ranges`, `Content-Length`, and a 24 h cache header. With `download=1`, sets `Content-Disposition: attachment`.
- Used for images in scraper output (and video, if a client uses it).
- The access check is commented out, so it is open to anyone (see §13).

### 8.4 `image-proxy.php`

- Takes `?url=` and fetches it through `https://www.codebeautify.com/URLService` (a third-party proxy).
- Caches the response in `temp_images/` for 5 minutes, keyed by `md5(url)`. Old files are cleaned up on each call.

### 8.5 `videoPlayer` API endpoint

`api/views/apiVideoPlayer.php` does the same resolution as `videoPlayer.php` but returns JSON `{link}`. It extracts the `jwplayer` source, then trims everything after `.m3u8`. It needs no token.

### 8.6 HLS and MP4 players

`videoPlayer.php` includes hls.js from a CDN. Its `setupVideoPlayer` picks HLS for `.m3u8`
(native HLS on Safari, hls.js elsewhere) and plain `<video src>` for `.mp4`.

---

## 9. Step by step: the downloader (`Downloader`)

`bladeDownload.php` calls `Downloader&action=info` (metadata) and
`Downloader&action=link` (media URLs). Both need a token and a `url` param.

1. Validate the URL with `filter_var`.
2. Allow only these hosts (and their subdomains): X/Twitter, Instagram, TikTok, YouTube.
3. Pick a provider by host:
   - **X/Twitter**: `api.vxtwitter.com`, reading `mediaURLs` or `media_extended`.
   - **TikTok**: `tikwm.com/api/`, trying the short URL, the numeric ID, and the canonical URL. Short links are resolved first.
   - **Instagram**: `vxinstagram.com/reels/<shortcode>/`, reading `og:video`, `twitter:player:stream`, or a `btn btn-success` link.
   - **YouTube**: a POST to `turboscribe.ai` to get `googlevideo.com` links. `info` returns metadata only. This path uses a hardcoded session cookie (see §13).
4. Return `{stream_url, all_urls, source}` for `link`, or metadata (`id`, `title`, `uploader`, `duration`, `thumbnail`, `extractor`) for `info`.

---

## 10. Step by step: games

- `games/*.php` each contain one game (`bricks`, `code`, `defender`, `dino`, `drive`, `flap`, `jumper`, `memory`, `merge`, `runner`, `smasher`, `snake`, `sudoku`, `tictactoe`).
- `bladeGames.php` includes every `games/*.php` file into the page, then calls `Games/Home&action=list` to fill the game grid (`api/views/Games/apiHome.php`).
- `Scores` (`api/views/apiScores.php`) is a finished endpoint for per-user high scores in table `gamescores`. No view calls it yet.

---

## 11. Database model (inferred from code)

No schema file is included. These tables are used:

| Table | Key columns | Used by |
|---|---|---|
| `users` | `id`, `username`, `usernameSmall`, `password` (sha1), `email`, `keepalive`, `firebaseToken`, `avatar`, `uptoboxToken`, `status`, `hidden` | User, checkLogin, all authed endpoints |
| `favourites` | `id`, `userId`, `server`, `title`, `poster`, `link`, `date` | Favorites |
| `watchedvideos` | `id`, `userId`, `server`, `title`, `poster`, `link`, `date` | History |
| `banners` | `id`, `title`, `endpoint`, `server`, `url`, `imageurl`, `status`, `hidden` | Main, Banners |
| `settings` | `about`, `terms`, `policy`, `whatsapp`, `instagram`, `twitter`, `tiktok`, `status`, `hidden` | Settings |
| `gamescores` | `id`, `userId`, `game`, `score`, `date` (unique `userId+game`) | Scores (auto-created) |

Database helpers in `functions/sql.php`:

- `selectDB($table, $where)`: raw `WHERE` text, not parameterized.
- `selectDBNew($table, $params, $where, $order)`: parameterized `WHERE` (`?` placeholders, all bound as strings).
- `selectDB2($select, $table, $where)`: raw `SELECT` columns and `WHERE`.
- `insertDB`, `updateDB`, `deleteDB`: write helpers. `insertDB` and `updateDB` use prepared statements. `deleteDB` and the `where` strings in the others are built with string concatenation.

---

## 12. Dependencies

### PHP (Composer, in `vendor/`)

- `nesk/puphpeteer`, `nesk/rialto`: Puppeteer bridge (not used by the app code).
- `symfony/process`, `symfony/polyfill-php80`, `psr/log`, `clue/socket-raw`: support libraries.
- `vierbergenlars/php-semver`: `functions/index.php` has `use function ...\valid;` for it.

### Required but missing

- **`try2/templates/simple_html_dom.php`** is required by `index.php`, `api/index.php`, and `debug_live.php`. It is **not** in this repository, and git has no tracked files under `try2/`. The app will fatal on `require_once` unless the file exists on the server. Every scraper calls `str_get_html`, which comes from this library.

### Front-end (CDN)

Bootstrap 5.3, Font Awesome 6, jQuery 3.6 (in `footer.php`), canvas-confetti, hls.js.

### External services

Scraped sites (see §6), TMDB images, TVDB API (`$tvdbToken`, sent as a Bearer token by the `tvdb*` scrapers), imgbb (avatar upload), codebeautify (image proxy), vxtwitter, tikwm, vxinstagram, turboscribe, and PHP `mail()`. `$scrappingBeeToken` is declared in `scrapePage` but never used there.

---

## 13. Known issues and risks

Found while reading the code. Ordered by severity.

1. **Hardcoded secrets in source control.** `admin/includes/config.php` has the MySQL password, the TVDB API token, and the ScrapingBee token. `admin/includes/functions/system.php` has an imgbb API key. `api/views/apiDownloader.php` has a session cookie for turboscribe. Move these into environment variables or a file outside the web root, and rotate them.
2. **SQL injection surface.** `selectDB`, `selectDB2`, `deleteDB`, and `updateDB` build SQL from strings. Many endpoints interpolate `$token` or `$_POST` values into `WHERE` clauses (for example `apiUser.php` with `keepalive = '{$token}'`). The `"` and `;` stripping is not a real defense. Use prepared statements everywhere.
3. **Weak password storage.** Passwords are SHA-1 with no salt. Use `password_hash()` / `password_verify()` and migrate on login.
4. **Open media proxy.** `video-proxy.php` and `api/views/apiVideoPlayer.php` fetch any URL the caller supplies. Access control is commented out in `video-proxy.php`, and `VideoPlayer` needs no token. This is a server-side request forgery risk. Restrict hosts or require login.
5. **Registration cookie bug.** `api/views/apiUser.php` register returns no `keepalive`, but `bladeRegister.php` writes it to the cookie. The user ends up with `tryq8flix2=undefined` and is not logged in. Either return a token on register or redirect to login.
6. **Dead or debug code in production.** `debug_live.php` is marked "DELETE AFTER USE". `Live&action=debug` will fetch any URL. Remove both.
7. **Stale-cookie side effect.** `checkLogin()` prints a `<script>location.reload(true)</script>` mid-page when the cookie points to no user. Since the cookie is cleared first, this should only run once per stale session, but it is an unusual place to emit HTML from a helper.
8. **Undefined helper.** `selectDataDB` and `deleteDB` call `outputError()` on failure, which is not defined anywhere in this repo. Those error paths would fatal.
9. **Fragile scrapers.** Each scraper depends on specific CSS classes and URL patterns. Site redesigns break them silently, returning empty lists.
10. **No caching.** Every listing request scrapes the source site live. Add a cache (file or Redis) for list pages.
11. **Service worker caches only `/` and `/index.php`.** It is a cache-first strategy with almost no offline value.
12. **Error handling is inconsistent.** Some endpoints `die()` silently, some echo raw HTML before the JSON envelope (`apiVideoPlayer.php`, `apiLive.php`), and `apiBanners.php` / `apiSettings.php` return `dataError(array(['error' => ...]))`, which is a malformed error shape. Clients must handle both.
13. **`session` and `keepalive` tokens are long-lived.** Tokens never expire. A stolen cookie stays valid until logout. Add expiry.

---

## 14. Step-by-step: adding a new API endpoint

1. Create `api/views/api<Name>.php`. Start with the token check pattern from `apiFavorites.php`.
2. Use `selectDBNew` / `insertDB` / `updateDB` for DB access (prepared statements where possible).
3. Return with `echo dataOutput(array(...)); die();` or `echo dataError(array("msg" => "..."));`.
4. Call it from the UI as `api/index.php?endpoint=<Name>&action=...`.
5. Add the endpoint to the table in §4.3 of this guide.

## 15. Step-by-step: adding a new scraped source

1. Add the base URL to `admin/includes/config.php` (`$websiteN`).
2. Create `admin/includes/functions/<source>.php` with three functions, modeled on `egydead.php`:
   - `<source>Home($url)`: the list used by `Home&action=view`.
   - `<source>Listings($url)`: seasons/episodes/details for `More`.
   - `<source>Servers($url)`: playable links for `Servers`.
   Each returns `shows`-style arrays with `href`, `image`, `title`, and related fields.
3. Add a `require_once` line in `admin/includes/functions/index.php`.
4. Add an `elseif ($_GET["server"] == N)` branch in `Home` (`apiHome.php`), `More` (`apiMore.php`), and `Servers` (`apiServers.php`).
5. Add the server to `apiMain.php` if it should appear in the UI.

## 16. Quick reference

| Question | Where to look |
|---|---|
| Which page renders for `?v=X`? | `views/blade<X>.php` |
| Which API file handles `endpoint=X`? | `api/views/api<X>.php` (`Games/Home` → `api/views/Games/apiHome.php`) |
| How does login work? | §5, `api/views/apiUser.php`, `views/bladeLogin.php` |
| Which scraper handles server N? | §6.1 table |
| How is a video played? | §8 |
| Where are the secrets? | §13 item 1 (fix this first) |

---

## 17. Verification notes

- All file references and endpoint names in this guide were checked against the source. The endpoint list was confirmed by grep of `views/`.
- Scraper internals were read in detail for `egydead.php` only. The other scraper files were confirmed to exist and to expose the function names listed in §6.1, but their parsing logic was not read line by line.
- The app was **not run**. No database or network calls were made. Behavior described here comes from reading the code.
