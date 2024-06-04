<?php
session_start();
include_once ("config.php");
include_once("includes/checksouthead.php");
if ( !isset($username) AND !in_array($username,$usernames) )
{
	header ("Location: ../index.php?error=category");
}
else
{
	include_once ("config.php");
	$mal = $_POST["mal"];
	$posttime = $_POST["posttime"];
	$postdate = $_POST["postdate"];
	$mal = explode("/",$mal);
	
	//str_replace('"',"",str_replace("'","",));
	
	$string = file_get_contents("https://api.jikan.moe/v3/anime/$mal[4]");
	$json_a = json_decode($string, true);
	$json_a = str_replace('"','',str_replace("'","",$json_a));
	
	$title = str_replace('"',"",str_replace("'","",$json_a["title"] . " - " . $json_a["title_english"]));
	$releasedate = $json_a["aired"]["prop"]["from"]["year"];
	$json_a["duration"] = explode(" per",$json_a["duration"]); 
	$duration = $json_a["duration"][0];
	$imdbrating = $json_a["score"];
	$description = str_replace('"',"",str_replace("'","",$json_a["synopsis"]));
	$poster = $json_a["image_url"];
	$json_a["rating"] = explode(" -",$json_a["rating"]); 
	$rating = $json_a["rating"][0];
	$json_a["trailer_url"] = explode("?",$json_a["trailer_url"]); 
	$trailer = str_replace("embed/","watch?v=",$json_a["trailer_url"][0]);

	foreach ( $json_a["genres"] as $genre )
	{
		$genres = $genres . $genre["name"] . ", ";
	}
	
	foreach ( $json_a["producers"] as $channel )
	{
		$channels = str_replace('"',"",str_replace("'","",$channels .$channel["name"] . ", "));
	}
	
	$sql= "SELECT id FROM category WHERE title like '$title' and type like '$type' and releasedate like '$releasedate'";
	$result = $dbconnect->query($sql);
	if ( $result->num_rows > 0 )
	{
		$row = $result->fetch_assoc();
		$id = $row["id"];
		goto jump;
	}
	
	$sql= "SELECT id FROM category ORDER BY id DESC LIMIT 1";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$id = $row["id"]+1;
	
	$sql = "INSERT INTO category 
			(id,type, title, rating, imdbrating, duration, genre, releasedate, posttime, postdate, language, country, channel, poster, description, trailer) 
			VALUES 
			('$id','Anime', '$title', '$rating', '$imdbrating', '$duration', '$genres', '$releasedate', '$posttime', '$postdate', 'Japanese', 'Japan', '$channels', '$poster', '$description', '$trailer')";
	$result = $dbconnect->query($sql);
	header ("Location: ../latest.php");
	jump:
	header ("location: ../category.php?id=$id");
}
?>
