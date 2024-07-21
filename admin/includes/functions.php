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
	$output = "";
	if( is_array($shows) && !empty($shows) && !empty($user["id"]) ){
		for ($i = 0; $i < sizeof($shows); $i++) {
			$checkVideoType = str_replace("film","watch",str_replace("post","watch",str_replace("episode","watch",$shows[$i]["href"])));
			if( strstr($shows[$i]["href"],"episode") ){
				$catgoryType = "categoryTitleTv";
				$shows[$i]["episode"] = $shows[$i]["episode"];
			}elseif( strstr($shows[$i]["href"],"film") ){
				$catgoryType = "categoryTitleMovie";
				$shows[$i]["episode"] = "تشغيل";
			}else{
				$catgoryType = "categoryTitlePost";
				$shows[$i]["episode"] = "تشغيل";
			}
			$realTitle = explode("الحلقة",$shows[$i]["title"]);
			$output .= "
				<div class='col-xl-3 col-lg-4 col-md-4 col-sm-12 p-3'>
					<div class='card w-100'>
						<div class='card-body'>
							<img src='requests?type=getImages&url={$shows[$i]["image"]}' style='width:100%;height:300px;border-radius: 10px; box-shadow: 0px 0px 10px 0px black;'>
							<div style='height:250px; overflow:auto;text-align: -webkit-right;' class='pt-2'>
								<h4 class='card-title {$catgoryType}' id='".str_replace(' ','-',$shows[$i]["category"])."' style='color:#9f8d5c'><b>{$shows[$i]["category"]}</b></h2>
								<h5 class='card-title postTitle{$i}'>{$realTitle[0]}</h3>
								<p class='card-text'>
									<b>العنوان:</b> {$shows[$i]["episode"]}<br>
									<b>التفاصيل:</b> {$shows[$i]["description"]}
								</p>
							</div>
							<div class='row w-100 p-0 m-0'>
								<div class='col-6 p-1'>
									<div data-bs-toggle='modal' data-bs-target='#playVideo' class='btn btn-danger w-100 playVideo' id='{$checkVideoType}'><i class='bi bi-play-fill'></i> {$shows[$i]["episode"]}</div>
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
		echo $output;
	}else{
		$msg = "<h1 class='text-center mt-5'>No result.<h1>";
		echo $msg;
	}
}

function outputData2($shows){ 
	$user = checkLogin();
	$output = "";
	if( is_array($shows) && !empty($shows) && !empty($user["id"]) ){
		for ($i = 0; $i < sizeof($shows); $i++) {
			$checkVideoType = str_replace("film","watch",str_replace("post","watch",str_replace("episode","watch",$shows[$i]["href"])));
			if( strstr($shows[$i]["href"],"episode") ){
				$catgoryType = "categoryTitleTv";
				$shows[$i]["episode"] = $shows[$i]["episode"];
			}elseif( strstr($shows[$i]["href"],"film") ){
				$catgoryType = "categoryTitleMovie";
				$shows[$i]["episode"] = "تشغيل";
			}else{
				$catgoryType = "categoryTitlePost";
				$shows[$i]["episode"] = "تشغيل";
			}
			$output .= "
				<div class='col-xl-3 col-lg-4 col-md-4 col-sm-12 p-3'>
					<div class='card w-100'>
						<div class='card-body'>
							<img src='{$shows[$i]["image"]}' style='width:100%;height:300px;border-radius: 10px; box-shadow: 0px 0px 10px 0px black;'>
							<div style='height:150px; overflow:auto;text-align: -webkit-right;' class='pt-2'>
								<h4 class='card-title {$catgoryType}' id='".str_replace(' ','-',$shows[$i]["category"])."' style='color:#9f8d5c'><b>{$shows[$i]["category"]}</b></h2>
								<h5 class='card-title postTitle{$i}'>{$shows[$i]["title"]}</h3>
							</div>
							<div class='row w-100 p-0 m-0'>
								<div class='col-6 p-1'>
									<div data-bs-toggle='modal' data-bs-target='#playVideo' class='btn btn-danger w-100 playVideo' id='{$checkVideoType}'><i class='bi bi-play-fill'></i> {$shows[$i]["episode"]}</div>
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
		echo $output;
	}else{
		$msg = "<h1 class='text-center mt-5'>No result.<h1>";
		echo $msg;
	}
}

function scrapePage($url) {
	GLOBAL $scrappingBeeToken;
	/*
	//var_dump($website.$collection.$category); die();
	//https://api.scraperapi.com/?api_key=ab4a8e030c1a48956b52356ec985bf14&render=true&follow_redirect=false&url=
	//https://app.scrapingbee.com/api/v1/?api_key={$scrappingBeeToken}&url=https%3A%2F%2Fshvip.cam%2F
	//https://app.scrapingbee.com/api/v1/?api_key={$scrappingBeeToken}&render_js=false&premium_proxy=true&country_code=kw&url=
	//https://app.scrapingbee.com/api/v1/?api_key={$scrappingBeeToken}&stealth_proxy=true&url=
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://app.scrapingbee.com/api/v1/?api_key={$scrappingBeeToken}&render_js=false&url=". urlencode("{$url}"),
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
*/
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	$response = curl_exec($ch);
	return $response;

    return $response;
}

function curlCall($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	$response = curl_exec($ch);
	return $response;
}

function outputImage($imageUrl) {
    $image = file_get_contents($imageUrl);
    header('Content-Type: image/jpeg');
    echo $image;
}

// make function to convert image url to base64
function convertImage($imageUrl) {
	$image = file_get_contents($imageUrl);
	return base64_encode($image);
}

function domTopCinema($dom) {
	$data = [
		'shows' => []
	];
	if ($dom) {
		foreach ($dom->find('.Posts--List .Small--Box') as $show) {
			$link = $show->find('a', 0);
			$image = $show->find('img', 0);
			$genre = $show->find('.liList li', 0);
			$title = $show->find('h3', 0);
			$jsonData = [
				'href' => $link->href,
				'image' => $image->getAttribute('data-src'),
				'episode' => '', // Not present in the provided HTML
				'category' => $genre ? $genre->plaintext : '',
				'title' => $title ? $title->plaintext : '',
				'description' => '', // Not present in the provided HTML
			];
			$data['shows'][] = $jsonData;
		}
		$shows = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	} else {
		echo 'Error: Invalid DOM object.';
		$shows = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	}
	$shows = ( isset($shows) && !empty($shows) ) ? json_decode($shows,true) : array() ;
	$dom->clear();
	unset($dom);
	return $shows = $shows["shows"];
}

function makeRequest($url, $postData = null, $referer = null) {
    $ch = curl_init();
    $headers = [
        'Accept: */*',
        'Accept-Language: en-US,en;q=0.5',
        'Accept-Encoding: gzip, deflate',
        'X-Requested-With: XMLHttpRequest',
        'Connection: keep-alive',
        'Sec-Fetch-Dest: empty',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Site: same-origin',
    ];
    if ($referer) {
        $headers[] = 'Referer: ' . $referer;
    }
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0',
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_ENCODING => '',
    ]);
    if ($postData) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    }
    $response = curl_exec($ch);
    curl_close($ch);
    $link = extractLink($response);
    return $link;
}

function extractLink($html) {
    if (preg_match('/<iframe.*?src="(.*?)"/', $html, $matches)) {
        return $matches[1];
    }
    if (preg_match('/https?:\/\/[^\s<>"]+/', $html, $matches)) {
        return $matches[0];
    }
    return "";
}
?>