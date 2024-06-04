<?php
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
			$links .= "<div class='col-3 p-1'><a class='btn btn-secondary w-100' style='color:white' href='#' id='{$servers[$i]["id"]}' onclick='sendIdToIframe(\"{$_POST["id"]}/watch/\"); return false;'>Serv-{$y}</a></div>";
			$server = $servers[$i]["i"];
			$mainServer[] = $servers[$i]["title"]; 
			$y++;
		}
	}
	$links .= "</div>";
	if( isset($mainServer) && sizeof($mainServer) > 0){
		$videoTag = "{$links}<iframe id='frame' src='{$mainServer[0]}' style='width:100%;height:300px;margin-top: 30px;' sandbox='allow-same-origin allow-scripts' allowFullScreen></iframe>";
		echo $videoTag;
	}else{
		echo "لا يوجد روابط متاحه للمشاهده حاليا، الرجاء المحاولة لاحقاً";
	}
    
}
?>
