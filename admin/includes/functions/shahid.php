<?php
function shahidCurl($url) {
    $curl = curl_init();
    /*
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://viewsource.net/api/source/stream',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => json_encode(array('url' => $url)),
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Accept: application/json, text/plain, ',
        'Origin: https://viewsource.net',
        'Referer: https://viewsource.net/'
      ),
    ));a
    */

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://www.codebeautify.com/URLService',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array('path' => $url),
    CURLOPT_HTTPHEADER => array(
        'Origin: https://codebeautify.org',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
    ),
    CURLOPT_SSL_VERIFYPEER => false
));

    $response = curl_exec($curl);
    $htmlContent = $response;
    curl_close($curl);
    /*
    // Parse event stream response
    $lines = explode("\n", $response);
    $htmlContent = '';

	foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        
        // Check if line starts with "data:" prefix (common in event streams)
        if (strpos($line, 'data:') === 0) {
            $line = trim(substr($line, 5)); // Remove "data:" prefix
        }
        
        $chunk = json_decode($line, true);
        if (isset($chunk['type']) && $chunk['type'] === 'chunk' && isset($chunk['data'])) {
            $htmlContent .= $chunk['data'];
        } elseif (isset($chunk['data'])) {
            // Fallback if type is not set
            $htmlContent .= $chunk['data'];
        }
    }
    */
    return $htmlContent;
}

function searchShahidListing($url){
	GLOBAL $website, $_GET;
	$collection = ( isset($_GET["collection"]) ) ? "?order={$_GET["collection"]}" : "" ;
	$category = ( isset($_GET["category"]) ) ? "&category={$_GET["category"]}" : "" ;
	$html = curlCall($url.$collection.$category);
    var_dump($html); die();
    // Debug: Check if HTML content is valid
    if (empty($html)) {
        echo 'Error: Empty HTML response from shahidCurl.';
        return [];
    }
    
    // Clean up HTML before parsing
    $html = trim($html);
    
    //var_dump($html); die();
	$dom = str_get_html($html);
	$data = [
		'shows' => []
	];
	if ($dom) {
		foreach ($dom->find('.shows-container .show-card') as $show) {
			$style = $show->style;
			preg_match('/\burl\s*\(\s*[\'"]?(.*?)[\'"]?\s*\)/', $style, $matches);
			$imageUrl = isset($matches[1]) ? $matches[1] : '';
            
            // Use image proxy for all images to avoid CORS/hotlink issues
            if (!empty($imageUrl)) {
                $imageUrl = "https://".$_SERVER['HTTP_HOST']."/image-proxy.php?url=".urlencode(trim($imageUrl));
            }
            
            $href = $show->href;
            $parts = explode('/', $href);
            if( count($parts) >= 5 ){
                $parts[4] = urlencode($parts[4]);
                $href = implode('/', $parts);
            }
			$jsonData = [
				'href' => $href,
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
    $html = shahidCurl("{$url}");
    $htmlDom = str_get_html($html);
    $seasonsData = [];
    foreach ($htmlDom->find('div.items a.epss') as $linkNode) {
        $link = $linkNode->href;
        $parts = explode('/', $link);
        if( count($parts) >= 5 ){
            $parts[4] = urlencode($parts[4]);
            $link = implode('/', $parts);
        }
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
        $parts = explode('/', $link);
        if( count($parts) >= 5 ){
            $parts[4] = urlencode($parts[4]);
            $link = implode('/', $parts);
        }
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
    $html = shahidCurl("{$url}");
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
?>