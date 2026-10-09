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
        $container = $dom->find('.col-md-8', 0);
        if ($container) {
            foreach ($container->find('.card.text-center') as $match) {
                $onclick = $match->getAttribute('onclick');
                $href = '';
                if (preg_match("/goToMatch\((\d+),'([^']+)'\)/", $onclick, $matches_link)) {
                    // Construct URL using the ID. Adjust path if needed based on actual site structure.
                    $href = "{$websiteLive}/match/" . $matches_link[1]; 
                }

                $cols = $match->find('.col-md-4');
                if (count($cols) >= 3) {
                    $rightTeamName = trim($cols[0]->find('.matchTeam', 0)->plaintext ?? '');
                    $rightTeamLogo = $cols[0]->find('.imgTeam', 0)->src ?? '';
                    
                    $leagueInfo = trim($cols[1]->find('.matchCompt', 0)->plaintext ?? '');
                    $matchTimeRaw = trim($cols[1]->find('.matchTime', 0)->plaintext ?? '');
                    
                    $leftTeamName = trim($cols[2]->find('.matchTeam', 0)->plaintext ?? '');
                    $leftTeamLogo = $cols[2]->find('.imgTeam', 0)->src ?? '';

                    $result = '';
                    $liveStatus = '';
                    
                    // Check if matchTimeRaw looks like a score (e.g., "1 - 1")
                    if (preg_match('/\d+\s*-\s*\d+/', $matchTimeRaw)) {
                        $result = $matchTimeRaw;
                    } else {
                        $liveStatus = $matchTimeRaw;
                    }

                    $jsonData = [
                        'href' => $href,
                        'rightTeamName' => $rightTeamName,
                        'leftTeamName' => $leftTeamName,
                        'rightTeamLogo' => $rightTeamLogo,
                        'leftTeamLogo' => $leftTeamLogo,
                        'matchTime' => $matchTimeRaw,
                        'result' => $result,
                        'liveStatus' => $liveStatus,
                        'league' => $leagueInfo,
                    ];
                    $data['matches'][] = $jsonData;
                }
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
    $dom = str_get_html($html);
	if ($dom) {
		$data = [
			'matches' => []
		];
		foreach ($dom->find('iframe') as $iframe) {
			if ($iframe) {
				$baseSrc = $iframe->getAttribute('src');
				for ($serv = 1; $serv <= 6; $serv++) {
					$srcWithIndex = $baseSrc . "index.php?serv=" . $serv;
					$iframeHtml = liveCurl($srcWithIndex);
					$iframeDom = str_get_html($iframeHtml);
					if ($iframeDom) {
						$foundIframe = $iframeDom->find('iframe', 0);
						if ($foundIframe) {
							$finalUrl = $foundIframe->getAttribute('src');
							// Remove any link with 'wallplaster' in the domain
							if (strpos($finalUrl, 'wallplaster') === false) {
								// Ensure the url starts with https
								$src = $finalUrl;
								if (strpos($src, 'https:') !== 0) {
									$src = 'https:' . $src;
								}
								$liveMatchesUrl = 'https://tryq8flix.com/liveMatches.php?match=' . urlencode($view);
								$jsonData = [
									'live' => $src,
									'serv' => $serv,
									'src' => $liveMatchesUrl
								];
								$data['matches'][] = $jsonData;
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
	return ( isset($matches) && !empty($matches) ) ? json_decode($matches, true)['matches'] : array();
}

if( isset($_GET['action']) && $_GET['action'] == 'match' ){
    $matches = liveMatch($_GET['match']);
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