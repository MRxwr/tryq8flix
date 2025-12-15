<?php
if ( isset($username) AND in_array($username,$usernames) ){
	?>
	<br><a  href="includes/deletepostdb.php?id=<?php echo $postid ?>&catid=<?php echo $id ?>"><img src="images/delete.png" width="25" height="25"></a>
	<a  href="editpost.php?id=<?php echo $postid ?>&catid=<?php echo $id ?>"><img src="images/edit1.png" width="25" height="25"></a></p>
	<hr>
	<?php
}
?>