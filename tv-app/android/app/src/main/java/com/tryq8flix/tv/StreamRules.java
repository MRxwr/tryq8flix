package com.tryq8flix.tv;

import android.net.Uri;
import android.webkit.WebResourceResponse;

import java.io.ByteArrayInputStream;
import java.util.Arrays;
import java.util.Collections;
import java.util.HashSet;
import java.util.Locale;
import java.util.Set;

/** Small, transparent rule set; never blocks a media/CDN host merely for being cross-origin. */
final class StreamRules {
    private static final Set<String> AD_HOSTS = new HashSet<>(Arrays.asList(
        "doubleclick.net", "googlesyndication.com", "googleadservices.com",
        "adservice.google.com", "adnxs.com", "popads.net", "popcash.net",
        "exoclick.com", "trafficjunky.net", "propellerads.com",
        "adsterra.com", "adskeeper.co.uk", "ad-maven.com", "adcash.com",
        "clickadu.com", "juicyads.com", "hilltopads.net", "onclickads.net",
        "taboola.com", "outbrain.com", "mgid.com", "revcontent.com"
    ));

    private StreamRules() {}

    static boolean isHttp(Uri uri) {
        if (uri == null || uri.getHost() == null) return false;
        String scheme = uri.getScheme();
        return "https".equalsIgnoreCase(scheme) || "http".equalsIgnoreCase(scheme);
    }

    static boolean isAd(Uri uri) {
        if (!isHttp(uri)) return false;
        String host = uri.getHost().toLowerCase(Locale.ROOT);
        for (String ad : AD_HOSTS) {
            if (host.equals(ad) || host.endsWith("." + ad)) return true;
        }
        return false;
    }

    static boolean isMedia(Uri uri) {
        if (!isHttp(uri)) return false;
        String path = uri.getPath();
        if (path == null) return false;
        path = path.toLowerCase(Locale.ROOT);
        // Do not add .ts, .m4s or image/audio segments as separate candidate videos.
        return path.endsWith(".m3u8") || path.endsWith(".mpd")
            || path.endsWith(".mp4") || path.endsWith(".m4v") || path.endsWith(".webm");
    }

    static String mediaLabel(Uri uri) {
        String path = uri.getPath() == null ? "" : uri.getPath().toLowerCase(Locale.ROOT);
        String kind = path.endsWith(".m3u8") ? "HLS"
            : path.endsWith(".mpd") ? "DASH" : "Video";
        return kind + " · " + uri.getHost();
    }

    static WebResourceResponse emptyResponse() {
        return new WebResourceResponse(
            "text/plain", "UTF-8", 200, "OK", Collections.emptyMap(),
            new ByteArrayInputStream(new byte[0])
        );
    }
}
