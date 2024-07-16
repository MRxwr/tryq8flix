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
	GLOBAL $website, $_GET,$scrappingBeeToken;
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
	$html = scrapePage($website.$collection.$category);
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

	$shows = ( isset($shows) && !empty($shows) ) ? json_decode($shows,true) : array("shows" => array()); ;
	return $shows = $shows["shows"];
	$dom->clear();
	unset($dom);
}

if( isset($_POST["type"]) && !empty($_POST["type"]) ){ 
	$user = checkLogin();
	if ( !empty($user["id"]) ){
		/*echo "<iframe id='frame' src='".getWebsite()."' style='width:100%;height:100vh;' sandbox='allow-same-origin allow-scripts' allowFullScreen></iframe>
		<script>
		$(document).ready(function() {
			$('.changeIframeSrc').on('click', function() {
				var link = $(this).attr('id');
				$('#frame').attr('src', link);
			});
		});
		</script>";
		*/
		if( $_POST["type"] == "get" ){
			$collection = ( isset($_GET["collection"]) ) ? "{$_GET["collection"]}" : "" ;
			$category = ( isset($_GET["category"]) ) ? "&category={$_GET["category"]}" : "" ;
			$user = checkLogin();
			$shows = searchShahid();
			echo "<div class='row m-0 w-100' id='content'>";
			if( !empty($shows) ){
				outputData($shows);
				echo "<div class='col-md-12 loadMoreBtn mb-3' style='text-align-last: center;' id='1'><div class='btn btn-secondary w-75' >تابع</div></div><div style='display:none' class='getCollection' id='{$collection}{$category}'></div>";
			}
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