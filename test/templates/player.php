<div class="videos__container">
  <!-- Single Video starts -->
	<?php
	$videos = selectDB('posts',"`id` = {$_GET["video"]}");
	$category = selectDB('category',"`id` = '{$videos[0]["catid"]}'");
	$link = uptoboxChecker($videos[0]["id"]);
	?>
	<div class="row m-0 w-100">
		<div class="col-12">
			<div class="video__thumbnail">
			  <a href="?video=<?php echo $videos[0]["id"] ?>" target="_self" ><img src="<?php echo $videos[0]["poster"] ?>" alt=" " class="rounded"/><a/>
			</div>
			<div class="video__details">
			  <div class="author">
				<a href="?category=<?php echo $category[0]["id"] ?>" target="_self" ><img src="<?php echo $category[0]["poster"] ?>" alt="" /><a/>
			  </div>
			  <div class="title">
				<a href="?video=<?php echo $videos[0]["id"] ?>" target="_self" ><h3>
				  <?php echo substr($videos[0]["title"],0,50) ?>
				</h3><a/>
				<a href="?category=<?php echo $category[0]["id"] ?>" target="_self" ><?php echo $category[0]["title"] . " " ?></a>
				<span>IMDb:<?php echo $category[0]["imdbrating"] ?> • <?php echo $category[0]["releasedate"] ?></span>
			  </div>
			</div>
		</div>
		
		<div class="col-12 p-3">
			<video width="100%" height="350" controls >
			  <source src="<?php echo $link ?>" type="video/mp4">
			  <source src="<?php echo $link ?>" type="video/ogg">
			  Your browser does not support the video tag.
			</video>
		</div>
		
		<div class="col-12 p-3">
			<div class="row w-100 m-0 text-center">
				<div class="col-3 p-3">
					<a class="btn btn-secondary w-50">Download</a>
				</div>
				<div class="col-3 p-3">
					<a class="btn btn-primary w-50">Like</a>
				</div>
				<div class="col-3 p-3">
					<a class="btn btn-warning w-50">Watch later</a>
				</div>
				<div class="col-3 p-3">
					<a class="btn btn-dark w-50">Subscribe</a>
				</div>
			</div>
		</div>
	</div>
  <!-- Single Video Ends -->
</div>