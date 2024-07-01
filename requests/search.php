<?php
function getWebsite($search){
	/*
	GLOBAL $website, $_GET;
	$collection = ( isset($_GET["collection"]) ) ? "?order={$_GET["collection"]}" : "" ;
	$category = ( isset($_GET["category"]) ) ? "&category={$_GET["category"]}" : "" ;
	if( isset($_GET["collection"]) ){
		$collection = "?order={$_GET["collection"]}";
		if( isset($_GET["category"]) ){
			$category = "&category={$_GET["category"]}";
		}
	}elseif( !isset($_GET["collection"]) && isset($_GET["category"])){
		$collection = "";
		$category = "?category={$_GET["category"]}";
	}else{
		$collection = "";
		$category = "";
	}
	return $website.$collection.$category;
	*/
	GLOBAL $website, $_GET;
	return "{$website}search?s={$search}";
}

function searchShahid($search){
	GLOBAL $website;
	//@$html = file_get_contents("{$website}search?s={$search}");
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://app.scrapingbee.com/api/v1/?api_key=IN9YLTOE0MBVC5BV5GASF63BEE472R7CRTLX4N77FWZBTNZL4L3XNANQ4XMZFDN82Z6IVRQ4BAVH8GR6&url=". urlencode("{$website}search?s={$search}"),
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
	// Create a DOM object
	$dom = str_get_html($html);
	// Check if the DOM object is valid
	if ($dom) {
		$data = [
			'shows' => []
		];
		// Loop through each show
		foreach ($dom->find('.shows-container .show-card') as $show) {
			// Extract background-image URL from style attribute
			$style = $show->style;
			preg_match('/\burl\s*\(\s*[\'"]?(.*?)[\'"]?\s*\)/', $style, $matches);
			$imageUrl = isset($matches[1]) ? $matches[1] : '';

			$jsonData = [
				'href' => $show->href,
				'image' => trim($imageUrl),
				'episode' => $show->find('.ep', 0)->plaintext,
				'category' => $show->find('.categ', 0)->plaintext,
				'title' => $show->find('.title', 0)->plaintext,
				'description' => trim(preg_replace('/\s+/', ' ', $show->find('.description', 0)->plaintext)),
			];

			// Add the JSON data to the array
			$data['shows'][] = $jsonData;
		}

		// Output the JSON array
		$shows = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	} else {
		echo 'Error: Invalid DOM object.';
	}

	$shows = ( isset($shows) && !empty($shows) ) ? json_decode($shows,true) : array("shows" => array());
	return $shows = $shows["shows"];
	// Clean up the DOM object
	$dom->clear();
	unset($dom);
}

if( isset($_POST["type"]) && !empty($_POST["type"]) ){
    if( $_POST["type"] == "get" ){
        $user = checkLogin();
		if( !empty($user["id"]) ){
			
			$shows = searchShahid($_POST["search"]);
			echo "<div class='row m-0 w-100' id='content'>";
			outputData($shows);
			echo '<div class="col-md-12 loadMoreSearchBtn mb-3" style="text-align-last: center;" id="1"><div class="btn btn-secondary w-75" >تابع</div></div><div style="display:none" class="getSearch" id="'.$_POST["search"].'"></div></div>';
			
			//echo "<iframe id='frame' src='".getWebsite($_POST["search"])."' style='width:100%;height:100vh;' sandbox='allow-same-origin allow-scripts' allowFullScreen></iframe>";
		}else{
			$msg = "Please login first.";
			echo $msg;
		}
    }
}else{
    $msg = "something wrong happened, Please try again.";
    echo $msg;
}
?>