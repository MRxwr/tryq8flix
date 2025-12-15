<?php
require("config.php");

$imdbid = $_POST["imdbid"];
$imdbid = explode("/",$imdbid);
$trailer = $_POST["trailer"];
$type = $_POST["type"];
$postdate = $_POST["postdate"];
$posttime = $_POST["posttime"];

$string = file_get_contents("http://www.omdbapi.com/?i=$imdbid[4]&apikey=95b9e7bf");
$json_a = json_decode($string, true);
$jsonIterator = new RecursiveIteratorIterator(
	new RecursiveArrayIterator(json_decode($string, TRUE)),
	RecursiveIteratorIterator::SELF_FIRST);

foreach ($jsonIterator as $key => $val) {
	if(is_array($val)) 
	{
		 echo "$key:\n";
	} 
	else 
	{
				if ( $key == "Title" )
				{
					$title = $val;
					$title = str_replace("'","",$title);
				}
				if ( $key == "Year" )
				{
					$Year = $val;
				}
				if ( $key == "Rated" )
				{
					$rating = $val;
				}
				if ( $key == "imdbRating" )
				{
					$imdbrating = $val;
				}
				if ( $key == "Released" )
				{
					$releasedate = $val;
					$releasedate = substr($releasedate, -4);
				}
				if ( $key == "Runtime" )
				{
					$duration = $val;
				}
				if ( $key == "Genre" )
				{
					$genre = $val;
				}
				if ( $key == "Actors" )
				{
					$channel = $val;
					$channel = str_replace("'","?singlequtation?",$channel);
				}
				if ( $key == "Plot" )
				{
					$description = $val;
					$description = str_replace("'","?singlequtation?",$description);
				}
				if ( $key == "Language" )
				{
					$language = $val;
				}
				if ( $key == "Country" )
				{
					$country = $val;
				}
				if ( $key == "Poster" )
				{
					$poster = $val;
				}		
	}
	}

$sql = "INSERT INTO
		`category`
		(`type`, `title`, `rating`, `imdbrating`, `duration`, `genre`, `releasedate`, `posttime`, `postdate`, `language`, `country`, `channel`, `poster`, `description`, `trailer`)
		VALUES
		('$type', '$title', '$rating', '$imdbrating', '$duration', '$genre', '$releasedate', '$posttime', '$postdate', '$language', '$country', '$channel', '$poster', '$description', '$trailer')
		";
$result = $dbconnect->query($sql);

header('LOCATION: ../index.php');
	
?>