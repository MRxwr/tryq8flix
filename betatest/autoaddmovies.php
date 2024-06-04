<?php
include_once ("includes/config.php");
include_once("includes/checksouthead.php");

$url = 'https://uptobox.com/api/upload';
$data = ['token' => 'c7592f3d7e8a2c6682fb51ebd2e9d96f6uvoo'];

$curl = curl_init();
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_URL, $url);

$result = curl_exec($curl);
curl_close($curl);

$result = explode('uploadLink":"',$result);
$result = explode('"',$result[1]);
$resultuptobox = str_replace("upload","remote",$result[0]);

if ( isset($_POST["links"]) ){
	$urls = preg_replace('/\n+/', "\n", trim($_POST['links']));
	$array = explode(PHP_EOL, $urls);
	
	$i = 0;
	while ( $i < sizeof($array) )
	{
		// ** change title from watch to view ** \\
		if( strpos($array[$i],"filmydz") === false ){
			$url = $array[$i] . "watch";
		}else{
			$url = $array[$i];
		}
		
		//$url = str_replace("watch.php","view.php",$url);
		$url = preg_replace('/\s/', '', $url);

		// ** get movie title and year ** \\
		if( strpos($url,"filmydz") === false ){
			$url = file_get_contents($url);
		}else{
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
				CURLOPT_HTTPHEADER => array(
					'Cookie: ci_session=8u8r06b5e2kmfaj26ltgj4kpcg002dj1; csrf_cookie_VideoOnline=1dc139738388d80698c23c9a4f248b91'
				),
			));
			$url = curl_exec($curl);
			curl_close($curl); 
		}
		$pattern = '/<div class="imdbS"><a href="https:\/\/www\.imdb\.com\/title\/([a-zA-Z0-9]+)"/';
		// Match the pattern against the HTML code
		if (preg_match($pattern, $url, $matches)) {
			// The first capture group ($matches[1]) contains the IMDb ID
			$imdb = $matches[1];
		}
		$pattern = '/https:\/\/uptobox\.com\/([a-zA-Z0-9]+)"/';
		// Match the pattern against the HTML code
		if (preg_match($pattern, $url, $matches)) {
			// The first capture group ($matches[1]) contains the IMDb ID
			$movieLink = "https://uptobox.com/" . $matches[1];
		}
		// ** get uptobox link ** \\
		$type = "MOVIE";

		// ** add new movie category ** \\
		$string = file_get_contents("http://www.omdbapi.com/?i=$imdb&apikey=95b9e7bf");
		$json_a = json_decode($string, true);
		$jsonIterator = new RecursiveIteratorIterator( new RecursiveArrayIterator(json_decode($string, TRUE)),RecursiveIteratorIterator::SELF_FIRST);
		foreach ($jsonIterator as $key => $val) {
			if( is_array($val) ){
				"$key:\n";
			} else{
				$val = str_replace('"','',str_replace("'","",$val));
				if ( $key == "Title" ){
					$title = $val;
				}
				if ( $key == "Year" ){
					$Year = $val;
				}
				if ( $key == "Rated" ){
					$rating = $val;
				}
				if ( $key == "imdbRating" ){
					$imdbrating = $val;
				}
				if ( $key == "Released" ){
					$releasedate = $val;
					$releasedate = substr($releasedate, -4);
				}
				if ( $key == "Runtime" ){
					$duration = $val;
				}
				if ( $key == "Genre" ){
					$genre = $val;
				}
				if ( $key == "Actors" ){
					$channel = $val;
				}
				if ( $key == "Plot" ){
					$description = $val;
				}
				if ( $key == "Language" ){
					$language = $val;
				}
				if ( $key == "Country" ){
					$country = $val;
				}
				if ( $key == "Poster" ){
					$poster = $val;
				}		
			}
		}

		$sql= "SELECT `id` FROM `category` WHERE `title` LIKE '{$title}' AND `type` LIKE '{$type}'";
		$result = $dbconnect->query($sql);
		if ( $result->num_rows > 0 ){
			goto jump;
		}

		if( strpos($url,"btn-trailer") === false ){
			$youtube = file_get_contents("https://www.youtube.com/results?search_query=".urlencode($title." ".$releasedate)."+trailer&aq=1&hl=en");
			$explode7 = explode("watch?v=",$youtube);
			$explode8 = explode('"',$explode7[1]);
			$trailer = "https://www.youtube.com/embed/" . $explode8[0];
		}else{
			$explode7 = explode("https://www.youtube.com/embed/",$url);
			$explode8 = explode("'",$explode7[1]);
			$trailer = "https://www.youtube.com/embed/" . $explode8[0];
		}

		$postdate = date("Y/m/d");
		$posttime = date("g:i A");

		$sql= "SELECT id FROM category ORDER BY id DESC LIMIT 1";
		$result = $dbconnect->query($sql);
		$row = $result->fetch_assoc();
		$id = $row["id"]+1;
		$sql = "INSERT INTO category (id,type, title, rating, imdbrating, duration, genre, releasedate, posttime, postdate, language, country, channel, poster, description, trailer) VALUES ('$id','$type', '$title', '$rating', '$imdbrating', '$duration', '$genre', '$releasedate', '$posttime', '$postdate', '$language', '$country', '$channel', '$poster', '$description', '$trailer')";
		$result = $dbconnect->query($sql);

		// ** jump to next title if any ** \\ 


		// ** saving uptobox links ** \\
		$uptoboxLinks[] = $movieLink;
		
		jump:
		$i++;
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
			if ( !empty($uptoboxLinks) ){
		?>
				<h3 style='text-align: center; color:red'>Your uptobox links are ready:</h3>
				
				<?php
				$i = 0 ;
				echo "<textarea style='width:100%; height:300px'>";
				while ( $i < sizeof($uptoboxLinks) )
				{
					echo $uptoboxLinks[$i] . "\n";
					$i = $i + 1 ;
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