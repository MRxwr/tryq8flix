<div id="videoplayer">
	<video
	id="my-player"
	class="video-js vjs-theme-fantasy"
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
	"
	>
	<source id="myvideo" src="<?php echo $videolink ?>" type='video/mp4'>
	<source id="myvideo" src='<?php echo $videolink ?>' type='video/webm'>
	<source id="myvideo" src='<?php echo $videolink ?>' type='video/ogg'>
	<track default srclang="ar" label="Arabic" src="<?php echo $videosubtitle ?>">
	</video>
</div>

<script>
var vid = document.getElementById("my-player");

$(document).ready(function() {
	$.ajax({
		type:"POST",
		url: "api/liveVideo.php",
		data: {
			postId: <?php echo $_GET["postid"] ?>,
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
    setInterval(function(){ myCall(); }, 3000);
});


function myCall() {
	$.ajax({
		type:"POST",
		url: "api/liveVideo.php",
		data: {
			currentTime: vid.currentTime,
			postId: <?php echo $_GET["postid"] ?>,
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