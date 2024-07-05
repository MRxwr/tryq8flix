<?php
function searchShahid($search){
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://web5.topcinema.world/?type=all&s=" . urlencode($search),
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
	
	$dom = str_get_html($html);
	if ($dom) {
		$data = [
			'shows' => []
		];
		foreach ($dom->find('.Small--Box') as $show) {
			$title = $show->find('.recent--block', 0)->title;
			$url = $show->find('.recent--block', 0)->href;
			$poster = $show->find('img', 0)->getAttribute('data-src');
			$genre = $show->find('.liList li', 0)->plaintext;
			@$resolution = $show->find('.liList li', 1)->plaintext;
			@$imdbRating = $show->find('.liList li', 2)->plaintext;
			$jsonData = [
				'href' => $url,
				'image' => trim($poster),
				'resolution' => $resolution,
				'category' => $genre,
				'title' => $title,
				'imdbRating' => $imdbRating,
				'description' => "",
			];
			$data['shows'][] = $jsonData;
		}
		$shows = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	} else {
		echo 'Error: Invalid DOM object.';
	}

	$shows = ( isset($shows) && !empty($shows) ) ? json_decode($shows,true) : array() ;
	return $shows = $shows["shows"];
	$dom->clear();
	unset($dom);
}

if( isset($_POST["type"]) && !empty($_POST["type"]) ){
    if( $_POST["type"] == "get" ){
        $user = checkLogin();
		$shows = searchShahid($_POST["search"]);
        outputData2($shows);
    }
}else{
    $msg = "something wrong happened, Please try again.";
    echo $msg;
}
?>