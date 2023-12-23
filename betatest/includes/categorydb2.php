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
	$explode2 = explode('&',$explode1[1]);
	$magnet = $explode2[0];
	$magnetbyradio = $_POST["magnet"];
	// $magnet = '0dcce2b9dfd1a68c93bc891040d0a0b128gn5'; // Can also use hash directly
	$apiEndpoint = "https://api.alldebrid.com/v4/magnet/upload?agent=myAppName&apikey=CK8F8PX7izXZpehmIOcX&magnets[]=" . urlencode($magnetbyradio);
	$result = json_decode(file_get_contents($apiEndpoint));

	$type = 'movie';
	
	
	include_once ("config.php");
	
	$string = file_get_contents("http://www.omdbapi.com/?i=$imdbid&apikey=95b9e7bf");
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
			$val = str_replace('"','',str_replace("'","",$val));
					if ( $key == "Title" )
					{
						$title = $val;
						//$title = str_replace("'","",$title);
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
						//$channel = str_replace("'","?singlequtation?",$channel);
					}
					if ( $key == "Plot" )
					{
						$description = $val;
						//$description = str_replace("'","?singlequtation?",$description);
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
	
	$sql = "INSERT INTO category (id,type, title, rating, imdbrating, duration, genre, releasedate, posttime, postdate, language, country, channel, poster, description, trailer) VALUES ('$id','$type', '$title', '$rating', '$imdbrating', '$duration', '$genre', '$releasedate', '$posttime', '$postdate', '$language', '$country', '$channel', '$poster', '$description', '$trailer')";
	$result = $dbconnect->query($sql);
	
	header ("Location: ../latest.php");
	
	jump:
	
	header ("location: ../category.php?id=$id");
}
?>

<!--{
"Title":"The Walking Dead",
"Year":"2010–",
"Rated":"TV-MA",
"Released":"31 Oct 2010",
"Runtime":"44 min",
"Genre":"Drama, Horror, Thriller",
"Director":"N/A",
"Writer":"Frank Darabont, Angela Kang",
"Actors":"Norman Reedus, Melissa McBride, Andrew Lincoln, Danai Gurira",
"Plot":"Sheriff Deputy Rick Grimes wakes up from a coma to learn the world is in ruins, and must lead a group of survivors to stay alive.",
"Language":"English",
"Country":"USA",
"Awards":"Nominated for 1 Golden Globe. Another 67 wins & 190 nominations.",
"Poster":"https://m.media-amazon.com/images/M/MV5BYWY4ODJiZjMtNWMxYi00ZmM5LWIwZmQtZWY0MjJmZGU5MjcxXkEyXkFqcGdeQXVyMTkxNjUyNQ@@._V1_SX300.jpg",
"Metascore":"N/A",
"imdbRating":"8.3",
"imdbVotes":"799,687",
"imdbID":"tt1520211",
"Type":"series",
"totalSeasons":"9",
"Response":"True"
}-->