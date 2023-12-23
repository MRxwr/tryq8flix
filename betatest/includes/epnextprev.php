<?php
//next and prevoius episodes
$sql = "SELECT `title`, `id`
		FROM `posts`
		WHERE
		`catid` LIKE '".$id."'
		ORDER BY `title` ASC";
$result = $dbconnect->query($sql);
$allids = array();
$alltitles = array();	   
while ( $row = $result->fetch_assoc() ){
	$alltitles[] = $row["title"];
	$allids[] = $row["id"];
}

$arraykey = array_search($postid,$allids);
$nextid = $arraykey + 1;
if ( isset ($allids[$nextid]) ){
	$nextepisodetitle = $alltitles[$nextid];
}
$previd = $arraykey - 1;
if ( isset ($allids[$previd]) ){
	$prevepidosetitle = $alltitles[$previd];
}
?>