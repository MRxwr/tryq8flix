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
	GLOBAL $websiteLive2;
	$html = liveCurl("{$websiteLive2}");
    $dom = str_get_html($html);
    if ($dom) {
        $data = [
            'matches' => []
        ];
        foreach ($dom->find('div[id=cardMatch]') as $match) {
			
			$onclick = $match->getAttribute('onclick');
			$matchId = '';
			if(preg_match("/goToMatch\((\d+)/", $onclick, $matchesArr)){
				$matchId = $matchesArr[1];
			}
			
			if( !empty($matchId) ){
				$rightTeamImg = $match->find('.imgTeam', 0);
				$leftTeamImg = $match->find('.imgTeam', 1);
				
				$rightTeamName = $match->find('.matchTeam', 0);
				$leftTeamName = $match->find('.matchTeam', 1);
				
				$matchCompt = $match->find('.matchCompt', 0);
				$matchTime = $match->find('.matchTime', 0);
				$textMatch = $match->find('.textMatch', 0);

				$href = $websiteLive2 . "bein/live/" . $matchId . "/2";
				
				$jsonData = [
					'href' => $href,
					'rightTeamName' => $rightTeamName ? trim($rightTeamName->plaintext) : '',
					'leftTeamName' => $leftTeamName ? trim($leftTeamName->plaintext) : '',
					'rightTeamLogo' => $rightTeamImg ? $rightTeamImg->src : '',
					'leftTeamLogo' => $leftTeamImg ? $leftTeamImg->src : '',
					'matchTime' => $matchTime ? trim($matchTime->plaintext) : '',
					'result' => '',
					'liveStatus' => $textMatch ? trim($textMatch->plaintext) : '',
					'league' => $matchCompt ? trim($matchCompt->plaintext) : '',
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
    $dom = str_get_html($html);
	if ($dom) {
		$data = [
			'matches' => []
		];
		foreach ($dom->find('iframe') as $iframe) {
			if ($iframe) {
				$baseSrc = $iframe->getAttribute('src');
                
                // Try to find the server menu in the player page
                $playerHtml = liveCurl($baseSrc);
                $playerDom = str_get_html($playerHtml);
                $menuFound = false;

                if ($playerDom) {
                    $serverLinks = $playerDom->find('.aplr-menu li a');
                    if (!empty($serverLinks)) {
                        $menuFound = true;
                        foreach ($serverLinks as $link) {
                            $serverUrl = $link->href;
                            $serverName = trim($link->plaintext);
                            
                            // Fetch individual server page
                            $serverHtml = liveCurl($serverUrl);
                            $serverDom = str_get_html($serverHtml);
                            if ($serverDom) {
                                $videoIframe = $serverDom->find('iframe', 0);
                                if ($videoIframe) {
                                    $finalUrl = $videoIframe->getAttribute('src');
                                    if (strpos($finalUrl, 'wallplaster') === false) {
                                        $src = $finalUrl;
                                        if (strpos($src, 'https:') !== 0) {
                                            $src = 'https:' . $src;
                                        }
                                        $liveMatchesUrl = 'https://tryq8flix.com/liveMatches.php?match=' . urlencode($view);
                                        $jsonData = [
                                            'live' => $src,
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

                if (!$menuFound) {
                    for ($serv = 1; $serv <= 6; $serv++) {
                        $srcWithIndex = $baseSrc . "index.php?serv=" . $serv;
                        $iframeHtml = liveCurl($srcWithIndex);
                        $iframeDom = str_get_html($iframeHtml);
                        if ($iframeDom) {
                            $foundIframe = $iframeDom->find('iframe', 0);
                            if ($foundIframe) {
                                $finalUrl = $foundIframe->getAttribute('src');
                                // Remove any link with 'wallplaster' in the domain
                                //if (strpos($finalUrl, 'wallplaster') === false) {
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
                               // }
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