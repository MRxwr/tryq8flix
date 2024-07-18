<?php
function searchShahid($more){
	GLOBAL $website2, $_GET;
	$collection = ( isset($_GET["collection"]) ) ? "order={$_GET["collection"]}" : "" ;
	$category = ( isset($_GET["category"]) ) ? "&category={$_GET["category"]}" : "" ;
	$html = file_get_contents("{$website2}/page/{$more}&{$collection}{$category}");
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
		$shows = searchShahid($_POST["more"]);
        $output = "<div class='row m-0 w-100'>";
        if( is_array($shows) && !empty($shows) ){
            for ( $i = 0 ; $i < sizeof($shows) ; $i++){
				$output .= "
					<div class='col-xl-3 col-lg-4 col-md-6 col-sm-12 p-3'>
					<div class='card w-100'>
						<div class='card-body'>
						<img src='data:image/jpeg;base64,".base64_encode(file_get_contents($shows[$i]["image"]))."' style='width:100%;height:300px'>
						<div style='height:225px; overflow:auto' >
							<h2 class='card-title categoryTitle{$i}' ><b>{$shows[$i]["category"]}</b></h2>
							<h3 class='card-title postTitle{$i}'>{$shows[$i]["title"]}</h3>
							<p class='card-text'>
								<b>Title:</b>{$shows[$i]["episode"]}<br>
								<b>Details:</b>{$shows[$i]["description"]}</p>
						</div>
							<div class='row w-100 p-0 m-0'>
								<div class='col-6 p-1'>
									<div data-bs-toggle='modal' data-bs-target='#playVideo' class='btn btn-danger w-100 playVideo' id='".str_replace("film","watch",str_replace("post","watch",str_replace("episode","watch",$shows[$i]["href"])))."'><i class='bi bi-play-fill'></i></div>
								</div>
								<div class='col-6 p-1'>
									<div data-bs-toggle='modal' data-bs-target='#threeDots' class='btn btn-warning w-100 threeDots' id='{$shows[$i]["href"]}'><i class='bi bi-three-dots'></i></div>
								</div>
							</div>
						</div>
					</div>
					</div>
					";
			}
            echo "</div>".$output;
			echo '<div class="col-md-12 loadMoreBtn mb-3" style="text-align-last: center;" id="'.$_POST["more"].'"><div class="btn btn-primary w-75" >تابع</div></div><div style="display:none" class="getCollection" id="'.$collection.$category.'"></div>';
        }else{
            $msg = "<h1 class='text-center mt-5'>No result.<h1>";
            echo $msg;
        }
    }
}else{
    $msg = "something wrong happened, Please try again.";
    echo $msg;
}
?>