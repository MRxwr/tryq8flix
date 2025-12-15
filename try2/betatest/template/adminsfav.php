<div style="padding-top: 40px">
<div class="header01">

   <div class="details" style="text-align: center;">
   <div style="color:gold; font-size: 30px; text-align: center;"><?php echo strtoupper($row["title"]) ?></div>
      <div class="rating">IMDb: <?php echo $row["imdbrating"] ?></div>
      <div class="year"><?php echo $row["releasedate"] ?></div>
      <div class="seasons"><?php if ( strtolower($row["type"]) == "animov" ) { echo "ANIME MOVIE";} else{ echo strtoupper($row["type"]);} ?></div>
      <div class="description"><?php echo str_replace("?singlequtation?","'",substr($row["description"],0,200)) . " ..."; ?></div>
      <table style=" width:100%"><tr><td><a href="category.php?id=<?php echo $row["id"] ?>" class="myButton">▶ Play</a></td><td><a href="<?php echo $row["trailer"] ?>" class="myButton">Trailer</a></td></tr></table>
   </div>
</div>	  
</div>