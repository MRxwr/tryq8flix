<?php
require_once("includes/config.php");
require_once("includes/checksouthead.php");
require_once("includes/functions.php");
if ( isset($_POST["links"]) && !empty($_POST["links"]) ){
	
	$array = preg_replace('/\n+/', "\n", trim($_POST['links']));
	$array = explode(PHP_EOL, $array);
	$i = 0;
	$finallinks = array();
	for( $i = 0; $i < sizeof($array); $i++ ){
		$array[$i] = trim(preg_replace('/\s+/', ' ', $array[$i]));
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $array[$i],
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => array('View' => '1'),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		$pattern = '/مشاهدة فيلم (.*?) (\d{4}) مترجم/i';
		// Perform the regular expression match
		if (preg_match($pattern, $response, $matches)) {
			$movieTitle = trim(preg_replace('/\d+\s*/', '', $matches[1]));
			$year = $matches[2];
		}
		$url = "http://www.omdbapi.com/?apikey=95b9e7bf&y={$year}&t=".urlencode($movieTitle);
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url, 
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'GET',
		));
		$response = curl_exec($curl);
		$response = json_decode($response,true);
		curl_close($curl);
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $array[$i],
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => array('View' => '1'),
		));
		$response1 = curl_exec($curl);
		curl_close($curl);
		preg_match('/href="([^"]*uptobox\.[^"]*)"/', $response1, $match);
		$downloadLink[] = $match[1];
		
		$youtube = file_get_contents("https://www.youtube.com/results?search_query=".urlencode($movieTitle." ".$year)."+trailer&aq=1&hl=en");
		$explode = explode("watch?v=",$youtube);
		$explode = explode('"',$explode[1]);
		$trailer = "https://www.youtube.com/embed/" . $explode[0];
		
		$insertMovieData = array(
			"type" => "MOVIE",
			"title" => $response["Title"],
			"rating" => $response["Rated"],
			"imdbrating" => $response["imdbRating"],
			"duration" => $response["Runtime"],
			"genre" => $response["Genre"],
			"releasedate" => $response["Year"],
			"imdbId" => $response["imdbID"],
			"language" => $response["Language"],
			"country" => $response["Country"],
			"channel" => $response["Actors"],
			"poster" => $response["Poster"],
			"description" => $response["Plot"],
			"trailer" => $trailer
		);
		insertDB("category",$insertMovieData);
	}
}
?>

<!DOCTYPE html>
<html>
<title>Add Category - Q8FLiX</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="images/logo.png">
<link rel="stylesheet" href="css/style1.css?dsdsa">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
html,body,h1,h2,h3,h4,h5,h6 {font-family: "Roboto", sans-serif}
</style>
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
<div class="w3-content" style="max-width: 1300px">

  <!-- The Grid -->
  <div class="">
 
<?php
require ("template/header.php");
?>
    <!-- Right Column -->
    <div class="w3-text-white" style="padding-top: 40px">
      <div class="w3-row-padding w3-padding-16 w3-center" id="food" >
	  <?php
		if ( !isset($_POST["links"]) )
		{
			?>
	    <h2 style="color: gold"> INSERT Movie Links </h2>
		<form method="post" action="" enctype="multipart/form-data">
		<table style="width:100%">
			<tr>
				<td>
					<textarea name="links" style="width: 100%; height: 300px;"></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<input type="submit" name="submit" value="Work The Magic Now" style="width:100%">
				</td>
			<tr>
		</table>
		</form>
		<?php
		}else{
			if ( !empty($downloadLink) ){
		?>
				<h3 style='text-align: center; color:red'>Your uptobox links are ready:</h3>
				
				<?php
				$i = 0 ;
				echo "<textarea style='width:100%; height:300px'>";
				for ( $i = 0; $i < sizeof($downloadLink); $i++ ){
					echo $downloadLink[$i] . "\n";
				}
				echo "</textarea>";
				?>
				
				<h3 style='text-align: center; color:tan'>Move Links to Uptobox:</h3>
				<div class='max d-flex column'>
				<div id='content' style="width: 100%" data-ui='{&quot;sendToUI&quot;:{&quot;promoEnd&quot;:1615816984},&quot;uploadUrl&quot;:&quot;<?php echo $resultuptobox ?>&quot;,&quot;text&quot;:{&quot;start_upload&quot;:&quot;Start upload&quot;,&quot;one_url_per_line&quot;:&quot;Copy the above generated links here&quot;}}'>
				<div id='homepage'>
				<div class='remote-url-content'></div>
				</div>
				</div>
		<?php
			}else{
				?>
				<h3 style='text-align: center; color:red'>All Movies are already posted...</h3>
				<?php
			}
		}
		?>
	  </div>
    </div>
    
  <!-- End Grid -->
  </div>
  
  <!-- End Page Container -->


<?php
require ("template/footer.php");
?>
</div>
<script type='text/javascript' src="https://uptobox.com/dist/uptobox-fileupload.min.js?cacheKiller=1589196732"></script>

</body>
</html>