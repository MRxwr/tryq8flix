<?php
function getWebsite($search){
	GLOBAL $website5, $_GET;
	return "{$website5}?s={$search}";
}

function searchShahid($search){
	GLOBAL $website5;
	$search = urlencode($search);
	$html = scrapePage("{$website5}?s={$search}");
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

	$shows = ( isset($shows) && !empty($shows) ) ? json_decode($shows,true) : array("shows" => array());
	return $shows = $shows["shows"];
	$dom->clear();
	unset($dom);
}

if( isset($_POST["type"]) && !empty($_POST["type"]) ){
	if( $_POST["type"] == "get" ){
		$user = checkLogin();
		if( !empty($user["id"]) ){
			$shows = searchShahid($_POST["search"]);
			echo "<div class='row m-0 w-100' id='content'>";
			outputData5($shows);
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