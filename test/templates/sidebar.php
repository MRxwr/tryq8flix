<div class="sidebar">
	<div class="sidebar__categories">
	  <div class="sidebar__category">
		<i class="material-icons">home</i>
		<span>Home</span>
	  </div>
	  <div class="sidebar__category">
		<i class="material-icons">local_fire_department</i>
		<span>Trending</span>
	  </div>
	  <div class="sidebar__category">
		<i class="material-icons">subscriptions</i>
		<span>Subcriptions</span>
	  </div>
	</div>
<hr />
	<div class="sidebar__categories">
	<?php
	$categories = selectDB("categorieslist",'');
	for ($i=0; $i<sizeof($categories); $i++){
		if (strtolower($categories[$i]["title"]) == "animov"){
			$title = "Anime Movies";
		}else{
			$title = $categories[$i]["title"];
		}
	?>
		<a href="?type=<?php echo $categories[$i]["title"] ?>" target="_self" style="text-decoration:none">
	  <div class="sidebar__category">
		<i class="material-icons"><?php echo substr($title,0,1) ?></i>
		<span><?php echo $title ?></span>
	  </div>
		<a>
	<?php
	}
	?>
	</div>
<hr />
	<div class="sidebar__categories">
	  <div class="sidebar__category">
		<i class="material-icons">library_add_check</i>
		<span>Library</span>
	  </div>
	  <div class="sidebar__category">
		<i class="material-icons">history</i>
		<span>History</span>
	  </div>
	  <div class="sidebar__category">
		<i class="material-icons">play_arrow</i>
		<span>Your Videos</span>
	  </div>
	  <div class="sidebar__category">
		<i class="material-icons">watch_later</i>
		<span>Watch Later</span>
	  </div>
	  <div class="sidebar__category">
		<i class="material-icons">thumb_up</i>
		<span>Liked Videos</span>
	  </div>
	</div>
<hr />
</div>