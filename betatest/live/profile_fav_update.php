<?php
include ("../includes/config.php");
include ("../includes/checksouthead.php");

$sql= "SELECT `id`
		FROM `users`
		WHERE
		`username` LIKE '$username'
		";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$userid = $row["id"];

if ( isset($_GET["q"]) )
{
	$id = $_GET["q"];
	$sql = "DELETE FROM `favourites`
			WHERE
			`userId` LIKE '".$userid."'
			AND
			`categoryId` LIKE '".$id."'
			";
	$result = $dbconnect->query($sql);
}

$sql = "SELECT *
		FROM `category`
		WHERE
		`id` IN (
					SELECT `categoryId`
					FROM `favourites`
					WHERE
					`userId` LIKE '".$userid."'
				) 
		";
$result = $dbconnect->query($sql);
if ( $result->num_rows > 0 )
{
	while ( $row = $result->fetch_assoc() )
	{
		echo "<a href='category.php?id=".$row["id"]."'><div class='w3-quarterindex' style='padding: 3px;  position: relative;text-align: center;color: white;'><img src='".$row["poster"]."' alt='' id='imageindex'></a>
		<a onclick='showUser(". $row["id"].")'><img src='images/unfavo.png' style='width:25px; height:25px'></a></div>";
	}
	
}
else
{
	echo "No shows has been added yet.";
}
?>