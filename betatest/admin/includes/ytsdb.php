<?php
require("config.php");
$ytslink = $_POST["ytslink"];
$postdate = $_POST["postdate"];
$posttime = $_POST["posttime"];
//getting imdb link
$getcontents = file_get_contents($ytslink);
$explode1 = explode("https://www.imdb.com/title/",$getcontents);
$explode2 = explode('" title',$explode1[1]);
$imdbid = str_replace("/","",$explode2[0]);
//getting youtube link
$explode1 = explode("https://www.youtube.com/embed/",$getcontents);
$explode2 = explode('" id',$explode1[1]);
$trailer = "https://www.youtube.com/embed/" . $explode2[0];
//uplaoding torrent to alldebrid
$explode1 = explode('magnet:?xt=urn:btih:',$getcontents);
$numberOfMagnets = sizeof($explode1);
$explode2 = explode('&',$explode1[$numberOfMagnets-1]);
$magnet = $explode2[0];
//get post title from yts
$explode1 = explode('modal-quality-',$getcontents);
$numberOfMagnets = sizeof($explode1);
$explode2 = explode('"',$explode1[$numberOfMagnets-1]);
$titlePost = $explode2[0];

// $magnet = '0dcce2b9dfd1a68c93bc891040d0a0b128gn5'; // Can also use hash directly
$apiEndpoint = "https://api.alldebrid.com/v4/magnet/upload?agent=myAppName&apikey=CK8F8PX7izXZpehmIOcX&magnets[]=" . urlencode($magnet);
$result = json_decode(file_get_contents($apiEndpoint),true);
$magnetId = $result["data"]["magnets"][0]["id"];

$context = stream_context_create([ 'http' => [ 'ignore_errors' => true ] ]); // Suppress PHP warnings on HTTP status code >= 400

// Authentificated endpoint with valid apikey
$apiEndpoint = "https://api.alldebrid.com/v4/magnet/status?agent=myAppName&apikey=CK8F8PX7izXZpehmIOcX";
$apiEndpointOnlyOne = "https://api.alldebrid.com/v4/magnet/status?agent=myAppName&apikey=CK8F8PX7izXZpehmIOcX&id=" . urlencode($magnetId);
$apiEndpointOnlyActive = "https://api.alldebrid.com/v4/magnet/status?agent=myAppName&apikey=CK8F8PX7izXZpehmIOcX&status=active";

$response = json_decode(file_get_contents($apiEndpointOnlyOne, false, $context),true);

if ( !empty($response["data"]["magnets"]["links"][0]["link"]) ){
	$uptoboxLink = $response["data"]["magnets"]["links"][0]["link"];
	$uptpboxLinkCode = explode("uptobox.com/",$uptoboxLink);
	$apiEndpoint = "https://uptobox.com/api/user/file/alias?token=c7592f3d7e8a2c6682fb51ebd2e9d96f6uvoo&file_code=".$uptpboxLinkCode[1];
	$result = json_decode(file_get_contents($apiEndpoint),true);
}

$type = 'Movie';
$string = file_get_contents("http://www.omdbapi.com/?i=$imdbid&apikey=95b9e7bf");
$json_a = json_decode($string, true);

$jsonIterator = new RecursiveIteratorIterator(
	new RecursiveArrayIterator(json_decode($string, TRUE)),
	RecursiveIteratorIterator::SELF_FIRST);

foreach ($jsonIterator as $key => $val) {
	if(is_array($val)) 
	{
		 "$key:\n";
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
			(type, title, rating, imdbrating, duration, genre, releasedate, posttime, postdate, language, country, channel, poster, description, trailer)
			VALUES
			('$type', '$title', '$rating', '$imdbrating', '$duration', '$genre', '$releasedate', '$posttime', '$postdate', '$language', '$country', '$channel', '$poster', '$description', '$trailer')
			";
$result = $dbconnect->query($sql);


// category finished // starting post //
$sql = "SELECT
		`poster`, `id`
		FROM `category`
		WHERE
		`title` LIKE '$title'
		AND
		`type` LIKE '$type'
		ORDER BY `id` DESC
		LIMIT 1
		";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$poster = $row["poster"];
$catid = $row["id"];
$category = $title;
$subtitle = "";

strtolower( $search = urlencode($category));
$url = "https://uptobox.com/api/user/files?token=c7592f3d7e8a2c6682fb51ebd2e9d96f6uvoo&path=//&limit=100&orderBy=file_name&searchField=file_name&search=".$search;
$return = file_get_contents($url);
$json_a = json_decode($return, true);

if ( isset($json_a['data']['files'][0]['file_code']) AND !empty($json_a['data']['files'][0]['file_code']) ){
	$allTitles = $titlePost;
	$allLinks = "http://uptobox.com/" . $json_a['data']['files'][0]['file_code'];

	$sql = "SELECT `id`
			FROM `posts`
			ORDER BY `id` DESC
			LIMIT 1";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$id = $row["id"]+1;

	$sql = "INSERT INTO `posts`
			(id,catid, title, category, type, views, poster, subtitle, videolink, download)
			VALUES
			('$id', '$catid', '$allTitles', '$category', '$type', '0', '$poster', '$subtitle', '$allLinks', '$allLinks')
			";
	$result = $dbconnect->query($sql);

	$sql = "INSERT INTO `postlinks`
			(id, uptobox, youtube, mycima)
			VALUES
			('$id', '$allLinks', '', '')
			";
	$result = $dbconnect->query($sql);

	header('LOCATION: ../tryquickadd.php');
}else{
	header('LOCATION: ../tryquickadd.php?post=no');
}

?>