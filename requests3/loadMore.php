<?php
function searchShahid($more){
	GLOBAL $website3, $_GET;
	$url = $website3;
	if( isset($_GET["category"]) && !empty($_GET["category"]) ){
		$category = explode("?", $_GET["category"]);
		$key = ( isset($category[1]) && !empty($category[1]) ) ? "?{$category[1]}" : "" ;
		$category = ( isset($category[0]) && !empty($category[0]) ) ? $category[0] : $$category;
		$url .= "/category/{$category}/page/{$more}/{$key}";
	}else{
		$url .= "/page/{$more}";
	}
	$html = scrapeWecima($url);
	$html = json_decode($html, true);
	return $html["shows"];
}

if( isset($_POST["type"]) && !empty($_POST["type"]) ){
    if( $_POST["type"] == "get" ){
		$collection = ( isset($_GET["collection"]) ) ? "{$_GET["collection"]}" : "" ;
		$category = ( isset($_GET["category"]) ) ? "&category={$_GET["category"]}" : "" ;
        $user = checkLogin();
		$shows = searchShahid($_POST["more"]);
        $output = outputData2($shows);
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