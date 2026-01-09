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
	$data = [
		'matches' => []
	];
	
	$queries = [];
	
	// Check if URL follows pattern to support multiple qualities
	// Expected format from searchMatches: .../live/{id}/2
	if (preg_match('/(.*\/live\/)(\d+)\/(\d+)/', $view, $matches)) {
        $baseUrl = $matches[1];
        $matchId = $matches[2];
		
		$queries[] = [
			'url' => $baseUrl . $matchId . '/2',
			'name' => 'High Quality (1080p)'
		];
		$queries[] = [
			'url' => $baseUrl . $matchId . '/1',
			'name' => 'Normal Quality'
		];
	} else {
		$queries[] = [
			'url' => $view,
			'name' => 'Server'
		];
	}
	
	foreach($queries as $q) {
		$html = liveCurl($q['url']);
		$dom = str_get_html($html);
		if ($dom) {
			$iframe = $dom->find('iframe', 0);
			if($iframe){
				$src = $iframe->getAttribute('src');
				if( !empty($src) ){
					if (strpos($src, '//') === 0) {
						$src = 'https:' . $src;
					}
					// Return raw src - app will wrap in videoPlayer.php
					$data['matches'][] = [
						'live' => $src,
						'name' => $q['name'],
						'src' => $q['url']
					];
				}
			}
		}
	}

	$matches = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
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