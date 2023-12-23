<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");

function extractCodeFromLink($link) {
    $pattern = "/[^\/]*$/";
    preg_match($pattern, $link, $matches);
    return $matches[0];
}

function callUptobox($data){
	$url = 'https://uptobox.com/api/user/files';
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PATCH");
	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_URL, $url);
	$result = curl_exec($curl);
	curl_close($curl);
}

if ( isset($_POST["start"]) ){
	$catid = $_POST["catid"];
	$urls = $_POST["videolink"]; 
	$array = explode(PHP_EOL, $urls);
	$categoy = selectDB("category","`id` = '{$_POST["catid"]}'");
	ini_set('max_execution_time', '10000'); 
	$token = "c7592f3d7e8a2c6682fb51ebd2e9d96f6uvoo";
	for ( $i = 0; $i < sizeof($array); $i++ ){
		if( !empty($array[$i]) ){
			$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
			$link = extractCodeFromLink($array[$i]);
			$curl = curl_init();
			curl_setopt_array($curl, array(
					CURLOPT_URL => "https://uptobox.com/api/link/info?fileCodes={$link}",
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'GET',
				)
			);
			$response = curl_exec($curl);
			curl_close($curl);
			$response = json_decode($response,true);

			$title = strtolower($response["data"]["list"][0]["file_name"]);
			if ( strpos($title,"s0") !== false && !empty($title) ){
				$newtitle = explode("s0",$title);
				$newtitle1 = explode("e",$newtitle[1]);
				$titleNo = "s0" . $newtitle1[0] . "e" ;
				$epiNo = $newtitle1[1];
				for( $y = 0; $y < strlen($epiNo); $y++ ){
					if ( is_numeric($epiNo[$y]) ){
						$titleNo = $titleNo . $epiNo[$y];
					}
				}
			}
			if ( strpos($title,"s1") !== false && !empty($title) ){
				$newtitle = explode("s1",$title);
				$newtitle1 = explode("e",$newtitle[1]);
				$titleNo = "s1" . $newtitle1[0] . "e" ;
				$epiNo = $newtitle1[1];
				for( $y = 0; $y < strlen($epiNo); $y++ ){
					if ( is_numeric($epiNo[$y]) ){
						$titleNo = $titleNo . $epiNo[$y];
					}
				}
			}
			if ( strpos($title,".e") !== false && strpos($title,"s0") === false && strpos($title,"s1") === false && !empty($title) ){
				$newtitle = explode(".e",$title);
				$newtitle1 = explode(".",$newtitle[1]);
				$titleNo = "s01e" . $newtitle1[0];
			}
			$postData = array(
				"catid" => $_POST["catid"],
				"download" => $array[$i],
				"videolink" => $array[$i],
				"title" => strtoupper($titleNo),
				"category" => $categoy[0]["title"],
				"poster" => $categoy[0]["poster"],
				"subtitle" => "",
				"type" => $categoy[0]["type"]
			);
			print_R($postData);echo"<br>";
			insertDB("posts",$postData);
			$getLatestId = selectDB("posts","`id` != '0' ORDER BY `id` DESC LIMIT 1");
			insertDB("postlinks",array("id"=>$getLatestId[0]["id"],"uptobox"=>$array[$i]));
		}
		// get file codes from uptobox \\
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://uptobox.com/api/user/files?token={$token}&path=//&limit=100&orderBy=file_name",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			)
		);
		$response = curl_exec($curl);
		curl_close($curl);
		$response = json_decode($response,true);
		$filecode = $response["data"]["files"];

		if( isset($filecode) && sizeof($filecode) > 0 ){
			for( $i = 0; $i < sizeof($filecode); $i++ ){
				// rename files in uptobox \\ 
				$data = array(
					"token" => $token,
					"file_code" => $filecode[$i]["file_code"],
					"new_name" => rand(),
					"public" => 0,
				);
				callUptobox($data);
				// move files to new destination \\
				$data = array(
					"token" => $token,
					"file_codes" => $filecode[$i]["file_code"],
					"destination_fld_id" => 918683715,
					"action" => 'move'
				);
				callUptobox($data);
			}
		}
	}
	header ("Location: category.php?id={$_POST["catid"]}");
}
?>

<!DOCTYPE html>
<html>
<title>Add Multiple Posts - Q8Flix</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/logo.png">
<link rel="stylesheet" href="css/style1.css?dsadsa">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
html,body,h1,h2,h3,h4,h5,h6 {font-family: "Roboto", sans-serif}
</style>

<body>

<!-- Page Container -->
<div class="w3-content" style="max-width:1300px">
<?php
require ("template/header.php");
?>
  <!-- The Grid -->
  <div class="">
 

    <!-- Right Column -->
    <div class="w3-text-white" style="padding-top: 40px;">
      <div class="w3-row-padding w3-padding-16 w3-center" id="food" >
	  <h2 style="color: gold"> INSERT LINKS TO BE POSTED </h2>
		<form method="post" action="" enctype="multipart/form-data">
			<table style="width:100%">
			<tr><td><textarea name="videolink" style="width: 100%; height: 300px;"></textarea></td></tr>
			<tr><td><input type="submit" name="submit" value="Post Them Now" style="width:100%">
					<input type="hidden" name="catid" value="<?php echo $_GET["id"] ?>">
					<input type="hidden" name="start" value="post"></td><tr>
			</table>
		</form>
	  
	  </div>
  	</div>
<?php 
require ("template/footer.php"); 
?>
  </div>
</div>

</body>
</html>