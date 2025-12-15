<?php
$sql = "SELECT
		pl.* , p.subtitle as subtitle, p.catid, p.poster, p.id as realId
		FROM `postlinks` as pl
		JOIN `posts` as p
		ON 
		pl.id = p.id
		WHERE p.id LIKE '".$_GET["videoId"]."'
		";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$videolink = $row["uptobox"];
$videosubtitle = $row["subtitle"];
$_GET["catid"] = $row["catid"];
$_GET["postid"] = $row["realId"];
$postPoster = $row["poster"];

if ( strstr($videolink,"uptobox.com/") === false ){
	$streamlink = $videolink;
}else{
	$streamlink = explode("uptobox.com/",$videolink);
	$streamlink = $streamlink[1];
}

$string = file_get_contents("https://uptobox.com/api/user/me?token=".$uptoboxToken);
$json_a = json_decode($string, true);
if ( isset($json_a["data"]["premium"]) AND $json_a["data"]["premium"] == 1 ){
	$string = file_get_contents("https://uptobox.com/api/link?token=".$uptoboxToken."&file_code=".$streamlink);
	$json_a = json_decode($string, true);
	if ( !isset($json_a["data"]["dlLink"]) OR empty($json_a["data"]["dlLink"]) ){
		if ( isset($username) AND in_array($username,$usernames) ){
			$string = "";
		}else{
			header("Location: ../reportBrokenLink.php?catid=".$_GET["catid"]."&postid=" . $_GET["postid"] );
			die();
		}
	}else{
		$videolink = $json_a["data"]["dlLink"];
	}
}else{
	header("LOCATION: ../maintenance.php");
	die();
}

$sql = "SELECT `videoId`
		FROM `watchedvideos`
		WHERE
		`videoId` = '".$_GET["videoId"]."'
		AND
		`userId` = '".$userID."'
		";
$result = $dbconnect->query($sql);
if ( $result->num_rows < 1 ){
	$sql = "INSERT INTO `watchedvideos`
			(`userId`, `categoryId`, `videoId`)
			VALUES
			('".$userID."', '".$_GET["catid"]."', '".$_GET["videoId"]."')
			";
	$result = $dbconnect->query($sql);
}else{
	date_default_timezone_set("Asia/Kuwait");
	$nowDate = date("Y-m-d H:i:s");
	$sql = "UPDATE `watchedvideos`
			SET
			`date` = '".$nowDate."'
			WHERE
			`videoId` = '".$_GET["videoId"]."'
			AND
			`userId` = '".$userID."'
			";
	$result = $dbconnect->query($sql);
}

?>
<div class="row w-100 m-0 p-0">
	<div class="col-12 p-0 m-0">
	<style>
	.vjs-control-bar { font-size: 100% }
	</style>
	<video
    id="my-player"
    class="video-js vjs-theme vjs-control-bar"
    controls
    preload="auto"
    poster="<?php echo $postPoster  ?>"
    data-setup='{}'
	height="350px"
	style="
	position: block;
    top: 0;
    left: 0;
    width: 100%;
	height: 98vh;
	background-color: black;
	"
	>
		  <source src="<?php echo $videolink ?>.mp4"></source>
		  <source src="<?php echo $videolink ?>.mp4"></source>
		  <track default srclang="ar" label="Arabic" src="<?php echo $videosubtitle ?>">
		</video>
	</div>
</div>

<script>
var vid = document.getElementById("my-player");

$(document).ready(function() {
	$.ajax({
		type:"POST",
		url: "../api/liveVideo.php",
		data: {
			postId: <?php echo $_GET["videoId"] ?>,
			userId: <?php echo $userID ?>,
			playFrom : 1,
		},
		success:function(result){
			vid.currentTime=parseFloat(result);
			//console.log(result);
		}
	});
});

$(function(){
    setInterval(function(){ myCall(); }, 1000);
});


function myCall() {
	$.ajax({
		type:"POST",
		url: "../api/liveVideo.php",
		data: {
			currentTime: vid.currentTime,
			postId: <?php echo $_GET["videoId"] ?>,
			userId: <?php echo $userID ?>,
		},
		success:function(result){
			//console.log(result);
		}
	});
};

function getCurTime() { 
  alert(vid.currentTime);
} 

function setCurTime() { 
  vid.currentTime=5;
} 

</script>