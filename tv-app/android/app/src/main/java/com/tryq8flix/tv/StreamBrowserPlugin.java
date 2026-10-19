package com.tryq8flix.tv;

import android.content.ActivityNotFoundException;
import android.content.Intent;
import android.net.Uri;

import com.getcapacitor.Plugin;
import com.getcapacitor.PluginCall;
import com.getcapacitor.PluginMethod;
import com.getcapacitor.annotation.CapacitorPlugin;

@CapacitorPlugin(name = "StreamBrowser")
public class StreamBrowserPlugin extends Plugin {

    /** Launch the in-app TV WebView with passive stream detection and ad filtering. */
    @PluginMethod
    public void openInternal(PluginCall call) {
        String value = call.getString("url");
        Uri uri = value == null ? null : Uri.parse(value);
        if (!StreamRules.isHttp(uri)) {
            call.reject("The stream page must use HTTP or HTTPS.");
            return;
        }
        getActivity().runOnUiThread(() -> {
            try {
                Intent intent = new Intent(getActivity(), StreamBrowserActivity.class);
                intent.putExtra(StreamBrowserActivity.EXTRA_URL, value);
                getActivity().startActivity(intent);
                call.resolve();
            } catch (Exception exception) {
                call.reject("Could not open the internal video browser.", exception);
            }
        });
    }

    @PluginMethod
    public void openExternal(PluginCall call) {
        String value = call.getString("url");
        Uri uri = value == null ? null : Uri.parse(value);
        String scheme = uri == null ? null : uri.getScheme();

        if (uri == null || uri.getHost() == null
            || (!"https".equalsIgnoreCase(scheme) && !"http".equalsIgnoreCase(scheme))) {
            call.reject("The stream URL must be a valid HTTP or HTTPS address.");
            return;
        }

        getActivity().runOnUiThread(() -> {
            try {
                Intent browserIntent = new Intent(Intent.ACTION_VIEW, uri);
                browserIntent.addCategory(Intent.CATEGORY_BROWSABLE);
                getActivity().startActivity(Intent.createChooser(browserIntent, "Open video with"));
                call.resolve();
            } catch (ActivityNotFoundException exception) {
                call.reject("No browser is available to open this stream.", exception);
            }
        });
    }
}
