<?php
function open1stVideo($id) {
	$curl = curl_init();
	curl_setopt_array($curl, array(
	CURLOPT_URL => 'https://web5.topcinema.world/wp-content/themes/movies2023/Ajaxat/Single/Server.php',
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => '',
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 0,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => 'POST',
	CURLOPT_POSTFIELDS => array('id' => '93065','i' => '1'),
	CURLOPT_HTTPHEADER => array(
		'X-Requested-With: XMLHttpRequest',
		'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1',
		'Cookie: prefetchAd_6969766=true; prefetchAd_6969540=true',
		'Dnt: 1',
		'Content-Type: application/x-www-form-urlencoded; charset=UTF-8'
	),
	));
	$response = curl_exec($curl);
	curl_close($curl);
	return $response;
}

function searchServers($id){
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => "{$id}/watch/",
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
	
	//$html = file_get_contents(getWebsite());
	// Create a DOM object
	$dom = str_get_html($html);
	// Check if the DOM object is valid
	if ($dom) {
		$data = [
			'servers' => []
		];
		// Loop through each show
		foreach ($dom->find('.server--item') as $servers) {
			// Extract background-image URL from style attribute
			$id = $servers->getAttribute('data-id');
            $server = $servers->getAttribute('data-server');
            $title = $servers->find('.playIC + span', 0)->plaintext;
			$jsonData = [
				'title' => $title,
				'id' => $id,
				'i' => $server,
			];

			// Add the JSON data to the array
			$data['servers'][] = $jsonData;
		}

		// Output the JSON array
		$shows = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	} else {
		echo 'Error: Invalid DOM object.';
	}

	$shows = ( isset($shows) && !empty($shows) ) ? json_decode($shows,true) : array() ;
	return $shows = $shows["servers"];
	// Clean up the DOM object
	$dom->clear();
	unset($dom);
}
 
if( isset($_POST["id"]) && !empty($_POST["id"]) ){
    $servers = searchServers($_POST["id"]);
	$links = "<div class='row m-0' >";
	$counter = 0;
	$notWanted = ["vembed.net","uqload.co","uqload.com","iioo.vadbam.net","emma.viidshar.com","uptostream.com", "embedv.net", "fdewsdc.sbs","ok.ru", "doodstream.com"];
	$y = 1;
	for( $i = 0; $i < sizeof($servers); $i++ ){
		$domain = strtolower($servers[$i]["title"]);
		if( !in_array(strtolower($domain),$notWanted) && isset($servers[$i]["id"]) ){
			$links .= "<div class='col-3 p-1'><a class='btn btn-secondary w-100' style='color:white' href='#' onclick='sendIdToIframe({$servers[$i]["id"]}&{$servers[$i]["i"]});'>Serv-{$y}</a></div>";
			$server = $servers[$i]["i"];
			$mainServer[] = $servers[$i]["title"]; 
			$y++;
		}
	}
	$links .= "</div>";
	if( isset($mainServer) && sizeof($mainServer) > 0){
		$video = open1stVideo($mainServer[0]);
		$videoTag = "{$links}<iframe id='frame' src='{$video}' style='width:100%;height:300px;margin-top: 30px;' sandbox='allow-same-origin allow-scripts' allowFullScreen></iframe>";
		echo $videoTag;
	}else{
		echo "لا يوجد روابط متاحه للمشاهده حاليا، الرجاء المحاولة لاحقاً";
	}
    
}
?>
