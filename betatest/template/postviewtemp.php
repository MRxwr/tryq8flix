<?php 	
if ( $result->num_rows > 0 )
	{
		while ( $row = $result->fetch_assoc() )
		{
			$letnumtit = strlen($row["title"]); 
			$letnumcat = strlen($row["category"]); 
			$letnum = $letnumcat + $letnumtit + 1;

if ( $username != "admin" )
	{
			?>

<a target="new" href="watch.php?postid=<?php echo $row["id"] ?>&catid=<?php echo $row["catid"] ?>"><div class="w3-quarterindex" style="padding: 3px;  position: relative;text-align: center;color: white;">
    <img src="<?php echo $row["poster"] ?>" alt="" style="width:100%; height: 250px">
	  <div style="position: absolute;bottom: 1.5%;right: 1.5%;left: 1%;background: rgba(0, 0, 0, .8);"><b style="font-size: 20px;"><?php echo $row["category"] ." ". $row["title"]; ?></b>
</div></a>
	
  
              <?php 
}
	elseif ( $username == "admin" )
	{
		?>
	<a target="new" href="watch.php?postid=<?php echo $row["id"] ?>&catid=<?php echo $row["catid"] ?>"><div class="w3-quarterindex" style="padding: 3px;  position: relative;text-align: center;color: white;">
    <img src="<?php echo $row["poster"] ?>" alt="" style="width:100%; height: 250px">
	  <div style="position: absolute;bottom: 10.5%;right: 1.5%;left: 1%;background: rgba(0, 0, 0, .8);"><b style="font-size: 20px;"><?php echo $row["category"] ." ". $row["title"]; ?></b>
</div></a>
	<?php 
		 if ( $row["download"] != "" )
			  {
              		echo "<a  target='new' href='"; echo $row['download']; echo "'><img src='images/download.png' width='25' height='25'></a>";
			  }
			  if ( $row["subtitle"] != "" )
			  {
              		echo " <a  target='new' href='"; echo $row['subtitle']; echo "'><img src='images/subtitle.png' width='25' height='25'></a>";
			  }
	?>
    <a  href="includes/deletepostdb.php?id=<?php echo $row["id"] ?>&catid=<?php echo $row["catid"] ?>"><img src="images/delete.png" width="25" height="25"></a>
    <a  href="editpost.php?id=<?php echo $row["id"] ?>&catid=<?php echo $row["catid"] ?>"><img src="images/edit.png" width="25" height="25"></a>
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
    ?>
    
  </div>