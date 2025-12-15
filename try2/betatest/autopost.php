<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");

$catid = $_GET["id"];
$sql = "SELECT * FROM `category` WHERE `id` = '{$catid}'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$category = $row["title"];
$poster = $row["poster"];
$posttime = date("g:i A");
$postdate = date("Y/m/d");
$subtitle = "";
$type = $row["type"];
$token = "c7592f3d7e8a2c6682fb51ebd2e9d96f6uvoo";
strtolower( $search = urlencode(str_replace(":"," ",$category)));
$url = "https://uptobox.com/api/user/files?token={$token}&path=//&limit=100&orderBy=file_name&searchField=file_name&search=".$search;
$return = file_get_contents($url);
$json_a = json_decode($return, true);

$allTitles = array();
$allLinks = array();

$i = 0;
while ( $i < sizeof($json_a['data']['files']) ) 
{
	$title = $json_a['data']['files'][$i]['file_name'];
	$title = strtolower ($title);
	
	if ( strpos($title,"s0") !== false )
	{
		$newtitle = explode("s0",$title);
		$newtitle1 = explode("e",$newtitle[1]);
		$titleNo = "s0" . str_replace(" ", "", $newtitle1[0]) . "e" ;
		$epiNo = $newtitle1[1];
		$y = 0;
		while ( $y < strlen($epiNo) )
		{
			if ( is_numeric($epiNo[$y]) )
			{
				$titleNo = $titleNo . $epiNo[$y];
			}
			else
			{
				break;
			}
			$y++;
		}
		$allTitles[] = str_replace(".", "",strtoupper($titleNo));
		$allLinks[] = "https://uptobox.com/" . $json_a['data']['files'][$i]['file_code'];
	}
	
	if ( strpos($title,"s1") !== false )
	{
		$newtitle = explode("s1",$title);
		$newtitle1 = explode("e",$newtitle[1]);
		$titleNo = "s1" . $newtitle1[0] . "e" ;
		$epiNo = $newtitle1[1];
		$y = 0;
		while ( $y < strlen($epiNo) )
		{
			if ( is_numeric($epiNo[$y]) )
			{
				$titleNo = $titleNo . $epiNo[$y];
			}
			else
			{
				break;
			}
			$y++;
		}
		$allTitles[] = str_replace(".", "",strtoupper($titleNo));
		$allLinks[] = "https://uptobox.com/" . $json_a['data']['files'][$i]['file_code'];
	}
	
	if ( strpos($title,"episode.") !== false AND strpos($title,"s0") === false AND strpos($title,"s1") === false)
	{
		if ( strpos($title,"season.") !== false )
		{
			$titleNo = "";
			$seasonNo = explode("season.",$title);
			$seasonNo = explode(".",$seasonNo[1]);
			$seasonNo = explode("season.",$title);
			$seasonNo = explode(".",$seasonNo[1]);
			$episodeNo = explode("episode.",$title);
			$episodeNo = explode(".",$episodeNo[sizeof($episodeNo)-1]);
			$epiNo = $episodeNo[0];
			$y = 0;
			while ( $y < strlen($epiNo) )
			{
				if ( is_numeric($epiNo[$y]) )
				{
					$titleNo = $titleNo . $epiNo[$y];
				}
				else
				{
					break;
				}
				$y++;
			}
			$titleNo = "s" .sprintf("%02d", $seasonNo[0]) . "e" . $titleNo ;
		}
		else
		{
			$newtitle = explode("episode.",$title);
			$newtitle1 = explode(".mp4",$newtitle[1]);
			$titleNo = "s01e";
			$epiNo = $newtitle1[0];
			$y = 0;
			while ( $y < strlen($epiNo) )
			{
				if ( is_numeric($epiNo[$y]) )
				{
					$titleNo = $titleNo . $epiNo[$y];
				}
				else
				{
					break;
				}
				$y++;
			}
		}
		$allTitles[] = strtoupper($titleNo);
		$allLinks[] = "https://uptobox.com/" . $json_a['data']['files'][$i]['file_code'];
	}
	
	if ( strpos($title,".e") !== false AND strpos($title,"s0") === false AND strpos($title,"s1") === false AND strpos($title,"episode.") === false)
	{
			$newtitle = explode(".e",$title);
			$newtitle1 = explode(".mp4",$newtitle[1]);
			$titleNo = "s01e";
			$epiNo = $newtitle1[0];
			$y = 0;
			while ( $y < strlen($epiNo) )
			{
				if ( is_numeric($epiNo[$y]) )
				{
					$titleNo = $titleNo . $epiNo[$y];
				}
				else
				{
					break;
				}
				$y++;
			}
		$allTitles[] = strtoupper($titleNo);
		$allLinks[] = "https://uptobox.com/" . $json_a['data']['files'][$i]['file_code'];
	}
	
	if ( strpos($title,".e") === false AND strpos($title,"s0") === false AND strpos($title,"s1") === false)
	{
		$allTitles[] = "720p";
		$allLinks[] = "https://uptobox.com/" . $json_a['data']['files'][$i]['file_code'];
	}
	
	$i++;
}

for( $i = 0; $i < sizeof($allLinks); $i++ ){
	$sql= "SELECT id FROM posts ORDER BY id DESC LIMIT 1";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	$id = $row["id"]+1;
	
	$sql = "INSERT INTO posts (id,catid, title, category, type, views, poster, subtitle, videolink, download) VALUES ('$id', '$catid', '$allTitles[$i]', '$category', '$type', '0', '$poster', '$subtitle', '$allLinks[$i]', '$allLinks[$i]')";
	$result = $dbconnect->query($sql);
	
	$sql = "INSERT INTO postlinks (id, uptobox, youtube, mycima) VALUES ('$id', '$allLinks[$i]', '', '')";
	$result = $dbconnect->query($sql);
}

header ("Location: category.php?id=$catid");

?>