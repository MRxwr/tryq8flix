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
		return 0;
	}
}

function selectDBNew($table, $placeHolders, $where, $order){
    GLOBAL $dbconnect;
    $check = [';', '"'];
    $where = str_replace($check, "", $where);
    $sql = "SELECT * FROM `{$table}`";
    if(!empty($where)) {
        $sql .= " WHERE {$where}";
    }
    if(!empty($order)) {
        $sql .= " ORDER BY {$order}";
    }
    if($stmt = $dbconnect->prepare($sql)) {
        $types = str_repeat('s', count($placeHolders));
        $stmt->bind_param($types, ...$placeHolders);
        $stmt->execute();
        $result = $stmt->get_result();
        $array = array();
        while ($row = $result->fetch_assoc()) {
            $array[] = $row;
        }
        if(isset($array) && is_array($array)) {
            return $array;
        }else{
            return 0;
        }
    }else{
        return 0;
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

function selectDB2($select, $table, $where){
    GLOBAL $dbconnect;
    $check = [';', '"'];
    $where = str_replace($check, "", $where);
    $sql = "SELECT {$select} FROM `{$table}`";
    if (!empty($where)) {
        $sql .= " WHERE {$where}";
    }
    if ($stmt = $dbconnect->prepare($sql)) {
        $stmt->execute();
        $result = $stmt->get_result();
        $array = array();
        while ($row = $result->fetch_assoc()) {
            $array[] = $row;
        }
        if (isset($array) && is_array($array)) {
            return $array;
        } else {
            return 0;
        }
    } else {
        $error = array("msg" => "select table error");
        return $error;
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
    $check = [';', '"'];
    $keys = array_keys($data);
    $sql = "INSERT INTO `{$table}`(";
    $placeholders = "";
    foreach ($keys as $key) {
        $sql .= "`{$key}`,";
        $placeholders .= "?,";
    }
    $sql = rtrim($sql, ",");
    $placeholders = rtrim($placeholders, ",");
    $sql .= ") VALUES ({$placeholders})";
    $stmt = $dbconnect->prepare($sql);
    $types = str_repeat('s', count($data));
    $stmt->bind_param($types, ...array_values($data));
    if($stmt->execute()){
        return 1;
    }else{
        return 0;
    }
}

function updateDB($table, $data, $where) {
    GLOBAL $dbconnect;
    $check = [';', '"'];
    $where = str_replace($check, "", $where);
    $keys = array_keys($data);
    $sql = "UPDATE `" . $table . "` SET ";
    $params = "";
    for ($i = 0; $i < sizeof($data); $i++) {
        $sql .= "`" . $keys[$i] . "` = ?";
        if (isset($keys[$i + 1])) {
            $sql .= ", ";
        }
        $params .= "s";
    }
    $sql .= " WHERE " . $where;
    $stmt = $dbconnect->prepare($sql); 
    $values = array_values($data);
    $stmt->bind_param($params, ...$values);
    if ($stmt->execute()) {
        return 1;
    } else {
        return 0;
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

//random letter
function randomLetter(){
	return substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"),0,1);
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
				<div class='col-xl-4 col-lg-6 col-md-6 col-sm-12 p-1'>
					<div class='card w-100'>
						<div class='card-body'>
							<div class='row w-100 p-0 m-0'>
								<div class='col-4 p-1'>
									<img src='requests?type=getImages&url={$shows[$i]["image"]}' style='width:100%;height:170px;border-radius: 10px; box-shadow: 0px 0px 10px 0px black;'>
								</div>
								<div class='col-8 p-1'>
									<div style='height:170px; overflow:auto;text-align: -webkit-right;' class='pt-2'>
										<h6 class='card-title {$catgoryType}' id='".str_replace(' ','-',$shows[$i]["category"])."' style='color:#9f8d5c'><b>{$shows[$i]["category"]}</b></h6>
										<h6 class='card-title postTitle{$i}'>{$realTitle[0]}</h6>
										<p class='card-text'>
											<b>العنوان:</b> {$shows[$i]["episode"]}<br>
											<b>التفاصيل:</b> ".substr($shows[$i]["description"],0,100)."...
										</p>
									</div>
								</div>
								<div class='col-6 p-1'>
									<div data-bs-toggle='modal' data-bs-target='#playVideo' class='btn btn-danger w-100 playVideo nextBtn' id='{$checkVideoType}'><i class='bi bi-play-fill'></i> {$shows[$i]["episode"]}</div>
								</div>
								<div class='col-6 p-1'>
									<div data-bs-toggle='modal' data-bs-target='#threeDots' class='btn btn-warning w-100 threeDots nextBtn' id='{$shows[$i]["href"]}'><i class='bi bi-three-dots'></i></div>
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

function outputData2($shows) { 
    $user = checkLogin();
    $output = "";
    
    if (is_array($shows) && !empty($shows) && !empty($user["id"])) {
        for ($i = 0; $i < sizeof($shows); $i++) {
            $checkVideoType = str_replace("film", "watch", str_replace("post", "watch", str_replace("episode", "watch", $shows[$i]["href"])));

            if (strstr($shows[$i]["href"], "episode")) {
                $catgoryType = "categoryTitleTv";
                $shows[$i]["episode"] = $shows[$i]["episode"];
            } elseif (strstr($shows[$i]["href"], "film")) {
                $catgoryType = "categoryTitleMovie";
                $shows[$i]["episode"] = "تشغيل";
            } else {
                $catgoryType = "categoryTitlePost";
                $shows[$i]["episode"] = "تشغيل";
            }

            $realTitle = explode("الحلقة", $shows[$i]["title"]);

            $output .= "
                <div class='col-xl-4 col-lg-6 col-md-6 col-sm-12 p-1'>
                    <div class='card w-100'>
                        <div class='card-body'>
                            <div class='row w-100 p-0 m-0'>
                                <div class='col-4 p-1'>
                                    <img src='{$shows[$i]["image"]}' style='width:100%;height:170px;border-radius: 10px; box-shadow: 0px 0px 10px 0px black;'>
                                </div>
                                <div class='col-8 p-1'>
                                    <div style='height:170px; overflow:auto; text-align: -webkit-right;' class='pt-2'>
                                        <h6 class='card-title {$catgoryType}' id='" . str_replace(' ', '-', $shows[$i]["category"]) . "' style='color:#9f8d5c'><b>{$shows[$i]["category"]}</b></h6>
                                        <h6 class='card-title postTitle{$i}'>{$realTitle[0]}</h6>
                                    </div>
                                </div>
                                <div class='col-6 p-1'>
                                    <div data-bs-toggle='modal' data-bs-target='#playVideo' class='btn btn-danger w-100 playVideo nextBtn' id='{$checkVideoType}'><i class='bi bi-play-fill'></i> {$shows[$i]["episode"]}</div>
                                </div>
                                <div class='col-6 p-1'>
                                    <div data-bs-toggle='modal' data-bs-target='#threeDots' class='btn btn-warning w-100 threeDots nextBtn' id='{$shows[$i]["href"]}'><i class='bi bi-three-dots'></i></div>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>
            ";
        }
        echo $output;
    } else {
        echo "<h1 class='text-center mt-5'>No result.</h1>";
    }
}

function outputData3($shows) { 
    $user = checkLogin();
    $output = "";
    
    if (is_array($shows) && !empty($shows) && !empty($user["id"])) {
        for ($i = 0; $i < sizeof($shows); $i++) {
            $checkVideoType = str_replace("film", "watch", str_replace("post", "watch", str_replace("episode", "watch", $shows[$i]["href"])));

            if (strstr($shows[$i]["href"], "episode")) {
                $catgoryType = "categoryTitleTv";
                $shows[$i]["episode"] = $shows[$i]["episode"];
            } elseif (strstr($shows[$i]["href"], "film")) {
                $catgoryType = "categoryTitleMovie";
                $shows[$i]["episode"] = "تشغيل";
            } else {
                $catgoryType = "categoryTitlePost";
                $shows[$i]["episode"] = "تشغيل";
            }

            $realTitle = explode("الحلقة", $shows[$i]["title"]);

            $output .= "
                <div class='col-xl-4 col-lg-6 col-md-6 col-sm-12 p-1'>
                    <div class='card w-100'>
                        <div class='card-body'>
                            <div class='row w-100 p-0 m-0'>
                                <div class='col-4 p-1'>
                                    <img src='{$shows[$i]["image"]}' style='width:100%;height:170px;border-radius: 10px; box-shadow: 0px 0px 10px 0px black;'>
                                </div>
                                <div class='col-8 p-1'>
                                    <div style='height:170px; overflow:auto; text-align: -webkit-right;' class='pt-2'>
                                        <h6 class='card-title {$catgoryType}' id='" . str_replace(' ', '-', $shows[$i]["category"]) . "' style='color:#9f8d5c'><b>{$shows[$i]["category"]}</b></h6>
                                        <h6 class='card-title postTitle{$i}'>{$realTitle[0]}</h6>
                                    </div>
                                </div>
                                <div class='col-6 p-1'>
                                    <div data-bs-toggle='modal' data-bs-target='#playVideo' class='btn btn-danger w-100 playVideo nextBtn' id='{$checkVideoType}'><i class='bi bi-play-fill'></i> {$shows[$i]["episode"]}</div>
                                </div>
                                <div class='col-6 p-1'>
                                    <div data-bs-toggle='modal' data-bs-target='#threeDots' class='btn btn-warning w-100 threeDots nextBtn' id='{$shows[$i]["href"]}'><i class='bi bi-three-dots'></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            ";
        }
        echo $output;
    } else {
        echo "<h1 class='text-center mt-5'>No result.</h1>";
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
	  CURLOPT_URL => "https://app.scrapingbee.com/api/v1/?api_key={$scrappingBeeToken}&stealth_proxy=true&url=". urlencode("{$url}"),
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
	curl_setopt($ch, CURLOPT_USERAGENT, "{$_SERVER['HTTP_USER_AGENT']}");
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	$response = curl_exec($ch);
	curl_close($ch);

    return $response;
}

function curlCall($url) {
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


function searchShahidListing($url){
	GLOBAL $website, $_GET;
	$collection = ( isset($_GET["collection"]) ) ? "?order={$_GET["collection"]}" : "" ;
	$category = ( isset($_GET["category"]) ) ? "&category={$_GET["category"]}" : "" ;
	$html = scrapePage($url.$collection.$category);
    var_dump($html); die();
	$dom = str_get_html($html);
	$data = [
		'shows' => []
	];
	if ($dom) {
		foreach ($dom->find('.shows-container .show-card') as $show) {
			$style = $show->style;
			preg_match('/\burl\s*\(\s*[\'"]?(.*?)[\'"]?\s*\)/', $style, $matches);
			$imageUrl = isset($matches[1]) ? $matches[1] : '';
			$jsonData = [
				'href' => $show->href,
				'image' => trim($imageUrl),
				'episode' => $show->find('.ep', 0)->plaintext,
				'category' => $show->find('.categ', 0)->plaintext,
				'title' => $show->find('.title', 0)->plaintext,
				'description' => ''//trim(preg_replace('/\s+/', ' ', $show->find('.description', 0)->plaintext)),
			];
			$data['shows'][] = $jsonData;
		}
		$shows = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	} else {
		echo 'Error: Invalid DOM object.';
		$shows = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	}

	$shows = ( isset($shows) && !empty($shows) ) ? json_decode($shows,true) : array() ;
	return $shows = $shows["shows"];
	$dom->clear();
	unset($dom);
}

function shahidMore($url){
    $html = scrapePage("{$url}");
    $htmlDom = str_get_html($html);
    $seasonsData = [];
    foreach ($htmlDom->find('div.items a.epss') as $linkNode) {
        $link = $linkNode->href;
        $title = trim($linkNode->find('h3', 0)->plaintext);
                if (stripos($link, 'season') !== false) {
            $seasonsData[] = [
                'link' => $link,
                'title' => $title,
                'season_number' => ''
            ];
        }
    }
    $episodesData = [];
    foreach ($htmlDom->find('div.items a.epss') as $linkNode) {
        $link = $linkNode->href;
        $title = trim($linkNode->find('h3', 0)->plaintext);
        if (stripos($link, 'season') === false) {
            $episodesData[] = [
                'link' => $link,
                'title' => $title,
                'episode_number' => ''
            ];
        }
    }
	if (strpos(strtolower($url), 'season') === false){
		$episodesData = array_reverse($episodesData);
		$seasonsData = array_reverse($seasonsData);
	}
    $data = [
        'seasons' => $seasonsData,
        'episodes' => $episodesData
    ];
    $htmlDom->clear();
	unset($htmlDom);
    return $data;
}

function shahidServers($url){
    $url = str_replace("film","watch",str_replace("post","watch",str_replace("episode","watch",$url)));
    $mainServer = [];
    $html = scrapePage("{$url}");
    $pattern = '/let servers\s*=\s*JSON\.parse\(\'(.*?)\'\);/s';
    preg_match($pattern, $html, $matches);
    if (isset($matches[1])) {
        $serversData = json_decode($matches[1], true);
        $server = json_encode($serversData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo 'Error: Server information not found.';
		$server = json_encode(array());
    }
    $servers = json_decode($server,true);
    foreach ($servers as $server) {
        $mainServer[]["link"] = $server["url"];
    }
    return $mainServer;
}

function domTopCinema($url) {
    $html = curlCall($url);
	$dom = str_get_html($html);
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

function TopCenimaListings($url) {
    $html = curlCall($url);
    $htmlDom = str_get_html($html);
    $seasonsData = [];
    $episodesData = [];
    foreach ($htmlDom->find('section.allseasonss .Small--Box.Season') as $seasonBox) {
        $link = $seasonBox->find('a', 0)->href;
        $title = trim($seasonBox->find('.title', 0)->plaintext);
        $seasonNumber = trim($seasonBox->find('.epnum', 0)->plaintext);
        $seasonNumber = preg_replace('/[^0-9]/', '', $seasonNumber); // Extract only the number

        $seasonsData[] = [
            'link' => $link,
            'title' => $title,
            'season_number' => $seasonNumber
        ];
    }

    // Scrape episodes
    foreach ($htmlDom->find('section.allepcont .row a') as $episodeLink) {
        $link = $episodeLink->href;
        $title = trim($episodeLink->find('.ep-info h2', 0)->plaintext);
        $episodeNumber = trim($episodeLink->find('.epnum', 0)->plaintext);
        $episodeNumber = preg_replace('/[^0-9]/', '', $episodeNumber); // Extract only the number

        $episodesData[] = [
            'link' => $link,
            'title' => $title,
            'episode_number' => $episodeNumber
        ];
    }

    $data = [
        'seasons' => $seasonsData,
        'episodes' => $episodesData
    ];
    $htmlDom->clear();
	unset($htmlDom);
    return $data;
}

function topCinemaServers($url) {
    GLOBAL $website2;
    $html = curlCall("{$url}watch/");
    $dom = str_get_html($html);
    $data = [
        'shows' => []
    ];
    if ($dom) {
        foreach ($dom->find('.server--item') as $server) {
            $id = $server->getAttribute('data-id');
            $i = $server->getAttribute('data-server');
            $jsonData = [
                'id' => $id,
                'i' => $i,
                'link' => "{$url}watch/",
            ];
            $data['shows'][] = $jsonData;
        }
        $servers = json_encode($data['shows'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo 'Error: Invalid DOM object.';
        $servers = json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    $servers = json_decode($servers, true);
    $mainServer = [];
    $ajaxUrl = "https://tryq8flix.com/requests2/index?type=getServer";
    $url1 = makeRequest($ajaxUrl, array("data"=>json_encode($servers[1])), "");
    $blackList = [0,3,4,5,6];
    for ($i = 0; $i < sizeof($servers); $i++) {
        if ( $i != $blackList[$i] ) {
            $mainServer[]["link"] = $servers[$i]["link"];
        }
    }
    return $servers;
}

function scrapEgyDead($url) {
	$html = curlCall($url);
	$dom = str_get_html($html);
	$mainSection = $dom->find('.main-section', 0);
	if (strpos($url, 'category') !== false) {
		$mainSection = $dom->find('.cat-page', 0);
	}
	if (strpos($url, '?s=') !== false) {
		$mainSection = $dom->find('.posts-list', 0);
	}
	$data = [
		'shows' => []
	];
	if ($dom) {
		if ($mainSection) {
			foreach ($mainSection->find('.movieItem') as $movie) {
				$link = $movie->find('a', 0);
				$image = $movie->find('img', 0);
				$title = $movie->find('.BottomTitle', 0);
				$category = $movie->find('.cat_name', 0);
				$episode = $movie->find('.number_episode em', 0);
				$label = $movie->find('.label', 0);

				$movieData = [
					'href' => $link ? $link->href : '',
					'image' => $image ? $image->src : '',
					'title' => $title ? $title->plaintext : '',
					'category' => $category ? $category->plaintext : '',
					'episode' => $episode ? $episode->plaintext : '',
					'description' => $label ? $label->plaintext : '',
				];
				$data['shows'][] = $movieData;
			}
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

function egyDeadListing($url) {
	$_POST["id"] = $url;
	$html = $_POST["id"];
    if (strpos(strtolower($_POST["id"]), 'season') === false && strpos(strtolower($_POST["id"]), 'episode') === false) {
        echo "<div>لا يوجد المزيد من الحلقات ... شاهد الفيديو مباشرة</div>"; die();
    }
    if (strpos(strtolower($_POST["id"]), 'season') === false) {
        $html = curlCall($_POST["id"]);
        $html = extractSeasonUrlEgyDead($html);
    }
    $html = curlCall($html);
    $htmlDom = str_get_html($html);
    $seasonsData = [];
    $episodesData = [];
    
    // Scrape seasons
    foreach ($htmlDom->find('.seasons-list .movieItem') as $seasonItem) {
        $seasonLink = $seasonItem->find('a', 0);
        $link = $seasonLink->href;
        $title = $seasonLink->title;
        $seasonNumber = preg_replace('/[^0-9]/', '', $title);
        $seasonsData[] = [
            'link' => $link,
            'title' => $title,
            'season_number' => $seasonNumber
        ];
    }

    // Scrape episodes
    foreach ($htmlDom->find('.episodes-list .EpsList li') as $episodeItem) {
        $episodeLink = $episodeItem->find('a', 0);
        $link = $episodeLink->href;
        $title = $episodeLink->title;
        $episodeNumber = preg_replace('/[^0-9]/', '', $episodeLink->plaintext);
        $episodesData[] = [
            'link' => $link,
            'title' => $title,
            'episode_number' => $episodeNumber
        ];
    }

    // Reverse arrays if not a season page
    if (strpos(strtolower($_POST["id"] ?? ''), 'season') === false) {
        $episodesData = array_reverse($episodesData);
        $seasonsData = array_reverse($seasonsData);
	}
	$data = [
		'seasons' => $seasonsData,
		'episodes' => $episodesData
	];
	$htmlDom->clear();
	unset($htmlDom);
	return $data;
}

function egyDeadServers($url) {
    $_POST["id"] = $url;
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => "{$_POST["id"]}",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array('View' => '1'),
    ));
    $html = curl_exec($curl);
    curl_close($curl);
    $dom = str_get_html($html);
    $data = [
        'shows' => []
    ];
    if ($dom) {
        $serversList = $dom->find('.watchAreaMaster .serversList', 0);
        if ($serversList) {
            foreach ($serversList->find('li') as $server) {
                $link = $server->getAttribute('data-link');
                $jsonData = [
                    'link' => str_replace(" ", "", $link)
                ];
                $data['shows'][] = $jsonData;
            }
            $servers = json_encode($data['shows'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            $servers = json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    } else {
        $servers = json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    $servers = json_decode($servers, true);
	$dom->clear();
	unset($dom);
	return $servers;
}

//get list of seasons and episodes wecima 
function wecimaListing($url) {
	$html = curlCall($url);
    $htmlDom = str_get_html($html);
    $seasonsData = [];
    $episodesData = [];
    foreach ($htmlDom->find('.List--Seasons--Episodes a') as $seasonLink) {
        $link = $seasonLink->href;
        $title = trim($seasonLink->plaintext);
        $seasonNumber = preg_replace('/[^0-9]/', '', $title);
        $seasonsData[] = [
            'link' => $link,
            'title' => $title,
            'season_number' => $seasonNumber
        ];
    }
    // Scrape episodes
    foreach ($htmlDom->find('.Episodes--Seasons--Episodes a') as $episodeLink) {
        $link = $episodeLink->href;
        $title = trim($episodeLink->find('episodetitle', 0)->plaintext);
        $episodeNumber = preg_replace('/[^0-9]/', '', $title);

        $episodesData[] = [
            'link' => $link,
            'title' => $title,
            'episode_number' => $episodeNumber
        ];
    }

	if (strpos(strtolower($url), 'season') === false){
		$episodesData = array_reverse($episodesData);
		$seasonsData = array_reverse($seasonsData);
	}
	$data = [
		'seasons' => $seasonsData,
		'episodes' => $episodesData
	];
	$htmlDom->clear();
	unset($htmlDom);
	return $data;
}

function extractSeasonUrlEgyDead($html) {
    if (preg_match('/<a itemprop="url" href="(https:\/\/[^"]*\/season\/[^"]*)"/', $html, $matches)) {
        return $matches[1];
    }
    return null;
}

//get we cima video list servers 
function scrapeWecimaServers($url) {
	$html = curlCall("{$url}");
    $dom = str_get_html($html);
    $data = [
        'shows' => []
    ];
    if ($dom) {
        foreach ($dom->find('.WatchServersList ul li') as $server) {
            $btn = $server->find('btn', 0);
            if ($btn) {
                $title = $btn->find('strong', 0)->plaintext;
                $dataUrl = $btn->getAttribute('data-url');
                $jsonData = [
                    'link' => str_replace(" ", "", $dataUrl)
                ];
                $data['shows'][] = $jsonData;
            }
        }
        $servers = json_encode($data['shows'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        $servers = json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    $servers = json_decode($servers, true);
	$dom->clear();
	unset($dom);
	return $servers;
}
function scrapeWecima($url) {
    $url = ( !isset($url) || empty($url) ) ? 'https://wecima.show' : $url;
    $html = file_get_contents($url);
    $dom = str_get_html($html);
    if ($dom) {
        $data = [
            'shows' => []
        ];
        foreach ($dom->find('.Grid--WecimaPosts .GridItem') as $item) {
            $thumbDiv = $item->find('.Thumb--GridItem', 0);
            $link = $thumbDiv->find('a', 0);
            $bgSpan = $thumbDiv->find('.BG--GridItem', 0);
            $titleStrong = $thumbDiv->find('strong', 0);

            // Extract image URL from data-lazy-style attribute
            $imageUrl = '';
            if ($bgSpan) {
                preg_match('/url\((.*?)\)/', $bgSpan->getAttribute('data-lazy-style'), $matches);
                $imageUrl = isset($matches[1]) ? $matches[1] : '';
            }

            // Extract year from the title
            $year = '';
            $title = '';
            if ($titleStrong) {
                $title = $titleStrong->plaintext;
                preg_match('/\((\d{4})\)/', $title, $matches);
                $year = isset($matches[1]) ? $matches[1] : '';
                $title = trim(preg_replace('/\(\d{4}\)/', '', $title));
            }

            $jsonData = [
				'href' => $link ? $link->href : '',
				'image' => trim($imageUrl),
				'episode' => '',
				'category' => '',
				'title' => $title,
				'description' => $year,
			];
            $data['shows'][] = $jsonData;
        }
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        return 0;
    }
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

// search for file name inside a folder \\
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
?>