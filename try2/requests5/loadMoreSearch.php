<?php
function searchShahid($search,$more){
	GLOBAL $website5;
	$search = urlencode($search);
	$url = "{$website5}?s={$search}&page={$more}";
	$html = scrapePage($url);
	$dom = str_get_html($html);
	if ($dom) {
		$data = [
			'shows' => []
		];
		foreach ($dom->find('.Small--Box') as $show) {
			$anchor = $show->find('a.recent--block', 0);
			$href = $anchor ? $anchor->href : '';
			$imageTag = $anchor ? $anchor->find('.Poster img', 0) : null;
			$image = $imageTag ? $imageTag->getAttribute('data-src') : '';
			$episodeEm = $anchor ? $anchor->find('.number em', 0) : null;
			$episode = $episodeEm ? $episodeEm->plaintext : '';
			$categoryLi = $anchor ? $anchor->find('ul.liList li.category', 0) : null;
			$category = $categoryLi ? $categoryLi->plaintext : '';
			$titleTag = $anchor ? $anchor->find('inner--title h2', 0) : null;
			$title = $titleTag ? $titleTag->plaintext : '';
			$descTag = $anchor ? $anchor->find('inner--title p', 0) : null;
			$description = $descTag ? $descTag->plaintext : '';

			$jsonData = [
				'href' => trim($href),
				'image' => trim($image),
				'episode' => trim($episode),
				'category' => trim($category),
				'title' => trim($title),
				'description' => trim($description),
			];
			$data['shows'][] = $jsonData;
		}
		$shows = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	} else {
		echo 'Error: Invalid DOM object.';
	}
	$shows = ( isset($shows) && !empty($shows) ) ? json_decode($shows,true) : array();
	return $shows = $shows["shows"];
	$dom->clear();
	unset($dom);
}

if( isset($_POST["type"]) && !empty($_POST["type"]) ){
	if( $_POST["type"] == "get" ){
		$user = checkLogin();
		$shows = searchShahid($_GET["search"],$_POST["more"]);
		outputData5($shows);
		echo '<div class="col-md-12 loadMoreSearchBtn mb-3" style="text-align-last: center;" id="'.$_POST["more"].'"><div class="btn btn-secondary w-75" >تابع</div></div><div style="display:none" class="getSearch" id="'.$_GET["search"].'"></div>';
	}
}else{
	$msg = "something wrong happened, Please try again.";
	echo $msg;
}
?>