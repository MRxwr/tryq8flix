<?php
if (!isset($_GET['action']) || empty($_GET['action'])) {
    echo dataError(array('msg' => 'action is required'));
    die();
}

if (empty($token)) {
    echo dataError(array('msg' => 'token is required'));
    die();
}

$action = $_GET['action'];
$url = isset($_REQUEST['url']) ? trim($_REQUEST['url']) : '';

if (empty($url)) {
    echo dataError(array('msg' => 'url is required'));
    die();
}

if (!filter_var($url, FILTER_VALIDATE_URL)) {
    echo dataError(array('msg' => 'invalid url'));
    die();
}

if (!isAllowedVideoUrl($url)) {
    echo dataError(array('msg' => 'Only X/Twitter, Instagram and TikTok links are supported'));
    die();
}

if (!in_array($action, array('info', 'link'), true)) {
    echo dataError(array('msg' => 'Invalid action'));
    die();
}

$result = runVideoDownloaderNode($action, $url);
if ($result['ok']) {
    echo dataOutput($result['data']);
} else {
    echo dataError(array('msg' => $result['error']));
}
die();

function isAllowedVideoUrl($url)
{
    $host = strtolower(parse_url($url, PHP_URL_HOST) ?: '');
    if (empty($host)) {
        return false;
    }

    $allowedHosts = array(
        'twitter.com',
        'x.com',
        'www.twitter.com',
        'www.x.com',
        'instagram.com',
        'www.instagram.com',
        'tiktok.com',
        'www.tiktok.com',
        'vm.tiktok.com',
        'vt.tiktok.com'
    );

    if (in_array($host, $allowedHosts, true)) {
        return true;
    }

    // Allow platform subdomains
    if (preg_match('/(^|\\.)instagram\\.com$/', $host)) {
        return true;
    }
    if (preg_match('/(^|\\.)tiktok\\.com$/', $host)) {
        return true;
    }
    if (preg_match('/(^|\\.)x\\.com$/', $host)) {
        return true;
    }
    if (preg_match('/(^|\\.)twitter\\.com$/', $host)) {
        return true;
    }

    return false;
}

function runVideoDownloaderNode($action, $url)
{
    $projectRoot = realpath(__DIR__ . '/../../');
    if ($projectRoot === false) {
        return array('ok' => false, 'error' => 'Unable to resolve project path');
    }

    $nodeScript = <<<'JS'
const fs = require('fs');
const path = require('path');

(async () => {
  const YTDlpWrap = require('yt-dlp-wrap').default;
  const action = process.argv[1];
  const targetUrl = process.argv[2];
  const projectRoot = process.argv[3];

  const binPath = path.join(projectRoot, 'bin', process.platform === 'win32' ? 'yt-dlp.exe' : 'yt-dlp');

  if (!fs.existsSync(binPath)) {
    fs.mkdirSync(path.dirname(binPath), { recursive: true });
    await YTDlpWrap.downloadFromGithub(binPath);
  }

  const ytDlpWrap = new YTDlpWrap(binPath);

  if (action === 'info') {
    const info = await ytDlpWrap.getVideoInfo(targetUrl);
    const normalized = {
      id: info.id || '',
      title: info.title || '',
      webpage_url: info.webpage_url || targetUrl,
      uploader: info.uploader || info.channel || '',
      duration: info.duration || 0,
      thumbnail: info.thumbnail || '',
      ext: info.ext || '',
      format: info.format || '',
      extractor: info.extractor_key || info.extractor || ''
    };
    process.stdout.write(JSON.stringify({ ok: true, data: normalized }));
    return;
  }

  if (action === 'link') {
    const raw = await ytDlpWrap.execPromise(['--no-warnings', '--no-playlist', '-f', 'mp4/best', '-g', targetUrl]);
    const candidates = String(raw || '')
      .split(/\r?\n/)
      .map(v => v.trim())
      .filter(v => v.length > 0);

    process.stdout.write(JSON.stringify({
      ok: true,
      data: {
        stream_url: candidates.length > 0 ? candidates[0] : '',
        all_urls: candidates
      }
    }));
    return;
  }

  process.stdout.write(JSON.stringify({ ok: false, error: 'Unsupported action' }));
})();
JS;

    $command = 'node -e ' . escapeshellarg($nodeScript) . ' '
        . escapeshellarg($action) . ' '
        . escapeshellarg($url) . ' '
        . escapeshellarg($projectRoot) . ' 2>&1';

    $raw = shell_exec($command);

    if ($raw === null) {
        return array('ok' => false, 'error' => 'Node execution failed');
    }

    $decoded = json_decode(trim($raw), true);
    if (!is_array($decoded)) {
        return array('ok' => false, 'error' => 'Invalid downloader response: ' . trim($raw));
    }

    if (isset($decoded['ok']) && $decoded['ok'] === true) {
        return array('ok' => true, 'data' => $decoded['data']);
    }

    $error = isset($decoded['error']) ? $decoded['error'] : 'Unknown downloader error';
    return array('ok' => false, 'error' => $error);
}
