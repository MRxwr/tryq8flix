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
	GLOBAL $website2, $_GET;
	$url = $website2;
	if( isset($_GET["category"]) && !empty($_GET["category"]) ){
		$url .= "/category/{$_GET["category"]}";
	}else{
		$url .= "/recenT";
	}
	$html = curlCall($url);
	return domTopCinema(str_get_html($html));
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
			outputData2($shows); 
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