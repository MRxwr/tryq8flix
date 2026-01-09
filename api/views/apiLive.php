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
                    // Try both .aplr-menu and .aplr-link class structures
                    $serverLinks = $playerDom->find('.aplr-menu li a');
                    if (empty($serverLinks)) {
                        $serverLinks = $playerDom->find('li a.aplr-link');
                    }
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