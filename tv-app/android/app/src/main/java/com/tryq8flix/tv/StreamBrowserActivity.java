package com.tryq8flix.tv;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.ActivityNotFoundException;
import android.content.Intent;
import android.graphics.Color;
import android.graphics.Typeface;
import android.net.Uri;
import android.os.Bundle;
import android.os.Message;
import android.view.Gravity;
import android.view.View;
import android.view.ViewGroup;
import android.view.WindowManager;
import android.webkit.CookieManager;
import android.webkit.WebChromeClient;
import android.webkit.WebResourceRequest;
import android.webkit.WebResourceResponse;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import java.util.ArrayList;
import java.util.HashSet;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Locale;
import java.util.Map;
import java.util.Set;

/**
 * Dedicated TV browser: ad-host blocking, popup/redirect approval and passive media detection.
 * No DRM bypass, network MITM, script decryption or extraction from blob: URLs.
 */
public class StreamBrowserActivity extends Activity {
    public static final String EXTRA_URL = "stream_page_url";

    private WebView browser;
    private Button detectedButton;
    private TextView status;
    private final LinkedHashMap<String, Candidate> candidates = new LinkedHashMap<>();
    private final Set<String> approvedHosts = new HashSet<>();
    private AlertDialog pendingDialog;
    private int blocked = 0;
    private String lastUrl = "";

    private static final class Candidate {
        final String url;
        final String referer;
        final String userAgent;

        Candidate(String url, String referer, String userAgent) {
            this.url = url;
            this.referer = referer;
            this.userAgent = userAgent;
        }
    }

    @Override
    public void onCreate(Bundle savedState) {
        super.onCreate(savedState);
        getWindow().addFlags(WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON);
        getWindow().setStatusBarColor(0xFF101018);
        getWindow().setNavigationBarColor(0xFF101018);
        String url = getIntent().getStringExtra(EXTRA_URL);
        if (url == null || !StreamRules.isHttp(Uri.parse(url))) {
            Toast.makeText(this, "Invalid stream page URL.", Toast.LENGTH_LONG).show();
            finish();
            return;
        }
        Uri initial = Uri.parse(url);
        approvedHosts.add(initial.getHost().toLowerCase(Locale.ROOT));

        LinearLayout root = new LinearLayout(this);
        root.setOrientation(LinearLayout.VERTICAL);
        root.setBackgroundColor(0xFF101018);
        setContentView(root);

        LinearLayout toolbar = new LinearLayout(this);
        toolbar.setPadding(dp(12), dp(9), dp(12), dp(9));
        toolbar.setGravity(Gravity.CENTER_VERTICAL);
        toolbar.setBackgroundColor(0xFF171720);
        root.addView(toolbar, new LinearLayout.LayoutParams(-1, -2));

        Button close = button("← Back");
        close.setOnClickListener(v -> finish());
        toolbar.addView(close);

        detectedButton = button("Videos (0)");
        detectedButton.setEnabled(false);
        detectedButton.setOnClickListener(v -> showCandidates());
        toolbar.addView(detectedButton);

        Button reload = button("Reload");
        reload.setOnClickListener(v -> browser.reload());
        toolbar.addView(reload);

        Button external = button("External browser");
        external.setOnClickListener(v -> openExternal());
        toolbar.addView(external);

        status = new TextView(this);
        status.setText("Opening server… · video detection is experimental");
        status.setTextSize(12);
        status.setTextColor(0xFFBFC4D4);
        status.setPadding(dp(16), dp(6), dp(16), dp(6));
        root.addView(status, new LinearLayout.LayoutParams(-1, -2));

        browser = new WebView(this);
        browser.setBackgroundColor(Color.BLACK);
        browser.setFocusable(true);
        browser.setFocusableInTouchMode(true);
        root.addView(browser, new LinearLayout.LayoutParams(-1, 0, 1));
        WebSettings settings = browser.getSettings();
        settings.setJavaScriptEnabled(true);
        settings.setDomStorageEnabled(true);
        settings.setMediaPlaybackRequiresUserGesture(false);
        settings.setSupportMultipleWindows(true);
        settings.setJavaScriptCanOpenWindowsAutomatically(false);
        settings.setAllowFileAccess(false);
        settings.setAllowContentAccess(false);
        settings.setMixedContentMode(WebSettings.MIXED_CONTENT_NEVER_ALLOW);
        CookieManager.getInstance().setAcceptCookie(true);
        CookieManager.getInstance().setAcceptThirdPartyCookies(browser, true);

        browser.setWebViewClient(new PlayerBrowserClient());
        browser.setWebChromeClient(new PlayerChromeClient());
        browser.loadUrl(url);
        detectedButton.requestFocus();
        close.requestFocus();
    }

    private int dp(int value) {
        return (int) (getResources().getDisplayMetrics().density * value + .5f);
    }

    private Button button(String label) {
        Button button = new Button(this);
        button.setText(label);
        button.setTextSize(13);
        button.setAllCaps(false);
        button.setTextColor(Color.WHITE);
        button.setTypeface(Typeface.DEFAULT, Typeface.BOLD);
        button.setBackgroundTintList(android.content.res.ColorStateList.valueOf(0xFF303340));
        LinearLayout.LayoutParams params = new LinearLayout.LayoutParams(-2, dp(48));
        params.setMargins(0, 0, dp(8), 0);
        button.setLayoutParams(params);
        return button;
    }

    private void updateStatus(String text) {
        status.setText(text + " · blocked ads: " + blocked);
    }

    private void detected(Uri uri, Map<String, String> headers) {
        if (!StreamRules.isMedia(uri) || StreamRules.isAd(uri)) return;
        String url = uri.toString();
        if (url.length() > 4096) return;
        String referer = header(headers, "Referer");
        String ua = header(headers, "User-Agent");
        // shouldInterceptRequest may run on a background thread: all UI/WebView reads on UI.
        runOnUiThread(() -> {
            if (isFinishing() || browser == null || candidates.containsKey(url)) return;
            if (candidates.size() >= 25) return;
            String from = referer.isEmpty() ? browser.getUrl() : referer;
            String agent = ua.isEmpty() ? browser.getSettings().getUserAgentString() : ua;
            candidates.put(url, new Candidate(url, from, agent));
            detectedButton.setEnabled(true);
            detectedButton.setText("Videos (" + candidates.size() + ")");
            updateStatus(candidates.size() + " possible stream(s) detected. Select Videos to test.");
        });
    }

    private static String header(Map<String, String> headers, String key) {
        if (headers == null) return "";
        for (Map.Entry<String, String> entry : headers.entrySet()) {
            if (entry.getKey().equalsIgnoreCase(key) && entry.getValue() != null) {
                return entry.getValue().length() <= 4096 ? entry.getValue() : "";
            }
        }
        return "";
    }

    private void showCandidates() {
        if (candidates.isEmpty()) return;
        List<Candidate> list = new ArrayList<>(candidates.values());
        String[] labels = new String[list.size()];
        for (int i = 0; i < list.size(); i++) {
            labels[i] = (i + 1) + ". " + StreamRules.mediaLabel(Uri.parse(list.get(i).url));
        }
        new AlertDialog.Builder(this)
            .setTitle("Play detected stream")
            .setItems(labels, (dialog, which) -> {
                Candidate candidate = list.get(which);
                String cookie = CookieManager.getInstance().getCookie(candidate.url);
                if (cookie != null && cookie.length() > 4096) cookie = null;
                startActivity(NativePlayerActivity.intent(
                    this, candidate.url, candidate.referer, cookie, candidate.userAgent
                ));
            })
            .setNegativeButton("Cancel", null)
            .show();
    }

    private void openExternal() {
        String url = browser != null ? browser.getUrl() : null;
        if (url == null || !StreamRules.isHttp(Uri.parse(url))) url = getIntent().getStringExtra(EXTRA_URL);
        if (url == null || !StreamRules.isHttp(Uri.parse(url))) return;
        try {
            Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse(url));
            intent.addCategory(Intent.CATEGORY_BROWSABLE);
            startActivity(Intent.createChooser(intent, "Open stream with"));
        } catch (ActivityNotFoundException error) {
            Toast.makeText(this, "No browser installed.", Toast.LENGTH_LONG).show();
        }
    }

    private void requestNavigation(String target, boolean popup) {
        Uri uri = Uri.parse(target);
        if (!StreamRules.isHttp(uri) || StreamRules.isAd(uri)) {
            blocked++;
            updateStatus("Blocked ad or invalid redirect");
            return;
        }
        if (pendingDialog != null && pendingDialog.isShowing()) return;
        String host = uri.getHost().toLowerCase(Locale.ROOT);
        // Never auto-approve a new top-level domain. Video CDNs in subrequests remain allowed.
        pendingDialog = new AlertDialog.Builder(this)
            .setTitle(popup ? "Open popup?" : "Leave current video page?")
            .setMessage("Destination: " + host + "\nAllow this page to open?")
            .setPositiveButton("Allow once", (dialog, which) -> {
                approvedHosts.add(host);
                browser.loadUrl(target);
            })
            .setNegativeButton("Cancel", (dialog, which) -> updateStatus("Redirect cancelled"))
            .create();
        pendingDialog.show();
    }

    private final class PlayerBrowserClient extends WebViewClient {
        @Override
        public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest request) {
            if (!request.isForMainFrame()) return false;
            Uri uri = request.getUrl();
            if (!StreamRules.isHttp(uri) || StreamRules.isAd(uri)) {
                blocked++;
                updateStatus("Blocked ad navigation");
                return true;
            }
            String host = uri.getHost().toLowerCase(Locale.ROOT);
            if (approvedHosts.contains(host)) return false;
            requestNavigation(uri.toString(), false);
            return true;
        }

        @Override
        public WebResourceResponse shouldInterceptRequest(WebView view, WebResourceRequest request) {
            Uri uri = request.getUrl();
            if (StreamRules.isAd(uri)) {
                runOnUiThread(() -> {
                    blocked++;
                    updateStatus("Filtering ad requests");
                });
                return StreamRules.emptyResponse();
            }
            detected(uri, request.getRequestHeaders());
            return null; // Allow real request to continue without proxying/altering its headers.
        }

        @Override
        public void onPageFinished(WebView view, String url) {
            super.onPageFinished(view, url);
            lastUrl = url;
            if (candidates.isEmpty()) updateStatus("Page loaded. Start video to detect streams.");
        }
    }

    private final class PlayerChromeClient extends WebChromeClient {
        @Override
        public boolean onCreateWindow(WebView view, boolean isDialog, boolean isUserGesture, Message resultMsg) {
            if (!isUserGesture) {
                blocked++;
                updateStatus("Automatic popup blocked");
                return false;
            }
            WebView popup = new WebView(StreamBrowserActivity.this);
            popup.setWebViewClient(new WebViewClient() {
                private boolean handled;
                private void capture(String url) {
                    if (handled) return;
                    handled = true;
                    requestNavigation(url, true);
                    popup.post(popup::destroy);
                }
                @Override
                public boolean shouldOverrideUrlLoading(WebView v, WebResourceRequest request) {
                    capture(request.getUrl().toString());
                    return true;
                }
                @Override
                public void onPageStarted(WebView v, String url, android.graphics.Bitmap icon) {
                    capture(url);
                }
            });
            WebView.WebViewTransport transport = (WebView.WebViewTransport) resultMsg.obj;
            transport.setWebView(popup);
            resultMsg.sendToTarget();
            return true;
        }
    }

    @Override
    public void onBackPressed() {
        if (browser != null && browser.canGoBack()) browser.goBack();
        else super.onBackPressed();
    }

    @Override
    protected void onDestroy() {
        if (pendingDialog != null) pendingDialog.dismiss();
        if (browser != null) {
            browser.stopLoading();
            browser.loadUrl("about:blank");
            browser.setWebChromeClient(null);
            browser.setWebViewClient(null);
            ((ViewGroup) browser.getParent()).removeView(browser);
            browser.destroy();
            browser = null;
        }
        super.onDestroy();
    }
}
