<?php
return [
    'origin' => getenv('V2_ORIGIN') ?: 'http://127.0.0.1:8788',
    'environment' => getenv('V2_ENV') ?: 'development',
    'db_dsn' => getenv('V2_DB_DSN') ?: 'mysql:host=127.0.0.1;port=3306;dbname=tryq8flix_v2;charset=utf8mb4',
    'db_user' => getenv('V2_DB_USER') !== false ? getenv('V2_DB_USER') : 'root',
    'db_password' => getenv('V2_DB_PASSWORD') ?: '',
    'tmdb_token' => getenv('TMDB_TOKEN') ?: '',
    'language' => 'en-US',
    'provider_urls' => [],
    'extra_hosts' => [],
    'embed_hosts' => ['www.vidking.net', 'vidsrc.cc', 'vidsrc.me', 'vsembed.su', 'vsembed.ru', 'player.videasy.net', 'vidcore.net', 'vaplayer.ru', 'zxcstream.xyz', 'mapple.uk', 'ok.ru', 'www.ok.ru', 'videa.hu', 'dailymotion.com', 'www.dailymotion.com', 'mega.nz', 'www.mp4upload.com', 'highload.to', 'drive.google.com', 'yonaplay.net'],
    'http_timeout' => 12,
    'http_budget' => 25,
    'mail_from' => 'noreply@tryq8flix.com',
];
