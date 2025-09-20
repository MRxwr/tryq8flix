<?php
// liveMatches.php
// Fetch live match links from the API
$apiUrl = 'api/views/apiLive.php?action=match&match=' . urlencode($_GET['match'] ?? '');
$matches = [];
if (isset($_GET['match'])) {
    $json = file_get_contents($apiUrl);
    $matches = json_decode($json, true);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Live Matches</title>
    <style>
        body { font-family: Arial, sans-serif; background: #181818; color: #fff; margin: 0; }
        .container { max-width: 800px; margin: 40px auto; padding: 20px; background: #222; border-radius: 8px; }
        h1 { text-align: center; }
        .match-list { display: flex; flex-wrap: wrap; gap: 16px; justify-content: center; }
        .match-btn { background: #0078d7; color: #fff; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; font-size: 1rem; transition: background 0.2s; }
        .match-btn:hover { background: #005fa3; }
        .iframe-container { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: #000; display: flex; align-items: center; justify-content: center; z-index: 9999; }
        .iframe-full { width: 90vw; height: 90vh; border: none; border-radius: 8px; }
        .close-btn { position: absolute; top: 32px; right: 32px; background: #d70022; color: #fff; border: none; padding: 10px 18px; border-radius: 6px; font-size: 1.2rem; cursor: pointer; z-index: 10000; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Choose a Live Match</h1>
        <form method="get" style="margin-bottom: 24px; text-align: center;">
            <input type="text" name="match" placeholder="Enter match URL or ID" value="<?= htmlspecialchars($_GET['match'] ?? '') ?>" style="padding: 8px; width: 60%; border-radius: 4px; border: 1px solid #444;">
            <button type="submit" class="match-btn">Load Matches</button>
        </form>
        <?php if (!empty($matches)) { ?>
            <div class="match-list">
                <?php foreach ($matches as $i => $match) {
                    if (!empty($match['src'])) {
                        $url = htmlspecialchars($match['src']);
                        echo "<button class='match-btn' onclick=\"showIframe('$url')\">Stream #" . ($i+1) . " (serv=" . ($match['serv'] ?? '') . ")</button>";
                    }
                } ?>
            </div>
        <?php } elseif (isset($_GET['match'])) { ?>
            <p style="text-align:center; color:#d70022;">No streams found for this match.</p>
        <?php } ?>
    </div>
    <div id="iframeScreen" style="display:none;"></div>
    <script>
        function showIframe(url) {
            var iframeScreen = document.getElementById('iframeScreen');
            iframeScreen.innerHTML = `<div class='iframe-container'><button class='close-btn' onclick='closeIframe()'>Close</button><iframe class='iframe-full' src='${url}' allowfullscreen></iframe></div>`;
            iframeScreen.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
        function closeIframe() {
            var iframeScreen = document.getElementById('iframeScreen');
            iframeScreen.innerHTML = '';
            iframeScreen.style.display = 'none';
            document.body.style.overflow = '';
        }
    </script>
</body>
</html>
