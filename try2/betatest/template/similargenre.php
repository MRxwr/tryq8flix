<?php
$sql = "SELECT * FROM category WHERE id like '$id'";
$result = $dbconnect->query($sql);
$row = $result->fetch_assoc();
$catgenre = explode (", ",$row["genre"]);
?>
<p>
<b style="color: white">You might also like: </b>
</p>
<?php 
$result = $dbconnect->query("SELECT collections FROM category WHERE id LIKE '$id'");
$row = $result->fetch_assoc();
$collections = explode(",",$row["collections"]);
$posters = array();
$i = 0 ;
while ( $i < sizeof($collections) ) 
{ 
	$result = $dbconnect->query("SELECT poster FROM category WHERE id LIKE '$collections[$i]'");
	$row = $result->fetch_assoc();
	$posters[] = $row["poster"];
	$i = $i + 1;
} 
	$i = 0 ;
while ( $i < sizeof($posters)) 
{ 
	if ( $collections[$i] !== "" )
	{	?>
		<a href="category.php?id=<?php echo $collections[$i] ?>"><div class="w3-quarterindex" style="padding: 3px;"><img src="<?php echo $posters[$i] ?>" alt="" id="imageindex"></a></div>
<?php
	}
	$i = $i + 1;
}
		  
// old stuff
$i = 0;
$y=0;
$repeatedvalue=0;
$checktitle[0]="";
$genres = count($catgenre);
$limitaion = 13 - sizeof($collections);
while ( $genres > 0 )
{	
	$sql = "SELECT * FROM category WHERE genre LIKE '%$catgenre[$i]%' AND type like '$categorytype' ORDER BY RAND() Limit 10";
	$result = $dbconnect->query($sql);
	if ( $result->num_rows > 0 )
	{
		while ( $row = $result->fetch_assoc() )
		{	
			if ( $y < $limitaion )
			{
				$repeatedvalue = array_search($row["title"], $checktitle, false);
				if ( $row["title"] == $categorytitle)
				{
				}
				elseif ( $checktitle[$repeatedvalue] != $row["title"] )
				{
					?>
					 <div class="w3-quarterindex" style="padding: 5px; align-items: center;"> <a href="category.php?id=<?php echo $row["id"] ?>"><img src="<?php echo $row["poster"] ?>" id="imageindex"></a></div>
			  <?php
					  $checktitle[$y] = $row["title"];
					  $checktitle[$repeatedvalue] = $checktitle[$y];  
				}
			}	
			$y++;
		}
	}
	$i++;
	$genres--;
}
?>
</p> 