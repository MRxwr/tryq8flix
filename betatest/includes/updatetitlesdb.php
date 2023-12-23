<?php

include_once("config.php");
ini_set('max_execution_time', 1000000);
$sql = "SELECT * 
		FROM `posts` 
		WHERE `category` LIKE '%Greys Anatomy%'";

$result = $dbconnect->query($sql);
while ( $row = $result->fetch_assoc() )
{
	$ids[] = $row["id"];
	$links[] = $row["download"];
}

$i = 0;

while ( $i < sizeof($ids) )
{
	$sql = "UPDATE `postlinks` 
			SET `uptobox`='".$links[$i]."' 
			WHERE `id` LIKE '".$ids[$i]."'";
	$result = $dbconnect->query($sql);
	$i++;
}

header ("Location: ../category.php?id=$categoryid")
?>

$i = 0;
$y = 0;
$categoryid = $_GET["id"];

$sql = "SELECT * FROM posts WHERE catid = '$categoryid' ORDER BY title ASC";
$result = $dbconnect->query($sql);

$loop = mysqli_num_rows($result);

while ( $row = $result->fetch_assoc() )
{
	
	$postid[$i] = $row["id"];
	$title = $row["title"];
	
	//Between E and EP
	if ( strpos($title,"EP") !== false )
	{
		$title = explode("EP",$title);
		$title = "S01E" . str_pad (ltrim($title[1], '0'), 3, "0", STR_PAD_LEFT);
	}
	//number of zeros
	/* var_dump($title = str_pad ($title, 3, "0", STR_PAD_LEFT));
	echo "<br>"; */
	
	//$posttitle[$i] = "S01E".$title;
	$posttitle[$i] = $title;
	$i = $i + 1;
}

while ( $y < $loop )
{
	$sql = "UPDATE posts SET title = '$posttitle[$y]' WHERE id = '$postid[$y]'";
	$result = $dbconnect->query($sql);
	$y = $y + 1;
}