<?php
function searchShahid($search,$more){
	GLOBAL $website2;
	$search = str_replace(' ','+',$search);
	$html = curlCall("{$website2}/page/{$more}/?s={$search}");
	return domTopCinema(str_get_html($html));
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