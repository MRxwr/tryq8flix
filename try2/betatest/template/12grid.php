<?php
if( $listOfPosts = selectDB("posts","`status` = '0' AND `type` LIKE '%{$categoryout}%' GROUP BY `catid` ORDER BY  MAX(`id`) DESC LIMIT 5") ){
	for ( $i = 0 ; $i < sizeof($listOfPosts) ; $i++ ){
	$listOfCategories = selectDB("category","`id` = '{$listOfPosts[$i]["catid"]}'");
	$getTitles = selectDB("posts","`catid` = '{$listOfPosts[$i]["catid"]}' ORDER BY `title` DESC LIMIT 1");
	$glose = "{$listOfCategories[0]["title"]} ({$listOfCategories[0]["releasedate"]}) ";
	?>
		<a target="" class="tags" glose="<?php echo $glose; ?>" href="category.php?id=<?php echo $listOfCategories[0]["id"] ?>">
		<div class="w3-quarterindex" style="padding: 3px;  position: relative;text-align: center;color: white;">
		<img src="<?php echo $listOfCategories[0]["poster"] ?>" alt="" id="imageindex">
		<div style="position: absolute;bottom: 1.5%;right: 1.5%;left: 1%;background: rgba(0, 0, 0, .8);">
		<b id="fontindex"><?php if ( $categoryout != "movie" AND $categoryout != "animov" ){ echo $getTitles[0]["title"];} else { echo "<b style='color:yellow'>IMDb: </b>" . $listOfCategories[0]["imdbrating"];} ?></b>
		</div>
		</a>
		</div>
	<?php
	}
}
?>