<?php
function searchShahid($more){
	GLOBAL $website2;
	$html = file_get_contents("{$website2}/recent/page/{$more}");
	$dom = str_get_html($html);
	$data = [
		'shows' => []
	];
	if ($dom) {
		foreach ($dom->find('.Block--Item') as $show) {
			$link = $show->find('a', 0);
			$image = $show->find('img', 0);
			$genre = $show->find('.Genres li', 0);
			$title = $show->find('h3', 0);
			$jsonData = [
				'href' => $link->href,
				'image' => $image->getAttribute('data-src'),
				'episode' => '', // Not present in the provided HTML
				'category' => $genre ? $genre->plaintext : '',
				'title' => $title ? $title->plaintext : '',
				'description' => '', // Not present in the provided HTML
			];
			$data['shows'][] = $jsonData;
		}
		$shows = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	} else {
		echo 'Error: Invalid DOM object.';
		$shows = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	}

	$shows = ( isset($shows) && !empty($shows) ) ? json_decode($shows,true) : array() ;
	return $shows = $shows["shows"];
	$dom->clear();
	unset($dom);
}

if( isset($_POST["type"]) && !empty($_POST["type"]) ){
    if( $_POST["type"] == "get" ){
		$collection = ( isset($_GET["collection"]) ) ? "{$_GET["collection"]}" : "" ;
		$category = ( isset($_GET["category"]) ) ? "&category={$_GET["category"]}" : "" ;
        $user = checkLogin();
		$shows = searchShahid($_POST["more"]);
        $output .= outputData2($shows);
		echo $output;
		echo '<div class="col-md-12 loadMoreBtn mb-3" style="text-align-last: center;" id="'.$_POST["more"].'"><div class="btn btn-primary w-75" >تابع</div></div><div style="display:none" class="getCollection" id="'.$collection.$category.'"></div>';
        }else{
            $msg = "<h1 class='text-center mt-5'>No result.<h1>";
            echo $msg;
        }
}else{
	$msg = "something wrong happened, Please try again.";
	echo $msg;
}
?>