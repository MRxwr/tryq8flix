package com.tryq8flix.tv;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.net.Uri;
import android.os.Bundle;
import android.view.Gravity;
import android.view.View;
import android.view.WindowManager;
import android.widget.FrameLayout;
import android.widget.TextView;

import androidx.annotation.OptIn;
import androidx.media3.common.MediaItem;
import androidx.media3.common.MimeTypes;
import androidx.media3.common.PlaybackException;
import androidx.media3.common.Player;
import androidx.media3.common.util.UnstableApi;
import androidx.media3.datasource.DefaultDataSource;
import androidx.media3.datasource.DefaultHttpDataSource;
import androidx.media3.exoplayer.ExoPlayer;
import androidx.media3.exoplayer.source.DefaultMediaSourceFactory;
import androidx.media3.ui.PlayerView;

import java.util.HashMap;
import java.util.Map;

/** Full-screen Media3 player. Does not attempt to play HTML/embed pages as media. */
@OptIn(markerClass = UnstableApi.class)
public class NativePlayerActivity extends Activity {
    private static final String URL = "media_url";
    private static final String REFERER = "media_referer";
    private static final String COOKIE = "media_cookie";
    private static final String UA = "media_user_agent";

    private ExoPlayer player;
    private PlayerView playerView;

    public static Intent intent(Context context, String url, String referer, String cookie, String userAgent) {
        return new Intent(context, NativePlayerActivity.class)
            .putExtra(URL, url)
            .putExtra(REFERER, referer)
            .putExtra(COOKIE, cookie)
            .putExtra(UA, userAgent);
    }

    @Override
    public void onCreate(Bundle state) {
        super.onCreate(state);
        getWindow().addFlags(WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON);
        getWindow().setStatusBarColor(Color.BLACK);
        getWindow().setNavigationBarColor(Color.BLACK);
        String url = getIntent().getStringExtra(URL);
        if (url == null || !StreamRules.isHttp(Uri.parse(url))) {
            finish();
            return;
        }

        FrameLayout root = new FrameLayout(this);
        root.setBackgroundColor(Color.BLACK);
        playerView = new PlayerView(this);
        playerView.setUseController(true);
        playerView.setControllerAutoShow(true);
        playerView.setControllerShowTimeoutMs(6000);
        root.addView(playerView, new FrameLayout.LayoutParams(-1, -1));

        TextView error = new TextView(this);
        error.setTextColor(Color.WHITE);
        error.setBackgroundColor(0xDD1B1B24);
        error.setTextSize(18);
        error.setPadding(36, 24, 36, 24);
        error.setGravity(Gravity.CENTER);
        error.setVisibility(View.GONE);
        FrameLayout.LayoutParams errorParams = new FrameLayout.LayoutParams(-1, -2, Gravity.CENTER);
        errorParams.setMargins(56, 0, 56, 0);
        root.addView(error, errorParams);
        setContentView(root);

        DefaultHttpDataSource.Factory http = new DefaultHttpDataSource.Factory()
            .setAllowCrossProtocolRedirects(true);
        String ua = getIntent().getStringExtra(UA);
        if (ua != null && !ua.isEmpty()) http.setUserAgent(ua);
        Map<String, String> headers = new HashMap<>();
        String referer = getIntent().getStringExtra(REFERER);
        String cookie = getIntent().getStringExtra(COOKIE);
        if (referer != null && !referer.isEmpty()) headers.put("Referer", referer);
        if (cookie != null && !cookie.isEmpty()) headers.put("Cookie", cookie);
        http.setDefaultRequestProperties(headers);

        DefaultDataSource.Factory source = new DefaultDataSource.Factory(this, http);
        player = new ExoPlayer.Builder(this)
            .setMediaSourceFactory(new DefaultMediaSourceFactory(source))
            .build();
        playerView.setPlayer(player);
        player.addListener(new Player.Listener() {
            @Override
            public void onPlayerError(PlaybackException exception) {
                // URL, cookies and headers may contain private provider tokens: do not log them.
                error.setText("This source could not play in the native player.\n"
                    + "Try the embed or external browser instead.\n\n"
                    + "Press Back to return.");
                error.setVisibility(View.VISIBLE);
            }
        });
        Uri uri = Uri.parse(url);
        String path = uri.getPath() == null ? "" : uri.getPath().toLowerCase(java.util.Locale.ROOT);
        MediaItem.Builder item = new MediaItem.Builder().setUri(uri);
        if (path.endsWith(".m3u8")) item.setMimeType(MimeTypes.APPLICATION_M3U8);
        if (path.endsWith(".mpd")) item.setMimeType(MimeTypes.APPLICATION_MPD);
        player.setMediaItem(item.build());
        player.prepare();
        player.play();
        playerView.requestFocus();
    }

    @Override
    protected void onStop() {
        if (player != null) player.pause();
        super.onStop();
    }

    @Override
    protected void onDestroy() {
        if (playerView != null) playerView.setPlayer(null);
        if (player != null) {
            player.release();
            player = null;
        }
        super.onDestroy();
    }
}
