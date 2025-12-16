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
        foreach ($dom->find('.albaflex .match-container') as $match) {
            $matchLink = $match->find('a', 0);
			if( !empty($matchLink) ){
				@$rightTeamName = $match->find('.right-team .team-name', 0)->plaintext;
				@$leftTeamName = $match->find('.left-team .team-name', 0)->plaintext;
				@$rightTeamLogo = $match->find('.right-team .team-logo img', 0)->getAttribute('data-src');
				@$leftTeamLogo = $match->find('.left-team .team-logo img', 0)->getAttribute('data-src');
				@$matchTime = $match->find('.match-center .match-time', 0)->plaintext;
				@$matchDate = $match->find('.match-center .date', 0)->plaintext;
				@$matchResult = $match->find('.match-center .result', 0)->plaintext;
				@$leagueInfo = $match->find('.match-info ul li', 2)->plaintext; // Assuming it's the third <li>
				$jsonData = [
					'href' => isset($matchLink->href) ? $matchLink->href : '',
					'rightTeamName' => trim($rightTeamName),
					'leftTeamName' => trim($leftTeamName),
					'rightTeamLogo' => $rightTeamLogo,
					'leftTeamLogo' => $leftTeamLogo,
					'matchTime' => $matchTime,
					'result' => $matchResult,
					'liveStatus' => $matchDate,
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