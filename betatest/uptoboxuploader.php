<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");

$url = 'https://uptobox.com/api/upload';
$data = [
    'token' => 'c7592f3d7e8a2c6682fb51ebd2e9d96f6uvoo'
];

$curl = curl_init();
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_URL, $url);

$result = curl_exec($curl);
curl_close($curl);

$result = explode('uploadLink":"',$result);
$result = explode('"',$result[1]);
$result = str_replace("upload","remote",$result[0]);
?>
<!DOCTYPE html>
<html>
<title>Uploader - Q8Flix</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/logo.png">
<link rel="stylesheet" href="css/style1.css?dasd">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
		.deleteLink,.sizeInfo,.text,.progressInfo,.eta
		{
			display: none;
		}
		textarea
		{
			width: 100%;
			height: 300px;
		}
		button
		{
			-moz-box-shadow:inset 0px 1px 0px 0px #ffffff;
			-webkit-box-shadow:inset 0px 1px 0px 0px #ffffff;
			box-shadow:inset 0px 1px 0px 0px #ffffff;
			background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, darkred), color-stop(1, #dfdfdf));
			background:-moz-linear-gradient(top, darkred 5%, red 100%);
			background:-webkit-linear-gradient(top, darkred 5%, red 100%);
			background:-o-linear-gradient(top, darkred 5%, red 100%);
			background:-ms-linear-gradient(top, darkred 5%, red 100%);
			background:linear-gradient(to bottom, darkred 5%, red 100%);
			filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='darkred', endColorstr='red',GradientType=0);
			background-color:darkred;
			-moz-border-radius:6px;
			-webkit-border-radius:6px;
			border-radius:6px;
			border:1px solid #2d2d2d;
			display:inline-block;
			cursor:pointer;
			color:palegoldenrod;
			font-family:Arial;
			font-size:15px;
			padding:6px 24px;
			text-decoration:none;
			text-shadow:0px 1px 0px #000000;
			width: 100%;
		}
		button:hover 
		{
			background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #dfdfdf), color-stop(1, darkred));
			background:-moz-linear-gradient(top, red 5%, darkred 100%);
			background:-webkit-linear-gradient(top, red 5%, darkred 100%);
			background:-o-linear-gradient(top, red 5%, darkred 100%);
			background:-ms-linear-gradient(top, red 5%, darkred 100%);
			background:linear-gradient(to bottom, red 5%, darkred 100%);
			filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='red', endColorstr='darkred',GradientType=0);
			background-color:red;
		}
		button:active 
		{
			position:relative;
			top:1px;
		}

		html,body,h1,h2,h3,h4,h5,h6 {font-family: "Roboto", sans-serif}
		.boxsizingBorder 
		{
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
	<h3>Upload to UPtoBOX: </h3>
<div class='max d-flex column'>
<div id='content' style="width: 100%" data-ui='{&quot;sendToUI&quot;:{&quot;promoEnd&quot;:1615816984},&quot;uploadUrl&quot;:&quot;<?php echo $result ?>&quot;,&quot;text&quot;:{&quot;start_upload&quot;:&quot;Start upload&quot;,&quot;one_url_per_line&quot;:&quot;Copy the above generated links here&quot;}}'>
<div id='homepage'>
<div class='remote-url-content'></div>
</div>
</div>

<h3><a target="" href="">Back To UPtoBOX Uploader</a></h3>

  </div>
    </div>
<?php
include_once ("template/footer.php");
?>
    <!-- End Right Column -->
    </div>

<script type='text/javascript' 
src="https://uptobox.com/dist/uptobox-fileupload.min.js?cacheKiller=1577364419"
></script>
</body>
</html>