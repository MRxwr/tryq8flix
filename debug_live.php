<?php
// TEMPORARY DEBUG FILE - DELETE AFTER USE
require_once("admin/includes/config.php");
require_once("try2/templates/simple_html_dom.php");

function liveCurl($url, $referer = '') {
	$url = trim($url);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36");
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	$headers = [
		'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
		'Accept-Language: en-US,en;q=0.9,ar;q=0.8',
		'Cache-Control: max-age=0',
		'Connection: keep-alive',
		'Upgrade-Insecure-Requests: 1',
	];
	if (!empty($referer)) $headers[] = 'Referer: ' . $referer;
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$response = curl_exec($ch);
	curl_close($ch);
	return $response;
}

$step = $_GET['step'] ?? '1';

// Step 1: fetch the match page, list its iframes
// Step 2: fetch a specific iframe URL
// Step 3: fetch serv=N page

$url     = $_GET['url'] ?? 'https://a12.kooora-sia.com/ssc-2/';
$referer = $_GET['referer'] ?? '';

$html = liveCurl($url, $referer);
$dom = str_get_html($html);

header('Content-Type: text/html; charset=UTF-8');
echo '<style>body{font-family:monospace;background:#111;color:#eee;padding:20px}
a{color:#4af}pre{background:#222;padding:10px;overflow:auto;white-space:pre-wrap;word-break:break-all}
.box{border:1px solid #444;margin:10px 0;padding:10px}</style>';

echo '<h2>Debug: <small>' . htmlspecialchars($url) . '</small></h2>';
echo '<p>Referer: <small>' . htmlspecialchars($referer) . '</small></p>';

if ($dom) {
	$iframes = $dom->find('iframe');
	echo '<h3>Iframes found: ' . count($iframes) . '</h3>';
	foreach ($iframes as $i => $if) {
		$src = trim($if->getAttribute('src'));
		if (strpos($src, '//') === 0) $src = 'https:' . $src;
		echo '<div class="box">';
		echo '<b>Iframe ' . ($i+1) . ':</b> <code>' . htmlspecialchars($src) . '</code><br>';
		if (!empty($src)) {
			$encodedSrc = urlencode($src);
			$encodedRef = urlencode($url);
			echo '<a href="?url=' . $encodedSrc . '&referer=' . $encodedRef . '">→ Follow this iframe</a>';
		}
		echo '</div>';
	}

	$menuLinks = $dom->find('a.aplr-link');
	echo '<h3>aplr-link menu items found: ' . count($menuLinks) . '</h3>';
	foreach ($menuLinks as $i => $link) {
		$href = trim($link->getAttribute('href'));
		$name = trim($link->plaintext);
		if (strpos($href, '//') === 0) $href = 'https:' . $href;
		echo '<div class="box">';
		echo '<b>' . htmlspecialchars($name) . '</b>: <code>' . htmlspecialchars($href) . '</code><br>';
		if (!empty($href)) {
			$encodedHref = urlencode($href);
			$encodedRef = urlencode($url);
			echo '<a href="?url=' . $encodedHref . '&referer=' . $encodedRef . '">→ Fetch this server page</a>';
		}
		
		// Auto-fetch each serv page and show iframe/src found
		if (!empty($href)) {
			$servHtml = liveCurl($href, $url);
			// Check static iframe
			$servDom = str_get_html($servHtml);
			$servIframe = $servDom ? $servDom->find('iframe', 0) : null;
			echo '<br><b>Auto-fetch result:</b><br>';
			if ($servIframe) {
				$iSrc = trim($servIframe->getAttribute('src'));
				$iData = trim($servIframe->getAttribute('data-initial'));
				echo '✅ iframe src: <code>' . htmlspecialchars($iSrc) . '</code>';
				if ($iData) echo '<br>✅ data-initial: <code>' . htmlspecialchars($iData) . '</code>';
			} elseif (preg_match('/iframe[^>]+src=["\']([^"\']+)["\']/', $servHtml, $m)) {
				echo '✅ Regex iframe src: <code>' . htmlspecialchars($m[1]) . '</code>';
			} else {
				echo '❌ No iframe found.';
			}
			echo '<br><details><summary>Raw HTML of this server page</summary><pre style="font-size:11px;max-height:400px;overflow:auto">' . htmlspecialchars($servHtml) . '</pre></details>';
		}
		echo '</div>';
	}
} else {
	echo '<p style="color:red">DOM parsing failed.</p>';
}

echo '<h3>Raw HTML (' . strlen($html) . ' bytes):</h3><pre>' . htmlspecialchars($html) . '</pre>';
echo '<hr><p style="color:#f88">DELETE debug_live.php when done!</p>';
