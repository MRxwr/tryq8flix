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
	
	$url = file_get_contents($mal);

	$explode = explode('<div class="panel jumbo">',$url);
	$explode = explode('<div class="row collapse medium-uncollapse">',$explode[1]);
	$content = $explode[0];

	$arabicTitle = explode('dir="rtl">',$content);
	$arabicTitle = explode('</span>',$arabicTitle[1]);
	$title = $arabicTitle[0];

	$englishTitle = explode('dir="ltr">',$content);
	$englishTitle = explode('</span>',$englishTitle[1]);
	$title = $title . " | " . $englishTitle[0];

	$poster = explode('<img src="',$content);
	$poster = explode('" alt',$poster[1]);
	$poster = $poster[0];

	$countryTime = explode('<ul class="list-separator">',$content);
	$countryTime = explode('<li></li>',$countryTime[1]);
	$countryTime = $countryTime[0];

	$country = explode('<li>',$countryTime);
	$country = explode('">',$country[2]);
	$country = explode('</a></li>',$country[1]);
	$country = $country[0];

	$time = explode('<li>',$countryTime);
	$time = explode('</li>',$time[3]);
	$time = explode(' ',$time[0]);
	$duration = $time[0] . " min";

	$year = explode('release_year',$content);
	$year = explode('">',$year[1]);
	$year = explode('</a>',$year[1]);
	$releasedate = $year[0];

	$details = explode('<p>',$content);
	$details = explode('</p>',$details[1]);
	$description = $details[0];

	$staff = explode('<ul class="list-separator list-title">',$content);
	$staff = explode('cast">',$staff[3]);
	$actors = $staff[0];
	$actors = explode('">',$actors);

	$allActros = "";
	for ( $i = 1 ; $i < sizeof($actors) ; $i++ ){
		$subActor = explode('</a>',$actors[$i]);
		$allActros = $subActor[0] . ", " . $allActros;
	}
	$channels = $allActros;

	$genres = "Comedy, Drama, Ramadan";
	
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
			('$id','Tv-Show', '$title', 'N/A', 'N/A', '$duration', '$genres', '$releasedate', '$posttime', '$postdate', 'Arabic', '$country', '$channels', '$poster', '$description', '')";
	$result = $dbconnect->query($sql);
	header ("Location: ../latest.php");
	jump:
	header ("location: ../category.php?id=$id");
}
?>
