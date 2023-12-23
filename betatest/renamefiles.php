<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");

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

if ( isset ($_POST["links"]) ){
	ini_set('max_execution_time', '10000'); 
	$token = "c7592f3d7e8a2c6682fb51ebd2e9d96f6uvoo";
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
	));
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
?>
<!DOCTYPE html>
<html>
<title>File Renamer - Q8Flix</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/logo.png">
<link rel="stylesheet" href="css/style1.css?dasd">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
html,body,h1,h2,h3,h4,h5,h6 {font-family: "Roboto", sans-serif}
.boxsizingBorder {
    -webkit-box-sizing: border-box;
       -moz-box-sizing: border-box;
            box-sizing: border-box;
}
	/* DivTable.com */
.divTable{
	display: table;
	width: 100%;
}
.divTableRow {
	display: table-row;
}
.divTableHeading {
	background-color: #EEE;
	display: table-header-group;
}
.divTableCell, .divTableHead {
	border: 1px solid #999999;
	display: table-cell;
	padding: 3px 10px;
}
.divTableHeading {
	background-color: #EEE;
	display: table-header-group;
	font-weight: bold;
}
.divTableFoot {
	background-color: #EEE;
	display: table-footer-group;
	font-weight: bold;
}
.divTableBody {
	display: table-row-group;
}
</style>

<body>
<!-- Page Container -->
<div class="w3-content" style="max-width:1300px">

  <!-- The Grid -->
  <div class="">
  <?php
include_once ("template/header.php");
?>
    <!-- Right Column -->
    <div class="w3-text-white" style="padding-top: 40px">
      <div class="w3-row-padding w3-padding-16 w3-center">
<h3>Enter UPTOBOX links below: </h3>
<form method="post" action="" enctype="multipart/form-data">

	<input type="hidden" style="width: 100%;" name="links" value="" placeholder="Start..."/>
	<input type="submit" name="submit" value="Rename Now" style="width: 100%">
	
</form>

<h3 style="text-align: center; color: burlywood">
	<?php 
	if ( isset ($_POST["links"]) )
	{
		echo "All Links have been renamed.";
	}
	?>
</h3>
    
  </div>
    </div>
<?php
include_once ("template/footer.php");
?>
    <!-- End Right Column -->
    </div>


</body>
</html>

