<?php
function searchShahid(){
	GLOBAL $website2, $_GET;
	$collection = ( isset($_GET["collection"]) ) ? "?order={$_GET["collection"]}" : "" ;
	$category = ( isset($_GET["category"]) ) ? "&category={$_GET["category"]}" : "" ;
	$html = file_get_contents("{$website2}{$collection}{$category}");
	var_dump($html);
	//var_dump($html);
	// Create a DOM object
	$dom = str_get_html($html);
	// Check if the DOM object is valid
	if ($dom) {
		$data = [
			'shows' => []
		];
		// Loop through each show
		foreach ($dom->find('ul#pm-grid li.col-xs-6.col-sm-4.col-md-3') as $show) {
			// Find the anchor tag and extract the href attribute for the URL
			$anchor = $show->find('a', 0);
			$href = $anchor ? $anchor->href : '';

			// Find the image tag and extract the src attribute for the image URL
			$image = $show->find('img', 0);
			$imageUrl = $image ? $image->src : '';

			// Extract additional information like episode, category, title, and description if available
			// This will depend on your actual HTML structure and where these pieces of information are located
			// For this example, let's assume they are stored in data attributes or within specific elements
			// You may need to adjust the selectors based on your actual HTML structure
			$episode = $anchor ? trim($anchor->getAttribute('data-episode')) : ''; // Adjust selector if needed
			$category = $anchor ? trim($anchor->getAttribute('data-category')) : ''; // Adjust selector if needed
			$title = $anchor ? trim($anchor->getAttribute('title')) : ''; // Using the title attribute of the anchor
			$description = $show->find('.description', 0) ? trim($show->find('.description', 0)->plaintext) : ''; // Adjust selector if needed

			// Add the extracted information to the data array
			$data['shows'][] = [
				'href' => $href,
				'image' => $imageUrl,
				'episode' => $episode,
				'category' => $category,
				'title' => $title,
				'description' => $description,
			];
		}

		// Output the JSON array
		$shows = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	} else {
		echo 'Error: Invalid DOM object.';
	}
	var_dump($shows);
	$shows = ( isset($shows) && !empty($shows) ) ? json_decode($shows,true) : array() ;
	
	return $shows = $shows["shows"];
	// Clean up the DOM object
	$dom->clear();
	unset($dom);
}

if( isset($_POST["type"]) && !empty($_POST["type"]) ){
    if( $_POST["type"] == "get" ){
		$collection = ( isset($_GET["collection"]) ) ? "{$_GET["collection"]}" : "" ;
		$category = ( isset($_GET["category"]) ) ? "&category={$_GET["category"]}" : "" ;
        $user = checkLogin();
		$shows = searchShahid();
        outputData($shows); 
		echo "<div class='col-md-12 loadMoreBtn mb-3' style='text-align-last: center;' id='1'><div class='btn btn-primary w-75' >تابع</div></div><div style='display:none' class='getCollection' id='{$collection}{$category}'></div>";
    }
}else{
    $msg = "something wrong happened, Please try again.";
    echo $msg;
}
?>