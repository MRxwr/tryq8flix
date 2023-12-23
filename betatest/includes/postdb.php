<!DOCTYPE html>
<html>
<title><?php echo $categorytitle . " " . $posttitle ?> - Q8Flix</title>
<meta charset="UTF-8">
</html>
<?php
include_once ("config.php");
include_once ("checksouthead.php");
//renaming srt file as txt file
if( is_uploaded_file($_FILES['subtitle']['tmp_name']) ){
	header('Content-Type:text/plain; charset=utf-8');
	//uploading the file
	$directory = "../subs/";
	$originalfile = $directory . round(microtime(true));
	move_uploaded_file($_FILES["subtitle"]["tmp_name"], $originalfile);
	$fileoldname = round(microtime(true));
	rename($originalfile,$fileoldname);

	//converting srt to vtt
	$content = file_get_contents($fileoldname);
	$content = str_replace(",",".",$content);
	$content = "WEBVTT \n\n" . $content;
	//$getfileencoding = mb_detect_encoding($fileoldname);
	//$content = mb_convert_encoding($content,"utf-8"); // iconv($getfileencoding, "utf-8//TRANSLIT//IGNORE", $content);
	file_put_contents($fileoldname, $content);

	//saving the file into vtt extension
	$filenewname = $directory . date("d-m-y") . time() .  round(microtime(true)). ".vtt";
	rename($fileoldname,$filenewname);
}
if ( !isset ( $_POST["catid"] ) ){
	$catid = "";
}else{
	$catid = $_POST["catid"];
}
if ( !isset($username) AND !in_array($username,$usernames) ){
	echo $username;//header ("Location: ../index.php?error=post");
}else{	
	$title = $_POST["title"];
	$category = $_POST["category"];
	$postdate = $_POST["postdate"];
	$poster = $_POST["poster"];
	
	$sql= "SELECT type FROM category WHERE id = '$catid'";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$type = $row["type"];
	
	if ( !isset ($originalfile) ){
		$subtitle = "https://www.opensubtitles.org/en/search2/sublanguageid-all/moviename-$category+$title";
	}else{
		$subtitle = $filenewname;
	}
	$uptobox = $_POST["uptobox"];
	$youtube = $_POST["youtube"];
	$mycima = $_POST["mycima"];
	$download = $_POST["uptobox"];
	$posttime = $_POST["posttime"];
	
	$sql= "SELECT id FROM posts ORDER BY id DESC LIMIT 1";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$id = $row["id"]+1;
	
	$sql = "INSERT INTO posts (id,catid, title, category, type, views, poster, subtitle, download) VALUES ('$id', '$catid', '$title', '$category', '$type', '0', '$poster', '$subtitle', '$download')";
	$result = $dbconnect->query($sql);
	
	$sql = "INSERT INTO postlinks (id, uptobox, youtube, mycima) VALUES ('$id', '$download', '$youtube', '$mycima')";
	$result = $dbconnect->query($sql);
	
	$sql = "UPDATE category SET posttime = '$posttime', postdate = '$postdate' WHERE id='$catid'";
	$result = $dbconnect->query($sql);
	
	
}
header ("Location: ../category.php?id=$catid&error=addpost");
?>