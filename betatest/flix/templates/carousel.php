<div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-indicators">
  <?php
  $sql = "SELECT f.categoryId, c.*
		  FROM `favourites` as f 
		  JOIN `category` as c
		  ON f.categoryId = c.id
		  WHERE `userId` = 1
		  ";
  $result = $dbconnect->query($sql);
  for ( $i = 0 ; $i < $result->num_rows ; $i++ ){
  ?>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="<?php echo $i ?>" class="active" aria-current="true" aria-label="Slide 1"></button>
	<?php
  }
  ?>
  </div>
  <div class="carousel-inner">
  <?php
  $i = 0;
  $sql = "SELECT f.categoryId, c.*
		  FROM `favourites` as f 
		  JOIN `category` as c
		  ON f.categoryId = c.id
		  WHERE `userId` = 1
		  ";
  $result = $dbconnect->query($sql);
  while ( $row = $result->fetch_assoc() ){
	  if ( $i == 0 ){
		  $active = "active";
	  }else{
		  $active = "";
	  }
	  if ( !empty($row["header"]) ){
	  ?>
    <div class="carousel-item <?php echo $active ?>">
      <img src="<?php echo $row["header"] ?>" class="d-block w-100 headerImages" style="">
      <div class="carousel-caption d-none d-md-block">
        <a style="color:white; text-decoration:none" href="?id=<?php echo $row["id"] ?>"><h2><?php echo $row["title"] ?> <h3 style="color:gold">IMDb: <?php echo $row["imdbrating"] ?> | Year: <?php echo $row["releasedate"] ?> | <?php echo $row["genre"] ?></h3></h2></a>
        <h5><?php echo $row["description"] ?></h5>
      </div>
    </div>
  <?php
	  }
	$i++;
  }
  ?>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>