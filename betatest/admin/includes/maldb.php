<?php
require("config.php");
$mal = $_POST["mal"];
$posttime = $_POST["posttime"];
$postdate = $_POST["postdate"];
$mal = explode("/",$mal);

$string = file_get_contents("https://api.jikan.moe/v3/anime/$mal[4]");
$json_a = json_decode($string, true);

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

$sql = "INSERT INTO category 
		(type, title, rating, imdbrating, duration, genre, releasedate, posttime, postdate, language, country, channel, poster, description, trailer) 
		VALUES 
		('Anime', '$title', '$rating', '$imdbrating', '$duration', '$genres', '$releasedate', '$posttime', '$postdate', 'Japanese', 'Japan', '$channels', '$poster', '$description', '$trailer')";
$result = $dbconnect->query($sql);

header('LOCATION: ../index.php');

?>