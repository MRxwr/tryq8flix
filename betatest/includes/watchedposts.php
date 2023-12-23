<?php
//getting user id
$userIds = selectDB("users","`username` = '{$username}'");
if ( selectDB("history","`postId` = '{$_GET["postid"]}' AND `userId` = '{$userIds[0]["id"]}'") ){
	date_default_timezone_set("Asia/Kuwait");
	$nowDate = date("Y-m-d H:i:s");
	updateDB("history",array("date"=>$nowDate),"`postId` = '{$_GET["postid"]}' AND `userId` = '{$userIds[0]["id"]}'");
}else{
	$insertData = array(
		"userId" => $userIds[0]["id"],
		"postId" => $_GET["postid"]
	);
	insertDB("history",$insertData);
}

$sql = "SELECT `postId`
		FROM `history`
		WHERE
		`userId` = '{$userIds[0]["id"]}'
		";
$result = $dbconnect->query($sql);
while ( $row = $result->fetch_assoc() ){
	$watchedposts = $row["postId"];
}
?>