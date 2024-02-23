<?php
function direction($valEn,$valAr){
	GLOBAL $directionHTML;
	if ( $directionHTML == "rtl" ){
		$response = $valAr;
	}else{
		$response = $valEn;
	}
	return $response;
}

function selectDB($table, $where){
	GLOBAL $dbconnect;
	GLOBAL $date;
	$check = [';','"'];
	$where = str_replace($check,"",$where);
	$sql = "SELECT * FROM `".$table."`";
	if ( !empty($where) ){
		$sql .= " WHERE " . $where;
	}
	if($result = $dbconnect->query($sql)){
		while($row = $result->fetch_assoc() ){
			$array[] = $row;
		}
		if ( isset($array) AND is_array($array) ){
			return $array;
		}else{
			return 0;
		}
	}else{
		$error = array("msg"=>"select table error");
		return outputError($error);
	}
}

function selectDataDB($select, $table, $where){
	GLOBAL $dbconnect;
	GLOBAL $date;
	$check = [';','"'];
	$where = str_replace($check,"",$where);
	$sql = "SELECT {$select} FROM {$table}";
	if ( !empty($where) ){
		$sql .= " WHERE " . $where;
	}
	if($result = $dbconnect->query($sql)){
		while($row = $result->fetch_assoc() ){
			$array[] = $row;
		}
		if ( isset($array) AND is_array($array) ){
			return $array;
		}else{
			return 0;
		}
	}else{
		$error = array("msg"=>"select table error");
		return outputError($error);
	}
}

function deleteDB($table, $where){
	GLOBAL $dbconnect;
	GLOBAL $date;
	$check = [';','"'];
	$where = str_replace($check,"",$where);
	$sql = "DELETE FROM `".$table."`";
	if ( !empty($where) ){
		$sql .= " WHERE " . $where;
	}
	if($result = $dbconnect->query($sql)){
		return 1;
	}else{
		$error = array("msg"=>"delete table error");
		return outputError($error);
	}
}

function insertDB($table, $data){
	GLOBAL $dbconnect;
	GLOBAL $date;
	$check = [';','"'];
	$data = str_replace($check,"",$data);
	$keys = array_keys($data);
	$sql = "INSERT INTO `".$table."`(";
	for($i = 0 ; $i < sizeof($keys) ; $i++ ){
		$sql .= "`".$keys[$i]."`";
		if ( isset($keys[$i+1]) ){
			$sql .= ", ";
		}
	}
	$sql .= ")VALUES(";
	for($i = 0 ; $i < sizeof($data) ; $i++ ){
		$text = $data[$keys[$i]];
		$sql .= "'".$text."'";
		if ( isset($keys[$i+1]) ){
			$sql .= ", ";
		}
	}		
	$sql .= ")";
	if($dbconnect->query($sql)){
		return 1;
	}else{
		$error = array("msg"=>"insert table error");
		return outputError($error);
	}
}

function updateDB($table ,$data, $where){
	GLOBAL $dbconnect;
	GLOBAL $date;
	$check = [';','"'];
	$data = str_replace($check,"",$data);
	$where = str_replace($check,"",$where);
	$keys = array_keys($data);
	$sql = "UPDATE `".$table."` SET ";
	for($i = 0 ; $i < sizeof($data) ; $i++ ){
		$sql .= "`".$keys[$i]."` = '".$data[$keys[$i]]."'";
		if ( isset($keys[$i+1]) ){
			$sql .= ", ";
		}
	}		
	$sql .= " WHERE " . $where;
	if($dbconnect->query($sql)){
		return 1;
	}else{
		$error = array("msg"=>"update table error");
		return outputError($error);
	}
}

function sendMail($data){
	$site = $data["site"];
	$subject = $data["subject"];
	$body = $data["body"];
	$from = "noreply@tryq8flix.com";
	$to = $data["to"];
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://createid.link/api/v1/send/notify',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => array(
			'site' => $site,
			'subject' => $subject,
			'body' => $body,
			'from_email' => $from,
			'to_email' => $to
		),
	));
	$response = curl_exec($curl);
	curl_close($curl);
}

function checkLogin(){
	if( isset($_COOKIE["tryq8flix2"]) && !empty($_COOKIE["tryq8flix2"]) ){
		if( $user = selectDB("users","`keepalive` = '{$_COOKIE["tryq8flix2"]}'")){
			$profileData = array(
				"username" => $user[0]["username"],
				"uptobox" => $user[0]["uptoboxToken"],
				"logo" => $user[0]["avatar"],
				"email" => $user[0]["email"],
				"id" => $user[0]["id"]
			);
		}else{
			$profileData = array(
				"username" => "",
				"uptobox" => "",
				"logo" => "",
				"email" => "",
				"id" => ""
			);
			setcookie( "tryq8flix2", "", time() - (86400 * 30) ,"/");
			?>
			<script>
			location.reload(true);
			</script>
			<?php
		}
	}else{
		$profileData = array(
			"username" => "",
			"uptobox" => "",
			"logo" => "",
			"email" => "",
			"id" => ""
		);
	}
	return $profileData;
}

function extractUptoboxId($url) {
	if (filter_var($url, FILTER_VALIDATE_URL) === false) {
		return trim($url);
	}
	$parsedUrl = parse_url($url);
	$path = $parsedUrl['path'];
	$path = ltrim($path, '/');
	return trim($path);
}

function validateInput($input) {
  $input = filter_var($input, FILTER_SANITIZE_STRING);
  if (preg_match('/\b(SELECT|INSERT|UPDATE|DELETE|FROM|WHERE|DROP)\b/i', $input)) {
	  return false;
  }
  if (preg_match('/[;:\"\']/', $input)) {
    return false;
  }
  return true;
}

function outputData($shows){ 
	$user = checkLogin();
	/**/
	$output = "<div id='content'><div class='row m-0 w-100'>";
	if( is_array($shows) && !empty($shows) && !empty($user["id"]) ){
		for ($i = 0; $i < sizeof($shows); $i++) {
			$output .= "
				<div class='col-xl-2 col-lg-4 col-md-6 col-sm-12 p-3'>
					<div class='card w-100'>
						<div class='card-body'>
							<img src='requests/getImages.php?url=" . urlencode($shows[$i]["image"]) . "' style='width:100%;height:300px'>
							<div style='height:300px; overflow:auto'>
								<h2 class='card-title categoryTitle{$i}'><b>{$shows[$i]["category"]}</b></h2>
								<h3 class='card-title postTitle{$i}'>{$shows[$i]["title"]}</h3>
								<p class='card-text'>
									<b>Title:</b>{$shows[$i]["episode"]}<br>
									<b>Details:</b>{$shows[$i]["description"]}
								</p>
							</div>
							<div class='row w-100 p-0 m-0'>
								<div class='col-6 p-1'>
									<div data-bs-toggle='modal' data-bs-target='#playVideo' class='btn btn-danger w-100 playVideo' id='".str_replace("film","watch",str_replace("post","watch",str_replace("episode","watch",$shows[$i]["href"])))."'><i class='bi bi-play-fill'></i></div>
								</div>
								<div class='col-6 p-1'>
									<div data-bs-toggle='modal' data-bs-target='#threeDots' class='btn btn-warning w-100 threeDots' id='{$shows[$i]["href"]}'><i class='bi bi-three-dots'></i></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			";
		}
		echo "</div>".$output;
	}else{
		$msg = "<h1 class='text-center mt-5'>No result.<h1>";
		echo $msg;
	}
}

function outputImage($imageUrl) {
    $image = file_get_contents($imageUrl);
    header('Content-Type: image/jpeg');
    echo $image;
}

?>