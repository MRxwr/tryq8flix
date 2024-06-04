		  <?php 
		$result = $dbconnect->query("SELECT collections FROM category WHERE id LIKE '$id'");
		$row = $result->fetch_assoc();
		$collections = explode(",",$row["collections"]);

		$posters = array();
		if ( isset($collections[1]) )
		{
?>
		<h3 style="color: white" class="w3-center">Movie Collection</h3><hr>
<?php
		}
		$i = 0 ;
		while ( $i < sizeof($collections) ) 
		{ 
			if ( !empty($collections[$i]) ){
				$sql = "SELECT `poster`
						FROM `category`
						WHERE
						`id` LIKE '$collections[$i]'
						";
				$result = $dbconnect->query($sql);
				$row = $result->fetch_assoc();
				if ( $result->num_rows > 0 ){
					$posters[] = $row["poster"];
				}
				$ids[] = $collections[$i];
			}
			
			$i = $i + 1;
		} 
		$i = 0 ;
		while ( $i < sizeof($posters)) { 
			?>
			<a href="category.php?id=<?php echo $ids[$i] ?>"><div class="w3-quarterindex" style="padding: 3px;"><img src="<?php echo $posters[$i] ?>" alt="" id="imageindex"></a></div>
<?php
			$i = $i + 1;
		}
?>	