<?php 
$servername = "localhost";
$username = "u905492195_mrnsr";
$password = "N@b$90949089";
$dbname = "u905492195_tryq8"; 

$dbconnect = new MySQLi($servername,$username,$password,$dbname);

if ( $dbconnect->connect_error )
{
	die("Connection Failed: " .$dbconnect->connect_error );
}
include ("../includes/checksouthead.php");
date_default_timezone_set('Asia/Kuwait');

if ( isset($_POST["bringData"]) ){
	$sql = "SELECT
			*
			FROM
			`category`
			WHERE
			`id` LIKE '".$_POST["bringData"]."'
			";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	
	$trailer = str_replace("watch?v=","embed/",str_replace("autoplay=1","",$row["trailer"]));
	$trailer = explode("?", $trailer);
	
	
	$data = $row["title"] . "^" . $row["rating"] . "^" . $row["imdbrating"] . "^" . $row["duration"] . "^" . $row["genre"] . "^" . $row["releasedate"] . "^" . $row["language"] . "^" . $row["country"] . "^" . str_replace("?singlequtation?" ,"'",$row["channel"]) . "^" . $row["poster"] . "^" . str_replace("?singlequtation?","'",$row["description"]) . "^" . $trailer[0] . "^" . $row["id"] . "^" . $row["notes"] . "^" . $row["type"] . "^" . $row["header"];
	
	$sql = "SELECT
			*
			FROM
			`favourites`
			WHERE
			`categoryId` LIKE '".$_POST["bringData"]."'
			AND
			`userId` LIKE '".$userID."'
			";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	if ( $result->num_rows > 0 ){
		$fovo = 1;
	}else{
		$fovo = 0;
	}
	
	echo $data . "^" . $fovo;
}

if ( isset($_POST["bringPostTitle"]) ){
	$sql = "SELECT
			`title`, `category`
			FROM
			`posts`
			WHERE
			`catid` LIKE '".$_POST["bringPostTitle"]."'
			ORDER BY `title` DESC
			";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$title = $row["title"];
	$category = $row["category"];
	
	$sql = "SELECT
			`type`
			FROM
			`category`
			WHERE
			`id` LIKE '".$_POST["bringPostTitle"]."'
			";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$type = $row["type"];
	
	if ( strtolower($type) == "movie" OR strtolower($type) == "animov" ){
		$title = "1080p";
	}else{
		if ( strstr(strtolower($title),"s") AND strstr(strtolower($title),"e") ){
			$newTitle = str_split(strtolower($title),4);
			$newTitle1 = (int)$newTitle[1] + 1;
			$newTitle1 = str_pad($newTitle1,2,"0",STR_PAD_LEFT);
			$title = $newTitle[0] . $newTitle1;
		}elseif( strstr(strtolower($title),"e") AND strstr(strtolower($title),"p")){
			$newTitle = explode("ep",strtolower($title));
			$newTitle1 = (int)$newTitle[1] + 1;
			$newTitle1 = str_pad($newTitle1,4,"0",STR_PAD_LEFT);
			$title = "ep" . $newTitle1;
		}else{
			$title = "";
		}
	}
	echo $category . "^" . strtoupper($title);
}

if ( isset($_POST["insertCatId"]) ){
	
	$title = $_POST["insertTitle"];
	$videolink = $_POST["insertLink"];
	$download = $videolink;
	$postDate = date('Y-m-d H:i:s');
	
	$sql = "SELECT
			`id`
			FROM
			`posts`
			ORDER BY `id` DESC
			LIMIT 1
			";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$id = $row["id"] + 1;
	
	$sql = "SELECT
			*
			FROM
			`category`
			WHERE
			`id` LIKE '".$_POST["insertCatId"]."'
			";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	
	$sql = "INSERT INTO `posts`
			(`id`, `catid`, `title`, `category`, `type`, `poster`, `videolink`, `download`, `postdate`)
			VALUES
			('".$id."', '".$row["id"]."', '".$title."', '".$row["title"]."', '".$row["type"]."', '".$row["poster"]."', '".$videolink."', '".$download."', '".$postDate."')
			";
	$result = $dbconnect->query($sql);
	
	$sql = "INSERT INTO `postlinks`
			(`id`, `uptobox`)
			VALUES
			('".$id."', '".$videolink."')
			";
	$result = $dbconnect->query($sql);
	
	echo "done";
}

if ( isset($_POST["cateId"]) ){
	
	$postDate = date('Y-m-d H:i:s');
	$sql = "UPDATE `category`
			SET
			`title` = '".$_POST["cateTitle"]."',
			`poster` = '".$_POST["catePoster"]."',
			`rating` = '".$_POST["cateRating"]."',
			`imdbrating` = '".$_POST["cateIMDb"]."',
			`genre` = '".$_POST["cateGenre"]."',
			`country` = '".$_POST["cateCountry"]."',
			`channel` = '".$_POST["cateChannel"]."',
			`notes` = '".$_POST["cateNotes"]."',
			`duration` = '".$_POST["cateDuration"]."',
			`language` = '".$_POST["cateLanguage"]."',
			`trailer` = '".$_POST["cateTrailer"]."',
			`type` = '".$_POST["cateType"]."',
			`header` = '".$_POST["cateHeader"]."',
			`description` = '".$_POST["cateDescription"]."',
			`releasedate` = '".$_POST["cateYear"]."',
			`postdate` = '".$postDate."'
			WHERE `id` LIKE '".$_POST["cateId"]."'
			";
	$result = $dbconnect->query($sql);
	
	echo "done";
}

if ( isset($_POST["reportId"]) ){
	
	$sql = "UPDATE `reports`
			SET
			`status` = 'Done'
			WHERE `postid` LIKE '".$_POST["reportId"]."'
			";
	$result = $dbconnect->query($sql);
	
	$sql= "SELECT id FROM notification ORDER BY id DESC LIMIT 1";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$id = $row["id"] + 1;

	$sql= "SELECT * FROM reports WHERE `postid` LIKE '".$_POST["reportId"]."'";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$postid = $row["postid"];
	$userid = $row["userid"];
	$catid = $row["catid"];

	$sql= "SELECT * FROM category WHERE id LIKE '$catid'";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$category = $row["title"];

	$type = "report";

	$title = $category ;
	$titleid = "category.php?id=$catid";
	$status = "unseen";

	$sql = "INSERT INTO notification (id, userid, type, title, titleid, status) VALUES ('$id','$userid', '$type', '$title', '$titleid', '$status')";
	$result = $dbconnect->query($sql);
}

?>