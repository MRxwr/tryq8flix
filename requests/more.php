<?php
if( isset($_POST["id"]) && !empty($_POST["id"]) ){
    //$html = file_get_contents($_POST["id"]);
    $curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://app.scrapingbee.com/api/v1/?api_key={$scrappingBeeToken}&url=". urlencode("{$_POST["id"]}"),
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
    // Load HTML
    $htmlDom = str_get_html($html);
    $seasonsData = [];
    foreach ($htmlDom->find('div.items a.epss') as $linkNode) {
        $link = $linkNode->href;
        $title = trim($linkNode->find('h3', 0)->plaintext); // Remove extra spaces from the title
        
        // Check if the link contains "season"
        if (stripos($link, 'season') !== false) {
            $seasonsData[] = [
                'link' => $link,
                'title' => $title,
            ];
        }
    }
    // Output the extracted data
    //echo json_encode($seasonsData, JSON_UNESCAPED_UNICODE);
    
    
    //$html = file_get_contents($_POST["id"]);
    // Load HTML
    //$htmlDom = str_get_html($html);
    $episodesData = [];
    foreach ($htmlDom->find('div.items a.epss') as $linkNode) {
        $link = $linkNode->href;
        $title = trim($linkNode->find('h3', 0)->plaintext); // Remove extra spaces from the title
        
        // Check if the link does not contain "season"
        if (stripos($link, 'season') === false) {
            $episodesData[] = [
                'link' => $link,
                'title' => $title,
            ];
			
        }
    }
    // Output the extracted data
    //echo json_encode($episodesData, JSON_UNESCAPED_UNICODE);
	if (strpos(strtolower($_POST["id"]), 'season') === false){
		$episodesData = array_reverse($episodesData);
		$seasonsData = array_reverse($seasonsData);
	}
	
    if (strpos(strtolower($_POST["id"]), 'season') === false){
		for( $i = 0; $i < sizeof($seasonsData); $i++){
			echo "<div data-bs-toggle='modal' data-bs-target='#threeDots2'  class='btn btn-warning m-1 threeDots2' id='".str_replace("film","watch",str_replace("post","watch",str_replace("episode","watch",$seasonsData[$i]["link"])))."'>{$seasonsData[$i]["title"]}</div>";
		}
		echo "<hr>";
	}
	
	for( $i = 0; $i < sizeof($episodesData); $i++){
		echo "<div data-bs-toggle='modal' data-bs-target='#playVideo'  class='btn btn-danger m-1 playVideo' id='".str_replace("film","watch",str_replace("post","watch",str_replace("episode","watch",$episodesData[$i]["link"])))."'>{$episodesData[$i]["title"]}</div>";
	}
	
	if( sizeof($episodesData) < 1 &&  sizeof($seasonsData) < 1 ){
		echo "<div>لا يوجد المزيد من الحلقات ... شاهد الفيديو مباشرة</div>";
	}
    
}

?>