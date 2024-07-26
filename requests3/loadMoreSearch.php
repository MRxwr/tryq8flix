<?php
function searchShahid($search,$more){
	GLOBAL $website3;
	$search = str_replace(' ','+',$search);
    $url = "{$website3}/search/{$search}/page/{$more}/";
    $html = scrapeWecima($url);
	$html = json_decode($html, true);
	return $html["shows"];
}

if( isset($_POST["type"]) && !empty($_POST["type"]) ){
    if( $_POST["type"] == "get" ){
        $user = checkLogin();
		$shows = searchShahid($_GET["search"],$_POST["more"]);
        outputData2($shows);
		echo '<div class="col-md-12 loadMoreSearchBtn mb-3" style="text-align-last: center;" id="'.$_POST["more"].'"><div class="btn btn-secondary w-75" >تابع</div></div><div style="display:none" class="getSearch" id="'.$_GET["search"].'"></div>';
    }
}else{
    $msg = "something wrong happened, Please try again.";
    echo $msg;
}
?>