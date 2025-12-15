<?php
include ("../includes/config.php");
include ("../includes/checksouthead.php");

$sql = "SELECT `id`
		FROM `users`
		WHERE
		`username` LIKE '".$username."'
		";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$userid = $row["id"];

$sql = "SELECT MAX(id) as `videoId`, catid AS categoryId
		FROM `posts`
		WHERE
		`catid` IN (
					SELECT `categoryId`
					FROM `favourites`
					WHERE
					`userId` LIKE '".$userid."'
					)
		GROUP BY `catid`
		ORDER BY `title` DESC
		";
$result = $dbconnect->query($sql);
while($row = $result->fetch_assoc()){
	$videoIds[] = $row["videoId"];
	$categoryIds[] = $row["categoryId"];
}

for ( $i = 0 ; $i < sizeof($videoIds) ; $i++ ){
	$sql = "SELECT `title`
			FROM `notificationnew`
			WHERE
			`titleid` LIKE '%".$videoIds[$i]."%'
			AND
			`userid` LIKE '".$userid."'
			";
	$result = $dbconnect->query($sql);
	if ( $result->num_rows < 1 ){
		$sql = "SELECT
				`title`, `category`
				FROM `posts`
				WHERE
				`id` LIKE '".$videoIds[$i]."'
				";
		$result = $dbconnect->query($sql);
		$row = $result->fetch_assoc();
		$title = $row["category"] . " " . $row["title"];
		$link = "preparevideo.php?postid=".$videoIds[$i]."&catid=".$categoryIds[$i];
		$sql = "INSERT INTO `notificationnew`
				(`userid`, `type`, `title`, `titleid`, `status`)
				VALUES
				('".$userid."', 'notwached', '".$title."', '".$link."', 'unseen')
				";
		$result = $dbconnect->query($sql);
	}
}

if ( isset($_GET['q']) )
{
	$id = $_GET['q'];
	$sql = "UPDATE `notificationnew`
			SET
			`status` = 'Seen'
			WHERE
			`id` LIKE '".$id."'
			AND
			`userid` LIKE '".$userid."'
			";
	$result = $dbconnect->query($sql);

}

$sql = "SELECT *
		FROM `notificationnew`
		WHERE
		`userid` LIKE '".$userid."'
		AND
		`status` LIKE 'unseen'
		ORDER BY `id` DESC
		LIMIT 5
		";
$result = $dbconnect->query($sql);

if ( $result->num_rows > 0 )
{
	echo "<br><b><div style='width: 100%;'>
	<table width='100%'><tr><td style='text-align: left; font-size: 20px;'><a style='color: white;text-decoration: none;' target='' href='notifications.php' >Notification</a></td></tr></table></div></b>";

	echo "<table>";
	while($row = $result->fetch_assoc()) 
	{
		if ( $row["type"] == "request")
		{
			echo "<tr>";
			echo "<td style='width:1%;font-size: 20px; text-decoration: none; color: white'><a onclick='showUser(". $row["id"].")'>&times;</a> </td>";
			echo "<td><a href=".$row["titleid"]." style='text-decoration: none'><b style='color: burlywood'>" . $row['title'] . "</b> is here.</a></td>";
			echo "</tr>";
		}
		elseif ( $row["type"] == "report")
		{
			echo "<tr>";
			echo "<td style='width:1%;font-size: 20px; text-decoration: none; color: white'><a onclick='showUser(". $row["id"].")'>&times;</a> </td>";
			echo "<td><a href=".$row["titleid"]." style='text-decoration: none'>Your report for <b style='color: palevioletred'>" . $row['title'] . "</b> is fixed.</a></td>";
			echo "</tr>";
		}
		elseif ( $row["type"] == "notwached")
		{
			echo "<tr>";
			echo "<td style='width:1%;font-size: 20px; text-decoration: none; color: white'><a onclick='showUser(". $row["id"].")'>&times;</a> </td>";
			echo "<td><a href=watch.php".str_replace('preparevideo.php','',$row['titleid'])." style='text-decoration: none; color:white;'>New from <b style='color: lightgreen'>" . $row['title'] . "</b></a></td>";
			echo "</tr>";
		}
	}
	echo "</table>";
}
?>