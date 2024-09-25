<?php
function searchShahid($search,$more){
	GLOBAL $website;
	$search = urlencode("$search");
	$html = scrapePage("{$website}search?s={$search}&page={$more}");
	$dom = str_get_html($html);
	if ($dom) {
		$data = [
			'shows' => []
		];
		foreach ($dom->find('.shows-container .show-card') as $show) {
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
		$shows = searchShahid($_GET["search"],$_POST["more"]);
        outputData($shows);
		echo '<div class="col-md-12 loadMoreSearchBtn mb-3" style="text-align-last: center;" id="'.$_POST["more"].'"><div class="btn btn-secondary w-75" >تابع</div></div><div style="display:none" class="getSearch" id="'.$_GET["search"].'"></div>';
    }
}else{
    $msg = "something wrong happened, Please try again.";
    echo $msg;
}
?>