<?php
function searchShahid($search){
	GLOBAL $website2;
	$html = curlCall("{$website2}/?s={$search}");
	return domTopCinema(str_get_html($html));
}

if( isset($_POST["type"]) && !empty($_POST["type"]) ){
    if( $_POST["type"] == "get" ){
        $user = checkLogin();
		$shows = searchShahid($_POST["search"]);
		echo "<div class='row m-0 w-100' id='content'>";
        outputData2($shows);
		echo '<div class="col-md-12 loadMoreSearchBtn mb-3" style="text-align-last: center;" id="1"><div class="btn btn-secondary w-75" >تابع</div></div><div style="display:none" class="getSearch" id="'.$_POST["search"].'"></div></div>';
		echo "</div>";
    }
}else{
    $msg = "something wrong happened, Please try again.";
    echo $msg;
}
?>