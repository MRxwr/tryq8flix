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
		$matchElement = $dom->find('.albaflex .AY_Match', 0);
		if (!$matchElement) {
			$matchElement = $dom->find('.AY_Match', 0);
		}

		if ($matchElement) {
			@$leftTeamName = $matchElement->find('.MT_Team.TM1 .TM_Name', 0)->plaintext;
			@$leftTeamLogo = $matchElement->find('.MT_Team.TM1 .TM_Logo img', 0)->getAttribute('data-src');
			if (empty($leftTeamLogo)) {
				@$leftTeamLogo = $matchElement->find('.MT_Team.TM1 .TM_Logo img', 0)->getAttribute('src');
			}

			@$rightTeamName = $matchElement->find('.MT_Team.TM2 .TM_Name', 0)->plaintext;
			@$rightTeamLogo = $matchElement->find('.MT_Team.TM2 .TM_Logo img', 0)->getAttribute('data-src');
			if (empty($rightTeamLogo)) {
				@$rightTeamLogo = $matchElement->find('.MT_Team.TM2 .TM_Logo img', 0)->getAttribute('src');
			}

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
				'league' => trim($leagueInfo),
				'channel' => trim($channel),
				'commentator' => trim($commentator),
			];
		}

		$iframes = $dom->find('iframe');

		// Find all iframes directly on the match page
		foreach ($iframes as $iframe) {
			if ($iframe) {
				$baseSrc = trim($iframe->getAttribute('src')); // Trim to remove spaces

				// Skip empty or invalid sources
				if (empty($baseSrc) || strpos($baseSrc, 'wallplaster') !== false) {
					continue;
				}

				// Ensure the url starts with https
				if (strpos($baseSrc, 'https:') !== 0 && strpos($baseSrc, 'http:') !== 0) {
					$baseSrc = 'https:' . $baseSrc;
				}

				// Fetch the player page to find server links - pass view URL as referer
				$playerHtml = liveCurl($baseSrc, $view);

				$playerDom = str_get_html($playerHtml);

				if ($playerDom) {
					// Find all server links with class aplr-link
					$serverLinks = $playerDom->find('a.aplr-link');

					if (!empty($serverLinks)) {
						foreach ($serverLinks as $link) {
							$serverUrl = $link->getAttribute('href');
							$serverName = trim($link->plaintext);

							// Skip if URL is empty or javascript
							if (empty($serverUrl) || strpos($serverUrl, 'javascript:') === 0) {
								continue;
							}

							// Handle relative URLs - make them absolute based on baseSrc
							if (strpos($serverUrl, 'http') !== 0) {
								$parsedBase = parse_url($baseSrc);
								$baseUrl = $parsedBase['scheme'] . '://' . $parsedBase['host'];
								if (strpos($serverUrl, '/') === 0) {
									$serverUrl = $baseUrl . $serverUrl;
								} else {
									$serverUrl = rtrim($baseSrc, '/') . '/' . $serverUrl;
								}
							}

							// Fetch the individual server page
							$serverHtml = liveCurl($serverUrl, $baseSrc);

							$serverDom = str_get_html($serverHtml);

							if ($serverDom) {
								// Find the iframe on the server page
								$videoIframe = $serverDom->find('iframe', 0);

								if ($videoIframe) {
									$finalUrl = $videoIframe->getAttribute('src');

									// Skip wallplaster links or empty
									if (empty($finalUrl) || strpos($finalUrl, 'wallplaster') !== false) {
										continue;
									}

									// Ensure the url starts with https
									if (strpos($finalUrl, 'https:') !== 0 && strpos($finalUrl, 'http:') !== 0) {
										$finalUrl = 'https:' . $finalUrl;
									}

									$liveMatchesUrl = 'https://tryq8flix.com/liveMatches.php?match=' . urlencode($view);

									$jsonData = [
										'live' => $finalUrl,
										'name' => $serverName,
										'src' => $liveMatchesUrl
									];
									$data['matches'][] = $jsonData;
								}
							}
						}
					}
				}
			}
		}
		$matches = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	} else {
		$matches = '';
	}
	return (isset($matches) && !empty($matches)) ? json_decode($matches, true) : array();
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
