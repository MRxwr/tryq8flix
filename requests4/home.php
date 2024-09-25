<?php
function getWebsite(){
	GLOBAL $website4, $_GET;
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
	return $website4.$collection.$category;
}

function searchShahid(){
	GLOBAL $website4, $_GET,$scrappingBeeToken;
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
	$html = scrapePage($website4.$collection.$category);
	$dom = str_get_html($html);
	$data = [
		'shows' => []
	];
	if ($dom) {
		$mainSection = $dom->find('.main-section', 0);
		if ($mainSection) {
			foreach ($mainSection->find('.movieItem') as $movie) {
				$link = $movie->find('a', 0);
				$image = $movie->find('img', 0);
				$title = $movie->find('.BottomTitle', 0);
				$category = $movie->find('.cat_name', 0);
				$episode = $movie->find('.number_episode em', 0);
				$label = $movie->find('.label', 0);

				$movieData = [
					'href' => $link ? $link->href : '',
					'image' => $image ? $image->src : '',
					'title' => $title ? $title->plaintext : '',
					'category' => $category ? $category->plaintext : '',
					'episode' => $episode ? $episode->plaintext : '',
					'label' => $label ? $label->plaintext : '',
				];
				$data['shows'][] = $movieData;
			}
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
		}else{
			echo "<iframe id='frame' src='".getWebsite()."' style='width:100%;height:100vh;' sandbox='allow-same-origin allow-scripts' allowFullScreen></iframe>
			<script>
			$(document).ready(function() {
				$('.changeIframeSrc').on('click', function() {
					var link = $(this).attr('id');
					$('#frame').attr('src', link);
				});
			});
			</script>";
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