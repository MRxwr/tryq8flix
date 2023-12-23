<?php
require('includes/config.php');
require('includes/checksouthead.php');
if ( isset($_GET["type"]) ){
	$sql = "SELECT * FROM `category` WHERE `type` LIKE '%".$_GET["type"]."%' AND `imdbrating` LIKE '' OR `imdbrating` LIKE '%N/A%' OR `imdbrating` LIKE '%not rated%'";
	$result = $dbconnect->query($sql);
	$i = 0;
	while ( $row = $result->fetch_assoc() ){
		$title = $row["title"];
		$year = $row["releasedate"];
		$id[$i] = $row["id"];
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://www.omdbapi.com/?t=$title&y=$year&apikey=95b9e7bf',
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
		$json_a = json_decode($response, true);
		$rating[$i] = $json_a["imdbRating"];
		if ( !isset($json_a["imdbRating"]) OR empty($json_a["imdbRating"]) OR $json_a["imdbRating"] == '0' ){
			unset($rating[$i]);
			unset($id[$i]);
		}
		$i++;
	}

	for ( $i = 0 ; $i < sizeof($id) ; $i++ ){
		$sql = "UPDATE `category`
				SET
				`imdbrating` = '".$rating[$i]."'
				WHERE
				`id` LIKE '".$id[$i]."'
				";
		$resutl = $dbconnect->query($sql);
	}
?>
	Updated ... try another type <br><br>

	<form action="" method="get">
		<input name="type" type="text" >
		<input name="submit" type="submit" value="Start">
	</form>
<?php
}else{
	?>
	<form action="" method="get">
		<input name="type" type="text" >
		<input name="submit" type="submit" value="Start">
	</form>
	<?php
}