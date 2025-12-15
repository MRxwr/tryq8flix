<?php
$sql1 = "SELECT * FROM category WHERE id LIKE '$id'";
$result = $dbconnect->query($sql1); 
$row = $result->fetch_assoc();
$trailer = $row["trailer"];
?>
</p>
<hr>
<?php 
if ( $row["description"] != "N/A" && $row["description"] != "" )
{ 	?> 
	<p>
	<div style="text-align: justify;">
	<?php 
	echo $description = str_replace ( "?singlequtation?", "'", $row["description"] ) 
	?>
	</div>
	</p>
	<hr>
	<?php 
} 
?>

<?php 
if ( $row["notes"] != "N/A" && $row["notes"] != "" )
{ 	?> 
	<p style="color:orange"><b>Notes: <?php echo $row["notes"] ?>
	</b></p>
	<hr>
	<?php 
} 
?>

<?php 
if ( $row["rating"] != "N/A" && $row["rating"] != "" )
{ 	?> 
	<p style="color:gold"><b>Rated: <?php echo $row["rating"] ?>
	</b></p>
	<hr>
	<?php 
} 
?>

<?php 
if ( $row["imdbrating"] != "N/A" && $row["imdbrating"] != "" )
{ 	?> 
	<p>IMDB Rating: <?php echo $row["imdbrating"] ?>
	</p>
	<hr>
	<?php 
} 
?>

<?php 
if ( $row["duration"] != "N/A" && $row["duration"] != "" )
{ 	?> 
	<p>Duration: <?php echo $row["duration"] ?>
	</p>
	<hr>
	<?php 
} 
?>

<?php 
if ( $row["genre"] != "N/A" && $row["genre"] != "" )
{ 	?> 
	<p>Genre: <?php echo $row["genre"] ?>
	</p>
	<hr>
	<?php 
} 
?>

<?php 
if ( $row["releasedate"] != "N/A" && $row["releasedate"] != "" )
{ 	?> 
	<p>Released: <?php echo $row["releasedate"] ?>
	</p>
	<hr>
	<?php 
} 
?>

<?php 
if ( $row["language"] != "N/A" && $row["language"] != "" )
{ 	?> 
	<p>Language: <?php echo $row["language"] ?>
	</p>
	<hr>
	<?php 
} 
?>

<?php 
if ( $row["country"] != "N/A" && $row["country"] != "" )
{ 	?> 
	<p>Country: <?php echo $row["country"] ?>
	</p>
	<hr>
	<?php 
} 
?>

<p>
<?php 
if ( $row["country"] != "N/A" && $row["country"] != "" )
{ 
	$channel = explode(",", str_replace ( "?singlequtation?", "'", $row["channel"] ));
	$i = 0; 
	while ( $i < sizeof($channel) )
	{ 
		echo '<a target="" href="search.php?search='.$channel[$i].'">'.$channel[$i].'</a>, ';
		$i = $i + 1;
	}
} 
?>
<hr>

