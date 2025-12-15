<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");
$token = "c7592f3d7e8a2c6682fb51ebd2e9d96f6uvoo";

function searchFileName($data, $substring) {
    $result = "";
    foreach ($data as $index => $file) {
        if(strpos(strtolower(str_replace("-"," ",str_replace("."," ",$file['file_name']))), strtolower($substring)) !== false){
            $result = "https://uptobox.com/" . $file['file_code'];
        }
    }
    return $result;
}

if ( isset($_GET["count"]) ){
	$url = "https://uptobox.com/api/user/files?token={$token}&path=//&limit=100&orderBy=file_name";
	$return = file_get_contents($url);
	$json_a = json_decode($return, true);
	$_GET["website"] = !empty($_GET["website"]) ? $_GET["website"] : "";
	$titles = selectDB("category","`type` LIKE '%movie%' ORDER BY `id` DESC LIMIT {$_GET["count"]}");
	for( $i = 0; $i < sizeof($titles); $i++ ){
		$link = searchFileName($json_a["data"]["files"],str_replace("-"," ",str_replace(":","",$titles[$i]["title"])));
		$postData = array(
			"catid" => $titles[$i]["id"],
			"title" => "1080p - {$_GET["website"]}",
			"category" => $titles[$i]["title"],
			"poster" => $titles[$i]["poster"],
			"type" => $titles[$i]["type"],
			"videolink" => $link,
			"download" => $link
		);
		insertDB("posts",$postData);
		$getLatestId = selectDB("posts","`id` != '0' ORDER BY `id` DESC LIMIT 1");
		insertDB("postlinks",array("id"=>$getLatestId[0]["id"],"uptobox"=>$link));
	}
	header ("Location: latest.php");
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
	    <h2 style="color: gold"> How Mnay Movise? </h2>
		<form method="get" action="" enctype="multipart/form-data">
		<table style="width:100%">
			<tr>
				<td>
					<input type="text" name="count" value="" style="width:100%" placeholder="Exp. 2 , 3 , 10">
				</td>
				<td>
					<input type="text" name="website" value="" style="width:100%" placeholder="Exp. EGY, M4U, TUK">	
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" name="submit" value="Add Us Now" style="width:100%">
				</td>
			<tr>
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