# TryQ8Flix TV — Experimental Native Stream Browser (Android)

This feature is a **local Android experiment**, not a server-side stream extractor.
Only `tv-app` files change. The web build still uses the existing iframe player.

## What it does

1. Selecting a movie/episode server or a live stream on **Android** launches `StreamBrowserActivity`.
2. The embedded page loads in a dedicated Android WebView (not the Ionic WebView).
3. WebView network-request URLs ending with `.m3u8`, `.mpd`, `.mp4`, `.m4v` or `.webm` are recorded **without changing the requests**.
4. The **Videos (N)** button lists the discovered URL candidates. Selecting one opens `NativePlayerActivity` using Media3 ExoPlayer (HLS, DASH, MP4) and forwards applicable Referer, User-Agent and per-host cookies.
5. Requests to a small list of known ad hosts are blocked; new top-level cross-domain navigations require **Allow once / Cancel**. Automatic popups are rejected and gesture-initiated popups require approval.
6. **External browser** is always available on the native toolbar as a fallback.

## Install and test on a TV

From the `tv-app` folder:

```bash
npm install
npm run build
npx cap sync android
npx cap open android
```

Build and install a new Android APK from Android Studio. A web-only `npm run build:web` deployment **will not** include native WebView/Media3 code.

Test on Xiaomi TV Stick:
- Open an episode/movie -> select a server -> internal browser should open.
- Start video from the embed (if needed); check whether `Videos (N)` increases.
- Select a detected candidate and confirm Media3 playback, remote D-pad/Play/Pause/Back, audio and subtitles.
- Try an unwanted redirect and verify Cancel keeps you on the original page.
- Verify known ad hosts are filtered, and verify the external-browser fallback.
- Try a live match stream.
- If none are found, report which provider, page and TV model; do **not** post signed stream URLs or cookies.

## Expected limitations

- Detection is passive and based on **URL extensions**, not response bodies/MIME types. Endpoints using extensionless paths or protected JS/DRM can be missed.
- WebView does not report `blob:` fetches or all redirected requests through `shouldInterceptRequest`. DASH/HLS streams depending on session cookies, signed URLs or device DRM might not play in Media3.
- It is a limited **host-blocklist**, not a full browser-grade ad blocking engine. Some ad redirects will be blocked, others require user approval.
- A detected URL is only a *candidate*. ExoPlayer may not support the media or might receive an authorization error.
- ExoPlayer will release video resources when the player activity is destroyed; returning to the browser keeps the prior page.
- This change does not expose credentials or add server-side proxies.

## Source locations

- `android/app/src/main/java/com/tryq8flix/tv/StreamBrowserActivity.java`
- `android/app/src/main/java/com/tryq8flix/tv/NativePlayerActivity.java`
- `android/app/src/main/java/com/tryq8flix/tv/StreamRules.java`
- `android/app/src/main/java/com/tryq8flix/tv/StreamBrowserPlugin.java`
- `src/plugins/streamBrowser.ts`
- `src/pages/ServersPage.tsx` and `src/pages/LivePage.tsx`

This is a beta and must be compiled and tested on a real Android TV before relying on it.
