<img src="<?php $newPoster = $row["poster"] ; echo $row["poster"] ?>" style="width:100%" alt="Avatar">

        </div>
        <div class="w3-container">
       <p><h2 style="text-align: center" ><?php echo $categoryTitle = $row["title"] ?></h2>
<?php 
if ( isset($username) AND !in_array($username,$usernames) )
{
?>
<div id="txtHint" ></div>
<?php
}elseif ( isset($username) AND in_array($username,$usernames) ){
	if ( isset($_GET["delPosts"]) ){
		$sql = "DELETE FROM `posts` WHERE  `catid` LIKE '".$_GET["id"]."'";
		$result = $dbconnect->query($sql);
	}
	
	if ( isset($username) AND in_array($username,$usernames) ){
		$result = $dbconnect->query("SELECT type FROM category WHERE id = '".$_GET["id"]."'");
		$row = $result->fetch_assoc();
		if ( strtolower($row["type"])  == "movie" ) {
			$eptitle = "1080p";
		}elseif ( strtolower($row["type"]) == "animov" ){
			$eptitle = "1080p";
		}elseif ( strtolower($row["type"]) == "tv-show" ){
			$result = $dbconnect->query("SELECT title FROM posts WHERE catid = '".$_GET["id"]."' ORDER BY title DESC");
			$row = $result->fetch_assoc();
			if ( strstr($row["title"],'E',true) != "" ){
				$eptitle = explode("E",$row["title"]);
				@$eptitle = $eptitle[0] . "E" . str_pad($eptitle[1]+1, 2, "0", STR_PAD_LEFT);
			}else{
				$eptitle = "S01E01";
			}
		}
		elseif ( strtolower($row["type"]) == "anime" ){
			$result = $dbconnect->query("SELECT * FROM posts WHERE catid = '".$_GET["id"]."' ORDER BY title DESC LIMIT 1");
			$row = $result->fetch_assoc();
			$findEP = strstr($row["title"],'EP',true);
			$findS = strstr($row["title"],'S',true);
			
			if ( $findEP === "" ){
				$eptitle = explode("EP",$row["title"]);
				@$eptitle = "EP" . str_pad($eptitle[1]+1, 4, "0", STR_PAD_LEFT);
			}
			elseif ( $findS === "" ){
				$result = $dbconnect->query("SELECT * FROM posts WHERE catid = '".$_GET["id"]."' ORDER BY title DESC");
				$row = $result->fetch_assoc();
				$eptitle = explode("E",$row["title"]);
				@$eptitle = $eptitle[0] . "E" . str_pad($eptitle[1]+1, 2, "0", STR_PAD_LEFT);
			}
			else{
				$eptitle = "S01E01";
			}
			
		}
		else{
			$eptitle = "";
		}
	}
?>
<div id="txtHint" ></div>

<form method="post" action="includes/postdb.php" enctype="multipart/form-data">

<table style="width:100%">
<tr>
<td>
<input type="text"   name="title"    value="<?php echo $eptitle ?>" style="">
<input type="text"   name="uptobox"  value="" style="" autofocus>
<input type="file" 	 name="subtitle" style="width:50%">
</td>
<td>
<input type="submit" name="submit"   value="Submit" style="width: 100px;height: 100px;">
</td>
</tr>
</table>
<input type="hidden" name="youtube"  value="">
<input type="hidden" name="mycima"   value="" >
<input type="hidden" name="posttime" value="<?php echo date("H:i:s") ?>" >
<input type="hidden" name="catid"    value="<?php echo $_GET["id"] ?>" >
<input type="hidden" name="postdate" value="<?php echo date("Y/m/d") ?>" >
<input type="hidden" name="poster"   value="<?php echo $newPoster ?>" >
<input type="hidden" name="category" value="<?php echo $categoryTitle ?>" >
<br><br>
</form>

<div align="center" class="w3-cell" style="width: 14.28%;">
<a href="addpost.php?catid=<?php echo $id ?>" style="text-decoration: none"><img src="images/addpost1.png" style="width: 30px; height: 30px;">
<div style="font-size: 10px">Single Posting</div></a>
</div>

<div align="center" class="w3-cell" style="width: 14.28%;">
<a href="autopost.php?id=<?php echo $id ?>" style="text-decoration: none"><img src="images/autoPost.png" style="width: 30px; height: 30px;">
<div style="font-size: 10px">Auto Posting</div></a>
</div> 

<div align="center" class="w3-cell" style="width: 14.28%;">
<a href="addmultipleposts.php?catid=<?php echo $id ?>" style="text-decoration: none"><img src="images/addmultipost1.png" style="width: 30px; height: 30px;">
<div style="font-size: 10px">Multi Posting</div></a>
</div>	

<br>

<div align="center" class="w3-cell" style="width: 14.28%;">
<a href="bulkpost.php?id=<?php echo $id ?>" style="text-decoration: none"><img src="images/multiedit.png" style="width: 30px; height: 30px;">
<div style="font-size: 10px">Manual Posting</div></a>
</div>

<div align="center" class="w3-cell" style="width: 14.28%;">
<a href="editcategory.php?id=<?php echo $id ?>" style="text-decoration: none"><img src="images/edit1.png" style="width: 30px; height: 30px;">
<div style="font-size: 10px">Edit Category</div></a>
</div> 
		  
<div align="center" class="w3-cell" style="width: 14.28%;">
<a href="editmultiposts?id=<?php echo $id ?>" style="text-decoration: none"><img src="https://i.imgur.com/5HiuQDr.jpg" style="width: 30px; height: 30px;">
<div style="font-size: 10px">Edit All Post</div></a>
</div>

<br>

<div align="center" class="w3-cell" style="width: 14.28%;">
<a href="includes/deletecatdb.php?id=<?php echo $id ?>" style="text-decoration: none"><img src="images/delete.png" style="width: 30px; height: 30px;">
<div style="font-size: 10px">Delete Category</div></a>
</div>

<div align="center" class="w3-cell" style="width: 14.28%;">
<a href="?id=<?php echo $id ?>&delPosts=1" style="text-decoration: none"><img src="images/delete.png" style="width: 30px; height: 30px;">
<div style="font-size: 10px">Delete All Post</div></a>
</div>

<div align="center" class="w3-cell" style="width: 14.28%;">
<a href="includes/updatetitlesdb11.php?id=<?php echo $id ?>" style="text-decoration: none"><img src="images/updatepoststitle1.png" style="width: 30px; height: 30px;">
<div style="font-size: 10px">Update Titles</div></a>
</div>



<?php
}
?>