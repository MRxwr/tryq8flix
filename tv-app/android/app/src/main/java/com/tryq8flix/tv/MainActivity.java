package com.tryq8flix.tv;

import android.net.Uri;
import android.webkit.WebResourceRequest;
import android.webkit.WebView;

import com.getcapacitor.Bridge;
import com.getcapacitor.BridgeActivity;
import com.getcapacitor.BridgeWebViewClient;

/**
 * Keeps the app on its own pages.
 *
 * Streaming embeds run third-party ad scripts that navigate the top window when a
 * viewer taps play, which used to replace the whole player with an ad page.
 * Capacitor's default client hands such a navigation to the system browser
 * (ACTION_VIEW); here it is cancelled outright, and new windows cannot open at all
 * because Capacitor's WebChromeClient implements no onCreateWindow.
 *
 * Only main-frame navigations are inspected, so the embeds themselves keep loading
 * inside their iframe and API calls are untouched.
 */
public class MainActivity extends BridgeActivity {

    /** Hosts the app may navigate to. Streaming embeds are never allowed here. */
    private static final String[] ALLOWED_HOSTS = { "tryq8flix.com" };

    @Override
    protected void load() {
        super.load();
        getBridge().setWebViewClient(new AppWebViewClient(getBridge()));
    }

    private static boolean isAllowed(Uri url) {
        String scheme = url.getScheme();
        String host = url.getHost();

        // capacitor://localhost and other non-web schemes are the app itself.
        if (scheme == null || host == null) return true;
        if (!"http".equals(scheme) && !"https".equals(scheme)) return true;

        for (String allowed : ALLOWED_HOSTS) {
            if (host.equals(allowed) || host.endsWith("." + allowed)) return true;
        }
        return false;
    }

    private static class AppWebViewClient extends BridgeWebViewClient {

        AppWebViewClient(Bridge bridge) {
            super(bridge);
        }

        @Override
        public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest request) {
            if (request.isForMainFrame() && !isAllowed(request.getUrl())) {
                return true; // block the navigation instead of leaving the app
            }
            return super.shouldOverrideUrlLoading(view, request);
        }

        // Required on API levels that still call the deprecated overload.
        @Override
        public boolean shouldOverrideUrlLoading(WebView view, String url) {
            if (!isAllowed(Uri.parse(url))) return true;
            return super.shouldOverrideUrlLoading(view, url);
        }
    }
}
