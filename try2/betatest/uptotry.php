<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");

if ( isset($_GET["run"]) )
{
	$url = "https://uptobox.com/api/user/files?token=c7592f3d7e8a2c6682fb51ebd2e9d96f6uvoo&path=//&limit=100&orderBy=file_name";
	$return = file_get_contents($url);
	$json_a = json_decode($return, true);
	
	for ( $i = 0 ; $i < sizeof($json_a["data"]["files"]) ; $i++ ){
		$uptobox = "https://uptobox.com/".$json_a["data"]["files"][$i]["file_code"];
		$filename = strtolower($json_a["data"]["files"][$i]["file_name"]);
		$title = $filename;
		if ( strstr($filename,"]") === false ){
			$filename1[1] = $filename;
		}else{
			$filename1 = explode("]", $filename);
		}
		/*
		$filename = explode("s0",$filename1[1]);
		if ( sizeof($filename) <= 0 ){
			$filename = explode("s01",$filename1[1]);
			if ( sizeof($filename) <= 0 ){
				$filename = explode("s02",$filename1[1]);
				if ( sizeof($filename) <= 0 ){
					$filename = explode("s03",$filename1[1]);
					if ( sizeof($filename) <= 0 ){
						$filename = explode("s04",$filename1[1]);
						if ( sizeof($filename) <= 0 ){
							$filename = explode("s05",$filename1[1]);
							if ( sizeof($filename) <= 0 ){
								$filename = explode("s06",$filename1[1]);
							}
						}
					}
				}
			}
		}
		*/
		for ($y = 0 ; $y < 50 ; $y++ ){
			$number = (string)"s".str_pad($y,2,"0",STR_PAD_LEFT);
			if( strpos($filename,$number) !== false ){
				$filename = explode("s".str_pad($y,2,"0",STR_PAD_LEFT),$filename1[1]);
				goto filename;
			}
		}
		filename:
		$filename = $filename[0];
		$filename = explode("20", $filename);
		$filename = $filename[0];
		
		$sql = "SELECT *
				FROM `category`
				WHERE
				`title` LIKE '".trim(str_replace("."," ",$filename))."'
				LIMIT 1
				";
		$result = $dbconnect->query($sql);

		if ( $result->num_rows > 0 ){
			while ( $row = $result->fetch_assoc() ){
				$catid = $row["id"];
				$categories = $row["title"];
				$category = $row["title"];
				$poster = $row["poster"];
				$posttime = date("g:i A");
				$postdate = date("Y/m/d");
				$subtitle = "";
				$type = $row["type"];
			}
		}else{
			goto jump;
		}
		
		$newtitle = explode($number,$title);
		$newtitle1 = explode("e",$newtitle[1]);
		$titleNo = $number . str_replace(" ", "", $newtitle1[0]) . "e" ;
		$epiNo = $newtitle1[1];
		$y = 0;
		while ( $y < strlen($epiNo) )
		{
			if ( is_numeric($epiNo[$y]) )
			{
				$titleNo = $titleNo . $epiNo[$y];
			}
			else
			{
				break;
			}
			$y++;
		}
		$title = str_replace(".", "",strtoupper($titleNo));
		
		$sql = "SELECT `id`
				FROM `posts`
				ORDER BY `id` DESC
				LIMIT 1
				";
		$result = $dbconnect->query($sql);
		$row = $result->fetch_assoc();
		$id = $row["id"]+1;
		
		$sql = "INSERT INTO posts
				(`id`, `catid`, `title`, `category`, `type`, `views`, `poster`, `subtitle`, `videolink`, `download`)
				VALUES
				('$id', '$catid', '$title', '$categories', '$type', '0', '$poster', '$subtitle', '$uptobox', '$uptobox')
				";
		$result = $dbconnect->query($sql);

		$sql = "INSERT INTO postlinks
				(`id`, `uptobox`, `youtube`, `mycima`)
				VALUES
				('$id', '$uptobox', '', '')
				";

		$result = $dbconnect->query($sql);

		jump:
		//echo "<br>";
	}

	header ("Location: tvshow.php");
}

?>
<!DOCTYPE html>
<html>
<title>Auto Add Movies - Q8FLiX</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/logo.png">
<link rel="stylesheet" href="css/style1.css?dsdsa">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
html,body,h1,h2,h3,h4,h5,h6 {font-family: "Roboto", sans-serif}
</style>
<body>

<!-- Page Container -->
<div class="w3-content" style="max-width: 1300px">

  <!-- The Grid -->
  <div class="">
 
<?php
require ("template/header.php");
?>
    <!-- Right Column -->
    <div class="w3-text-white" style="padding-top: 40px">
      <div class="w3-row-padding w3-padding-16 w3-center" id="food" >
	    <h2 style="color: gold"> Start moving files from uptobox to tryq8flix? </h2>
		<form method="get" action="" enctype="multipart/form-data">
		<table style="width:100%">
			<tr>
				<td>
					<input type="submit" name="run" value="Start" >
				</td>
			</tr>
		</table>
		</form>
	  </div>
    </div>
    
  <!-- End Grid -->
  </div>
  
  <!-- End Page Container -->


<?php
require ("template/footer.php");
?>
</div>
</body>
</html>