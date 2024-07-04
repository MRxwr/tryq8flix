<?php
function getWebsite(){
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
}

function searchShahid(){
	GLOBAL $website, $_GET;
	if( isset($_GET["collection"]) && $_GET["collection"] == "last_eps" ){
		$type = "مسلسلات-اجنبي-1/?key=episodes";
	}elseif( isset($_GET["collection"]) && $_GET["collection"] == "last_films" ){
		$type = "movies/";
	}elseif( isset($_GET["collection"]) && $_GET["collection"] == "last_eps&category=مسلسلات-انمي" ){
		$type = "مسلسلات-انمي/?key=episodes";
	}else{
		$type = "";
	}
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
	
	//var_dump($website.$collection.$category); die();
	
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://web5.topcinema.world/{$type}",
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
			'shows' => []
		];
		// Loop through each show
		foreach ($dom->find('.Small--Box') as $show) {
			// Extract background-image URL from style attribute
			$title = $show->find('.recent--block', 0)->title;
			$url = $show->find('.recent--block', 0)->href;
			$poster = $show->find('img', 0)->getAttribute('data-src');
			$genre = $show->find('.liList li', 0)->plaintext;
			$resolution = $show->find('.liList li', 1)->plaintext;
			$jsonData = [
				'href' => $url,
				'image' => trim($poster),
				'episode' => $resolution,
				'category' => $genre,
				'title' => $title,
				'description' => "",
			];

			// Add the JSON data to the array
			$data['shows'][] = $jsonData;
		}

		// Output the JSON array
		$shows = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	} else {
		echo 'Error: Invalid DOM object.';
	}

	$shows = ( isset($shows) && !empty($shows) ) ? json_decode($shows,true) : array() ;
	return $shows = $shows["shows"];
	// Clean up the DOM object
	$dom->clear();
	unset($dom);
}

if( isset($_POST["type"]) && !empty($_POST["type"]) ){ 
	$user = checkLogin();
	if ( !empty($user["id"]) ){
		if( $_POST["type"] == "get" ){
			$collection = ( isset($_GET["collection"]) ) ? "{$_GET["collection"]}" : "" ;
			$category = ( isset($_GET["category"]) ) ? "&category={$_GET["category"]}" : "" ;
			$user = checkLogin();
			$shows = searchShahid();
			echo "<div class='row m-0 w-100' id='content'>";
			outputData($shows); 
			echo "<div class='col-md-12 loadMoreBtn mb-3' style='text-align-last: center;' id='1'><div class='btn btn-secondary w-75' >تابع</div></div><div style='display:none' class='getCollection' id='{$collection}{$category}'></div>";
			echo "</div>";
		}
		
	}else{
		$msg = "Please login first.";
		echo $msg;
	}
}else{
    $msg = "something wrong happened, Please try again.";
    echo $msg;
}
?>