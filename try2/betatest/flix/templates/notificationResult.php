<?php
require('../../includes/config.php');
require('../../includes/checksouthead.php');

$sql = "SELECT `id`
		FROM `users`
		WHERE
		`username` LIKE '$username'
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
		$sql = "SELECT `title`, `category` FROM `posts` WHERE `id` LIKE '".$videoIds[$i]."'";
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
		";
$result = $dbconnect->query($sql);

echo "<table>";
while($row = $result->fetch_assoc()) 
{
	if ( $row["type"] == "request"){
		$title = str_replace('search.php?search=','?searchid=1&query=',$row['titleid']);
		echo "<tr>";
		echo "<td style='width:1%;font-size: 20px; text-decoration: none; color: white'><a class='deleteNoti' id='".$row["id"]."'>&times;</a> </td>";
		echo "<td><a href=".$title." style='text-decoration: none'><b style='color: burlywood'>" . $row['title'] . "</b> is here.</a></td>";
		echo "</tr>";
	}
	elseif ( $row["type"] == "report"){
		$title = str_replace('category.php','',$row['titleid']);
		echo "<tr>";
		echo "<td style='width:1%;font-size: 20px; text-decoration: none; color: white'><a class='deleteNoti' id='".$row["id"]."'>&times;</a> </td>";
		echo "<td><a href=".$title." style='text-decoration: none'>Your report for <b style='color: palevioletred'>" . $row['title'] . "</b> is fixed.</a></td>";
		echo "</tr>"; 
	}
	elseif ( $row["type"] == "notwached"){
		$title = str_replace('preparevideo.php','',$row['titleid']);
		$title = explode('&',$title);
		$title = str_replace('?postid=','',$title[0]);
		echo "<tr>";
		echo "<td style='width:1%;font-size: 20px; text-decoration: none; color: white'><a class='deleteNoti' id='".$row["id"]."'>&times;</a> </td>";
		echo "<td style='width:99%' ><a target='_blank' href=?videoId=".$title." style='text-decoration: none; color:white;'>New from <b style='color: lightgreen'>" . $row['title'] . "</b></a></td>";
		echo "</tr>";
	}
}
echo "</table>";
mysqli_close($dbconnect);
?>
<script>
$(function(){
	$('.deleteNoti').on('click',function(e){
		e.preventDefault();
		catID = $(this).attr('id');
		$.ajax({
			type:"GET",
			url: "templates/notificationResult.php",
			data: {
				q: catID,
			},
			success:function(result){
				$('.txtHint').html(result);
			}
		});
		
	});
});
</script>