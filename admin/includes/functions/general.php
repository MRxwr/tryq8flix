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

// showing the response in a json form \\
function dataOutput($data){
	$response["ok"] = true;
	$response["error"] = "0";
	$response["status"] = "successful";
	$response["data"] = $data;
	return json_encode($response);
}

// showing erros in json form \\
function dataError($data){
	$response["ok"] = false;
	$response["error"] = "1";
	$response["status"] = "Error";
	$response["data"] = $data;
	return json_encode($response);
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

//random letter
function randomLetter(){
	return substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"),0,1);
}

function scrapePage($url) {
	GLOBAL $scrappingBeeToken;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_USERAGENT, "{$_SERVER['HTTP_USER_AGENT']}");
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	$response = curl_exec($ch);
	curl_close($ch);

    return $response;
}

function curlCall($url) {
	var_dump($url. "\n" . $_SERVER['HTTP_USER_AGENT']);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_USERAGENT, "{$_SERVER['HTTP_USER_AGENT']}");
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

function searchFile($path, $fileName) {
	if ($handle = opendir($path)) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry == $fileName) {
				closedir($handle);
				return $entry;
			}
		}
		closedir($handle);
	}
	return false;
}

function makeRequest($url, $postData = null, $referer = null) {
    $ch = curl_init();
    $headers = [
        //'Accept: */*',
        //'Accept-Language: en-US,en;q=0.5',
        //'Accept-Encoding: gzip, deflate',
        'X-Requested-With: XMLHttpRequest',
        //'Connection: keep-alive',
        //'Sec-Fetch-Dest: empty',
        //'Sec-Fetch-Mode: cors',
        //'Sec-Fetch-Site: same-origin',
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
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
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

function getJsonDataApi($shows) { 
    $user = checkLogin();
    
    if (!is_array($shows) || empty($shows) || empty($user["id"])) {
        return json_encode(["error" => "No result."]);
    }

    $output = [];

    foreach ($shows as $i => $show) {
        $checkVideoType = str_replace(
            ["film", "post", "episode"], 
            "watch", 
            $show["href"]
        );

        if (strstr($show["href"], "episode")) {
            $categoryType = "categoryTitleTv";
            $episode = $show["episode"];
        } elseif (strstr($show["href"], "film")) {
            $categoryType = "categoryTitleMovie";
            $episode = "تشغيل";
        } else {
            $categoryType = "categoryTitlePost";
            $episode = "تشغيل";
        }

        $realTitle = explode("الحلقة", $show["title"])[0];

        $output[] = [
            "category" => $show["category"],
            "categoryType" => $categoryType,
            "title" => $realTitle,
            "image" => $show["image"],
            "href" => $show["href"],
            "videoType" => $checkVideoType,
            "episode" => $episode,
            "description" => substr($show["description"], 0, 100) . "..."
        ];
    }

    return json_encode($output, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

?>