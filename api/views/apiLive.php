<?php
function searchMatches() {
	GLOBAL $websiteLive;
	$html = curlCall("{$websiteLive}");
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
				@$matchTime = $match->find('.match-center #match-time', 0)->plaintext;
				@$matchDate = $match->find('.match-center .date', 0)->plaintext;
				@$matchResult = $match->find('.match-center .result', 0)->plaintext;
				@$leagueInfo = $match->find('.match-info ul li', 2)->plaintext; // Assuming it's the third <li>
				$jsonData = [
					'href' => isset($matchLink->href) ? $matchLink->href : '',
					'matchName' => isset($matchLink->title) ? $matchLink->title : '',
					'rightTeamName' => trim($rightTeamName),
					'leftTeamName' => trim($leftTeamName),
					'rightTeamLogo' => $rightTeamLogo,
					'leftTeamLogo' => $leftTeamLogo,
					'matchTime' => $matchTime,
					'result' => $matchResult,
					'date' => $matchDate,
					'league' => trim($leagueInfo),
				];
				$data['matches'][] = $jsonData;
			}else{
				$jsonData = [
					'href' => '',
					'matchName' => '',
					'rightTeamName' => '',
					'leftTeamName' => '',
					'rightTeamLogo' => '',
					'leftTeamLogo' => '',
					'matchTime' => '',
					'result' => '',
					'date' => '',
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
	$html = curlCall("{$view}");
    $dom = str_get_html($html);
    if ($dom) {
		$data = [
			'matches' => []
		];
		foreach ($dom->find('.post-content-wrap .post-content') as $match) {
			$matchLink = $match->find('iframe', 0);
			if ($matchLink) { 
				$jsonData = [
					'src' => $matchLink->getAttribute('src'),
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

if( isset($_GET['live']) ){
    $matches = liveMatch($_GET['live']);
    echo dataOutput($matches);
}elseif( isset($_GET['matchId']) ){
    $matches = searchMatches();
    echo dataOutput($matches);
}else{
    echo dataError('Invalid request.');
}

?>