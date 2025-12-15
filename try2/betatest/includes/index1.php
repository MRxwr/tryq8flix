<?php

include_once("config.php");

$i = 0;
$y = 0;
$categoryid = $_GET["id"];

$sql = "SELECT * FROM posts WHERE catid = '$categoryid' ORDER BY title ASC";
$result = $dbconnect->query($sql);

var_dump ($loop = mysqli_num_rows($result));
echo "<br>";

while ( $row = $result->fetch_assoc() )
{
	
	$postid[$i] = $row["id"];
	var_dump($title = $row["title"]);
	echo "<br>";
	
	$title = explode("EP",$title);
	$title = ltrim($title[1], '0');
	var_dump($title = str_pad ($title, 2, "0", STR_PAD_LEFT));
	echo "<br>";
	
	$posttitle[$i] = "S01E".$title;
	
	$i = $i + 1;
}

while ( $y < $loop )
{
	$sql = "UPDATE posts SET title = '$posttitle[$y]' WHERE id = '$postid[$y]'";
	$result = $dbconnect->query($sql);
	$y = $y + 1;
}

header ("Location: ../category.php?id=$categoryid")
?>

<!-- 
	
-->

<!--

// filling category types into posts
$i = 0;
$loop = 0;

while ( $i < 80 )
{
	$sql = "SELECT * FROM posts WHERE type = ''";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	var_dump($catid = $row["catid"]);

	$sql = "SELECT * FROM category WHERE id = $catid ";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
	var_dump($cattype = $row["type"]);

	$sql = "UPDATE posts SET type = '$cattype' WHERE catid = '$catid'";
	$result = $dbconnect->query($sql);
	
	$i = $i - 1;
}

//change date of category

	$sql = "SELECT * FROM category WHERE type ='movie' AND releasedate like '%$i%'";
	$result = $dbconnect->query($sql);
	
	while ( $row = $result->fetch_assoc() )
	{
		echo "<br> number of rows of " . $i . " is: " . $result->num_rows . "<br>";
		$id = $row["id"];
		$sql1 = "UPDATE category SET releasedate = 'q8flix' WHERE id = $id ";
		$result = $dbconnect->query($sql1);
		echo '<br>From ' . $i . ' To q8flix<br>';	
		$sql = "SELECT * FROM category WHERE type ='movie' AND releasedate like '%$i%'";
		$result = $dbconnect->query($sql);
	}
	
	$sql = "SELECT * FROM category WHERE type ='movie' AND releasedate like 'q8flix'";
	$result = $dbconnect->query($sql);
	
	while ( $row = $result->fetch_assoc() )
	{
		$id = $row["id"];
		$sql1 = "UPDATE category SET releasedate = $i WHERE id = $id ";
		$result = $dbconnect->query($sql1);
		echo '<br>---------------<br>From q8flix To ' . $i . "<br>" ;	
		$sql = "SELECT * FROM category WHERE type ='movie' AND releasedate like 'q8flix'";
		$result = $dbconnect->query($sql);
	}
	
	$loop = $loop +1;
	$i = $i -1 ;
-->