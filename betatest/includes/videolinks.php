<?php
function extractUptoboxCode($url) {
  $path = parse_url($url, PHP_URL_PATH);
  $parts = explode('/', $path);
  $code = end($parts);
  return $code;
}

$videolink = trim($uptobox);
$streamlink = extractUptoboxCode($videolink) ;
$checkAccount = "https://uptobox.com/api/user/me?token={$uptoboxToken}";
$getLink = "https://uptobox.com/api/link?token={$uptoboxToken}&file_code={$streamlink}";
$string = file_get_contents($checkAccount);
$json_a = json_decode($string, true);
if ( isset($json_a["data"]["premium"]) AND $json_a["data"]["premium"] == 1 ){
	$string = file_get_contents($getLink);
	$json_a = json_decode($string, true);
	if ( !isset($json_a["data"]["dlLink"]) OR empty($json_a["data"]["dlLink"]) ){
		if ( isset($username) AND in_array($username,$usernames) ){
			$string = "";
		}else{
			header("Location: reportBrokenLink.php?catid=".$_GET["catid"]."&postid=" . $_GET["postid"] );
		}
	}else{
		$videolink = $json_a["data"]["dlLink"];
		$downloadlink = $videolink;
		$vlclink = explode("https://" , $videolink);
		$vlclink = $vlclink[1];
	}
}else{
	header("LOCATION: maintenance.php");
}
?>