<?php
require('includes/config.php');
require('includes/checksouthead.php');
$postIds = array();
if ( isset($username) AND in_array($username,$usernames) ){
	$sql = "SELECT * FROM `postlinks`";
	$result = $dbconnect->query($sql);
	while ( $row = $result->fetch_assoc() ){
		$explode = explode("uptobox.com/",$row["uptobox"]);
		if ( isset($explode[1]) AND !empty($explode[1]) ){
			$link = file_get_contents('https://uptobox.com/api/link/info?fileCodes='.$explode[1]);
			$link = json_decode($link,true);
			if ( isset($link["data"]["list"][0]["error"]["code"]) AND $link["data"]["list"][0]["error"]["code"] == "28" ){
				$postIds[] = $row["id"];
				if ( sizeof($postIds) >= 100 ){
					goto jump;
				}
			}
		}
	}
}else{
	echo "must be an admin to access this page.";
}

jump:
for ( $i = 0 ; $i < sizeof($postIds) ; $i++ ){
	$sql = "INSERT INTO `deadlinks`
			(`postid`)
			VALUES
			('".$postIds[$i]."')
			";
	$result = $dbconnect->query($sql);
}
?>