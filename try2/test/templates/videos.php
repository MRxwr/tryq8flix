<div class="videos__container">
  <!-- Single Video starts -->
  <?php
	if ( isset($_GET["type"]) && !empty($_GET["type"]) ){
		$type = "`type` = '{$_GET["type"]}' AND `poster` != ''";
	}else{
		$type = "`poster` != ''";
	}
  $videos = selectDB('posts',"{$type} ORDER BY `id` DESC LIMIT 30");
  for ( $i=0; $i<sizeof($videos) ; $i++){
	  $category = selectDB('category',"`id` = '{$videos[$i]["catid"]}'");
	  ?>
  <div class="video">
	<div class="video__thumbnail">
	  <a href="?video=<?php echo $videos[$i]["id"] ?>" target="_self" ><img src="<?php echo $videos[$i]["poster"] ?>" alt=" " class="rounded"/><a/>
	</div>
	<div class="video__details">
	  <div class="author">
		<a href="?category=<?php echo $category[0]["id"] ?>" target="_self" ><img src="<?php echo $category[0]["poster"] ?>" alt="" /><a/>
	  </div>
	  <div class="title">
		<a href="?video=<?php echo $videos[$i]["id"] ?>" target="_self" ><h3>
		  <?php echo substr($videos[$i]["title"],0,50) ?>
		</h3><a/>
		<a href="?category=<?php echo $category[0]["id"] ?>" target="_self" ><?php echo $category[0]["title"] . " " ?></a>
		<span>IMDb:<?php echo $category[0]["imdbrating"] ?> • <?php echo $category[0]["releasedate"] ?></span>
	  </div>
	</div>
  </div>
  <?php
  }
  ?>
  <!-- Single Video Ends -->
</div>