<div style="padding-bottom: 15%;"></div>

<?php 

if ( isset($username) AND !in_array($username,$usernames) )
{
	?>
<footer class="w3-container w3-cell-row" style="border-top-left-radius: 20px;border-top-right-radius: 20px;position: fixed;bottom: 0;width: 100%;background-color: #2E2E2E;color: white;text-align: center; max-width: 1300px;">
  <div class="w3-cell-row" style="width: 100%; padding-top: 3px">

	  <div align="center" class="w3-cell" style="width: 16.6%;">
	  <a href="index.php"><img src="images/home1.png" style="width: 25px;"></a><div style="font-size: 10px">Home</div>
	  </div>
	  
	  <div align="center" class="w3-cell" style="width: 16.6%;">
	  <a href="<?php if ( !isset($mobileWidth) ){ echo "newmovies.php";}else{echo "cate?type=movie" ;} ?>"><img src="images/movies.png" style="width: 25px;"></a><div style="font-size: 10px">Movies</div>
	  </div>
	  
	  <div align="center" class="w3-cell" style="width: 16.6%;">
	  <a href="<?php if ( !isset($mobileWidth) ){ echo "tvshow.php";}else{echo "cate?type=tv-show" ;} ?>"><img src="images/tvshows.png" style="width: 25px;"></a><div style="font-size: 10px">TV-Shows</div>
	  </div>
	  
	  <div align="center" class="w3-cell" style="width: 16.6%;">
	  <a href="<?php if ( !isset($mobileWidth) ){ echo "anime.php";}else{echo "cate?type=anime" ;} ?>"><img src="images/animes.png" style="width: 25px;"></a><div style="font-size: 10px">Animes</div>
	  </div>
	  
	  <div align="center" class="w3-cell" style="">
	  <a href="<?php if ( !isset($mobileWidth) ){ echo "wrestling.php";}else{echo "cate?type=wrestling" ;} ?>"><img src="images/wrestling.png" style="width: 25px;"></a><div style="font-size: 10px">Wrestling</div>
	  </div>
	  
	  <div align="center" class="w3-cell" style="width: 16.6%;">
	  <a href="profile.php"><img src="images/profile1.png" style="width: 30px;"></a><div style="font-size: 10px">Profile</div>
	  </div>
	  
	</div>
</footer>

<?php 
}
elseif ( isset($username) AND in_array($username,$usernames) )
{
	?>
<footer class="w3-container w3-cell-row" style="border-top-left-radius: 20px;border-top-right-radius: 20px;position: fixed;bottom: 0;width: 100%;background-color: #2E2E2E;color: white;text-align: center; max-width: 1300px;">
  <div class="w3-cell-row" style="width: 100%; padding-top: 3px">
	  
	  <div align="center" class="w3-cell" style="width: 16.6%;">
	  <a href="addcategory1.php"><img src="images/imdb21.png" style="width: 45px;"></a><div style="font-size: 10px">Add IMDB</div>
	  </div>
	  
	  <div align="center" class="w3-cell" style="width: 16.6%;">
	  <a href="addcategory.php"><img src="images/addcat.png" style="width: 25px;"></a><div style="font-size: 10px">Category</div>
	  </div>
	  
	  <div align="center" class="w3-cell" style="width: 16.6%;">
	  <a href="index.php"><img src="images/home1.png" style="width: 25px;"></a><div style="font-size: 10px">Home</div>
	  </div>
	  
	  <div align="center" class="w3-cell" style="width: 16.6%;">
	  <a href="profile.php"><img src="images/profile1.png" style="width: 30px;"></a><div style="font-size: 10px">Profile</div>
	  </div>
	  
	  <div align="center" class="w3-cell" style="width: 16.6%;">
	  <a href="https://tryq8flix.com/getlinks.php"><img src="https://i.imgur.com/3qawygU.png" style="width: 25px;"></a><div style="font-size: 10px">Grabber</div>
	  </div>
	  
	  <div align="center" class="w3-cell" style="">
	  <a href="logout.php"><img src="images/logout1.png" style="width: 25px;"></a><div style="font-size: 10px">Exit</div>
	  </div>
	  
	</div>
</footer>

<?php
}
?>

<script>
/*
  function initFreshChat() {
    window.fcWidget.init({
      token: "91daf21e-6eb5-42ee-92cd-f167a2d6b9de",
      host: "https://wchat.freshchat.com"
    });
  }
  function initialize(i,t){var e;i.getElementById(t)?initFreshChat():((e=i.createElement("script")).id=t,e.async=!0,e.src="https://wchat.freshchat.com/js/widget.js",e.onload=initFreshChat,i.head.appendChild(e))}function initiateCall(){initialize(document,"freshchat-js-sdk")}window.addEventListener?window.addEventListener("load",initiateCall,!1):window.attachEvent("load",initiateCall,!1);
  */
</script>