<?php
function liveCurl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36");
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function searchMatches() {
	GLOBAL $websiteLive;
	$html = liveCurl("{$websiteLive}");
    $dom = str_get_html($html);
    if ($dom) {
        $data = [
            'matches' => []
        ];
        // Find all matches with class AY_Match (live, comming-soon, not-started, finished)
        foreach ($dom->find('.albaflex .AY_Match') as $match) {
            $matchLink = $match->find('a', 0);
			if( !empty($matchLink) ){
				// Extract team 1 (TM1) info
				@$leftTeamName = $match->find('.MT_Team.TM1 .TM_Name', 0)->plaintext;
				@$leftTeamLogo = $match->find('.MT_Team.TM1 .TM_Logo img', 0)->getAttribute('data-src');
				if(empty($leftTeamLogo)) {
					@$leftTeamLogo = $match->find('.MT_Team.TM1 .TM_Logo img', 0)->getAttribute('src');
				}
				
				// Extract team 2 (TM2) info
				@$rightTeamName = $match->find('.MT_Team.TM2 .TM_Name', 0)->plaintext;
				@$rightTeamLogo = $match->find('.MT_Team.TM2 .TM_Logo img', 0)->getAttribute('data-src');
				if(empty($rightTeamLogo)) {
					@$rightTeamLogo = $match->find('.MT_Team.TM2 .TM_Logo img', 0)->getAttribute('src');
				}
				
				// Extract match data
				@$matchTime = $match->find('.MT_Data .MT_Time', 0)->plaintext;
				@$matchResult = $match->find('.MT_Data .MT_Result', 0)->plaintext;
				@$matchStatus = $match->find('.MT_Data .MT_Stat', 0)->plaintext;
				
				// Extract league info (third li element)
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
				];
				$data['matches'][] = $jsonData;
			}else{
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
    return ( isset($matches) && !empty($matches) ) ? json_decode($matches, true)['matches'] : array();
}

function liveMatch($view) {
	$html = liveCurl("{$view}");
	echo "<pre>1. Match Page URL: " . $view . "</pre>";
	echo "<pre>2. Match Page HTML Length: " . strlen($html) . "</pre>";
	
    $dom = str_get_html($html);
	if ($dom) {
		$data = [
			'matches' => []
		];
		
		$iframes = $dom->find('iframe');
		echo "<pre>3. Found " . count($iframes) . " iframes on match page</pre>";
		
		// Find all iframes directly on the match page
		foreach ($iframes as $iframe) {
			if ($iframe) {
				$baseSrc = $iframe->getAttribute('src');
				echo "<pre>4. Iframe src: " . htmlspecialchars($baseSrc) . "</pre>";
				
				// Skip empty or invalid sources
				if (empty($baseSrc) || strpos($baseSrc, 'wallplaster') !== false) {
					echo "<pre>5. Skipping this iframe (empty or wallplaster)</pre>";
					continue;
				}
				
				// Ensure the url starts with https
				if (strpos($baseSrc, 'https:') !== 0 && strpos($baseSrc, 'http:') !== 0) {
					$baseSrc = 'https:' . $baseSrc;
				}
				echo "<pre>6. Fetching player page: " . htmlspecialchars($baseSrc) . "</pre>";
				
				// Fetch the player page to find server links
				$playerHtml = liveCurl($baseSrc);
				echo "<pre>7. Player page HTML length: " . strlen($playerHtml) . "</pre>";
				
				$playerDom = str_get_html($playerHtml);
				
				if ($playerDom) {
					// Find all server links with class aplr-link
					$serverLinks = $playerDom->find('a.aplr-link');
					echo "<pre>8. Found " . count($serverLinks) . " server links with class 'aplr-link'</pre>";
					echo "<pre>8. Found " . count($serverLinks) . " server links with class 'aplr-link'</pre>";
					
					if (!empty($serverLinks)) {
						foreach ($serverLinks as $link) {
							$serverUrl = $link->getAttribute('href');
							$serverName = trim($link->plaintext);
							
							echo "<pre>9. Server Link - Name: " . htmlspecialchars($serverName) . ", Href: " . htmlspecialchars($serverUrl) . "</pre>";
							
							// Skip if URL is empty
							if (empty($serverUrl)) {
								echo "<pre>10. Skipping - empty URL</pre>";
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
								echo "<pre>11. Converted to absolute URL: " . htmlspecialchars($serverUrl) . "</pre>";
							}
							
							// Fetch the individual server page
							echo "<pre>12. Fetching server page: " . htmlspecialchars($serverUrl) . "</pre>";
							$serverHtml = liveCurl($serverUrl);
							echo "<pre>13. Server page HTML length: " . strlen($serverHtml) . "</pre>";
							
							$serverDom = str_get_html($serverHtml);
							
							if ($serverDom) {
								// Find the iframe on the server page
								$videoIframe = $serverDom->find('iframe', 0);
								
								if ($videoIframe) {
									$finalUrl = $videoIframe->getAttribute('src');
									echo "<pre>14. Found video iframe src: " . htmlspecialchars($finalUrl) . "</pre>";
									
									// Skip wallplaster links or empty
									if (empty($finalUrl) || strpos($finalUrl, 'wallplaster') !== false) {
										echo "<pre>15. Skipping - empty or wallplaster</pre>";
										continue;
									}
									
									// Ensure the url starts with https
									if (strpos($finalUrl, 'https:') !== 0 && strpos($finalUrl, 'http:') !== 0) {
										$finalUrl = 'https:' . $finalUrl;
									}
									
									echo "<pre>16. Final URL added: " . htmlspecialchars($finalUrl) . "</pre>";
									
									$liveMatchesUrl = 'https://tryq8flix.com/liveMatches.php?match=' . urlencode($view);
									
									$jsonData = [
										'live' => $finalUrl,
										'name' => $serverName,
										'src' => $liveMatchesUrl
									];
									$data['matches'][] = $jsonData;
								} else {
									echo "<pre>17. No iframe found on server page</pre>";
								}
							} else {
								echo "<pre>18. Failed to parse server page DOM</pre>";
							}
						}
					} else {
						echo "<pre>19. No server links found (empty array)</pre>";
					}
				} else {
					echo "<pre>20. Failed to parse player page DOM</pre>";
				}
			}
		}
		echo "<pre>21. Total servers added to data array: " . count($data['matches']) . "</pre>";
		$matches = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	} else {
		echo "<pre>22. Failed to parse match page DOM</pre>";
		$matches = '';
	}
	return ( isset($matches) && !empty($matches) ) ? json_decode($matches, true)['matches'] : array();
}

if( isset($_GET['action']) && $_GET['action'] == 'match' ){
    echo "<pre>DEBUG MODE - Fetching match: " . htmlspecialchars($_GET['match']) . "</pre>";
    echo "<hr>";
    $matches = liveMatch($_GET['match']);
    echo "<hr>";
    echo "<pre>FINAL RESULT:</pre>";
    echo "<pre>" . print_r($matches, true) . "</pre>";
    echo "<hr>";
    /*
    $data[] = array("src" => $_GET['match']);
    $matches = $data;
    */
    echo dataOutput($matches);
}elseif( isset($_GET['action']) && $_GET['action'] == 'live' ){
    $matches = searchMatches();
    echo dataOutput($matches);
}else{
    echo dataError('Invalid request.');
}

?>