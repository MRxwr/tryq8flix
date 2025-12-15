<?php
$sql = "SELECT `postId`
		FROM `history`
		WHERE
		`userId` = '".$userid."'
		";
$result = $dbconnect->query($sql);
while ( $row = $result->fetch_assoc() ){
	$watchedposts[] = $row["postId"];
}

$sql = "SELECT * FROM category WHERE id LIKE '$id'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$type = $row["type"];
$type = strtolower($type);
	
$categorylist = array();
$sql = "SELECT * FROM categorieslist";
$result = $dbconnect->query($sql);
while ( $row = $result->fetch_assoc() )
{
	if ( !in_array($row["title"], $categorylist) AND (strstr($type,"tv-show") OR strstr($type,"anime")) )
	{
		$categorylist[] = strtolower($row["title"]);
	}
}
	if ( in_array($type, $categorylist) )
{
	$seasonnum = array();
	$sql = "SELECT * FROM posts WHERE catid LIKE '$id' ORDER BY title ASC";
	$result = $dbconnect->query($sql);
	$row = $result->fetch_assoc();
		if ( strstr($row["title"],'S') )
		{
			while ( $row = $result->fetch_assoc() )
			{
			$seasonnumberfromtitle = explode("E",$row["title"]);
			$seasonnumber = explode("S",$seasonnumberfromtitle[0]);
			if ( !in_array($seasonnumberfromtitle[0], $seasonnum) )
			{
				$seasonnum[] = $seasonnumberfromtitle[0];
			}
			}
		}
		if ( sizeof($seasonnum) > 1 )
		{
	?>
	  <h3 style="color: white">Filter By Season:</h3>
			<select onchange="location = this.options[this.selectedIndex].value;" style="width: 100%;">
			<option>Please select</option>
				<?php
	$i = 0 ;
sort($seasonnum);
$seasonnum = array_map('ucfirst', $seasonnum);
$seasonnum = array_filter($seasonnum);
	while ( $i < sizeof($seasonnum))
	{
		?>
	  
	<option value="category.php?postnum=<?php echo $seasonnum[$i] ?>&id=<?php echo $id ?>"><?php echo str_replace ( "Season", "Season ", str_replace("S","Season",$seasonnum[$i] ))?></option>
				<?php
		$i = $i + 1 ;
	}
		?>
				
			</select><hr>
	  
	  <?php
}
	}
	?>

<?php
if ( $postnum != "" )
{
$sql = "SELECT * FROM posts WHERE catid LIKE '$id' AND title LIKE '%$postnum%' ORDER BY title ASC";
$result = $dbconnect->query($sql);

if ( $result->num_rows > 0 )
{
	?>
	  <h3 style="color: white">Season <?php $snumber = explode("S",$postnum); echo $snumber[1] ?></h3><hr>
	  <?php
	while ( $row = $result->fetch_assoc() )
	{
	  $letnumtit = strlen($row["title"]); 
	  $letnum = $letnumtit + 1;
	  ?>
	  <div class="w3-half" style="padding: 3px;  position: relative;text-align: center;color: white;">
	  <a href="watch.php?postid=<?php echo $row["id"] ?>&catid=<?php echo $id ?>" class="<?php if ( in_array( $row["id"] , $watchedposts ) ) {echo 'myButtongreen';} else { echo 'myButton'; } ?>"><?php echo $row["title"] ?>
	  </a>
			
	  <?php 
		if ( isset($username) AND in_array($username,$usernames) )
		{
		?>
			<a  href="includes/deletepostdb.php?id=<?php echo $row["id"] ?>&catid=<?php echo $id ?>"><img src="images/delete.png" width="25" height="25"></a>
			<a  href="editpost.php?id=<?php echo $row["id"] ?>&catid=<?php echo $id ?>"><img src="images/edit1.png" width="25" height="25"></a>
		<?php
		}
		?>

		  
</div>
	<?php	
	}
}
}
else
{
	$sql = "SELECT id,title,download
			FROM `posts`
			WHERE catid LIKE '$id' 
			ORDER BY title DESC
			";
	$result = $dbconnect->query($sql);

	if ( $result->num_rows > 0 )
	{
		?>
		  <h3 style="color: white">All Titles</h3><hr><p></p>
		  <?php
		while ( $row = $result->fetch_assoc() )
		{

			$letnumtit = strlen($row["title"]); 
			$letnum = $letnumtit + 1;
			?>
			<div class="w3-half" style="padding: 3px;  position: relative;text-align: center;color: white;"><a href="watch.php?postid=<?php echo $row["id"] ?>&catid=<?php echo $id ?>" class="<?php if ( in_array( $row["id"] , $watchedposts ) ) {echo 'myButtongreen';} else { echo 'myButton'; } ?>"><?php echo $row["title"] ?></a>

			<?php
			if ( isset($username) AND in_array($username,$usernames) )
			{
			?>
						<a style="text-decoration:none" href="includes/deletepostdb.php?id=<?php echo $row["id"] ?>&catid=<?php echo $id ?>">
						<img src="images/delete.png" width="25" height="25">
						</a>
						
						<a style="text-decoration:none" href="editpost.php?id=<?php echo $row["id"] ?>&catid=<?php echo $id ?>">
						<img src="images/edit1.png" width="25" height="25">
						</a>
						
						<a style="text-decoration:none" href="<?php echo $row["download"] ?>">
						<img src="images/download.png" width="25" height="25">
						</a>

			<?php
			}
					?>
			</div>
		<?php	
		}
	}
	else
	{
		echo "No shows has been added yet.";
	}
}
?>