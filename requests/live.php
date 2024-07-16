<?php 
function searchMatches() {
	GLOBAL $websiteLive;
	/*
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => "{$websiteLive}",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
    ));
    $html = curl_exec($curl);
    curl_close($curl);
	*/
	$html = scrapePage("{$websiteLive}");
    $dom = str_get_html($html);
    if ($dom) {
        $data = [
            'matches' => []
        ];
        foreach ($dom->find('.albaflex .match-container') as $match) {
            // Assuming the match-link has both href and title (title as match name here)
            $matchLink = $match->find('a', 0);
			if( !empty($matchLink) ){
				$rightTeamName = $match->find('.right-team .team-name', 0)->plaintext;
				$leftTeamName = $match->find('.left-team .team-name', 0)->plaintext;
				$rightTeamLogo = $match->find('.right-team .team-logo img', 0)->getAttribute('data-src');
				$leftTeamLogo = $match->find('.left-team .team-logo img', 0)->getAttribute('data-src');
				@$matchTime = $match->find('.match-center #match-time', 0)->plaintext;
				$matchDate = $match->find('.match-center .date', 0)->plaintext;
				@$matchResult = $match->find('.match-center .result', 0)->plaintext;
				$leagueInfo = $match->find('.match-info ul li', 2)->plaintext; // Assuming it's the third <li>

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
	GLOBAL $websiteLive;
	/*
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => "{$view}",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
    ));
    $html = curl_exec($curl);
    curl_close($curl);
	*/
	$html = scrapePage("{$view}");
	var_dump($view);
    $dom = str_get_html($html);
    if ($dom) {
		$data = [
			'matches' => []
		];
		foreach ($dom->find('.post-content-wrap .post-content') as $match) {
			// Find the first iframe within the match
			$matchLink = $match->find('iframe', 0);
			if ($matchLink) { // Make sure the iframe was found
				$jsonData = [
					// Correctly access the src attribute
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

$user = checkLogin();

if( !empty($user["id"]) ){
	if( isset($_GET["view"]) ){
		$matches = liveMatch($_GET["view"]);
		@$output = "<iframe id='frame' src='{$_GET["view"]}' style='width:100%;height:100vh;margin-top: 30px;' sandbox='allow-same-origin allow-scripts' allowFullScreen></iframe>";
	}else{
		$matches = searchMatches();
		$output = "<div class='row p-0 m-3'>";
		for( $i = 0; $i < sizeof($matches); $i++ ){
			if( !empty($matches[$i]["href"]) ){
				$inputTime = explode("+",$matches[$i]["matchTime"]);
				$formattedTime = date("g:i A", strtotime( str_replace("T"," ",$inputTime[0])));
				$output .= "
				<div class='col-sm-12 mb-3'>
					<a class='liveMatch' id='{$matches[$i]["href"]}' style='text-decoration: none;color: black;'>
					<div class='row p-3' style='background-color:#a28c5a;border-radius: 10px;box-shadow: 0px 0px 3px 0px #3b3b3b;'>
						<div class='col-4 text-center' style='align-self: center;'>{$matches[$i]["rightTeamName"]}</div>
						<div class='col-4 text-center' style='align-self: center;'></div>
						<div class='col-4 text-center' style='align-self: center;'>{$matches[$i]["leftTeamName"]}</div>
						<div class='col-4 text-center' style='align-self: center;'><img src='{$matches[$i]["rightTeamLogo"]}' style='width:70px;height:70px'></div>
						<div class='col-4 text-center' style='align-self: center;'>{$matches[$i]["date"]}</div>
						<div class='col-4 text-center' style='align-self: center;'><img src='{$matches[$i]["leftTeamLogo"]}' style='width:70px;height:70px'></div>
						<div class='col-4 text-center' style='align-self: center;'>{$matches[$i]["league"]}</div>
						<div class='col-4 text-center' style='align-self: center;'>{$matches[$i]["result"]}</div>
						<div class='col-4 text-center' style='align-self: center;'>{$matches[$i]["matchTime"]}</div>
					</div>
					</a>
				</div>
							";
			}
		}
		$output .= "</div>";
	}
	
}else{
	$output = "something wrong happened, Please try again.";
}

echo $output;

?>