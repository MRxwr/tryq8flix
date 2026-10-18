package com.tryq8flix.tv;

import android.os.SystemClock;
import android.view.KeyEvent;
import android.webkit.WebView;

import com.getcapacitor.Plugin;
import com.getcapacitor.PluginCall;
import com.getcapacitor.PluginMethod;
import com.getcapacitor.annotation.CapacitorPlugin;

@CapacitorPlugin(name = "VideoControls")
public class VideoControlsPlugin extends Plugin {

    @PluginMethod
    public void sendMediaKey(PluginCall call) {
        String action = call.getString("action");
        final int keyCode;

        if ("play".equals(action)) {
            keyCode = KeyEvent.KEYCODE_MEDIA_PLAY;
        } else if ("pause".equals(action)) {
            keyCode = KeyEvent.KEYCODE_MEDIA_PAUSE;
        } else {
            call.reject("Unsupported media key action.");
            return;
        }

        WebView webView = getBridge().getWebView();
        if (webView == null) {
            call.reject("The video WebView is not available.");
            return;
        }

        webView.post(() -> {
            long time = SystemClock.uptimeMillis();
            webView.dispatchKeyEvent(new KeyEvent(time, time, KeyEvent.ACTION_DOWN, keyCode, 0));
            webView.dispatchKeyEvent(new KeyEvent(time, time, KeyEvent.ACTION_UP, keyCode, 0));
            call.resolve();
        });
    }
}
