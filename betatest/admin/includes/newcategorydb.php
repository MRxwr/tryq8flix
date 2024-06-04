<?php
require ("config.php");

$title = $_POST["title"];
$rating = $_POST["rating"];
$imdbrating = $_POST["imdbrating"];
$duration = $_POST["duration"];
$genre = $_POST["genre"];
$releasedate = $_POST["releasedate"];
$postdate = $_POST["postdate"];
$language = $_POST["language"];
$notes = $_POST["notes"];
$country = $_POST["country"];
$channel = $_POST["channel"];
$poster = $_POST["poster"];
$header = $_POST["header"];
$description = $_POST["description"];
$description = str_replace("'","?singlequtation?",$description);
$trailer = $_POST["trailer"];
$type = $_POST["type"];
$posttime = $_POST["posttime"];

$sql = "INSERT INTO 
		`category`
		(type, title, rating, imdbrating, duration, genre, releasedate, posttime, postdate, language, country, channel, poster, header, description, trailer, notes) 
		VALUES
		('$type', '$title', '$rating', '$imdbrating', '$duration', '$genre', '$releasedate', '$posttime', '$postdate', '$language', '$country', '$channel', '$poster', '$header', '$description', '$trailer','$notes')
		";
$result = $dbconnect->query($sql);

header('LOCATION: ../index.php');

?>