package com.tryq8flix.tv;

import android.net.Uri;
import android.os.Bundle;
import android.view.KeyEvent;
import android.view.MotionEvent;
import android.webkit.WebResourceRequest;
import android.webkit.WebResourceResponse;
import android.webkit.WebView;

import java.io.ByteArrayInputStream;
import java.util.Arrays;
import java.util.Collections;
import java.util.HashSet;
import java.util.Set;

import com.getcapacitor.Bridge;
import com.getcapacitor.BridgeActivity;
import com.getcapacitor.BridgeWebViewClient;

/**
 * Keeps the app on its own pages.
 *
 * Streaming embeds can redirect the top window to ad pages and request resources
 * from known ad networks. Third-party player controls remain provider-dependent.
 */
public class MainActivity extends BridgeActivity {

    /** Hosts the app may navigate to. Streaming embeds are never allowed here. */
    private static final String[] ALLOWED_HOSTS = { "tryq8flix.com" };

    private static final Set<String> BLOCKED_AD_HOSTS = new HashSet<>(Arrays.asList(
        "doubleclick.net",
        "googlesyndication.com",
        "googleadservices.com",
        "adservice.google.com",
        "adnxs.com",
        "popads.net",
        "popcash.net",
        "exoclick.com",
        "trafficjunky.net",
        "propellerads.com",
        "adsterra.com",
        "adskeeper.co.uk",
        "ad-maven.com",
        "adcash.com",
        "clickadu.com",
        "juicyads.com",
        "hilltopads.net",
        "onclickads.net"
    ));

    @Override
    public void onCreate(Bundle savedInstanceState) {
        registerPlugin(StreamBrowserPlugin.class);
        registerPlugin(VideoControlsPlugin.class);
        super.onCreate(savedInstanceState);
    }

    @Override
    protected void load() {
        super.load();
        getBridge().setWebViewClient(new AppWebViewClient(getBridge()));
    }

    @Override
    public boolean dispatchKeyEvent(KeyEvent event) {
        if (event.getAction() == KeyEvent.ACTION_DOWN && event.getKeyCode() == KeyEvent.KEYCODE_DPAD_DOWN) {
            dispatchPlayerControlsEvent(true);
        }
        return super.dispatchKeyEvent(event);
    }

    @Override
    public boolean dispatchTouchEvent(MotionEvent event) {
        if (event.getAction() == MotionEvent.ACTION_DOWN) {
            dispatchPlayerControlsEvent(false);
        }
        return super.dispatchTouchEvent(event);
    }

    private void dispatchPlayerControlsEvent(boolean focusControls) {
        if (getBridge() == null || getBridge().getWebView() == null) return;

        String focus = focusControls ? "true" : "false";
        getBridge().getWebView().evaluateJavascript(
            "window.dispatchEvent(new CustomEvent('tv-player-controls-show',{detail:{focus:" + focus + "}}));",
            null
        );
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

    private static boolean isBlockedAdHost(Uri url) {
        String host = url.getHost();
        if (host == null) return false;

        host = host.toLowerCase();
        for (String blockedHost : BLOCKED_AD_HOSTS) {
            if (host.equals(blockedHost) || host.endsWith("." + blockedHost)) return true;
        }
        return false;
    }

    private static WebResourceResponse emptyResponse() {
        return new WebResourceResponse(
            "text/plain",
            "UTF-8",
            200,
            "OK",
            Collections.emptyMap(),
            new ByteArrayInputStream(new byte[0])
        );
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

        @Override
        public WebResourceResponse shouldInterceptRequest(WebView view, WebResourceRequest request) {
            if (!request.isForMainFrame() && isBlockedAdHost(request.getUrl())) {
                return emptyResponse();
            }
            return super.shouldInterceptRequest(view, request);
        }

        @Override
        public WebResourceResponse shouldInterceptRequest(WebView view, String url) {
            if (isBlockedAdHost(Uri.parse(url))) {
                return emptyResponse();
            }
            return super.shouldInterceptRequest(view, url);
        }
    }
}
