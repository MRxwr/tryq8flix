<?php
function liveCurl($url, $referer = '')
{
	$url = trim($url); // Remove any trailing/leading spaces
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36");
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ch, CURLOPT_TIMEOUT, 60);

	// Add more realistic headers
	$headers = [
		'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
		'Accept-Language: en-US,en;q=0.9,ar;q=0.8',
		'Cache-Control: max-age=0',
		'Connection: keep-alive',
		'Upgrade-Insecure-Requests: 1',
	];

	if (!empty($referer)) {
		$headers[] = 'Referer: ' . $referer;
	}

	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	$response = curl_exec($ch);
	curl_close($ch);

	return $response;
}

function searchMatches()
{
	global $websiteLive;
	$html = liveCurl("{$websiteLive}");
	$dom = str_get_html($html);
	if ($dom) {
		$data = [
			'matches' => []
		];
		// Find all matches with class AY_Match (live, comming-soon, not-started, finished)
		foreach ($dom->find('.albaflex .AY_Match') as $match) {
			$matchLink = $match->find('a', 0);
			if (!empty($matchLink)) {
				// Extract team 1 (TM1) info
				@$leftTeamName = $match->find('.MT_Team.TM1 .TM_Name', 0)->plaintext;
				@$leftTeamLogo = $match->find('.MT_Team.TM1 .TM_Logo img', 0)->getAttribute('data-src');
				if (empty($leftTeamLogo)) {
					@$leftTeamLogo = $match->find('.MT_Team.TM1 .TM_Logo img', 0)->getAttribute('src');
				}

				// Extract team 2 (TM2) info
				@$rightTeamName = $match->find('.MT_Team.TM2 .TM_Name', 0)->plaintext;
				@$rightTeamLogo = $match->find('.MT_Team.TM2 .TM_Logo img', 0)->getAttribute('data-src');
				if (empty($rightTeamLogo)) {
					@$rightTeamLogo = $match->find('.MT_Team.TM2 .TM_Logo img', 0)->getAttribute('src');
				}

				// Extract match data
				@$matchTime = $match->find('.MT_Data .MT_Time', 0)->plaintext;
				@$matchResult = $match->find('.MT_Data .MT_Result', 0)->plaintext;
				@$matchStatus = $match->find('.MT_Data .MT_Stat', 0)->plaintext;

				// Extract info
				@$channel = $match->find('.MT_Info ul li', 0)->plaintext;
				@$commentator = $match->find('.MT_Info ul li', 1)->plaintext;
				@$leagueInfo = $match->find('.MT_Info ul li', 2)->plaintext;

				$jsonData = [
					'href' => isset($matchLink->href) ? trim($matchLink->href) : '',
					'rightTeamName' => trim($rightTeamName),
					'leftTeamName' => trim($leftTeamName),
					'rightTeamLogo' => $rightTeamLogo,
					'leftTeamLogo' => $leftTeamLogo,
					'matchTime' => trim($matchTime),
					'result' => trim($matchResult),
					'liveStatus' => trim($matchStatus),
					'league' => trim($leagueInfo),
					'channel' => trim($channel),
					'commentator' => trim($commentator),
				];
				$data['matches'][] = $jsonData;
			} else {
				$jsonData = [
					'href' => '',
					'rightTeamName' => '',
					'leftTeamName' => '',
					'rightTeamLogo' => '',
					'leftTeamLogo' => '',
					'matchTime' => '',
					'result' => '',
					'liveStatus' => '',
					'league' => '',
				];
				$data['matches'][] = $jsonData;
			}
		}
		$matches = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	} else {
		echo 'Error: Invalid DOM object.';
	}
	return (isset($matches) && !empty($matches)) ? json_decode($matches, true)['matches'] : array();
}

function liveMatch($view)
{
	$html = liveCurl("{$view}");
	$dom = str_get_html($html);
	$data = [
		'matches' => [],
		'details' => null
	];

	if ($dom) {
		// Scrape match details (teams, flags, etc.)
		$matchElement = $dom->find('.albaflex .AY_Match', 0) ?: $dom->find('.AY_Match', 0);

		if ($matchElement) {
			@$leftTeamName = $matchElement->find('.MT_Team.TM1 .TM_Name', 0)->plaintext;
			@$leftTeamLogo = $matchElement->find('.MT_Team.TM1 .TM_Logo img', 0)->getAttribute('data-src') ?: $matchElement->find('.MT_Team.TM1 .TM_Logo img', 0)->getAttribute('src');

			@$rightTeamName = $matchElement->find('.MT_Team.TM2 .TM_Name', 0)->plaintext;
			@$rightTeamLogo = $matchElement->find('.MT_Team.TM2 .TM_Logo img', 0)->getAttribute('data-src') ?: $matchElement->find('.MT_Team.TM2 .TM_Logo img', 0)->getAttribute('src');

			@$matchTime = $matchElement->find('.MT_Data .MT_Time', 0)->plaintext;
			@$matchResult = $matchElement->find('.MT_Data .MT_Result', 0)->plaintext;
			@$matchStatus = $matchElement->find('.MT_Data .MT_Stat', 0)->plaintext;

			@$channel = $matchElement->find('.MT_Info ul li', 0)->plaintext;
			@$commentator = $matchElement->find('.MT_Info ul li', 1)->plaintext;
			@$leagueInfo = $matchElement->find('.MT_Info ul li', 2)->plaintext;

			$data['details'] = [
				'rightTeamName' => trim($rightTeamName),
				'leftTeamName' => trim($leftTeamName),
				'rightTeamLogo' => $rightTeamLogo,
				'leftTeamLogo' => $leftTeamLogo,
				'matchTime' => trim($matchTime),
				'result' => trim($matchResult),
				'liveStatus' => trim($matchStatus),
				'league' => trim($leagueInfo) ?: 'مباراة',
				'channel' => trim($channel) ?: 'غير معروف',
				'commentator' => trim($commentator) ?: 'غير معروف',
			];
		}

		$foundServers = [];
		$addUniqueServer = function ($url, $name) use (&$data, &$foundServers, $view) {
			$url = trim($url);
			if (empty($url) || isset($foundServers[$url]) || strpos($url, 'wallplaster') !== false) return;
			if (strpos($url, '//') === 0) $url = 'https:' . $url;

			$data['matches'][] = [
				'live' => $url,
				'name' => $name,
				'src' => 'https://tryq8flix.com/liveMatches.php?match=' . urlencode($view)
			];
			$foundServers[$url] = true;
		};

		$extractMenu = function ($currentDom, $currentUrl) use ($addUniqueServer) {
			$menuLinks = $currentDom->find('a.aplr-link');
			if (!empty($menuLinks)) {
				foreach ($menuLinks as $link) {
					$sUrl = trim($link->getAttribute('href'));
					$sName = trim($link->plaintext);
					if (empty($sUrl) || strpos($sUrl, 'javascript:') === 0) continue;

					if (strpos($sUrl, 'http') !== 0) {
						$parsed = parse_url($currentUrl);
						$baseUrl = $parsed['scheme'] . '://' . $parsed['host'];
						$sUrl = (strpos($sUrl, '/') === 0) ? $baseUrl . $sUrl : rtrim($currentUrl, '/') . '/' . $sUrl;
					}

					$sHtml = liveCurl($sUrl, $currentUrl);
					$sDom = str_get_html($sHtml);
					if ($sDom) {
						$si = $sDom->find('iframe', 0);
						if ($si) $addUniqueServer($si->getAttribute('src'), $sName);
					}
				}
				return true;
			}
			return false;
		};

		$menuFound = false;
		foreach ($dom->find('iframe') as $iframe) {
			if ($menuFound) break;

			$baseSrc = trim($iframe->getAttribute('src'));
			if (empty($baseSrc) || strpos($baseSrc, 'wallplaster') !== false) continue;
			if (strpos($baseSrc, '//') === 0) $baseSrc = 'https:' . $baseSrc;

			// Pass match page as referer when fetching the first player iframe
			$p1Html = liveCurl($baseSrc, $view);
			$p1Dom = str_get_html($p1Html);
			if (!$p1Dom) continue;

			// Level 1: check the direct player page for aplr-link menu
			if ($extractMenu($p1Dom, $baseSrc)) {
				$menuFound = true;
				break;
			}

			// Level 2: look inside iframes of the player page
			foreach ($p1Dom->find('iframe') as $p2) {
				if ($menuFound) break;

				$p2Src = trim($p2->getAttribute('src'));
				if (empty($p2Src) || strpos($p2Src, 'wallplaster') !== false) continue;
				if (strpos($p2Src, '//') === 0) $p2Src = 'https:' . $p2Src;

				// Pass level-1 URL as referer when fetching level-2 iframe
				$p2Html = liveCurl($p2Src, $baseSrc);
				$p2Dom = str_get_html($p2Html);
				if (!$p2Dom) continue;

				if ($extractMenu($p2Dom, $p2Src)) {
					$menuFound = true;
					break;
				}
			}
		}

		// Fallback: no menu found anywhere, grab first valid iframe from each player page
		if (!$menuFound) {
			foreach ($dom->find('iframe') as $iframe) {
				$baseSrc = trim($iframe->getAttribute('src'));
				if (empty($baseSrc) || strpos($baseSrc, 'wallplaster') !== false) continue;
				if (strpos($baseSrc, '//') === 0) $baseSrc = 'https:' . $baseSrc;

				$p1Html = liveCurl($baseSrc, $view);
				$p1Dom = str_get_html($p1Html);
				if (!$p1Dom) continue;

				$kb = $p1Dom->find('.koora-bar', 0);
				$targets = ($kb && $kb->parent()) ? $kb->parent()->find('iframe') : $p1Dom->find('iframe');
				foreach ($targets as $fIf) {
					$addUniqueServer($fIf->getAttribute('src'), 'Server ' . (count($data['matches']) + 1));
				}
			}
		}
		$matches = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	} else {
		$matches = '';
	}
	$result = (isset($matches) && !empty($matches)) ? json_decode($matches, true) : array();
	return (isset($result['matches'])) ? $result['matches'] : $result;
}

if (isset($_GET['action']) && $_GET['action'] == 'match') {
	$matches = liveMatch($_GET['match']);
	echo dataOutput($matches);
} elseif (isset($_GET['action']) && $_GET['action'] == 'live') {
	$matches = searchMatches();
	echo dataOutput($matches);
} else {
	echo dataError('Invalid request.');
}
